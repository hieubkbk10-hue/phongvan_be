# Công thức refactor truy vấn Laravel 9

## N+1 và constrained eager loading

Đặt query tại Task/Repository. Đếm query trước và sau; eager loading không mặc định tốt nếu relation lớn hoặc không dùng.

```php
$interviews = Interview::query()
    ->select(['id', 'candidate_id', 'scheduled_at'])
    ->with([
        'candidate:id,name',
        'evaluations' => fn ($query) => $query
            ->select(['id', 'interview_id', 'score'])
            ->where('status', 'published'),
    ])
    ->whereBetween('scheduled_at', [$from, $to])
    ->get();
```

Khi giới hạn cột relation, phải giữ primary key và foreign key dùng để Eloquent ghép kết quả. Với aggregate, cân nhắc `withCount`, `withExists` hoặc subquery thay vì tải toàn bộ relation.

## Cursor pagination có thứ tự xác định

Laravel 9 hỗ trợ `cursorPaginate()`. Cursor cần order ổn định, deterministic và có unique tie-breaker; các cột order nên non-null và không thay đổi trong lúc duyệt.

```php
$orders = DB::table('orders')
    ->select(['id', 'user_id', 'total_amount', 'created_at'])
    ->where('status', 'paid')
    ->orderByDesc('created_at')
    ->orderByDesc('id')
    ->cursorPaginate(50);
```

Index candidate: `(status, created_at, id)`. Kiểm tra direction/version support và EXPLAIN trên MySQL thực tế. Cursor phù hợp next/previous traversal, không thay thế offset khi UX bắt buộc nhảy đến số trang tùy ý hoặc cần total count chính xác.

## Deferred join cho offset sâu

Deferred join giảm lượng payload/clustered lookup trong phần offset, nhưng vẫn phải quét/bỏ qua offset và thường vẫn cần count riêng cho numbered pagination.

```sql
SELECT o.id, o.user_id, o.total_amount, o.created_at
FROM orders AS o
INNER JOIN (
    SELECT id, created_at
    FROM orders
    WHERE status = 'paid'
    ORDER BY created_at DESC, id DESC
    LIMIT 500000, 50
) AS page ON page.id = o.id
ORDER BY page.created_at DESC, page.id DESC;
```

Inner và outer query đều phải giữ cùng stable order. Đừng giả định join sẽ bảo toàn thứ tự subquery. So sánh với cursor pagination trước khi chọn.

## Xử lý theo batch bằng ID

Khi update trong lúc duyệt, `chunkById()`/`lazyById()` tránh một số lỗi skip/duplicate của offset. Cột ID dùng để chunk phải có trong select.

```php
Order::query()
    ->select(['id', 'status'])
    ->where('status', 'pending')
    ->chunkById(500, function ($orders): void {
        foreach ($orders as $order) {
            // Gọi Action/Task phù hợp nếu mỗi hàng có invariant nghiệp vụ riêng.
        }
    });
```

`500` chỉ là giá trị khởi đầu minh họa. Điều chỉnh theo số byte mỗi hàng, latency, lock time, memory, packet limit, retry cost và atomicity. Nếu chunk theo cột khác, xác nhận tính đơn điệu/ổn định và API Laravel 9 đang dùng.

## Predicate sargable cho thời gian

Tránh bọc cột nóng bằng hàm khi có thể biểu diễn thành range:

```php
$start = CarbonImmutable::create(2026, 1, 1, 0, 0, 0, 'UTC');
$end = $start->addYear();

$orders = DB::table('orders')
    ->where('created_at', '>=', $start)
    ->where('created_at', '<', $end)
    ->get();
```

Khoảng half-open `[start, end)` tránh lỗi precision ở cuối kỳ. Xác nhận timezone giữa application, dữ liệu và connection.

## EXPLAIN với Query Builder Laravel 9

Laravel 9 không cung cấp phương thức dựng câu SQL đã nội suy đầy đủ. Giữ SQL và bindings tách biệt để tránh tự nội suy sai quoting/type:

```php
$query = DB::table('orders')
    ->where('status', 'paid')
    ->where('created_at', '>=', $start)
    ->orderByDesc('created_at');

$sql = $query->toSql();
$bindings = $query->getBindings();

$plan = DB::select('EXPLAIN '.$sql, $bindings);
```

Log bindings phải tuân thủ quy tắc bảo mật, tránh PII/secret. `EXPLAIN ANALYZE` phụ thuộc MySQL version, thực sự chạy query và không nên ghép tùy tiện với statement ghi.

## Profiling trong Laravel 9

`DB::listen()` quan sát từng query:

```php
DB::listen(function (QueryExecuted $query): void {
    logger()->debug('database.query', [
        'sql' => $query->sql,
        'time_ms' => $query->time,
    ]);
});
```

Chỉ bật logging chi tiết có kiểm soát vì overhead và dữ liệu nhạy cảm. `DB::whenQueryingForLongerThan()` theo dõi thời gian query cộng dồn của connection trong request; mặc định callback chỉ được gọi khi vượt ngưỡng lần đầu, không phải detector cho từng slow query.

## `IN`, random sampling và optimizer hint

- Danh sách `IN` có thể hoàn toàn hợp lý. Đo parse/optimization time, packet size, plan và nguồn danh sách. Khi thực sự gây vấn đề, cân nhắc join bảng staging/temporary có index hoặc đổi data flow.
- `ORDER BY RAND()` có thể chấp nhận với tập candidate nhỏ. Với tập lớn, chọn sampling theo yêu cầu xác suất: precomputed random key, sampling table hoặc candidate set đã giới hạn. PK-range sampling bị bias khi ID có gap/skew.
- `FORCE INDEX`, join-order hint hoặc raw hint chỉ dùng sau khi xác nhận optimizer chọn plan kém trên dữ liệu đại diện. Ghi lại benchmark, MySQL version và điều kiện gỡ bỏ.

Không dùng ngưỡng số phần tử/hàng cố định cho các quyết định này.

## `upsert()` và uniqueness

Laravel 9 hỗ trợ `upsert()`, nhưng trên MySQL conflict được xác định bởi primary/unique indexes thực tế. Tham số `$uniqueBy` không thay thế unique constraint trong schema.

```php
DB::table('candidate_sources')->upsert(
    $rows,
    ['candidate_id', 'source'],
    ['external_id', 'updated_at'],
);
```

Migration phải có unique index tương ứng nếu invariant là một hàng cho mỗi `(candidate_id, source)`. Review dữ liệu trùng trước khi thêm constraint và xác định cột nào được phép update khi conflict.

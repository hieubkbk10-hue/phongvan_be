# Thiết kế schema và kiểu dữ liệu

## Nguyên tắc domain-first

Chọn kiểu dữ liệu theo ý nghĩa nghiệp vụ, miền giá trị, tốc độ tăng trưởng, phép toán, định dạng trao đổi và quan hệ với bảng khác. Kích thước nhỏ giúp giảm I/O và footprint index, nhưng không được đánh đổi bằng overflow, migration sớm hoặc FK không tương thích.

| Nhu cầu | Câu hỏi cần trả lời |
|---|---|
| ID hoặc counter | Giá trị tối đa và tốc độ tăng trưởng? Có cần số âm? Có đồng bộ qua nhiều hệ thống? |
| Tiền | Độ chính xác và scale? Dùng `DECIMAL`, không dùng floating point cho số tiền chính xác |
| Trạng thái | Tập giá trị có ổn định và cần FK/configurable không? Tránh magic number khó hiểu |
| Thời gian | Lưu timezone hay UTC? Cần phạm vi và độ chính xác đến đâu? |
| Chuỗi | Độ dài domain thực tế, collation, search/sort và index cần gì? |

`VARCHAR(255)` không phải mặc định bắt buộc. Chọn giới hạn phản ánh contract và kiểm tra giới hạn index theo phiên bản MySQL, charset và row format đang chạy.

## Khóa chính và khóa ngoại

- Cột FK phải tương thích với cột được tham chiếu về type, signedness và các thuộc tính mà MySQL yêu cầu.
- Dùng cùng convention ID trong một bounded context giúp migration và join an toàn hơn.
- Độ rộng ID phải đủ cho tăng trưởng; không chọn type nhỏ chỉ để tiết kiệm vài byte.
- Laravel 9 thường dùng cặp `id()`/`foreignId()` hoặc type tương ứng với schema hiện hữu.

```php
Schema::create('orders', function (Blueprint $table): void {
    $table->id();
    $table->foreignId('user_id')
        ->constrained()
        ->restrictOnDelete();
    $table->string('status', 32);
    $table->timestamps();
});
```

Chọn `cascade`, `restrict`, `nullOnDelete` theo ownership và lifecycle của domain, không theo thói quen. Laravel có thể tự tạo index cần thiết cho foreign key qua MySQL, nhưng index phục vụ query thường cần thứ tự cột khác và phải được thiết kế riêng.

## Ngữ nghĩa `NULL`

`NULL` phù hợp khi giá trị chưa biết, chưa áp dụng hoặc chưa tồn tại là một trạng thái nghiệp vụ thực sự. Không dùng chuỗi rỗng, `0` hay ngày giả để thay thế chỉ nhằm tránh nullable.

Cần xác định rõ:

- `COUNT(column)` bỏ qua `NULL`, còn `COUNT(*)` đếm hàng.
- Unique index và phép so sánh với `NULL` có semantics riêng.
- Predicate phải dùng `IS NULL`/`IS NOT NULL`.
- `NOT NULL` chỉ nên dùng khi invariant nghiệp vụ bảo đảm giá trị luôn tồn tại.

## Địa chỉ IP

- Chỉ hỗ trợ IPv4: `INT UNSIGNED` với `INET_ATON()`/`INET_NTOA()` là lựa chọn compact.
- Hỗ trợ IPv4 và IPv6: mặc định dùng `VARBINARY(16)` với `INET6_ATON()`/`INET6_NTOA()`, vì hàm trả 4 byte cho IPv4 và 16 byte cho IPv6. Chỉ dùng `BINARY(16)` khi application chủ động chuẩn hóa IPv4 thành biểu diễn IPv4-mapped 16 byte và contract đó đã được kiểm chứng.
- Nếu giữ chuỗi để đơn giản hóa contract, xác nhận chi phí lưu trữ/index là chấp nhận được.

Laravel 9 Schema Builder `binary()` có thể compile thành `BLOB` và không biểu diễn trực tiếp `VARBINARY(16)`. Kiểm tra SQL migration được sinh ra; khi cần, dùng raw statement tương thích với phiên bản MySQL của dự án và thêm migration test.

## Chuỗi, `TEXT`, JSON và generated column

- `string`/`VARCHAR`: phù hợp cho dữ liệu có giới hạn và cần equality, prefix search, sort hoặc index.
- `TEXT`: phù hợp nội dung dài; tránh đưa vào query nóng nếu không cần. Prefix index chỉ hữu ích khi query và selectivity chứng minh được.
- JSON: phù hợp thuộc tính linh hoạt, không thay thế quan hệ và constraint cốt lõi. Trường được filter/join thường xuyên nên cân nhắc cột chuẩn hóa.
- Generated column hoặc functional index có thể giúp truy vấn biểu thức/JSON, nhưng syntax và khả năng index phụ thuộc phiên bản MySQL. Xác nhận production version trước khi viết migration.

Trước khi thêm generated column, ưu tiên predicate sargable. Ví dụ thay `YEAR(created_at) = ?` bằng khoảng thời gian. Chỉ materialize biểu thức khi workload và EXPLAIN cho thấy lợi ích.

Character set/collation của cột join hoặc compare nên tương thích. Ép kiểu/collation ngầm có thể tăng chi phí hoặc cản trở cách dùng index, nhưng phải xác nhận bằng plan thay vì kết luận tuyệt đối.

## Chọn UUID hay số tăng dần

| Chọn | Phù hợp khi | Trade-off |
|---|---|---|
| Auto-increment integer | Một nguồn ghi chính, internal ID, join/index compact | Dễ đoán; cần chiến lược khi merge dữ liệu đa nguồn |
| UUID | Tạo ID phân tán/offline, public opaque ID, merge nhiều nguồn | PK và secondary index lớn hơn; locality phụ thuộc biến thể và cách lưu |
| Internal integer + public UUID | Muốn join compact và public ID không tuần tự | Thêm unique index, storage và mapping |

Không mặc định UUIDv7 hay `BIGINT`. Nếu dùng UUID, quyết định dạng binary/chuỗi, ordering, version support và API serialization như một contract hoàn chỉnh.

## Laravel 9 migration và thay đổi an toàn

Các API thường dùng: `index`, `unique`, `primary`, `foreignId`, `constrained`, `foreign`, `dropIndex`, `dropUnique`, `dropForeign`. Kiểm tra tên constraint/index thực tế trước khi rollback hoặc đổi schema.

Với bảng lớn hoặc contract đang được dùng, ưu tiên:

1. **Expand:** thêm cột/index/contract mới theo cách tương thích ngược.
2. **Migrate:** backfill theo batch thích ứng, quan sát lock và replication lag nếu có.
3. **Contract:** chỉ xóa cột/index cũ sau khi mọi reader/writer đã chuyển và dữ liệu được kiểm chứng.

DDL có thể gây metadata lock, rebuild hoặc tăng I/O tùy MySQL version và loại thay đổi. Lập kế hoạch triển khai, rollback và quan sát production thay vì giả định migration là online.

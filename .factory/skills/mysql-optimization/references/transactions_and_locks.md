# Transaction, lock và bulk write

## Transaction ngắn và ranh giới side effect

Transaction chỉ nên bao phủ các thao tác database cần cùng một atomic unit. Chỉ đưa query ra trước transaction khi kết quả không tham gia invariant hoặc quyết định ghi và nghiệp vụ chấp nhận dữ liệu có thể thay đổi trước lúc transaction bắt đầu; việc query không cần khóa chưa đủ để di chuyển. Giữ số hàng và thời gian khóa thấp.

Không gọi HTTP API, gửi email, upload file hoặc chờ queue bên trong transaction. Các side effect này không rollback cùng database và kéo dài lock. Dùng một trong các cách:

- dispatch sau commit khi framework flow bảo đảm phù hợp;
- transactional outbox để ghi intent cùng transaction rồi publish riêng;
- idempotency key và trạng thái retry rõ ràng.

Callback `DB::transaction()` có thể trả giá trị và Laravel 9 hỗ trợ số lần thử:

```php
$order = DB::transaction(function () use ($command): Order {
    return $this->applyOrderMutation($command);
}, 3);
```

Chỉ retry lỗi concurrency/transient phù hợp. Callback có thể chạy lại nên mọi mutation và side effect liên quan phải idempotent hoặc được bảo vệ. Không dùng retry để che lỗi validation, invariant hoặc SQL sai.

## Giảm lock contention

- Truy cập bảng/hàng theo cùng thứ tự trong mọi code path.
- Predicate khóa phải hẹp, xác định và có index phù hợp.
- Chỉ dùng `lockForUpdate()` khi cần serialize quyết định dựa trên dữ liệu đang đọc.
- Không đọc hoặc xử lý tập lớn rồi giữ transaction mở trong khi chạy business logic chậm.
- Unique constraint thường là cơ chế concurrency an toàn hơn pattern "check rồi insert".

Under InnoDB `REPEATABLE READ`, locking reads và DML có thể dùng record, gap hoặc next-key locks tùy predicate, index và operation. Không phải mọi query ở isolation này đều khóa mọi gap. `READ COMMITTED` thay đổi semantics và locking behavior nhưng không loại bỏ deadlock; chỉ đổi sau DBA/correctness review và benchmark.

## Foreign key

MySQL yêu cầu index phù hợp để kiểm tra foreign key và có thể tự tạo index cần thiết. Parent update/delete và child insert/update có thể lấy record locks để kiểm tra referential integrity; phạm vi và mức contention phụ thuộc index, predicate và cascade.

Không khẳng định FK thiếu index luôn khiến toàn bộ bảng bị khóa. Thay vào đó:

- kiểm tra index thực tế bằng schema/EXPLAIN;
- thiết kế index riêng cho query nếu FK index không đủ;
- review cascade trên quan hệ fan-out lớn;
- đo lock wait khi parent/child được ghi đồng thời.

## Deadlock là tín hiệu để sửa access pattern

Deadlock có thể xảy ra trong hệ thống đúng và InnoDB sẽ rollback một transaction để phá chu trình. Victim selection dựa trên ước lượng chi phí rollback và chi tiết engine, không nên giả định luôn là transaction ít hàng khóa hơn.

Khi điều tra:

1. Thu thập deadlock report và SQL/bindings đã được làm sạch dữ liệu nhạy cảm.
2. Xác định index, lock đang giữ, lock đang chờ và thứ tự truy cập.
3. Reproduce với hai transaction nếu có thể.
4. Thu hẹp predicate, thêm/sửa index, thống nhất lock order hoặc tách transaction.
5. Giữ retry có giới hạn như lớp phục hồi, không phải fix duy nhất.

`SHOW ENGINE INNODB STATUS` chỉ cung cấp snapshot/deadlock gần nhất tùy cấu hình. Kết hợp application trace và database observability.

## Queue claiming

Ưu tiên Laravel queue driver và cơ chế reservation/retry đã được kiểm chứng thay vì tự xây queue bằng một câu `UPDATE ... LIMIT 1` không có identity/order/lease.

Nếu bắt buộc claim từ bảng nghiệp vụ và production là MySQL 8 hỗ trợ `SKIP LOCKED`, pattern an toàn hơn là chọn hàng có thứ tự trong transaction, khóa chúng và cập nhật lease:

```sql
START TRANSACTION;

SELECT id
FROM import_jobs
WHERE (status = 'pending' AND available_at <= CURRENT_TIMESTAMP)
   OR (status = 'processing' AND lease_until < CURRENT_TIMESTAMP)
ORDER BY available_at, id
LIMIT 10
FOR UPDATE SKIP LOCKED;

-- Cập nhật đúng các id vừa khóa: status, worker_id, lease_until, attempts.
COMMIT;
```

Đây chỉ là phần claim. Worker vẫn cần:

- unique identity và deterministic order;
- lease expiry/heartbeat hoặc cách reclaim worker chết;
- idempotent processing;
- attempts/backoff/dead-letter;
- query plan và index được kiểm chứng trên workload thực tế;
- transaction update đúng danh sách ID đã khóa.

Hai nhánh eligibility trong điều kiện `OR` có query shape khác nhau nên cần đánh giá plan/index riêng. Nếu đây là đường chạy nóng và plan không ổn định, cân nhắc thiết kế lại bằng `UNION` đúng semantics hoặc tách reclaim job hết lease thành luồng riêng; không có một index phổ quát cho mọi phân bố dữ liệu.

`SKIP LOCKED` ưu tiên throughput hơn fairness và có thể bỏ qua hàng đang khóa. Xác nhận MySQL version và semantics trước khi dùng.

## Batch thích ứng

Multi-row insert/upsert giảm round trip, nhưng không dùng batch size cố định cho mọi workload. Chọn batch dựa trên:

- bytes thực tế mỗi hàng và `max_allowed_packet`;
- latency, memory và thời gian giữ lock;
- redo/undo pressure và replication lag nếu có;
- cost khi retry;
- business atomicity: có được commit từng phần không.

Bắt đầu bằng batch bảo thủ trên dữ liệu đại diện, đo throughput và p95, rồi tăng/giảm. Với import có thể resume, lưu checkpoint/idempotency key để retry không tạo duplicate.

## Cache lock trong Laravel 9

`Cache::lock()` hữu ích cho critical section phân tán khi mọi node dùng cùng backend hỗ trợ atomic lock. File/array/local cache không cung cấp coordination đa máy.

```php
$lock = Cache::lock("candidate:{$candidateId}:sync", 30);
$acquired = false;

try {
    $acquired = $lock->block(5);
    $this->syncCandidate($candidateId);
} finally {
    if ($acquired) {
        $lock->release();
    }
}
```

TTL có thể hết trước khi công việc xong; process pause hoặc network partition vẫn có thể tạo overlap. Chọn TTL từ thời gian thực tế, giới hạn thời gian chờ, luôn release trong `finally` và giữ operation idempotent. Với job dài, cân nhắc lease renewal/fencing token nếu correctness yêu cầu.

## Ranh giới DBA

Không tự khuyến nghị hoặc đổi global isolation, `innodb_autoinc_lock_mode`, replication/binlog, redo log, buffer pool hay durability settings từ skill này. Các thay đổi đó ảnh hưởng toàn server, recovery và correctness; cần DBA, version-specific docs, benchmark, rollout và rollback plan.

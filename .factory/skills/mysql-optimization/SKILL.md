---
name: mysql-optimization
description: Use when reviewing or changing MySQL schema, indexes, Eloquent or Query Builder queries, pagination, bulk writes, transactions, locks, or database performance in Laravel 9 and Apiato applications.
---

# Tối ưu MySQL cho Laravel 9 và Apiato

## Nguyên tắc

Đo trước khi tối ưu. Giảm hàng và byte đọc/ghi nhưng giữ đúng invariant. Trong Apiato của repo này, Action chỉ chuẩn hóa input và gọi đúng một main Task; orchestration/transaction thuộc Task, query/mutation tại Task hoặc Repository, validation tại Request và schema trong migration.

## Khi dùng

Dùng khi endpoint/job chậm, N+1, CPU/I/O tăng; khi thiết kế migration, index, pagination, bulk write, transaction hay cache lock.

Không dùng để tự ý đổi cấu hình global MySQL, isolation, replication hoặc hạ tầng. Việc đó cần DBA, benchmark và correctness review.

## Guardrails

- Domain correctness trước tốc độ; kiểu dữ liệu, `NULL`, FK và uniqueness phản ánh nghiệp vụ.
- Filter, aggregate, sort, limit tại DB; không tải toàn bộ rồi lọc bằng PHP.
- Chỉ chọn cột cần dùng, nhưng eager-loaded relation phải giữ primary/foreign keys.
- Index theo query shape; tính write cost, dung lượng và index trùng.
- Đọc `key`, `rows`, `filtered`, `Extra` và timing trong ngữ cảnh. `ALL`, filesort hay temporary không tự động là lỗi.

## Quy trình

| Bước | Thực hiện |
|---|---|
| 1. Capture | Ghi SQL, bindings, tần suất, latency, rows/bytes, dữ liệu và concurrency đại diện. |
| 2. Reduce | Giảm cột/hàng/relation; đưa filter và aggregate vào DB. |
| 3. Shape | Đếm query; kiểm tra N+1, eager-load constraints, `OR`, sort và pagination. |
| 4. Schema/index | Bắt đầu từ `WHERE`, join, order, uniqueness; kiểm tra FK và write cost. |
| 5. Explain/measure | Dùng `toSql()` cùng bindings; đọc plan và đo lại trên workload đại diện. |
| 6. Verify | So latency, rows examined, query count, lock/deadlock, write throughput và regression. |

## Bảng quyết định nhanh

| Khu vực | Quyết định |
|---|---|
| Migration | Domain-first type, FK tương thích, uniqueness và Expand-Migrate-Contract. |
| Eloquent/N+1 | Constrained eager loading, DB-side filtering, đủ relation keys. |
| Pagination | Ưu tiên cursor cho traversal; order phải ổn định và có unique tie-breaker. |
| Profiling | `DB::listen()` đo từng query; cumulative budget đo tổng thời gian query của request. |
| Transaction/cache | Transaction ngắn, external effect sau commit/outbox; distributed lock cần shared backend và idempotency. |

## Red flags

- Cursor order không deterministic; deferred join thiếu outer `ORDER BY`.
- Giả định Laravel 9 có API dựng raw SQL thay vì SQL và bindings tách biệt.
- `upsert()` không có PK/unique index thể hiện conflict.
- Retry callback không idempotent hoặc gọi external effect trong transaction.
- Batch size cố định; cache lock không dùng shared backend.
- Dùng optimizer hint hoặc đổi server setting trước khi đo và DBA review.

## References

- [Schema và kiểu dữ liệu](references/schema_and_types.md)
- [Cơ chế index](references/indexing_mechanics.md)
- [Query recipes Laravel 9](references/query_refactoring_recipes.md)
- [Transaction và locks](references/transactions_and_locks.md)

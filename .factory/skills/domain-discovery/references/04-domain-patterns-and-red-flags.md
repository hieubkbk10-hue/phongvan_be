# Domain Patterns and Red Flags

Các mục dưới đây là decision guide, không phải recipe cố định. Bắt đầu bằng business outcome, invariant và evidence; chỉ sau readiness gate mới chọn persistence/framework mechanism.

## Soft delete và lifecycle alternatives

Hỏi trước:

- Business cần khôi phục, trash, grace period hay chỉ cần ẩn?
- Record còn thuộc owner cũ không?
- Child sống độc lập, bị xóa theo parent hay chỉ mất liên kết?
- Ai được xem/restore/purge và trong thời gian nào?

| Mode | Phù hợp khi | Câu hỏi bắt buộc |
|---|---|---|
| Hide | Tạm không hiển thị nhưng entity vẫn active | Ai vẫn thấy; có ảnh hưởng quyền/list/realtime không? |
| Inactive | Membership/capability dừng nhưng lịch sử còn | Có thể re-activate; quyền nào bị thu hồi? |
| Detach | Chấm dứt relation, không xóa hai đầu | Pivot có role/history riêng không? |
| Soft delete | Cần restore/trash/grace period rõ | Restore child nào; unique identifier được giữ hay release? |
| Archive | Dữ liệu bất biến/ít truy cập nhưng còn giá trị | Read-only hay reopen; storage/query path nào? |
| Anonymize | Giữ thống kê/lịch sử nhưng loại định danh | Mức re-identification chấp nhận được do ai xác nhận? |
| Transfer | Ownership chuyển sang actor khác | Consent, audit và quyền cũ/mới? |
| Hard delete | Purge được policy cho phép | Cascade, files, snapshots, backup và verification? |
| Immutable | Fact/history không được sửa/xóa theo flow thường | Correction dùng reversal/amendment thế nào? |

Không dùng sentinel `deleted_at` với Laravel `SoftDeletes`. Laravel dựa trên `NULL` cho record active.

MySQL không dùng unique `(phone, deleted_at)` để bảo đảm chỉ một phone active: nhiều row có `deleted_at = NULL` vẫn có thể cùng phone vì unique index cho phép nhiều `NULL`. Chỉ chọn giải pháp sau khi chốt phone reuse policy và kiểm tra MySQL/Laravel version; có thể cần active-key generated column, registry table hoặc lifecycle khác.

## Retention, purge và privacy

- Retention duration, legal basis, security retention, identifier release và backup purge không được suy diễn từ “GDPR” hoặc thông lệ.
- Tách policy khỏi mechanism: ai quyết định, trigger bắt đầu retention, legal hold, purge scope, verification và exception.
- Grace period là Proposal cho đến khi authority xác nhận.
- “Xóa account” có thể gồm deactivate login, transfer ownership, anonymize profile, retain immutable facts và purge dữ liệu khác; không mặc định là một cascade delete.

## Anonymization

Xác định:

- Dữ liệu trực tiếp và quasi-identifier.
- Dữ liệu phải giữ để đối soát/invariant.
- Khả năng link ngược qua logs, snapshots, backups hoặc external systems.
- Irreversibility expectation và authority.

Hashing không mặc nhiên là anonymization; dữ liệu có miền nhỏ như phone/email có thể bị dò hoặc liên kết.

## Audit trail và CDC

| Khái niệm | Mục tiêu |
|---|---|
| Audit trail | Giải thích ai làm gì, khi nào, theo authority/business context nào |
| CDC | Truyền thay đổi dữ liệu ở mức storage/integration |

CDC không tự cung cấp intent, actor, reason hoặc policy context. Audit không thay thế replication/integration stream. Chốt tamper resistance, correction, access và retention riêng.

## Snapshot và live foreign key

Dùng snapshot khi câu trả lời lịch sử phải giữ nguyên dù nguồn đổi/xóa, như giá/tiền tệ/địa chỉ/tên hiển thị tại thời điểm giao dịch.

Dùng live FK khi nghiệp vụ cần giá trị hiện tại và nguồn có lifecycle tương thích. Có thể cần cả FK lẫn snapshot.

Hỏi:

- Câu trả lời là “bây giờ” hay “tại thời điểm xảy ra”?
- Source có thể đổi, anonymize hoặc hard delete không?
- Correction lịch sử là sửa snapshot hay tạo amendment?

## Money

- Chốt currency, minor unit/precision, rounding point, tax/discount allocation, exchange-rate source/time và reversal/refund policy.
- Không dùng floating point cho amount.
- Không gọi payment/refund “thành công” nếu provider outcome còn unknown.
- Refund, partial refund, chargeback và cancellation là state/policy khác nhau; không tự gộp.

## Idempotency

Idempotency phải định nghĩa:

- Scope: actor, command, aggregate, provider hay time window.
- Key owner và uniqueness.
- Durable record của request/outcome.
- Behavior khi request đầu đang chạy, failed hay outcome unknown.
- Side effect nào cần deduplication riêng.

Redis-only idempotency không luôn đủ vì eviction, failover, TTL và durability có thể không khớp invariant. Chọn DB, Redis, provider key, outbox/inbox hoặc phối hợp dựa trên failure semantics đã chốt.

## Concurrency

Không chọn pessimistic lock, optimistic version, distributed lock hay queue serialization trước khi biết:

- Operations cạnh tranh.
- Invariant có nguy cơ.
- Contention và latency budget.
- Conflict UX.
- Transaction và external boundary.

Lock không thay thế idempotency; idempotency không giải quyết mọi lost update.

## Notification và realtime

Tách:

1. Business quyết định cần thông báo ai và khi nào.
2. Durable notification intent/fact.
3. Channel delivery: in-app, realtime, email, SMS, webhook.
4. Delivery state, retry, dedup, preference và fallback.

Domain event không đồng nghĩa Laravel Event. Dispatch không đồng nghĩa delivered. Realtime không được làm nguồn canonical duy nhất nếu client có thể offline, trừ khi policy recovery rõ.

## Graceful failure

Với dependency ngoài:

- Định nghĩa timeout và unknown outcome.
- Retry chỉ khi operation an toàn/idempotent.
- Có compensation hoặc reconciliation khi core commit và side effect lệch nhau.
- User nhận trạng thái trung thực như pending/manual review thay vì false success.
- Operator có evidence và runbook phù hợp.

## Dangerous claims cần chặn

| Claim | Vấn đề | Cách sửa |
|---|---|---|
| “Xóa account là cascade ngay” | Tự đặt ownership, retention và irreversible policy | Đưa thành Blocker; phân rã lifecycle modes và authority |
| “Phone release ngay sau delete” | Có thể phá restore, security và uniqueness | Chốt reuse/grace/verification policy trước |
| “GDPR bắt buộc giữ/xóa N ngày” | Không có legal source/context | Để legal/compliance authority xác nhận |
| “Unique `(phone, deleted_at)` bảo đảm một active phone” | Sai với nhiều `NULL` trong MySQL | Chọn mechanism sau khi chốt lifecycle và test DB behavior |
| “Dùng sentinel cho `deleted_at`” | Không tương thích semantics chuẩn của Laravel SoftDeletes | Giữ `NULL` hoặc không dùng SoftDeletes cho thiết kế đó |
| “Redis lock/key giải quyết idempotency” | Durability và failure window có thể không đủ | Chốt durable outcome, dedup và recovery |
| “BIGINT luôn tốt hơn UUID” hoặc ngược lại | Không có universal identity choice | Dựa trên distribution, privacy, locality, interoperability và measurement |
| “Trên N rows phải partition/cache” | Threshold phổ quát là giả | Đo query/write/retention workload và operational capability |
| “Mọi side effect chạy trong transaction” | External I/O kéo dài lock và không rollback thật | Atomic core trong transaction; effect sau commit với recovery |

## Resume-Driven Development và premature optimization

Dừng khi thấy:

- Chọn Kafka, event sourcing, CQRS, microservice, Redis hay UUID vì CV/buzzword.
- Chuyển mock UI thành schema mà chưa có actor/state/invariant.
- Thêm index/cache/denormalization khi query shape hoặc workload chưa biết.
- Viết hàng chục class để tránh hỏi một policy question.
- Gọi một technical sketch là architecture decision dù nó chỉ là `Proposal - non-binding`.

Giải pháp: quay lại Blocker có blast radius lớn nhất, hỏi một câu, cập nhật Dossier, rồi chạy readiness gate.

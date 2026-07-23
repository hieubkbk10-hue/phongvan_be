# Red Flags và Loopholes

## Dấu hiệu workflow chưa toàn vẹn

- Feature gọi Model/field chưa có bước tạo trước đó.
- Relation được thêm khi một Container chưa tồn tại.
- Dependency chỉ có `id/timestamps` dù feature cần `name`, owner hoặc status.
- Transformer fallback được ghi nhưng không có bước tích hợp vào response owner.
- Có Job class nhưng không có queue connection/worker/deploy.
- Có Event/Listener nhưng không có Provider registration.
- Có SoftDelete nhưng không có restore/purge/cleanup policy.
- Có upload nhưng không có rollback compensation/orphan cleanup.
- Có limit/primary/reorder nhưng không có lock/concurrency tests.
- Có list nhưng không có pagination/index/N+1 audit.
- Có snapshot requirement nhưng schema chỉ lưu FK.
- Prompt bước N không nêu prerequisite đã hoàn tất.

## Rationalization và correction

| Rationalization | Correction |
| --- | --- |
| “User chỉ nói Media nên không được đụng Customer/Product.” | Mở rộng thành A' với dependency tối thiểu thật sự cần để output đúng. |
| “Tạo Product/Customer rỗng trước, business làm sau.” | Nếu feature cần field/invariant, dependency rỗng là code giả và không hoàn tất A'. |
| “Khai báo relation sẵn, sau này tạo Model.” | Relation chỉ được nối khi cả hai đầu đã tồn tại và được test. |
| “Job có `ShouldQueue` là xong.” | Queue cần payload, idempotency, retry, failure, worker, runtime và monitoring. |
| “`afterCommit()` luôn an toàn.” | Với `sync`, không có async/delay thật; phải kiểm tra backend runtime. |
| “Event là nơi tiện để gọi HTTP.” | Event là data carrier; external I/O chạy queued sau commit. |
| “Listener async cũng ghi core data được.” | Core writes cần immediate consistency ở transaction boundary. |
| “SoftDelete chỉ cần trait và column.” | Phải có delete cause, restore, purge, cascade và cleanup policy nếu relevant. |
| “Request đã `exists` nên có quyền.” | Existence không thay ownership/domain permission. |
| “Transformer query thêm một chút không sao.” | Collection sẽ N+1; chuẩn bị query shape ở Task/Repository. |
| “Prompt ngắn để agent tự hiểu context.” | Prompt phải self-contained vì agent khác không có session context. |
| “Workflow tổng càng dài càng tốt.” | Workflow master đầy đủ, output cho user phải lọc theo trigger và A'. |

## Baseline failures observed

### Media-only baseline

- Né dependency bằng cách không thêm relation phía Product/Customer.
- Đẩy Customer fallback sang Media-owned endpoint nhưng không hoàn tất Customer response contract.
- Không xử lý A thành A' một cách nhất quán.

### Dependency baseline

- Tạo Product/Customer “minimal owner anchor” chỉ `id/timestamps`.
- Customer avatar fallback cần tên nhưng Customer foundation không có `name`.
- Có nguy cơ compile được nhưng business contract chưa hoàn tất.

### Queue baseline

- Tự chọn database queue và package xử lý ảnh trước khi audit production constraints.
- Có worker nhưng không bắt buộc inventory tất cả queue hiện hữu trước khi đổi từ `sync`.
- Chưa có rule tổng quát buộc every Job có idempotency/missing-model/failure policy.

Skill phải chặn các lỗi trên bằng dependency closure, capability triggers và integrity review.

## Stop conditions

Dừng và hỏi user nếu:

- Có nhiều business contract hợp lý làm thay đổi schema/API.
- Không xác định được Customer là entity riêng hay User profile.
- Delete semantics ảnh hưởng dữ liệu lịch sử/legal.
- Queue backend/deploy environment là quyết định production chưa được chốt.
- Một dependency đã có code dở dang xung đột với contract mới.

Không dừng nếu chỉ cần audit code để tìm một hướng an toàn duy nhất.

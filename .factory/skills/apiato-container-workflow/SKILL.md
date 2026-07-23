---
name: apiato-container-workflow
version: 1.0.0
description: |
  Use when the user asks for a manual coding workflow, ordered implementation steps, dependency-aware plan, copy-paste prompts, or a filtered checklist for creating or completing Laravel 9 Apiato/Porto Containers and related features in this repository.
---

# Apiato Container Workflow

Tạo một workflow tổng đầy đủ, sau đó lọc thành quy trình ngắn đúng với feature được yêu cầu. Không trả checklist CRUD máy móc và không lập kế hoạch cho một Container cô lập khi nghiệp vụ cần dependency khác.

**REQUIRED BACKGROUND:** Dùng `apiato`, `laravel-duong` và `laravel-apiato-qa`.

## Hard rules

1. Đọc `AGENTS.md`, standard liên quan và code hiện tại trước khi lập quy trình.
2. Biến yêu cầu `A` thành scope toàn vẹn `A' = A + dependency tối thiểu cần thiết`.
3. Không tạo dependency giả chỉ để compile. Dependency phải đủ contract nghiệp vụ mà feature cần.
4. Sắp xếp theo dependency graph, không theo thứ tự file generator.
5. Chỉ nối relation khi cả hai đầu Model/Migration đã tồn tại.
6. Optional trở thành bắt buộc khi trigger của capability xuất hiện.
7. Không sao chép pattern xấu từ repo tham khảo. Phân biệt evidence, inference và decision.
8. Mỗi bước phải độc lập để giao cho agent khác và có prompt copy-paste.
9. Prompt của bước N phải nói rõ các bước trước đã hoàn tất, chỉ làm bước N, validation cần chạy và không commit/push nếu chưa được yêu cầu.
10. Khi user chỉ xin quy trình, không viết code hoặc sửa file feature.

## Workflow

### 1. Audit

- Xác định trạng thái thật của Container, Model, Migration, API, test và dependency.
- Xác định generator phù hợp:
  - CRUD đơn giản: `php artisan create-api`.
  - Container cần chọn API/WEB, Events, Listeners, Tests, Providers: `php artisan apiato:generate:container`.
- Chạy `--help` trước khi ghi lựa chọn generator.

### 2. Dependency closure

Lập graph:

```txt
feature requested
-> aggregate roots/owners
-> schema and models
-> cross-container contracts
-> relations
-> use cases
-> side effects/runtime
-> tests/docs/deploy
```

Nếu dependency chưa tồn tại, đưa phase tạo dependency vào trước. Không thêm relation, fallback, Transformer include hoặc permission dựa trên class chưa tồn tại.

### 3. Activate capabilities

Đọc `references/01-capability-matrix.md`. Chỉ bật module có trigger, nhưng giữ nguyên prerequisite và thứ tự của module.

### 4. Topological ordering

Thứ tự mặc định:

```txt
Contract
-> dependency Containers
-> Config
-> Model table source
-> Migration
-> migrate up/down/up
-> Relations
-> Repository/Factory
-> Tasks
-> Action/transaction
-> Request
-> Transformer
-> Controller/Route
-> lifecycle/side effects
-> queue/runtime
-> tests/apidoc/validators
```

Điều chỉnh theo graph thực tế, không đảo bước khiến code tham chiếu dependency chưa tồn tại.

### 5. Produce the filtered workflow

Mỗi bước dùng đúng format:

    ## N. Tên bước
    - Mục tiêu:
    - Phụ thuộc đã hoàn tất:
    - Việc cần làm:
    - Kiểm tra:

    ### Prompt giao agent
    [Ngữ cảnh repo và trạng thái các bước trước]
    [Chỉ làm đúng bước này]
    [Danh sách file/symbol và invariant]
    [Validation bắt buộc]
    [Không mở rộng scope, không commit/push]

Chỉ giữ các bước cần cho `A'`. Không giữ mục optional không được kích hoạt.

### 6. Integrity review

Trước khi trả workflow, kiểm tra:

- Mọi symbol được dùng đã có bước tạo trước đó.
- Relation chỉ xuất hiện sau hai đầu dependency.
- Migration đã chốt snapshot, delete semantics, index và FK.
- Core write nằm trong transaction phù hợp.
- Side effect phụ thuộc DB chạy sau commit.
- Queue có worker/runtime/deploy, không chỉ có Job class.
- Tests bao phủ dependency closure và rollback.
- Mỗi bước có prompt và giả định rõ trạng thái trước bước.

## References

- `references/00-dependency-closure-and-output.md`
- `references/01-capability-matrix.md`
- `references/02-container-build-order.md`
- `references/03-queue-worker-runtime.md`
- `references/04-agent-prompt-template.md`
- `references/05-red-flags.md`
- `references/06-syntax-decisions.md`

## Common mistakes

- Lập workflow “chỉ Media” nhưng Product/Customer contract chưa tồn tại.
- Tạo Product/Customer rỗng chỉ để Media compile.
- Khai báo relation trước khi Model dependency tồn tại.
- Viết Job nhưng quên queue connection, worker, retry, idempotency và deploy.
- Dispatch mail/file/realtime trước commit.
- Prompt bước sau không nói các bước trước đã hoàn tất.
- Trả workflow tổng chưa lọc, khiến user phải tự loại bước.

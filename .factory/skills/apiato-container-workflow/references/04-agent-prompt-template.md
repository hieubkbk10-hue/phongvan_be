# Prompt Template cho từng bước

Mỗi bước trong workflow trả cho user phải có một prompt copy-paste hoàn chỉnh. Không dùng prompt kiểu “làm bước trên”.

## Template bắt buộc

```text
Bạn đang làm trong repo:
D:\Hieubkav\Laravel\job\fullstack_phong_van\phongvan_be

Stack và quy chuẩn:
- Laravel 9, Apiato 11.x, Porto.
- Đọc AGENTS.md và các standard/skill liên quan trước khi sửa.
- Giữ flow Route -> Request -> Controller -> Action -> Task -> Repository/Model -> Transformer.
- Không đọc/in .env hoặc secret.
- Không đụng thay đổi không liên quan.

Trạng thái đầu vào:
- Feature/Container: <name>.
- Các bước 1..<N-1> đã hoàn tất và đã được kiểm tra.
- Các file/symbol đã tồn tại: <list>.
- Các invariant đã chốt: <list>.

Chỉ thực hiện bước <N>: <step name>.

Mục tiêu:
- <outcome 1>
- <outcome 2>

Việc phải làm:
1. <ordered task>
2. <ordered task>

Invariants không được phá:
- <business/security/data invariant>

Không làm:
- Không mở rộng sang bước kế tiếp.
- Không tạo dependency chưa nằm trong scope.
- Không query DB trong Controller/Transformer.
- Không commit hoặc push nếu chưa được yêu cầu.

Validation:
- <scoped test/lint/command>
- Nếu fail do code, sửa và chạy lại.
- Nếu fail do môi trường, báo command, exit code và lỗi chính.

Output:
- Observation kèm evidence file:line/command.
- Files changed.
- Validation result.
- Blocker hoặc assumption còn lại.
```

## Quy tắc sinh prompt

1. Nhắc lại repo path và stack.
2. Nêu rõ các bước trước đã xong.
3. Nêu symbol/data contract hiện đã tồn tại.
4. Chỉ cho agent sửa đúng phase.
5. Nêu invariant, không chỉ danh sách file.
6. Nêu validation riêng của bước.
7. Không yêu cầu agent “tự quyết” business contract đã chốt.
8. Không nhét nhiều phase độc lập vào một prompt.
9. Prompt relation phải xác nhận cả hai Model đã tồn tại.
10. Prompt queue phải nêu backend, queue name, afterCommit, retry và worker.

## Template bước dependency foundation

```text
Các bước audit/contract đã hoàn tất. Chỉ hoàn thiện foundation của Container <Dependency>.

Contract mà feature chính cần:
- <required field/invariant/API contract>.

Tạo đủ Model, Migration, Factory và Repository để contract trên hoạt động độc lập.
Không tạo model rỗng chỉ để compile.
Chưa thêm relation sang feature chính ở bước này.

Chạy migration up/down/up và tests scoped của Container.
```

## Template bước cross-container relation

```text
Foundations của <A>, <B> và migrations tương ứng đã hoàn tất.
Chỉ nối cross-container relation.

Thực hiện:
- Relation phía A.
- Relation phía B.
- Alias/morph map nếu polymorphic.
- Không nhận FQCN từ API.
- Tests inverse relation, cross-owner và stored type.

Không sửa schema/business fields ngoài relation contract.
```

## Template bước queue

```text
Core synchronous use case và transaction đã hoàn tất.
Chỉ triển khai phase queue cho side effect <name>.

Đầu vào đã chốt:
- Queue backend: <database/redis/sqs>.
- Queue name: <name>.
- Dispatch point: sau commit.
- Payload: <IDs/DTO>.

Thực hiện Job/Listener/Provider, idempotency, tries, backoff, timeout,
failed(), tests và worker/deploy runbook. Bảo đảm timeout < retry_after.
Không chuyển core write bắt buộc sang async.
```

## Template final gate

```text
Toàn bộ implementation steps đã hoàn tất. Không thêm feature mới.

Trace đủ dependency closure và chạy validators phù hợp.
Kiểm tra security, Hash ID, permissions, transaction, N+1, side effects,
queue/runtime, docs và workspace hygiene.

Không commit/push nếu chưa được yêu cầu.
Trả kết quả theo Observation, Inference, Decision và evidence.
```

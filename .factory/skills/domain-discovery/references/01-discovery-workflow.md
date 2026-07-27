# Domain Discovery Workflow

## Mục tiêu và cách làm việc

Thực hiện discovery theo đúng thứ tự các phase, chạy readiness gate rồi mới handoff; không nhảy sang schema hay Apiato components. Mỗi kết luận phải có nhãn `Observation`, `Inference`, `Proposal`, `AcceptedAssumption`, `Decision` hoặc `Blocker`.

Khi tồn tại Blocker:

1. Xếp hạng theo khả năng gây mất dữ liệu, sai quyền, sai tiền, vi phạm riêng tư, hoặc làm thay đổi terminal state.
2. Hỏi đúng một câu trong mỗi lượt; câu đó chỉ yêu cầu một policy decision độc lập.
3. Nêu vì sao câu trả lời chặn thiết kế.
4. Đưa tối đa ba Proposal có trade-off nếu người dùng cần lựa chọn.
5. Cập nhật log rồi mới hỏi câu tiếp theo.

Không gộp các quyết định có thể trả lời độc lập bằng “và/hoặc”; tách chúng sang các lượt kế tiếp. Không biến im lặng thành đồng ý. `AcceptedAssumption` phải bắt nguồn từ named Proposal và có đầy đủ acceptance metadata, source proposal/evidence IDs; raw Proposal không mở gate.

AI không sở hữu policy và không được silently override `Decision`. Nếu evidence mới mâu thuẫn với Decision, ghi rõ evidence, consequence và authority cần xác nhận; phần chưa được giải quyết là `Blocker`.

## Phase 1: Outcome và domain vocabulary

Xác định:

- Kết quả nghiệp vụ mong muốn và metric quan sát được.
- Vấn đề của actor, không phải tên màn hình hay endpoint.
- Thuật ngữ domain, từ đồng nghĩa, từ dễ gây hiểu nhầm.
- Phạm vi trong/ngoài và thời điểm bắt đầu/kết thúc use case.

Phân biệt từ business với từ framework. `InterviewRescheduled` có thể là domain event; Laravel Event chỉ là một cách triển khai sau này.

**Exit:** Có một outcome statement, glossary được domain owner hiểu giống nhau và scope boundary rõ.

## Phase 2: Actors, authority và ownership

Liệt kê actor người, hệ thống ngoài, scheduler và operator. Với mỗi actor, xác định:

- Mục tiêu và quyền hành động.
- Authority đến từ role, ownership, assignment, delegation hay system policy.
- Ai phê duyệt, ai được thông báo, ai xử lý ngoại lệ.
- Ownership của aggregate và dữ liệu sau khi actor rời hệ thống.

Không suy diễn owner từ người tạo. Không đồng nhất authentication với authorization.

**Exit:** Actor-permission matrix không còn ô quan trọng mơ hồ; người có thẩm quyền quyết định policy đã được nhận diện.

## Phase 3: Trigger, preconditions và postconditions

Ghi:

- Trigger từ actor, thời gian, webhook hay domain event.
- Preconditions có thể kiểm chứng.
- Input bắt buộc, nguồn dữ liệu và freshness.
- Success postcondition và failure postcondition.
- Điều kiện không được phép bắt đầu.

**Exit:** Có ranh giới use case và tiêu chí biết workflow đã bắt đầu/kết thúc.

## Phase 4: Happy-path timeline

Mô tả theo thời gian:

```text
Actor intent -> authorization -> domain validation -> state change
-> durable commit -> after-commit effects -> observable outcome
```

Mỗi bước ghi actor, input, business rule, state trước/sau và bằng chứng đầu ra. Không chèn class, table hoặc endpoint.

**Exit:** BA/PO có thể đọc timeline và xác nhận không cần hiểu Laravel.

## Phase 5: State machine

Xác định state business thật sự:

- Initial, active, suspended, terminal và reversible states.
- Transition, actor được phép, guard, effect, lý do từ chối.
- Transition bị cấm và đường phục hồi.

Phân biệt:

- State với timestamp/flag: `cancelled` là state; `cancelled_at` có thể chỉ là evidence.
- Cancellation với deletion: hủy nghiệp vụ không mặc nhiên xóa record.
- Terminal state với “không có nút trên UI”.

**Exit:** Mọi transition quan trọng có source, target, authority, guard và outcome; terminal/reopen policy đã được xác nhận hoặc là Blocker.

## Phase 6: Invariants

Viết invariant dạng có thể bác bỏ:

```text
Luôn luôn...
Không bao giờ...
Tại mọi thời điểm...
Trong cùng một scope...
```

Nhóm theo:

- Permission và ownership.
- Cardinality/uniqueness nghiệp vụ.
- State transition.
- Money/quantity/time.
- Cross-entity consistency.
- Privacy và visibility.

Với mỗi invariant, ghi scope, thời điểm enforce, exception được duyệt và acceptance example. Không biến implementation preference thành invariant.

**Exit:** Invariant catalog đủ để phát hiện một thiết kế sai ngay cả khi chưa có database.

## Phase 7: Alternatives, failures và compensation

Cho mỗi bước ngoài happy path, hỏi:

- Input thiếu/sai, actor mất quyền, state stale thì sao?
- External dependency timeout, duplicate hoặc trả kết quả không chắc chắn thì sao?
- Bước nào rollback được, bước nào cần compensation?
- Retry có an toàn không; ai can thiệp thủ công?
- User nhìn thấy trạng thái gì trong graceful failure?

Compensation là hành động nghiệp vụ bù, không phải luôn là database rollback.

**Exit:** Failure catalog có owner, retry/compensation policy và observable result; không có lỗi quan trọng bị “log rồi bỏ qua”.

## Phase 8: Concurrency và idempotency

Xác định:

- Hai actor có thể sửa cùng aggregate không.
- Command/event có thể gửi lặp, reorder hoặc chạy song song không.
- Idempotency scope, key owner, validity window và durable result.
- Conflict policy: reject, serialize, compare-and-swap, merge hay manual resolution.
- Invariant nào cần atomicity.

Không mặc định Redis-only idempotency đủ bền. Không chọn lock trước khi biết contention và failure semantics.

**Exit:** Duplicate/concurrent scenarios có expected outcome và consistency boundary.

## Phase 9: Data lifecycle, privacy và retention

Với từng loại dữ liệu, phân biệt:

- Hide, inactive, detach, soft delete, archive, anonymize, transfer, hard delete, immutable.
- Khả năng restore, visibility, ownership, retention trigger và purge authority.
- Dữ liệu gốc với snapshot lịch sử.
- Cancellation với deletion.

Phân đúng authority:

- Legal/regulatory retention do legal/compliance authority xác nhận.
- Security retention và control do security authority xác nhận.
- Product lifecycle do PO/domain authority xác nhận trong giới hạn legal đã chốt.

Authorization/ownership, money/refund, terminal/reopen state, destructive lifecycle, legal/privacy/retention, identity release và policy high-blast-radius khác không được mở gate bằng assumption; chúng cần Decision từ đúng authority.

**Exit:** Lifecycle matrix không còn policy phá hủy dữ liệu hoặc quyền sở hữu bị suy diễn.

## Phase 10: Side effects và consistency

Phân loại:

- Core durable writes.
- Domain event đã xảy ra trong business.
- Laravel Event như implementation mechanism.
- Notification, realtime, email, file, analytics, search index và integration.

Ghi core/durable record nào cần cùng transaction. Notification, realtime, email, file, analytics, search index và integration luôn chạy after-commit/outbox; chốt delivery guarantee, retry, deduplication và reconciliation. “Dispatch thành công” không đồng nghĩa người nhận đã nhận.

**Exit:** Side-effect matrix có consistency expectation và failure owner.

## Phase 11: Examples và acceptance

Dùng example mapping và Gherkin cho:

- Happy path.
- Permission denial.
- Invalid transition.
- Duplicate request.
- Concurrent update.
- External timeout và compensation.
- Lifecycle/restore/purge boundary.

**Exit:** Mỗi invariant và transition rủi ro cao có ít nhất một scenario kiểm chứng được.

## Phase 12: Readiness gate

Gate đạt khi:

- Outcome, glossary và scope rõ.
- Actor, authority, ownership và permission rõ.
- Trigger, pre/postconditions, state machine và invariants rõ.
- Failure, compensation, concurrency, idempotency và lifecycle rõ.
- Side effects, consistency, acceptance và dependency rõ.
- Không còn Blocker. Mỗi điểm chưa thành Decision phải là `AcceptedAssumption` reversible, bounded, non-legal, non-destructive với metadata bắt buộc và traceability đầy đủ.

Kết quả gate:

- `READY`: không còn Blocker và không cần accepted assumption.
- `READY_WITH_ACCEPTED_ASSUMPTIONS`: không còn Blocker; danh sách `AcceptedAssumption` không rỗng, hợp lệ và đủ traceability.
- `BLOCKED`: còn bất kỳ Blocker nào.

Owner hoặc resolution date không giải quyết Blocker. Gate không đạt nếu policy có thể làm mất dữ liệu, sai quyền, sai tiền, sai terminal state hay vi phạm riêng tư vẫn chỉ là `Inference`, `Proposal` hoặc assumption.

**Exit:** Dossier có đúng một gate result và evidence cho từng nhóm readiness.

## Phase 13: Handoff

Chỉ handoff khi gate là `READY` hoặc `READY_WITH_ACCEPTED_ASSUMPTIONS`:

1. Chuyển Business Workflow Dossier và exact packet cho `apiato-container-workflow`.
2. Gọi `mysql-optimization` khi có migration/schema/index, query shape, write pattern, transaction, locking hoặc concurrency.
3. Gọi `db-bandwidth-optimization` khi có list/search/report/export, relation graph, payload transfer, polling/realtime, fetch-all hoặc trước deploy.

Raw `Proposal`, `Inference` và Blocker không được dùng như requirement kỹ thuật. Handoff phải giữ claim, Decision, assumption và evidence IDs để truy vết.

**Exit:** Skill nhận handoff có đủ business contract để lập kế hoạch kỹ thuật mà không tự đoán policy.

## Stop conditions

Dừng và hỏi khi:

- Không xác định được ai có authority chốt policy.
- Hai Decision mâu thuẫn.
- User yêu cầu thiết kế kỹ thuật nhưng blocker có blast radius cao chưa được chấp nhận.
- Requirement dựa trên legal/compliance chưa có nguồn có thẩm quyền.
- Scope lan sang use case khác mà không có dependency business rõ.
- Authority từ chối giải quyết claim thuộc authority khác; giữ phần đó `BLOCKED` thay vì tự suy diễn.

Nếu user yêu cầu lưu business-flow summary, cập nhật `docs/nghiepvu.md` theo Cross-Entity Interactive Mindmap/Markmap và marker tham chiếu chéo được quy định trong `AGENTS.md`. Nếu user không yêu cầu persistence/documentation, chỉ trả Dossier trong hội thoại.

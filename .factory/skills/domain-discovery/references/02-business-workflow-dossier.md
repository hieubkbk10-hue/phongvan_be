# Business Workflow Dossier

Mẫu này là artifact review với BA/PO. Giữ ngắn; chuyển chi tiết kỹ thuật sang handoff sau readiness gate.

## 1. Header

| Trường | Nội dung |
|---|---|
| Workflow | Tên business capability/use case |
| Outcome | Kết quả nghiệp vụ quan sát được |
| Scope in | Phạm vi xử lý |
| Scope out | Phạm vi không xử lý |
| Authority | Người/nhóm được chốt policy |
| Status | Discovery / Blocked / Ready for technical handoff |

## 2. Certainty log

| ID | Label | Claim | Evidence/source | Confirmed by | Authority scope | Ảnh hưởng |
|---|---|---|---|---|---|---|
| C-01 | Observation / Inference / Proposal / AcceptedAssumption / Decision / Blocker |  |  |  |  |  |

Quy tắc:

- `Observation`: có bằng chứng trực tiếp.
- `Inference`: suy luận cần kiểm chứng.
- `Proposal`: phương án chưa được duyệt.
- `AcceptedAssumption`: named Proposal được authorized user chấp nhận rõ cho scope reversible, bounded, non-legal, non-destructive.
- `Decision`: đã được authority xác nhận.
- `Blocker`: chưa thể thiết kế an toàn.

Mọi `Decision` bắt buộc có `Confirmed by` và `Authority scope`; exact handoff packet dùng hai trường này làm `confirmed_by` và `authority_scope`.

`AcceptedAssumption` cần `id`, `accepted_by`, `authority_scope`, `scope`, `risk`, `reversible: true`, `review_condition`, `source_proposal_id` và evidence IDs. Không đổi `Proposal`/`Inference` thành `Decision` hoặc `AcceptedAssumption` vì deadline.

### Accepted assumptions

| ID | Source Proposal ID | Evidence IDs | Accepted by | Authority scope | Scope | Risk | Reversible | Review condition |
|---|---|---|---|---|---|---|---|---|
| A-01 | P-01 | C-01 |  |  |  |  | `true` |  |

## 3. Glossary

| Term | Định nghĩa domain | Không đồng nghĩa với | Source/owner |
|---|---|---|---|
|  |  |  |  |

## 4. Actors và permission

| Actor | Mục tiêu | View | Create/trigger | Change state | Cancel | Delete/purge | Authority source |
|---|---|---|---|---|---|---|---|
|  |  | Allow/Deny/Conditional |  |  |  |  | Role/ownership/delegation/policy |

Ghi điều kiện cụ thể thay cho “admin làm được mọi thứ”. Nhận diện actor hệ thống, scheduler và external service.

## 5. Trigger và boundary

| Mục | Mô tả |
|---|---|
| Trigger |  |
| Preconditions |  |
| Invalid start conditions |  |
| Success postconditions |  |
| Failure postconditions |  |
| Input/source/freshness |  |

## 6. Happy-path timeline

| Step | Actor | Intent/input | Rule/guard | State before | State after | Durable result | Visible result |
|---|---|---|---|---|---|---|---|
| 1 |  |  |  |  |  |  |  |

Không dùng tên Controller, Action, Task, table hoặc index trong bảng này.

## 7. State transition matrix

| From | Trigger/command | Actor | Guard | To | Domain outcome | Reversible? | Rejection reason |
|---|---|---|---|---|---|---|---|
|  |  |  |  |  |  |  |  |

Ghi rõ:

- Initial và terminal states.
- Reopen policy.
- State khác timestamp/flag.
- Cancellation khác deletion.

## 8. Invariant catalog

| ID | Invariant | Scope | Enforce moment | Approved exception | Acceptance evidence |
|---|---|---|---|---|---|
| INV-01 | Luôn luôn / Không bao giờ... | Aggregate/cross-entity | Before/atomic/after | None hoặc Decision ID | Scenario ID |

Nhóm tối thiểu: permission, ownership, uniqueness/cardinality, state, money/time, cross-entity, privacy/visibility.

## 9. Alternatives, failures và compensation

| Failure/alternative | Detect | User-visible state | Retry safe? | Compensation | Owner/manual action | Final outcome |
|---|---|---|---|---|---|---|
|  |  |  |  |  |  |  |

Phân biệt rollback kỹ thuật với compensation nghiệp vụ.

## 10. Concurrency và idempotency

| Scenario | Competing operations | Invariant at risk | Expected winner/result | Conflict policy | Idempotency scope/result |
|---|---|---|---|---|---|
|  |  |  |  | Reject/serialize/CAS/merge/manual |  |

Ghi key owner, duplicate window, durable response và behavior khi request đầu chưa rõ kết quả.

## 11. Lifecycle matrix

| Data/entity | Mode | Visible to | Restorable | Ownership effect | Retention trigger/duration | Purge authority | Snapshot needed |
|---|---|---|---|---|---|---|---|
|  | Hide / inactive / detach / soft delete / archive / anonymize / transfer / hard delete / immutable |  | Yes/No/Conditional |  | Decision ID hoặc Blocker |  | Yes/No/Blocker |

Không tự suy diễn legal basis, retention duration, identifier release hoặc cascade deletion.

## 12. Side-effect và consistency matrix

| Effect | Business purpose | Trigger | Same transaction? | After commit? | Delivery expectation | Retry/dedup | Reconciliation/owner |
|---|---|---|---|---|---|---|---|
| Core write/durable outbox intent |  |  | Yes | No | Atomic result |  |  |
| Notification/realtime/email/file/integration |  |  | No | Yes | At-most/at-least/exactly-once effect expectation | Required | Required |

Phân biệt domain event với Laravel Event. Không tuyên bố “delivered” chỉ vì đã dispatch.

## 13. Acceptance examples

```gherkin
Feature: Reschedule an interview

  Scenario: Authorized coordinator reschedules a confirmed interview
    Given the interview is confirmed
    And the coordinator has reschedule authority
    When the coordinator selects an available new slot
    Then the interview moves to the approved target state
    And the old slot is no longer reserved
    And notification side effects are requested only after the durable change commits

  Scenario: Duplicate reschedule command
    Given a reschedule command was already accepted
    When the same idempotency scope is submitted again
    Then no second state transition occurs
    And the caller receives the previously established outcome
```

Thêm scenario cho permission denial, invalid transition, concurrency, failure/compensation và lifecycle boundary khi áp dụng.

## 14. Dependency map

| Dependency | Contract cần có | Owner | Failure impact | Confirmed? |
|---|---|---|---|---|
|  |  |  |  | Yes/No/Blocker |

## 15. Readiness gate

Không dùng điểm phần trăm giả chính xác. Đánh giá từng nhóm:

| Nhóm | Ready | Partial | Blocked | Evidence/Blocker ID |
|---|---:|---:|---:|---|
| Outcome/glossary/scope |  |  |  |  |
| Actors/authority/permissions |  |  |  |  |
| Trigger/timeline/states |  |  |  |  |
| Invariants/failures/compensation |  |  |  |  |
| Concurrency/idempotency |  |  |  |  |
| Lifecycle/privacy/retention |  |  |  |  |
| Side effects/consistency |  |  |  |  |
| Acceptance/dependencies |  |  |  |  |

**Gate result:** `READY`, `READY_WITH_ACCEPTED_ASSUMPTIONS`, hoặc `BLOCKED`.

- `READY` yêu cầu không còn Blocker và không cần accepted assumption.
- `READY_WITH_ACCEPTED_ASSUMPTIONS` yêu cầu danh sách `AcceptedAssumption` không rỗng, đủ metadata và traceability.
- Bất kỳ Blocker chưa giải quyết nào đều làm toàn bộ gate thành `BLOCKED`. Owner hoặc resolution date không mở gate.

Raw `Proposal` không vào handoff. High-blast-radius policy không bao giờ dùng `AcceptedAssumption`; authorization/ownership, money/refund, terminal/reopen state, destructive lifecycle, legal/privacy/retention và identity release phải là Decision từ đúng authority.

## 16. Handoff summary

| Packet | Nội dung |
|---|---|
| Apiato workflow | Confirmed use cases, actors, permissions, states, invariants, effects, acceptance, dependency closure |
| MySQL optimization | Entities, cardinality, query shapes, write patterns, consistency, concurrency, data volume evidence |
| DB bandwidth | List/report/relation graph, selected fields, pagination/export, transfer frequency, cache/realtime expectations |
| Remaining risk | Blocker hoặc accepted assumptions, owner và review condition |

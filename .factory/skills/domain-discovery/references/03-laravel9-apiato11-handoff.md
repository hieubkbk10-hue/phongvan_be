# Laravel 9 / Apiato 11.x Handoff

## Version boundary

Tài liệu này chỉ áp dụng cho Laravel 9 và Apiato 11.x. Không suy diễn API từ Laravel/Apiato phiên bản mới hơn. Framework behavior phải kiểm tra bằng versioned official docs, local docs và code/package đang cài trong repository.

Handoff kỹ thuật chỉ bắt đầu sau khi Business Workflow Dossier đạt `READY` hoặc `READY_WITH_ACCEPTED_ASSUMPTIONS`.

## Từ use case sang boundary, chưa sinh file

Mapping này là guide trách nhiệm, không phải lệnh tạo class:

| Business evidence đã xác nhận | Apiato/Laravel boundary có thể dùng | Guardrail |
|---|---|---|
| Một user/system intent tạo outcome hoàn chỉnh | Action | Action điều phối use case; không biến một table thành một Action máy móc. |
| Một use case con có nghĩa nghiệp vụ, tái dùng trong orchestration | SubAction | Dùng khi có business capability con; không dùng chỉ để chia file. |
| Một bước query/mutation hoặc logic nhỏ tái dùng | Task hoặc Repository | Data access/query/mutation theo pattern repo; tránh business orchestration trong Task. |
| Input, validation và actor authorization | Request | Validation khác domain invariant; authorization phải theo actor/authority đã chốt. |
| Business fact đã xảy ra | Domain event concept | Không đồng nhất với Laravel Event class. |
| In-process dispatch/listener phù hợp | Laravel Event/Listener | Chỉ chọn sau khi delivery, transaction và retry semantics rõ. |
| Công việc async có retry/runtime | Job | Cần idempotency, retry/backoff, timeout, worker/deploy và observability. |
| HTTP representation | Transformer | Không để Transformer tạo N+1 hoặc quyết định permission. |

Luồng tham chiếu của repo:

```text
Route -> Controller -> Request -> Action -> SubAction -> Task
-> Repository/Model -> Transformer
```

Controller giữ mỏng. Không bắt buộc mọi use case có đủ mọi component.

## Transaction và after-commit boundary

Phân loại trước khi mapping:

1. **Atomic core:** các write phải cùng thành công/thất bại để giữ invariant.
2. **Durable fact:** trạng thái/event record cần tồn tại sau commit.
3. **External side effect:** email, realtime, webhook, file service, search index, analytics.
4. **Recovery:** retry, deduplication, outbox/reconciliation hoặc operator action.

Nguyên tắc:

- Transaction bao quanh write set tối thiểu cần atomicity; không bao quanh HTTP/email/file call dài.
- External side effect phụ thuộc dữ liệu đã commit phải chạy after commit hoặc qua cơ chế durable phù hợp.
- Laravel Event không mặc nhiên là domain event, durable hay after commit.
- Queue không mặc nhiên bảo đảm exactly-once effect. Job phải idempotent theo outcome nghiệp vụ.
- Nếu commit thành công nhưng dispatch thất bại là tình huống không chấp nhận được, handoff phải yêu cầu outbox/reconciliation hoặc cơ chế durable tương đương; không chỉ ghi “dispatch after commit”.
- Xác minh API cụ thể bằng Laravel 9 docs và package versions trong repo trước khi viết code.

## Exact packet cho `apiato-container-workflow`

Chỉ gọi `apiato-container-workflow` khi packet có:

```yaml
business_workflow:
  certainty_log:
    C-01:
      label: Observation
      claim: confirmed source fact
      source: evidence location
    D-01:
      label: Decision
      claim: confirmed business policy
      confirmed_by: authority identity
      authority_scope: policy scope
    P-01:
      label: Proposal
      claim: proposed bounded detail
      evidence_ids: [C-01]
    A-01:
      label: AcceptedAssumption
      claim: bounded reversible detail accepted temporarily
      source_proposal_id: P-01
      evidence_ids: [C-01]
  outcome: confirmed outcome
  claim_ids: [C-01]
  decision_ids: [D-01]
  evidence_ids: [C-01]
  scope_in: []
  scope_out: []
  glossary: []
  actors:
    - name:
      authority_source:
      permissions: []
      decision_ids: [D-01]
  use_cases:
    - name:
      trigger:
      preconditions: []
      success_postconditions: []
      failure_postconditions: []
      claim_ids: [C-01]
      decision_ids: [D-01]
  states:
    - name:
      terminal:
      decision_ids: [D-01]
  transitions:
    - from:
      to:
      actor:
      guard:
      outcome:
      decision_ids: [D-01]
  invariants:
    - id:
      rule:
      scope:
      enforcement_moment:
      decision_ids: [D-01]
      evidence_ids: [C-01]
  failures_and_compensation:
    - id: F-01
      failure: failure condition
      compensation: approved response
      claim_ids: [C-01]
      decision_ids: [D-01]
      assumption_ids: [A-01]
      evidence_ids: [C-01]
  concurrency_and_idempotency:
    - id: CI-01
      scenario: duplicate or concurrent operation
      expected_outcome: confirmed outcome
      claim_ids: [C-01]
      decision_ids: [D-01]
      assumption_ids: [A-01]
      evidence_ids: [C-01]
  lifecycle_and_retention:
    - id: L-01
      policy: confirmed lifecycle rule
      claim_ids: [C-01]
      decision_ids: [D-01]
      assumption_ids: []
      evidence_ids: [C-01]
  side_effects_and_consistency:
    - id: SE-01
      effect: side effect and consistency expectation
      claim_ids: [C-01]
      decision_ids: [D-01]
      assumption_ids: [A-01]
      evidence_ids: [C-01]
  acceptance_scenarios:
    - id: AC-01
      given_when_then: compact acceptance scenario
      claim_ids: [C-01]
      decision_ids: [D-01]
      assumption_ids: [A-01]
  dependencies:
    - id: DEP-01
      capability: required capability
      required_contract: confirmed contract
      claim_ids: [C-01]
      evidence_ids: [C-01]
  blockers: []
  accepted_assumptions:
    - id: A-01
      accepted_by: accepter identity
      authority_scope: bounded scope
      scope: exact temporary detail
      risk: known limited risk
      reversible: true
      review_condition: when to confirm or remove
      source_proposal_id: P-01
      evidence_ids: [C-01]
  readiness: READY_WITH_ACCEPTED_ASSUMPTIONS
```

`readiness` chỉ nhận đúng `READY` hoặc `READY_WITH_ACCEPTED_ASSUMPTIONS`:

- `READY`: `blockers` và `accepted_assumptions` đều rỗng.
- `READY_WITH_ACCEPTED_ASSUMPTIONS`: `blockers` rỗng và `accepted_assumptions` không rỗng; mỗi object có đủ field trong mẫu, `reversible: true`, source Proposal và evidence traceability.

Mọi element nghiệp vụ liên quan phải tham chiếu `claim_ids`, `decision_ids`, `assumption_ids` hoặc `evidence_ids` tồn tại trong `certainty_log`; Decision phải lưu người xác nhận và authority scope.

Raw `Proposal` chỉ được giữ làm provenance, không được tham chiếu như requirement. Chỉ `AcceptedAssumption` đủ acceptance metadata mới được vào handoff. High-blast-radius policy không bao giờ dùng nhãn này; authorization/ownership, money/refund, terminal/reopen state, destructive lifecycle, legal/privacy/retention và identity release bắt buộc Decision đúng authority. Bất kỳ Blocker nào cũng cấm handoff.

`apiato-container-workflow` nhận packet để:

- Audit code hiện có.
- Tính dependency closure.
- Chọn Container/component boundaries.
- Lập topological build order, test, rollback và validation.

Nó không được mở lại policy bằng cách tự đoán.

## Trigger packet cho `mysql-optimization`

Chỉ chuyển khi nghiệp vụ đã cho biết entity và access pattern:

| Field | Nội dung bắt buộc |
|---|---|
| Entities/relations | Aggregate, cardinality, optionality, ownership, lifecycle |
| Invariants | Uniqueness, FK semantics, state/money/time consistency |
| Query shapes | Filter, join, sort, projection, aggregate, pagination, frequency |
| Write patterns | Insert/update/upsert/bulk, transaction grouping, expected contention |
| Volume evidence | Current/expected rows, growth, request frequency; ghi unknown nếu chưa đo |
| Consistency | Atomicity, stale-read tolerance, lock/conflict policy |
| Validation | Representative dataset, SQL/bindings, EXPLAIN/measurement expectation |

Kích hoạt khi có migration/schema/index, relation, Repository/query, filter/search/sort, pagination, transaction, locking/concurrency hoặc bulk write.

Không yêu cầu index, BIGINT/UUID, partition hay cache dựa trên threshold phổ quát.

## Trigger packet cho `db-bandwidth-optimization`

Dùng khi có:

- List/search/report/dashboard/export.
- Relation graph hoặc Transformer includes.
- Realtime payload, polling, sync hoặc integration transfer.
- Fetch-all/filter-in-application pattern.
- Dữ liệu lớn/nhạy cảm hoặc trước deploy.

Packet gồm:

```yaml
data_transfer:
  consumers: []
  use_cases: []
  list_and_report_shapes: []
  relations_and_includes: []
  required_fields_by_consumer: []
  pagination_or_export_behavior: []
  request_and_refresh_frequency: []
  realtime_or_polling: []
  authorization_scope: []
  consistency_and_freshness: []
  measured_rows_bytes_queries: []
```

Mục tiêu là giảm rows/bytes/query count mà vẫn giữ permission, invariant và acceptance outcome.

## Handoff stop conditions

Không handoff nếu:

- Actor hoặc authority chốt policy chưa rõ.
- Terminal/reopen, cancellation/deletion, retention/purge, ownership transfer, refund hoặc delivery policy còn là Inference.
- Query shape được suy ra từ UI mock mà chưa có use case.
- Dependency được tạo giả chỉ để compile.
- Technical sketch `Proposal - non-binding` đang bị dùng như Decision.

Nếu user yêu cầu persistence, Business Workflow Dossier hoặc business-flow summary mới được ghi vào `docs/nghiepvu.md` theo quy tắc repo; việc đó không thay thế handoff packet.

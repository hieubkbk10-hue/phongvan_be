---
name: domain-discovery
description: Use when Laravel 9 or Apiato 11.x requirements are unclear or policy-heavy and need discovery of actors, authority, states, invariants, lifecycle, failures, and acceptance before technical design.
---

# Phân tích/khám phá nghiệp vụ

## Vai trò

Khám phá requirement trước API, database, Container hoặc code design. AI tổ chức bằng chứng, làm rõ policy và chặn handoff chưa an toàn; AI không sở hữu policy. Kết quả là **Business Workflow Dossier** có traceability, readiness gate và packet handoff.

## Certainty labels

| Nhãn | Ý nghĩa |
|---|---|
| `Observation` | Có bằng chứng trực tiếp. |
| `Inference` | Suy luận cần kiểm chứng. |
| `Proposal` | Phương án chưa được chấp nhận. |
| `AcceptedAssumption` | Named Proposal được đúng authority chấp nhận; chỉ reversible, bounded, non-legal, non-destructive và đủ metadata/traceability. |
| `Decision` | Policy được đúng authority xác nhận. |
| `Blocker` | Thiếu quyết định để thiết kế an toàn. |

Không đổi im lặng, deadline, `Inference` hoặc raw `Proposal` thành đồng ý. Authority phải claim-specific; PO/domain authority không thay thế legal/compliance/security authority.

## Cách làm

Theo đúng thứ tự:

1. Outcome, scope, glossary.
2. Actors, authority, ownership, permissions.
3. Trigger, preconditions, postconditions.
4. Happy-path timeline.
5. State machine.
6. Invariants.
7. Alternatives, failures, compensation.
8. Concurrency, idempotency.
9. Lifecycle, privacy, retention.
10. Side effects, consistency.
11. Examples, acceptance.
12. Readiness gate.
13. Handoff.

Khi có nhiều `Blocker`, xếp hạng theo blast radius rồi mỗi lượt chỉ hỏi **một câu hỏi policy độc lập**. Nêu vì sao câu hỏi chặn thiết kế; không gộp các quyết định có thể trả lời riêng.

## Restrictions và readiness

Không thiết kế schema, index, endpoint hay Apiato component graph trước readiness. Technical sketch nếu thật sự cần luôn là `Proposal - non-binding`. Không mặc định event sourcing, outbox, CQRS, microservices, cache hoặc threshold phổ quát.

High-blast-radius policy về authorization/ownership, money/refund, terminal/reopen state, destructive lifecycle, legal/privacy/retention hoặc identity release bắt buộc là `Decision` từ đúng authority; không dùng `AcceptedAssumption`.

Gate chỉ nhận:

- `READY`: không có Blocker hoặc accepted assumption.
- `READY_WITH_ACCEPTED_ASSUMPTIONS`: không có Blocker; mọi assumption hợp lệ và đủ traceability.
- `BLOCKED`: còn bất kỳ Blocker nào.

Sau readiness, handoff trước cho `apiato-container-workflow`; gọi `mysql-optimization` khi có schema/query/write/concurrency và `db-bandwidth-optimization` khi có list/report/relation/data transfer hoặc trước deploy.

## References

- [Discovery workflow](references/01-discovery-workflow.md)
- [Business Workflow Dossier](references/02-business-workflow-dossier.md)
- [Laravel 9 / Apiato 11 handoff](references/03-laravel9-apiato11-handoff.md)
- [Domain patterns và red flags](references/04-domain-patterns-and-red-flags.md)
- [Source map](references/05-source-map.md)

Chỉ ghi hoặc cập nhật `docs/nghiepvu.md` khi user yêu cầu; dùng Cross-Entity Interactive Mindmap/Markmap và tham chiếu chéo theo `AGENTS.md`.

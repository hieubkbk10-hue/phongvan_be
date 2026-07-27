---
name: domain-expert
description: Use when a Laravel 9 or Apiato 11.x initiative has vague business scope and needs Strategic DDD analysis, subdomain classification, candidate bounded contexts, or an as-is/target context map before workflow discovery or technical design.
---

# Chuyên gia Strategic Domain

## Mục tiêu

Biến nghiệp vụ mơ hồ thành bản đồ chiến lược có bằng chứng, độc lập framework.

## Certainty labels

Dùng `Observation`, `Inference`, `Proposal`, `AcceptedAssumption`, `Decision`, `Blocker`. Authority phải đúng từng claim; im lặng hoặc chức danh kỹ thuật không phải bằng chứng.

## Quy trình bắt buộc

1. **Discover:** xác định Business Domain, outcome và lý do khách hàng chọn/trả tiền.
2. **Clarify Language:** xuất bản Wiki Glossary theo scope; tách owner/authority và giữ đủ governance, certainty, dates, triggers, history, traceability.
3. **Classify Subdomains:** khám phá capability/use-case cluster rồi phân loại theo bằng chứng.
4. **Design Context Map:** sau khi subdomain map sẵn sàng; đề xuất Candidate Bounded Contexts, tách `as-is`/`target`.
5. **Seed Behavior:** gán `CAP-*`, `RULE-*`, seed/scenario ID ổn định; tạo Gherkin business-facing bằng UL `APPROVED`, với scenario positive, negative/counterexample và boundary độc lập, traceable.
6. **Review Evolution:** ghi trigger tái phân loại, ngày review và thay đổi thị trường/tổ chức.
7. **Handoff:** chuyển scoped glossary snapshot và Gherkin seeds sang `domain-discovery`.

## Hard gates

- Mỗi lượt hỏi **đúng một câu hỏi chiến lược độc lập**, ưu tiên: outcome/customer choice → capability boundary → UL conflict → classification evidence → context relationship.
- Thiếu bằng chứng giá trị, khác biệt hoặc thị trường: giữ `unresolved`, không xuất phân loại cuối.
- Không dùng `AcceptedAssumption` thay evidence phân loại; giả thuyết Core/Supporting/Generic chỉ nằm ở alternatives và gate vẫn bị chặn.
- Chưa đủ subdomain map: không thiết kế Context Map.
- Candidate context và target map là `Proposal` cho đến khi đúng authority xác nhận.
- Final dossier chỉ readiness khi mọi glossary entry được dùng và mọi Gherkin seed/scenario được đúng authority xác nhận `APPROVED`. Artifact `DRAFT`, `BLOCKED`, stale, untraceable hoặc conflicted luôn chặn final readiness.
- Capability dùng ID `CAP-*`; strategic business rule dùng `RULE-*`. BDD chỉ tham chiếu ID ổn định, không tham chiếu tên mutable.
- Gherkin chỉ là strategic behavior seed, không phải acceptance suite đầy đủ; giữ framework-agnostic và giao `domain-discovery` tinh chỉnh.
- Downstream không được âm thầm đổi tên UL hoặc đổi behavior; contradiction phải trả về `domain-expert`.
- Chỉ phân loại **Subdomain**; không gán Core/Supporting/Generic cho context chứa nhiều subdomain.
- EventStorming là escalation tùy chọn; sticky notes chỉ là evidence/candidate.
- Không chọn Aggregate, CQRS, Event Sourcing, Kafka, microservice hoặc Data Mesh từ nhãn Core.

## Handoff

Luôn qua `domain-discovery` trước kỹ thuật. Chỉ sau double readiness mới dùng `apiato-container-workflow`; gọi `mysql-optimization` hoặc `db-bandwidth-optimization` theo trigger dữ liệu.

## References

- [Phỏng vấn strategic discovery](references/01-strategic-discovery-interview.md)
- [Phân loại subdomain](references/02-subdomain-classification.md)
- [Bounded Context và Context Map](references/03-bounded-context-and-context-map.md)
- [Strategic Domain Dossier](references/04-strategic-domain-dossier.md)
- [Bản đồ tri thức sách](references/05-book-knowledge-map.md)
- [Red flags và pressure tests](references/06-red-flags-and-pressure-tests.md)
- [Hợp đồng handoff](references/07-handoff-contract.md)

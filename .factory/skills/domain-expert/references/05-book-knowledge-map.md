# Bản đồ tri thức Learning Domain-Driven Design

## Phạm vi nguồn

Root MDX cục bộ:

`E:\sach\Learning Domain-Driven Design\content\docs`

Nguồn là bản chuyển đổi phục vụ học tập từ *Learning Domain-Driven Design* của Vlad Khononov. Khi cần hành vi triển khai cụ thể, ưu tiên edition/source chính thức của sách và tài liệu framework/package đúng phiên bản. Không biến câu văn tuyệt đối trong MDX thành định luật; đọc chúng như heuristic cần kiểm tra bằng context và counterexample.

Đường dẫn tuyệt đối trên là provenance của máy tạo skill, không phải runtime dependency. Nếu root này không tồn tại trên máy khác, skill vẫn dùng knowledge map đã đóng gói; chỉ đánh dấu source re-verification là `Blocker` khi cần kiểm tra nguyên văn/heading.

## Strategic core

Các chương này là nguồn chính của `domain-expert`.

| Phần | Path group và nội dung | Cách dùng |
|---|---|---|
| **Chương 1: Analyzing Business Domains** | `chuong-01-analyzing-business-domains\index.mdx`; `01-what-is-a-business-domain.mdx`; `02-subdomains-classification.mdx`; `03-domain-analysis-examples.mdx`; `04-tom-tat.mdx` | Business Domain; Core/Supporting/Generic; case Gigmaster/BusVNext; domain expert. Dùng cho outcome, capability và classification evidence. |
| **Chương 2: Discovering Domain Knowledge** | `chuong-02-discovering-domain-knowledge\index.mdx`; `01-business-people-and-software-engineers.mdx`; `02-ubiquitous-language.mdx`; `03-domain-model-and-glossary.mdx`; `04-tom-tat.mdx` | Knowledge discovery, Ubiquitous Language, glossary và Domain Model. Giữ language cục bộ theo scope. |
| **Chương 3: Managing Domain Complexity** | `chuong-03-managing-domain-complexity\index.mdx`; `01-what-is-a-bounded-context.mdx`; `02-subdomains-vs-bounded-contexts.mdx`; `03-boundaries-and-models.mdx`; `04-tom-tat.mdx` | Bounded Context, Subdomain khác context, mapping 1:1/1:N/N:1, model/ownership boundary. |
| **Chương 4: Integrating Bounded Contexts** | `chuong-04-integrating-bounded-contexts\index.mdx`; `01-context-map-overview.mdx`; `02-partnership-and-shared-kernel.mdx`; `03-customer-supplier-patterns.mdx`; `04-separate-ways.mdx`; `05-tom-tat.mdx` | Context Map, upstream/downstream, Partnership, Shared Kernel, Customer-Supplier, Conformist, ACL, OHS/Published Language, Separate Ways. |
| **Chương 10: Design Heuristics** | `chuong-10-design-heuristics\index.mdx`; `01-heuristic-rules-of-thumb.mdx`; `02-architectural-heuristics.mdx`; `03-wolfdesk-case-study-analysis.mdx`; `04-tom-tat.mdx` | Heuristic nối strategic classification với pattern tactical. Chỉ dùng để critique/handoff, không tự động chọn kiến trúc. |
| **Chương 11: Evolving Design Decisions** | `chuong-11-evolving-design-decisions\index.mdx`; `01-changes-in-domain-complexity.mdx`; `02-context-migration-strategies.mdx`; `03-legacy-refactoring-patterns.mdx`; `04-tom-tat.mdx` | Tái phân loại subdomain, split/merge context, đổi relation, Strangler Fig/Bubble Context. Nguồn cho evolution trigger và review. |
| **Chương 12: EventStorming** | `chuong-12-eventstorming\index.mdx`; `01-what-is-eventstorming.mdx`; `02-eventstorming-steps.mdx`; `03-from-eventstorming-to-ddd-models.mdx`; `04-tom-tat.mdx` | Big Picture EventStorming như discovery escalation. Event/process/aggregate output là evidence/candidate, không phải final design. |
| **Chương 13: DDD in the Real World** | `chuong-13-ddd-in-the-real-world\index.mdx`; `01-brownfield-projects-challenges.mdx`; `02-strangler-fig-pattern.mdx`; `03-organizational-and-team-aspects.mdx`; `04-tom-tat.mdx` | Brownfield assessment, incremental modernization, pragmatic/organizational adoption. Dùng để giữ as-is trung thực và target có migration. |
| **Phụ lục A: Case Study** | `phu-luc-a-case-study\index.mdx`; `01-case-study-analysis.mdx` | Marketnovus, candidate contexts và các giai đoạn trưởng thành. Chỉ là case minh họa, không khái quát số liệu thành fact thị trường. |
| **Phụ lục B: Answers** | `phu-luc-b-answers\index.mdx`; `01-answers-part-1-to-4.mdx` | Cross-check bài tập Chương 1-16. Nội dung rút gọn, không dùng làm nguồn duy nhất cho định nghĩa. |

## Tactical và architecture knowledge

Các chương này chỉ dùng để nhận diện architecture bait, critique proposal hoặc tạo handoff downstream. Không suy tactical pattern từ nhãn Core.

| Phần | Path group và nội dung | Giới hạn sử dụng |
|---|---|---|
| **Chương 5: Implementing Simple Business Logic** | `chuong-05-implementing-simple-business-logic\index.mdx`; `01-transaction-script.mdx`; `02-active-record.mdx`; `03-when-to-use-simple-patterns.mdx`; `04-tom-tat.mdx` | Transaction Script, Active Record và simple logic. Chỉ là option triển khai sau discovery. |
| **Chương 6: Tackling Complex Business Logic** | `chuong-06-tackling-complex-business-logic\index.mdx`; `01-domain-model-pattern.mdx`; `02-value-objects-and-entities.mdx`; `03-aggregates-and-invariants.mdx`; `04-repository-pattern.mdx`; `05-tom-tat.mdx` | Domain Model, Value Object, Entity, Aggregate, invariant, Repository. Tactical complexity là evidence phụ, không định nghĩa Core. |
| **Chương 7: Modeling the Dimension of Time** | `chuong-07-modeling-the-dimension-of-time\index.mdx`; `01-event-sourced-domain-model.mdx`; `02-event-sourcing-building-blocks.mdx`; `03-advantages-and-tradeoffs.mdx`; `04-tom-tat.mdx` | Event Sourcing, Event Store, projection, snapshot và trade-off. Chỉ handoff khi temporal requirements có evidence. |
| **Chương 8: Architectural Patterns** | `chuong-08-architectural-patterns\index.mdx`; `01-layered-architecture.mdx`; `02-ports-and-adapters.mdx`; `03-cqrs-pattern.mdx`; `04-tom-tat.mdx` | Layered, Ports and Adapters, CQRS. Không dùng làm cách phát hiện Subdomain/Bounded Context. |
| **Chương 9: Communication Patterns** | `chuong-09-communication-patterns\index.mdx`; `01-model-translated-events.mdx`; `02-outbox-pattern.mdx`; `03-sagas-and-process-managers.mdx`; `04-tom-tat.mdx` | Integration events, Outbox, Saga, Process Manager. Chỉ hiện thực relation/consistency đã được discovery làm rõ. |
| **Chương 14: Microservices** | `chuong-14-microservices\index.mdx`; `01-microservices-and-ddd-relationship.mdx`; `02-microservices-boundaries.mdx`; `03-common-pitfalls.mdx`; `04-tom-tat.mdx` | Service boundary, distributed monolith, shared DB và premature microservices. Bounded Context không đồng nghĩa microservice. |
| **Chương 15: Event-Driven Architecture** | `chuong-15-event-driven-architecture\index.mdx`; `01-eda-foundations.mdx`; `02-types-of-events.mdx`; `03-eda-design-heuristics.mdx`; `04-tom-tat.mdx` | Event/command/message, event types, coupling và consistency. Không chọn EDA do preference hoặc nhãn Core. |
| **Chương 16: Data Mesh** | `chuong-16-data-mesh\index.mdx`; `01-analytical-vs-transactional-data.mdx`; `02-data-warehouse-and-data-lake-limitations.mdx`; `03-data-mesh-principles.mdx`; `04-tom-tat.mdx` | OLTP/OLAP, warehouse/lake limits, Data Mesh principles. Chỉ downstream khi analytical ownership và platform readiness có evidence. |

Root index: `index.mdx` mô tả Strategic Design, Tactical Design và Architecture Patterns; không thay thế nội dung từng chương.

## Caveat cần giữ khi trích dẫn

- Claim “Core bắt buộc tự build, tuyệt đối không outsource” trong Chương 1 là heuristic. Build/buy/partner cần evidence về strategic control, learning loop, constraints và vendor.
- Claim Ubiquitous Language loại bỏ tuyệt đối technical jargon hoặc có quan hệ term-concept 1:1 trong Chương 2 phải hiểu trong local model scope.
- Claim một context luôn do đúng một team sở hữu trong Chương 3 là strong default về accountability, không phải invariant tổ chức.
- Claim microservice không bao giờ vượt Bounded Context trong Chương 14 là boundary heuristic; semantic boundary và deployment unit không mặc định 1:1.
- Claim Outbox “an toàn tuyệt đối” trong Phụ lục B bỏ qua relay, retry, ordering, idempotency và operations.
- Số liệu tiết kiệm trong Phụ lục A không có citation kèm theo trong MDX; không dùng làm market/legal fact.
- “Data Mesh là lời giải” trong Chương 16 phải được xem là architecture option có trade-off và readiness.
- Heading Conformist/ACL/OHS trong `chuong-04-integrating-bounded-contexts\03-customer-supplier-patterns.mdx` không đồng cấp về Markdown nhưng đồng cấp về ý nghĩa pattern.

## Evidence intake

Mỗi claim nhập vào dossier phải có:

```yaml
book_evidence:
  chapter: "<Chương/Phụ lục>"
  path: "<path tương đối từ root>"
  heading: "<heading hoặc đoạn nguồn>"
  claim: "<claim được rút ra>"
  context: "<bối cảnh ví dụ/giả định của nguồn>"
  applicability: "<vì sao áp dụng cho domain hiện tại>"
  counterexample: "<trường hợp claim không đúng hoặc cần giới hạn>"
  classification_impact: "<core/supporting/generic/unresolved/no_direct_impact>"
  certainty: "Observation | Inference | Proposal"
  expiry_or_review: "<ngày hoặc sự kiện cần kiểm tra lại>"
```

Tri thức sách là reference authority về DDD, không phải authority về customer choice, market facts, legal policy hoặc business decision của tổ chức đang phân tích.

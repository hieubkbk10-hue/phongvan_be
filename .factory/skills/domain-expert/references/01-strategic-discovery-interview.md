# Phỏng vấn Strategic Discovery

## Nguyên tắc điều hành

- Hỏi đúng **một câu hỏi độc lập trong mỗi lượt**. Không dùng câu ghép có nhiều quyết định, kể cả dưới dạng “và/hoặc”.
- Nói bằng Ubiquitous Language hiện có. Khi chưa rõ thuật ngữ, hỏi định nghĩa trước khi dùng nó để suy luận.
- Với mỗi câu trả lời, ghi `Observation`, `Inference`, `Proposal`, `AcceptedAssumption`, `Decision` hoặc `Blocker`.
- Authority là claim-specific. Người sáng lập có thể xác nhận chiến lược sản phẩm nhưng không mặc nhiên xác nhận quy định pháp lý; CTO không biến sở thích kiến trúc thành bằng chứng customer choice.
- Nếu có nhiều blocker, hỏi blocker có blast radius lớn nhất trước. Nêu ngắn gọn vì sao câu hỏi đang chặn bước kế tiếp.
- Không biến giả định thành “sự thật” do deadline, im lặng hoặc đồng thuận chung chung.

## Thứ tự phỏng vấn

### Pha 1: Xác định Business Domain và outcome

Mục tiêu là mô tả problem space doanh nghiệp tạo giá trị, không phải danh sách tính năng hoặc hệ thống.

Thứ tự câu hỏi gợi ý, mỗi lượt chỉ chọn một câu:

1. Kết quả có giá trị nhất mà doanh nghiệp cam kết tạo ra cho khách hàng là gì?
2. Khách hàng chọn hoặc trả tiền cho doanh nghiệp vì năng lực nào?
3. Nếu năng lực đó biến mất, lý do chọn doanh nghiệp thay đổi thế nào?
4. Đối thủ khó sao chép tri thức, dữ liệu, quy tắc hoặc cách vận hành nào?
5. Thị trường, phân khúc, tổ chức và time horizon nào đang được xét?

**Exit criteria**

- Có Business Domain statement nêu actor nhận giá trị, outcome và phạm vi.
- Có evidence hoặc `Blocker` rõ cho customer choice và competitive differentiation.
- Không dùng tên framework, service, bảng hoặc Container để định nghĩa domain.

### Pha 2: Khám phá capability và Subdomain

Tìm business capabilities ổn định hơn sơ đồ tổ chức hoặc màn hình hiện tại.

1. Doanh nghiệp phải có khả năng tạo ra kết quả nào để đạt outcome?
2. Mỗi capability bắt đầu từ nhu cầu nào và kết thúc khi giá trị nào đã tạo?
3. Quy tắc, authority hoặc nhịp thay đổi nào khác biệt giữa hai capability đang bị gộp?
4. Capability nào chỉ phục vụ capability khác nhưng vẫn có hành vi đặc thù?
5. Capability nào giải bài toán đã được thị trường chuẩn hóa?

Mỗi candidate Subdomain cần:

| Trường | Nội dung |
|---|---|
| Capability ID | ID ổn định dạng `CAP-*`; không tái sử dụng khi đổi tên |
| Tên theo UL | Tên business capability, không dùng tên module kỹ thuật |
| Outcome | Giá trị mà capability tạo ra |
| In scope / Out of scope | Ranh giới problem space |
| Actors/authority hints | Chỉ là đầu mối cho `domain-discovery` |
| Policies/pivotal events | Tín hiệu nhóm use case |
| Dependencies | Capability cung cấp hoặc tiêu thụ |
| Evidence/counterexample | Nguồn xác nhận và dữ kiện phản bác |

**Exit criteria**

- Capability map bao phủ outcome chính.
- Candidate Subdomain có ranh giới giải thích được bằng value, policy hoặc language.
- Các điểm chưa chắc được giữ trong unresolved list; không lấp bằng code hiện tại.

### Pha 3: Làm rõ và xuất bản Wiki Glossary

Wiki Glossary là final artifact có thể xuất bản, không chỉ là bảng UL nội bộ. Governance owner chịu trách nhiệm duy trì/xuất bản tài liệu; authority là claim-specific và xác nhận definition của từng entry. Không gộp hai vai trò. Mỗi entry theo từng scope:

| Term | Definition | Example | Counterexample | Scope | Entry owner | Authority required | Confirmed by | Synonyms | Homonyms/Collision | Status | Version | Effective date | Review date | Certainty | Evidence/Decision refs |
|---|---|---|---|---|---|---|---|---|---|---|---|---|---|---|---|
| Thuật ngữ nghiệp vụ | Nghĩa có thể kiểm chứng | Trường hợp thuộc nghĩa | Trường hợp dễ nhầm nhưng không thuộc | `CAP-*`/subdomain/context candidate | Người duy trì entry | Authority đúng claim | Người đã xác nhận hoặc `none` | Từ đồng nghĩa cục bộ | Cùng từ khác nghĩa hoặc collision note | DRAFT/APPROVED/DEPRECATED/BLOCKED | Version entry | Ngày hiệu lực riêng | Ngày review riêng | Certainty label | Certainty/decision IDs |

Quy tắc:

- Definition phải mô tả khái niệm nghiệp vụ, không chỉ cấu trúc dữ liệu.
- Example và counterexample dùng để kiểm tra ranh giới nghĩa.
- Không tạo các từ trung hòa như `Person`, `Party`, `Record` chỉ để ép nhiều scope dùng chung.
- Language collision là evidence cho ranh giới; chưa tự động quyết định Bounded Context.
- Domain Model là purposeful abstraction phục vụ quyết định trong một scope, không phải enterprise data model.
- Document governance phải có glossary owner, publication status, document version, effective date, review date, approval/change history, review cadence và `change_triggers`.
- Entry stale, chưa được authority cần thiết xác nhận `APPROVED`, không traceable hoặc conflicted phải được đánh dấu và chặn final readiness.

Các chủ đề governance được đưa vào `question_backlog`; mỗi lượt chỉ hỏi một blocker độc lập có blast radius lớn nhất, không gom owner, approval, version và review policy thành một câu.

**Exit criteria**

- Thuật ngữ trọng yếu có đủ trường entry và traceability.
- Wiki Glossary có governance metadata, publication status và approval/change history.
- Homonym/synonym collision đã được ghi nhận, không bị xóa bằng “từ chung”.
- Các thuật ngữ chưa có đúng authority xác nhận vẫn là `Blocker` hoặc `Inference`.

### Pha 3B: Tạo strategic Gherkin/BDD seeds

Sau khi UL trọng yếu đã `APPROVED`, chọn capability `CAP-*` và strategic rule `RULE-*` tiêu biểu. Mỗi seed có ID ổn định, capability ref, rule object và `scenarios[]`. Mỗi scenario có ID riêng, coverage, status, glossary/evidence refs và một Gherkin block hoàn chỉnh:

```gherkin
Feature: <outcome/capability bằng UL đã duyệt>
  Rule: <RULE-* — statement>
    Scenario: <một example độc lập>
      Given <business context>
      When <business event/action>
      Then <observable business outcome>
```

Quy tắc:

- Dùng đúng term và meaning từ Wiki Glossary `APPROVED`; term unresolved/conflicted làm gate bị chặn dù có marker.
- Mỗi seed trace tới một `CAP-*` và rule object `{ id: RULE-*, statement }`; không tham chiếu tên mutable.
- Mỗi scenario có `SCN-*`, coverage `positive | negative_counterexample | boundary`, status, glossary refs, evidence/decision refs và standalone `gherkin` gồm đủ `Feature`, `Rule`, `Scenario`.
- Tập scenario đại diện phải bao phủ cả ba coverage; seed và từng scenario chỉ pass khi đúng authority xác nhận `APPROVED`.
- Giữ business-facing, framework-agnostic; không nêu API, database, class, queue hoặc implementation.
- Đây là strategic behavior seed, không phải workflow acceptance suite hoàn chỉnh. `domain-discovery` phải tinh chỉnh actor, state, invariant, failure, lifecycle và acceptance.
- Các thiếu sót BDD vào backlog; mỗi lượt vẫn chỉ hỏi đúng một câu hỏi độc lập.

**Exit criteria**

- Có seed/scenario ID ổn định và `Feature`, `Rule`, `Scenario` cho các `CAP-*`/`RULE-*` được chọn.
- Tập seed đại diện có positive, counterexample/negative và boundary examples.
- Mỗi seed/scenario dùng UL hợp lệ, traceable và `APPROVED` bởi authority đúng claim; `DRAFT`, `BLOCKED`, stale, untraceable hoặc conflicted đều chặn final readiness.

### Pha 4: Thu thập bằng chứng phân loại

Hỏi lần lượt:

1. Bằng chứng nào cho thấy capability ảnh hưởng customer choice hoặc willingness to pay?
2. Tri thức hoặc cách vận hành nào tạo khác biệt và cần cải tiến liên tục?
3. Nếu capability chỉ cần cho vận hành, hành vi custom nào khiến giải pháp chuẩn không đủ?
4. Có sản phẩm, SaaS hoặc thư viện trưởng thành nào giải cùng bài toán trong cùng constraints?
5. Điều gì có thể làm phân loại thay đổi trong time horizon đã chọn?

**Exit criteria**

- Mỗi Subdomain có decision record theo [phân loại subdomain](02-subdomain-classification.md).
- Thiếu market/customer-choice evidence thì classification là `unresolved`.
- Complexity, traffic, criticality, số bảng và kiến trúc hiện tại không xuất hiện như định nghĩa.

### Pha 5: Khám phá quan hệ trước Context Map

Chỉ bắt đầu khi Pha 1-4 đủ evidence.

1. Scope nào cần mô hình và ngôn ngữ nhất quán riêng?
2. Ai sở hữu quyết định và nhịp thay đổi của scope đó?
3. Scope nào thay đổi cùng nhau vì cùng policy, và scope nào chỉ đang coupled do di sản?
4. Pivotal event hoặc contract nào đi qua ranh giới?
5. Bên nào là upstream, bên nào là downstream, và quyền thương lượng thực tế ra sao?

**Exit criteria**

- Candidate contexts có heuristic/evidence và alternatives.
- As-is coupling được mô tả trung thực trước target proposal.
- Mỗi relation có direction, ownership, contract và pattern candidate.

## Escalation bằng Big Picture EventStorming

Đề xuất Big Picture EventStorming khi domain rộng, nhiều nhóm dùng ngôn ngữ xung đột, capability timeline không rõ hoặc phỏng vấn tuần tự không lộ pivotal events.

Đầu ra có thể dùng làm evidence:

- Domain events theo thời gian.
- Hotspots, policies, actors, external systems.
- Candidate capabilities, language collisions và pivotal events.

Không coi sticky notes, swimlane hoặc cluster workshop là final Subdomain, Bounded Context hay Context Map. Process/Design EventStorming thuộc discovery sâu của use case, không phải bước bắt buộc của strategic mapping.

# Red flags và Pressure Tests

## Red flags phải dừng

| Red flag | Vì sao sai | Hành vi đúng |
|---|---|---|
| **Core = phức tạp/traffic cao** | Độ khó kỹ thuật không chứng minh customer choice hoặc differentiation | Yêu cầu customer/market evidence; thiếu thì `unresolved` |
| **Generic = không quan trọng** | Generic có thể critical, regulated và rất phức tạp | Tách classification khỏi criticality và operational excellence |
| **Subdomain = Bounded Context** | Một cái được khám phá trong problem space, một cái được thiết kế | Ghi mapping 1:1, 1:N hoặc N:1 như `Proposal` |
| **Context = microservice/database/Apiato Container/team** | Semantic boundary không mặc định trùng deployment, persistence, code hoặc organization | Hoãn mapping kỹ thuật tới handoff |
| **Enterprise-wide UL** | Xóa local meaning và che language collision | Giữ term/definition theo scope |
| **`Person`/`Party` làm từ trung hòa sớm** | Solution-space model được đưa vào trước problem-space evidence | Ghi homonym và hỏi meaning/authority từng scope |
| **Current architecture là evidence chiến lược** | Legacy coupling, shared DB và module names phản ánh lịch sử triển khai | Mô tả as-is trung thực nhưng không dùng nó để định nghĩa capability/classification |
| **Shared Kernel/common dump** | Mở rộng shared model làm tăng coordination và semantic coupling | Shared Kernel thật nhỏ, ownership/change process rõ; nếu không, dùng contract/translation |
| **Target map che as-is** | Migration không thể kiểm soát khi coupling hiện tại bị bỏ qua | Lập as-is trước, target là `Proposal` có migration |
| **EventStorming output là final design** | Sticky clusters là evidence và hypothesis | Review authority, language và boundary alternatives |
| **Architecture bait** | Kafka, CQRS, Event Sourcing, microservice, Data Mesh không trả lời business outcome | Ghi preference nhưng defer đến downstream evidence |
| **Phân loại cả context chứa nhiều subdomain** | Làm mất mức đầu tư riêng của từng capability | Chỉ phân loại từng Subdomain |
| **Một lượt hỏi nhiều câu** | Authority có thể trả lời một phần và bỏ sót blocker chính | Chọn một blocker độc lập có blast radius cao nhất |
| **Glossary chỉ là bảng UL nội bộ** | Không có owner, version, approval, review policy và publication status nên không thể quản trị/xuất bản | Tạo Wiki Glossary first-class với governance, entry metadata và traceability |
| **Gherkin kỹ thuật hoặc không traceable** | Scenario theo API/class/database không xác nhận behavior nghiệp vụ và dễ lệch UL | Dùng `Feature`/`Rule`/`Scenario` business-facing, approved UL và evidence/decision refs |
| **Owner được dùng thay authority** | Người duy trì artifact không mặc nhiên có quyền xác nhận từng definition/behavior claim | Tách `owner` khỏi `authority.required/confirmed_by` cho glossary và BDD |
| **BDD tham chiếu tên mutable** | Rename capability/rule làm mất traceability | Dùng `CAP-*`, `RULE-*`, `SEED-*`, `SCN-*` ổn định |
| **Seed có nhiều scenario nhưng một status/trace chung** | Không biết example nào đã được duyệt hoặc bị stale | Mỗi scenario có ID, coverage, status, authority, glossary/evidence refs và standalone Gherkin |
| **“Duyệt khi policy yêu cầu”** | Cho phép DRAFT/stale lọt qua final readiness khi policy mơ hồ | Final readiness chỉ pass với entry, seed và scenario `APPROVED` bởi đúng authority |
| **Downstream âm thầm đổi term/behavior** | Làm mất continuity của strategic decision | Trả contradiction về `domain-expert`; chỉ refine/expand có traceability |

## Self-check trước mỗi câu trả lời

- Tôi đang hỏi đúng một câu độc lập?
- Câu hỏi có ưu tiên blocker chiến lược lớn nhất?
- Claim mới có certainty label và đúng authority?
- Tôi có invent UL, Subdomain hoặc customer-choice evidence?
- Classification đang áp dụng cho Subdomain, không phải context/module?
- Tôi đã có as-is trước khi đưa target?
- Tôi có đang biến preference kiến trúc thành strategic evidence?
- Wiki Glossary đã đủ governance, entry metadata, approval/freshness và traceability?
- Glossary owner đã tách khỏi authority xác nhận từng definition?
- Capability/rule/seed/scenario đã dùng ID ổn định `CAP-*`/`RULE-*`/`SEED-*`/`SCN-*`?
- Gherkin scenarios có standalone `Feature`/`Rule`/`Scenario`, scenario-level traceability, status và đủ positive/negative/boundary coverage?
- Mọi glossary entry, seed và scenario được dùng đã `APPROVED` bởi đúng authority?

Nếu bất kỳ câu trả lời nào không đạt, dừng và sửa trước khi tiếp tục.

## Pressure scenario 1: Founder statement mơ hồ

**Bối cảnh:** Founder nói: “Xây nền tảng phỏng vấn tốt nhất, dùng AI, microservices và Kafka; cần domain map đầy đủ hôm nay.”

**Expected pass**

- Ghi founder statement là `Observation`; “AI/microservices/Kafka” là preference, không phải differentiation evidence.
- Không tạo danh sách Core Subdomain, context hoặc target map.
- Tạo `Blocker` về business outcome/customer choice.
- Hỏi đúng một câu: “Kết quả nào khiến khách hàng chọn hoặc trả tiền cho nền tảng phỏng vấn này thay vì lựa chọn khác?”

**Expected fail**

- Sinh ngay Core “AI Matching”, “Interview Management”, “Analytics”.
- Vẽ microservice/Kafka Context Map.
- Hỏi gộp customer, market, actor, pricing và workflow trong một lượt.

## Pressure scenario 2: Simple-but-Core và complex Generic

**Bối cảnh:** Matching hiện chỉ có vài rule nhưng win/loss interviews xác nhận khách hàng mua vì chất lượng matching. Payroll/auth có nhiều rule, traffic cao và yêu cầu uptime nghiêm ngặt; thị trường có SaaS trưởng thành.

**Expected pass**

- Matching có thể là Core dù tactical logic đơn giản, nếu evidence và authority hợp lệ.
- Payroll/auth có thể là Generic dù phức tạp/critical.
- Ghi operational/technical concerns cho downstream, không đổi classification vì chúng.

**Expected fail**

- Gán Core cho payroll/auth vì phức tạp hoặc critical.
- Gán Supporting cho matching vì ít bảng/ít code.

## Pressure scenario 3: Language collision

**Bối cảnh:** Sales gọi `Candidate` là cơ hội thương mại; Interview gọi `Candidate` là người đang trải qua assessment; Billing gọi `Candidate` là account chịu phí.

**Expected pass**

- Tạo ba glossary entries theo scope với definition/example/counterexample.
- Ghi collision là boundary signal.
- Hỏi một authority về một meaning đang chặn phân tích.
- Candidate contexts vẫn là `Proposal`.

**Expected fail**

- Chuẩn hóa cả ba thành `Person` hoặc `Party`.
- Tạo `Party Registry`/shared database trước evidence.
- Tuyên bố ba microservices từ ba nghĩa.

## Pressure scenario 4: SaaS làm thay đổi classification

**Bối cảnh:** Capability scheduling từng tạo khác biệt, nhưng vendor mới đạt parity và khách hàng không còn nêu scheduling trong lý do mua.

**Expected pass**

- Mở lại decision record theo organization/market/time horizon.
- Xem xét Core → Generic hoặc Supporting dựa trên customer-choice, switching constraints và counterexample.
- Ghi build/buy/partner là `Proposal`, review date và migration risk.

**Expected fail**

- Giữ Core vĩnh viễn vì đã từng là Core hoặc đã đầu tư nhiều.
- Chuyển Generic tự động chỉ vì có vendor mà không kiểm tra constraints.

## Pressure scenario 5: Xung đột ownership

**Bối cảnh:** Sales sở hữu pricing policy, Finance sở hữu revenue recognition, Engineering muốn một Billing Context và shared tables để “đơn giản”.

**Expected pass**

- Tách authority theo claim; không để Engineering quyết định business boundary một mình.
- Ghi pricing và revenue recognition là capability/subdomain candidates riêng nếu language/policy evidence hỗ trợ.
- Mô tả shared tables trong as-is nếu chúng tồn tại; target split/merge là `Proposal`.
- Hỏi đúng một câu về authority/policy có blast radius lớn nhất.

**Expected fail**

- Gộp capability vì cùng database.
- Gán cả Billing Context là Core/Supporting/Generic.
- Dùng team chart làm ranh giới cuối cùng.

## Pressure scenario 6: Deadline và yêu cầu “cứ giả định”

**Bối cảnh:** Sponsor yêu cầu hoàn tất Context Map trước cuộc họp, chưa có customer-choice evidence và nói “cứ giả định matching là Core”.

**Expected pass**

- Không dùng `AcceptedAssumption` thay evidence phân loại; ghi `classification: unresolved`.
- Lưu “Matching có thể là Core” trong alternatives/hypotheses, giữ `classification_evidence: block`.
- Chỉ dùng `AcceptedAssumption` cho claim khác nếu reversible, bounded, non-legal, non-destructive, có đúng authority, scope và review event.
- Không thiết kế target Context Map nếu subdomain map chưa đủ readiness.

**Expected fail**

- Đổi assumption thành fact vì deadline hoặc chức danh.
- Ghi `classification: core` với certainty `AcceptedAssumption`.
- Hoàn thiện final map để “có tài liệu trình bày”.

## Pressure scenario 7: Nhiều blocker cùng lúc

**Bối cảnh:** Còn thiếu customer-choice evidence, nghĩa của `Candidate` trong Billing và authority quyết định pricing.

**Expected pass**

- Dossier có thể liệt kê ba blocker trong `question_backlog` dưới dạng chủ đề, không viết thành ba câu.
- Chọn đúng một `active_next_question` theo blast radius.
- Response hiện tại chỉ hỏi một câu cho đúng authority.

**Expected fail**

- Kết thúc response bằng hai hoặc ba câu hỏi.
- Dùng bullet list câu hỏi rồi giải thích rằng đó chỉ là “backlog”.

## Pressure scenario 8: Glossary và BDD “đủ để bàn giao”

**Bối cảnh:** Dossier có bảng term/definition cũ, không owner/version/approval history. Sponsor yêu cầu bàn giao ngay ba scenario dạng “POST endpoint ghi Candidate vào database”; một scenario dùng `Candidate` nhưng Sales và Interview đang định nghĩa khác nhau.

**Expected pass**

- Đánh dấu `wiki_glossary: block` vì artifact thiếu governance, stale/chưa duyệt và không traceable.
- Đánh dấu `gherkin_bdd: block` vì scenario kỹ thuật, thiếu `Rule`, thiếu positive/negative/boundary coverage và dùng UL collision chưa resolve/marker.
- Tạo backlog cho các blocker governance, UL và behavior; chỉ hỏi một câu độc lập có blast radius lớn nhất.
- Không handoff kỹ thuật. Packet dự kiến cho `domain-discovery` phải mang scoped glossary snapshot và business-facing Gherkin seeds.
- Khi đủ evidence, tạo fenced `gherkin` có `Feature`, `Rule`, `Scenario`, trace tới capability/rule, glossary entries và evidence/decision.
- Chỉ pass final readiness khi glossary entries, seeds và từng scenario đều `APPROVED` bởi claim-specific authority; owner không thay thế authority.
- Dùng `CAP-*`, `RULE-*`, `SEED-*`, `SCN-*`; mỗi positive/negative/boundary scenario có standalone Gherkin và traceability riêng.

**Expected fail**

- Coi bảng UL cũ là publishable Wiki Glossary.
- Đổi `Candidate` thành từ chung hoặc tự chọn một nghĩa để scenario “chạy được”.
- Chấp nhận API/database steps như strategic BDD hoặc bàn giao mà thiếu một trong hai artifact.
- Gom các câu hỏi owner, approval, meaning và behavior trong một lượt.

## Tiêu chí skill test

Một lượt test chỉ pass khi agent:

1. Dùng certainty labels nhất quán.
2. Hỏi đúng một câu chiến lược độc lập.
3. Không invent customer/market evidence.
4. Giữ classification `unresolved` khi thiếu evidence.
5. Phân biệt Subdomain và Candidate Bounded Context.
6. Tách as-is khỏi target.
7. Defer tactical/architecture choices.
8. Tạo Wiki Glossary publishable với governance, đủ trường entry, freshness/approval và traceability.
9. Tách governance owner khỏi claim-specific authority và giữ `required/confirmed_by`.
10. Dùng stable IDs `CAP-*`, `RULE-*`, `SEED-*`, `SCN-*`; BDD không tham chiếu tên mutable.
11. Tạo Gherkin/BDD business-facing có standalone `Feature`/`Rule`/`Scenario`, scenario-level status/traceability và đủ positive/negative/boundary coverage.
12. Chặn readiness khi entry, seed hoặc scenario không `APPROVED` bởi đúng authority, hoặc artifact thiếu, stale, untraceable, conflicted/unresolved.
13. Tạo handoff bảo toàn governance/status/authority/review/traceability và đúng one-question semantics; không cho downstream âm thầm đổi term/behavior.

Chỉ cần vi phạm một hard gate là fail, dù phần còn lại của dossier trông đầy đủ.

# Bounded Context và Context Map

## Tách Problem Space khỏi Solution Space

| Khái niệm | Space | Bản chất |
|---|---|---|
| Business Domain | Problem Space | Lĩnh vực doanh nghiệp tạo giá trị |
| Subdomain | Problem Space | Business capability/use-case cluster được khám phá |
| Bounded Context | Solution Space chiến lược | Ranh giới được thiết kế, nơi một Domain Model và Ubiquitous Language có nghĩa nhất quán |
| Context Map | Solution Space chiến lược và hiện trạng tổ chức | Bản đồ quan hệ, quyền lực, contract và translation giữa contexts |

Subdomains are discovered; Bounded Contexts are designed. Không lấy cấu trúc code hiện tại làm bằng chứng duy nhất cho cả hai.

## Candidate Bounded Context là Proposal

Chỉ thiết kế candidate sau khi Business Domain, capability map, UL và classification evidence đủ sẵn sàng. Mỗi candidate ghi:

```yaml
candidate_context:
  name: "<tên theo local Ubiquitous Language>"
  certainty: "Proposal"
  model_purpose: "<quyết định nghiệp vụ mà model phục vụ>"
  included_subdomains:
    - "<subdomain>"
  language_scope:
    owned_terms:
      - "<term>"
    collisions:
      - term: "<homonym/synonym>"
        other_scope: "<scope>"
        meaning_difference: "<khác biệt>"
  authority_and_ownership:
    business_decision_owner: "<authority hoặc unresolved>"
    operational_owner: "<owner hoặc unresolved>"
  boundary_evidence:
    language:
      - "<evidence>"
    authority:
      - "<evidence>"
    ownership:
      - "<evidence>"
    change_coupling:
      - "<evidence>"
    pivotal_events:
      - "<evidence>"
  alternatives:
    - "<split/merge/keep-as-is alternative>"
  blockers:
    - "<evidence còn thiếu>"
```

### Heuristics thiết kế

- **Language:** thuật ngữ cần một nghĩa nhất quán cục bộ; collision bền vững gợi ý boundary.
- **Authority:** policy được quyết định bởi authority khác nhau thường cần bảo vệ model riêng.
- **Ownership:** accountability và change authority rõ giúp boundary vận hành được; sơ đồ team hiện tại chỉ là evidence, không phải định nghĩa.
- **Change coupling:** những phần đổi cùng một business reason có thể thuộc cùng context; coupling do shared database hoặc legacy không chứng minh chúng cùng model.
- **Pivotal events:** thay đổi ý nghĩa hoặc trách nhiệm quanh một event có thể chỉ ra chuyển context.
- **Model purpose:** hai scope cùng dữ liệu nhưng ra quyết định khác nhau có thể cần model riêng.

Không tạo `Party Registry`, canonical enterprise model hoặc shared database để xóa language collision trước khi có evidence.

## Mapping giữa Subdomain và Bounded Context

| Mapping | Khi có thể phù hợp | Rủi ro cần kiểm tra |
|---|---|---|
| 1 Subdomain : 1 Context | Capability và model boundary trùng tương đối | Đồng nhất hai khái niệm theo thói quen |
| 1 Subdomain : N Contexts | Subdomain lớn có nhiều model purpose, lifecycle hoặc authority | Chia theo kỹ thuật thay vì business reason |
| N Subdomains : 1 Context | Các subdomain nhỏ dùng chung model/language và đổi cùng nhau | Gán một classification cho toàn context; che khuất Core bên trong |

Classification luôn nằm trên từng Subdomain. Context chứa Core, Supporting và Generic vẫn không được gọi là “Core Context” như một classification record.

## Lập Context Map trung thực

Luôn có hai lớp:

1. **As-is:** coupling thật, ownership thật, shared database, manual handoff, batch file, duplicated data và power relationship hiện tại.
2. **Target:** Proposal về boundary, contract, translation và migration. Không trình bày target như hiện trạng hoặc `Decision`.

Mỗi relation cần:

```yaml
context_relationship:
  from: "<context>"
  to: "<context>"
  view: "as_is | target"
  certainty: "Observation | Inference | Proposal | AcceptedAssumption | Decision | Blocker"
  upstream: "<context hoặc mutual>"
  downstream: "<context hoặc mutual>"
  ownership:
    contract_owner: "<authority/team>"
    change_process: "<cách thương lượng thay đổi>"
  contract:
    purpose: "<business information/capability exchanged>"
    form: "<API/event/file/manual/shared model; as-is có thể chưa chuẩn>"
    published_language: "<tên/schema/version hoặc none>"
  pattern: "<pattern>"
  translation: "<ACL/conformist/shared representation/none>"
  current_coupling:
    - "<coupling evidence>"
  target_change:
    - "<Proposal hoặc none>"
  risks:
    - "<semantic/organizational/operational risk>"
```

## Patterns của Context Map

| Pattern | Quan hệ | Điều kiện và cảnh báo |
|---|---|---|
| **Partnership** | Hai bên phụ thuộc thành công lẫn nhau, phối hợp kế hoạch | Cần commitment và đồng bộ thường xuyên; không chỉ là “hai API gọi nhau” |
| **Shared Kernel** | Chia sẻ một phần model/code/schema có ownership chung | Giữ thật nhỏ; mọi thay đổi cần coordination. Không biến thành common dump |
| **Customer-Supplier** | Upstream cung cấp, downstream có tiếng nói như khách hàng | Cần feedback/planning mechanism thực tế |
| **Conformist** | Downstream chấp nhận model upstream | Chỉ phù hợp khi translation không đáng giá hoặc downstream ít quyền lực; ghi semantic cost |
| **Anti-Corruption Layer (ACL)** | Downstream dịch model upstream sang local model | Bảo vệ UL/model; không mặc định phải là service riêng |
| **Open Host Service / Published Language** | Upstream cung cấp protocol ổn định cho nhiều consumer | Published Language phải có ownership, compatibility và versioning |
| **Separate Ways** | Không tích hợp trực tiếp | Chấp nhận duplication/manual process khi integration cost vượt value |

`Published Language` là contract được công bố để trao đổi, không phải enterprise-wide Ubiquitous Language.

## Những điều Bounded Context không đồng nghĩa

Bounded Context không mặc định là:

- Microservice hoặc deployment unit.
- Database/schema riêng.
- Apiato Container.
- Team.
- Aggregate.
- Repository/package/folder.

Các mapping kỹ thuật chỉ được quyết định downstream sau strategic và workflow readiness. Một context có thể được triển khai trong modular monolith; một deployment có thể chứa nhiều context khi translation và ownership vẫn rõ; một context có thể cần nhiều deployable unit vì lý do vận hành. Mọi lựa chọn phải có evidence riêng.

## Readiness trước target map

Có thể ghi nhận **as-is observations** ngay khi có evidence trực tiếp, kể cả lúc classification còn `unresolved`. Không được thiết kế Candidate Bounded Context hoặc target Context Map khi classification/customer-market blocker có thể làm thay đổi capability boundary, investment boundary hoặc relationship.

Không chốt candidate/target nếu còn thiếu:

- Outcome và customer-value scope.
- Capability/Subdomain map đủ bao phủ.
- UL trọng yếu và collision.
- Classification record đủ evidence; mọi unresolved còn lại được chứng minh không ảnh hưởng boundary/relationship đang đề xuất.
- Authority/ownership thực tế.
- Upstream/downstream và contract as-is.

Khi thiếu, chỉ giữ as-is evidence, dừng candidate/target ở `Blocker` và hỏi đúng một câu chiến lược tiếp theo.

# Strategic Domain Dossier

Dùng packet này để review với người dùng. Không điền khoảng trống bằng giả định ẩn. Trường thiếu phải có `Blocker`, authority cần hỏi và next strategic question. Confidence chỉ dùng `low`, `medium`, `high`; không tạo phần trăm hoặc điểm số giả.

## YAML packet

```yaml
strategic_domain_dossier:
  metadata:
    title: "<tên dossier>"
    organization: "<organization scope>"
    market: "<market/segment scope>"
    time_horizon: "<strategic horizon>"
    prepared_at: "<YYYY-MM-DD>"
    review_date: "<YYYY-MM-DD hoặc review event>"
    repository_boundary: "Laravel 9 / Apiato 11.x"
    strategic_output_boundary: "framework-agnostic"

  certainty_log:
    - id: "C-001"
      label: "Observation | Inference | Proposal | AcceptedAssumption | Decision | Blocker"
      claim: "<một claim độc lập>"
      evidence: "<nguồn trực tiếp hoặc lý do suy luận>"
      authority:
        required: "<authority đúng claim>"
        confirmed_by: "<authority hoặc none>"
      scope: "<claim áp dụng ở đâu>"
      accepted_assumption:
        reversible: true
        bounded: true
        non_legal: true
        non_destructive: true
        accepted_by: "<authority hoặc none>"
        review_event: "<sự kiện review hoặc none>"

  business_domain:
    statement: "<ai nhận giá trị, outcome nào, trong problem space nào>"
    business_outcomes:
      - "<outcome>"
    in_scope:
      - "<phạm vi>"
    out_of_scope:
      - "<phạm vi>"
    evidence_refs:
      - "C-001"

  customer_value_and_competition:
    customer_segments:
      - "<segment>"
    value_hypotheses:
      - hypothesis: "<giá trị khách hàng>"
        certainty: "Inference | Proposal | AcceptedAssumption | Decision | Blocker"
        evidence_refs:
          - "<certainty id>"
    customer_choice_hypotheses:
      - hypothesis: "<lý do chọn/trả tiền>"
        certainty: "<label>"
        evidence_refs:
          - "<certainty id>"
    competitive_hypotheses:
      - hypothesis: "<khác biệt khó sao chép>"
        certainty: "<label>"
        evidence_refs:
          - "<certainty id>"

  wiki_glossary:
    governance:
      glossary_owner: "<owner chịu trách nhiệm xuất bản>"
      publication_status: "DRAFT | APPROVED | DEPRECATED | BLOCKED"
      version: "<semantic/document version>"
      effective_date: "<YYYY-MM-DD hoặc pending>"
      review_date: "<YYYY-MM-DD hoặc review event>"
      review_policy: "<cadence và điều kiện review>"
      change_triggers:
        - "<language collision/policy/market/organization/discovery trigger>"
      approval_change_history:
        - version: "<version>"
          date: "<YYYY-MM-DD>"
          change: "<thay đổi>"
          approved_by: "<authority hoặc pending>"
          decision_refs:
            - "<decision id>"
    entries:
      - term: "<term>"
        definition: "<definition>"
        example: "<example>"
        counterexample: "<counterexample>"
        scope: "<CAP-*/subdomain/candidate context>"
        entry_owner: "<owner duy trì entry>"
        authority:
          required: "<authority đúng claim definition>"
          confirmed_by: "<authority hoặc none>"
        synonyms:
          - "<synonym hoặc none>"
        homonyms_collision_notes:
          - term: "<same term hoặc none>"
            other_scope: "<scope>"
            other_meaning: "<meaning>"
            resolution_status: "resolved | unresolved | not_applicable"
        status: "DRAFT | APPROVED | DEPRECATED | BLOCKED"
        version: "<entry version>"
        effective_date: "<YYYY-MM-DD hoặc pending>"
        review_date: "<YYYY-MM-DD hoặc review event>"
        certainty: "<label>"
        evidence_decision_refs:
          - "<certainty/decision id>"

  gherkin_bdd:
    purpose: "Strategic behavior seeds; domain-discovery phải tinh chỉnh thành workflow acceptance"
    publication_status: "DRAFT | APPROVED | BLOCKED"
    version: "<version>"
    owner: "<owner duy trì/xuất bản BDD artifact>"
    authority:
      required: "<authority xác nhận behavior>"
      confirmed_by: "<authority hoặc none>"
    review_date: "<YYYY-MM-DD hoặc review event>"
    selected_capabilities_rules:
      - capability_ref: "CAP-001"
        rule_refs:
          - "RULE-001"
    seeds:
      - id: "SEED-001"
        capability_ref: "CAP-001"
        rule:
          id: "RULE-001"
          statement: "<strategic business rule>"
        status: "DRAFT | APPROVED | BLOCKED"
        scenarios:
          - id: "SCN-001"
            coverage: "positive"
            status: "DRAFT | APPROVED | BLOCKED"
            glossary_entry_refs:
              - "<term@version>"
            evidence_decision_refs:
              - "<certainty/decision id>"
            authority:
              required: "<authority đúng behavior claim>"
              confirmed_by: "<authority hoặc none>"
            gherkin: |
              Feature: <outcome/capability bằng UL APPROVED>
                Rule: RULE-001 — <strategic business rule>
                  Scenario: <business-facing example>
                    Given <business context>
                    When <business event/action>
                    Then <observable business outcome>
          - id: "SCN-002"
            coverage: "negative_counterexample"
            status: "DRAFT | APPROVED | BLOCKED"
            glossary_entry_refs:
              - "<term@version>"
            evidence_decision_refs:
              - "<certainty/decision id>"
            authority:
              required: "<authority đúng behavior claim>"
              confirmed_by: "<authority hoặc none>"
            gherkin: |
              Feature: <outcome/capability bằng UL APPROVED>
                Rule: RULE-001 — <strategic business rule>
                  Scenario: <negative/counterexample>
                    Given <business context không đủ điều kiện>
                    When <business event/action>
                    Then <business outcome bị từ chối hoặc thay đổi>
          - id: "SCN-003"
            coverage: "boundary"
            status: "DRAFT | APPROVED | BLOCKED"
            glossary_entry_refs:
              - "<term@version>"
            evidence_decision_refs:
              - "<certainty/decision id>"
            authority:
              required: "<authority đúng behavior claim>"
              confirmed_by: "<authority hoặc none>"
            gherkin: |
              Feature: <outcome/capability bằng UL APPROVED>
                Rule: RULE-001 — <strategic business rule>
                  Scenario: <boundary example>
                    Given <giá trị/trạng thái tại ranh giới>
                    When <business event/action>
                    Then <kết quả tại ranh giới>
    unresolved_language_markers:
      - "<term/collision marker hoặc none>"

  capabilities:
    - id: "CAP-001"
      name: "<capability theo UL>"
      outcome: "<value produced>"
      in_scope:
        - "<use-case cluster>"
      out_of_scope:
        - "<boundary>"
      actors_authority_hints:
        - "<hint cho domain-discovery>"
      pivotal_events:
        - "<event>"
      dependencies:
        provides_to:
          - "CAP-002"
        consumes_from:
          - "CAP-003"
      evidence_refs:
        - "<certainty id>"

  strategic_business_rules:
    - id: "RULE-001"
      capability_ref: "CAP-001"
      statement: "<business rule ổn định>"
      certainty: "<label>"
      authority:
        required: "<authority đúng rule claim>"
        confirmed_by: "<authority hoặc none>"
      evidence_decision_refs:
        - "<certainty/decision id>"

  subdomain_cards:
    - name: "<subdomain>"
      capability_refs:
        - "CAP-001"
      purpose: "<business purpose>"
      boundaries:
        in:
          - "<scope>"
        out:
          - "<scope>"
      classification: "core | supporting | generic | unresolved"
      classification_certainty: "<label>"
      confidence: "low | medium | high"
      organization: "<organization>"
      market: "<market>"
      time_horizon: "<horizon>"
      evidence:
        customer_choice:
          - "<evidence hoặc missing>"
        differentiation:
          - "<evidence hoặc missing>"
        custom_behavior:
          - "<evidence hoặc not_applicable>"
        market_alternatives:
          - "<evidence hoặc missing>"
      counterexamples:
        - "<evidence phản chứng>"
      alternative_classifications:
        - classification: "<alternative>"
          condition: "<khi nào alternative đúng>"
      blockers:
        - "<blocker hoặc none>"
      classification_rule: "Thiếu customer-choice/differentiation/market evidence thì classification bắt buộc unresolved; AcceptedAssumption chỉ được nằm trong alternatives"
      investment_implication:
        certainty: "Proposal"
        statement: "<investment direction>"
      build_buy_partner:
        certainty: "Proposal"
        statement: "<direction cần thẩm định>"
      reclassification_triggers:
        - "<observable trigger>"
      review_date: "<YYYY-MM-DD hoặc review event>"

  classification_summaries:
    core:
      - "<subdomain>"
    supporting:
      - "<subdomain>"
    generic:
      - "<subdomain>"
    unresolved:
      - subdomain: "<subdomain>"
        missing_evidence: "<evidence>"
        required_authority: "<authority>"

  candidate_bounded_contexts:
    - name: "<candidate context>"
      certainty: "Proposal"
      model_purpose: "<decision model supports>"
      included_subdomains:
        - "<subdomain>"
      local_language:
        - "<term>"
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
        - "<alternative boundary>"
      blockers:
        - "<blocker hoặc none>"

  context_map:
    as_is:
      contexts:
        - "<observed context/system boundary>"
      relationships:
        - from: "<context>"
          to: "<context>"
          upstream: "<context hoặc mutual>"
          downstream: "<context hoặc mutual>"
          ownership: "<contract/change owner>"
          contract: "<API/event/file/manual/shared model>"
          published_language: "<name/version hoặc none>"
          pattern: "<observed pattern hoặc unresolved>"
          translation: "<ACL/conformist/shared/none>"
          coupling:
            - "<honest current coupling>"
          certainty: "<label>"
    target:
      certainty: "Proposal"
      contexts:
        - "<candidate context>"
      relationships:
        - from: "<context>"
          to: "<context>"
          upstream: "<context hoặc mutual>"
          downstream: "<context hoặc mutual>"
          contract: "<proposed contract purpose>"
          published_language: "<proposed language hoặc none>"
          pattern: "Partnership | Shared Kernel | Customer-Supplier | Conformist | ACL | OHS/Published Language | Separate Ways"
          migration_from_as_is: "<incremental change>"
          authority_required: "<authority>"

  question_backlog:
    - id: "U-001"
      blocker: "<một blocker độc lập>"
      blast_radius: "<high | medium | low, kèm lý do định tính>"
      required_authority: "<authority>"
      question_topic: "<chủ đề cần làm rõ, không viết thành câu hỏi>"

  active_next_question:
    blocker_ref: "U-001"
    question: "<đúng một câu hỏi chiến lược duy nhất được hỏi trong lượt hiện tại>"
    why_it_blocks: "<lý do ngắn gọn>"

  evolution:
    triggers:
      - "<market/organization/vendor/discovery trigger>"
    review_cadence: "<cadence hoặc event-based review>"
    revisit_boundaries_when:
      - "<contradictory workflow evidence>"

  downstream_handoff:
    selected_subdomains:
      - "<subdomain/use case>"
    wiki_glossary_snapshot_refs:
      - "<scoped glossary version/ref>"
    gherkin_bdd_seed_refs:
      - "<SEED-* và SCN-* refs>"
    domain_discovery_packets:
      - "<packet ref>"
    technical_handoff_allowed: false
    technical_handoff_blockers:
      - "<strategic/workflow blocker>"

  strategic_readiness_gate:
    status: "READY | READY_WITH_ACCEPTED_ASSUMPTIONS | BLOCKED"
    checks:
      domain_outcome: "pass | block"
      wiki_glossary: "pass | block"
      gherkin_bdd: "pass | block"
      capability_map: "pass | block"
      classification_evidence: "pass | block"
      candidate_contexts: "pass | block"
      as_is_target_context_map: "pass | block"
      evolution_triggers: "pass | block"
      assumptions_and_blockers: "pass | block"
      downstream_packet: "pass | block"
    blockers:
      - "<blocker hoặc none>"
```

## Markdown review

````markdown
# Strategic Domain Dossier: <tên>

## 1. Certainty log
| ID | Label | Claim | Evidence | Authority | Scope |
|---|---|---|---|---|---|

## 2. Business Domain
- Statement:
- Outcomes:
- In scope:
- Out of scope:

## 3. Customer value và competitive hypotheses
| Hypothesis | Certainty | Evidence | Counterexample | Authority |
|---|---|---|---|---|

## 4. Wiki Glossary
- Glossary owner:
- Publication status:
- Document version:
- Effective date:
- Review date:
- Review policy:
- Change triggers:

### Approval/change history
| Version | Date | Change | Approved by | Decision refs |
|---|---|---|---|---|

### Entries
| Term | Definition | Example | Counterexample | Scope | Entry owner | Authority required | Confirmed by | Synonyms | Homonym term | Other scope | Other meaning | Resolution status | Status | Version | Effective date | Review date | Certainty | Evidence/Decision refs |
|---|---|---|---|---|---|---|---|---|---|---|---|---|---|---|---|---|---|---|

## 5. Strategic Gherkin/BDD seeds
- Purpose: strategic behavior seed, không phải workflow acceptance suite hoàn chỉnh.
- Publication status/version:
- Owner:
- Authority required/confirmed by:
- Review date:
- Selected `CAP-*`/`RULE-*` refs:
- Unresolved language markers:

### Seed `SEED-001`
- Capability ref: `CAP-001`
- Rule: `RULE-001` — <statement>
- Seed status:

```gherkin
Feature: <outcome/capability bằng UL đã duyệt>
  Rule: RULE-001 — <business rule>
    Scenario: [SCN-001] <positive example>
      Given <business context>
      When <business event/action>
      Then <observable business outcome>
```

```gherkin
Feature: <outcome/capability bằng UL đã duyệt>
  Rule: RULE-001 — <business rule>
    Scenario: [SCN-002] <negative/counterexample>
      Given <business context không đủ điều kiện>
      When <business event/action>
      Then <business outcome bị từ chối hoặc thay đổi>
```

```gherkin
Feature: <outcome/capability bằng UL đã duyệt>
  Rule: RULE-001 — <business rule>
    Scenario: [SCN-003] <boundary example>
      Given <giá trị/trạng thái tại ranh giới>
      When <business event/action>
      Then <kết quả tại ranh giới>
```

### Scenario traceability
| Seed ID | Scenario ID | Capability ref | Rule ID | Coverage | Status | Glossary refs | Evidence/Decision refs | Authority required | Confirmed by |
|---|---|---|---|---|---|---|---|---|---|

## 6. Capability map
| Capability ID | Name | Outcome | In/Out | Pivotal events | Provides/Consumes (`CAP-*`) | Evidence |
|---|---|---|---|---|---|---|

### Strategic business rules
| Rule ID | Capability ref | Statement | Certainty | Authority required | Confirmed by | Evidence/Decision refs |
|---|---|---|---|---|---|---|

## 7. Subdomain cards
### <Subdomain>
- Classification:
- Confidence:
- Evidence:
- Counterexample:
- Alternatives:
- Blockers:
- Investment implication: Proposal
- Build/buy/partner: Proposal
- Reclassification triggers:
- Review date:

## 8. Classification summaries
### Core
### Supporting
### Generic
### Unresolved

## 9. Candidate Bounded Contexts
| Candidate | Subdomains | Model purpose | Boundary evidence | Alternatives | Certainty |
|---|---|---|---|---|---|

## 10. Context Map as-is
| Upstream | Downstream | Ownership | Contract | Published Language | Pattern | Coupling | Certainty |
|---|---|---|---|---|---|---|---|

## 11. Target Context Map
| From | To | Proposed pattern | Contract | Migration | Required authority | Certainty |
|---|---|---|---|---|---|---|

## 12. Investment implications
Mọi mục giữ nhãn Proposal cho đến khi đúng authority quyết định.

## 13. Evolution và review
- Reclassification triggers:
- Boundary review triggers:
- Review date/cadence:

## 14. Unresolved
| Blocker | Blast radius | Required authority | Question topic |
|---|---|---|---|

### Active next question
- Blocker ref:
- Question: <đúng một câu hỏi duy nhất trong lượt hiện tại>
- Why it blocks:

## 15. Strategic Readiness Gate
- Status:
- Passed:
- Blocked:
- Downstream handoff:
````

## Quy tắc gate

- `READY`: mọi check đạt và không có `Blocker` hoặc accepted assumption.
- `READY_WITH_ACCEPTED_ASSUMPTIONS`: không có `Blocker`; mọi assumption reversible, bounded, non-legal, non-destructive, có authority và review event.
- `BLOCKED`: còn bất kỳ `Blocker`, classification giả định như final, target map chưa có as-is evidence, hoặc Wiki Glossary/Gherkin BDD không đạt gate.

`wiki_glossary` bắt buộc `block` nếu governance/metadata không đầy đủ hoặc bất kỳ entry được dùng không có status `APPROVED` bởi authority đúng claim. `gherkin_bdd` bắt buộc `block` nếu publication, seed hoặc bất kỳ scenario được chọn không có status `APPROVED` bởi authority đúng behavior claim. `DRAFT`, `BLOCKED`, stale, untraceable, conflicted/unresolved hoặc thiếu authority confirmation luôn chặn final readiness, không phụ thuộc policy tùy chọn.

Mọi capability dùng ID ổn định `CAP-*`; mọi strategic business rule dùng `RULE-*`. Seed `SEED-*` chỉ tham chiếu các ID này. Mỗi scenario `SCN-*` là đơn vị traceability độc lập, có coverage, status, glossary refs, evidence/decision refs, authority và standalone Gherkin block gồm đủ `Feature`, `Rule`, `Scenario`. Tập đại diện phải có đủ `positive`, `negative_counterexample` và `boundary`.

`AcceptedAssumption` không hợp lệ để điền Core/Supporting/Generic khi thiếu evidence phân loại. Trường hợp đó luôn dùng `classification: unresolved`, `classification_evidence: block` và status `BLOCKED`.

Một response có thể hiển thị toàn bộ `question_backlog` dưới dạng chủ đề, nhưng chỉ `active_next_question.question` được viết và hỏi như một câu. Không chuyển backlog thành danh sách nhiều câu hỏi.

Full output chỉ được coi là hoàn chỉnh khi có domain outcome, Wiki Glossary publishable với entry `APPROVED`, strategic Gherkin/BDD seed và scenario `APPROVED`, capability/rule IDs ổn định, classification evidence, candidate contexts, as-is/target map, evolution triggers, assumptions/blockers và downstream packet bảo toàn toàn bộ governance/status/traceability.

# Hợp đồng handoff

## Pipeline bắt buộc

```text
domain-expert
  -> domain-discovery
  -> apiato-container-workflow
  -> mysql-optimization / db-bandwidth-optimization theo trigger
```

Không bỏ qua `domain-discovery` để đi thẳng từ strategic map sang file, class, endpoint, schema hoặc Container. `domain-expert` có thể quay lại sau discovery nếu workflow evidence bác bỏ boundary/classification.

## Trách nhiệm từng skill

| Skill | Trách nhiệm | Không làm |
|---|---|---|
| `domain-expert` | Business Domain, capability, Wiki Glossary publishable, strategic Gherkin/BDD seeds, Subdomain classification, Candidate Bounded Context, as-is/target Context Map, strategic evolution | Actor/workflow dossier đầy đủ; API/schema/architecture design |
| `domain-discovery` | Actor, claim-specific authority, trigger, workflow, states, invariants, failures, compensation, concurrency, lifecycle, acceptance; refine/expand glossary và BDD seeds có traceability | Tự đổi UL, behavior, strategic classification/boundary mà không trả contradiction |
| `apiato-container-workflow` | Dependency-aware implementation plan cho Laravel 9 / Apiato 11.x sau readiness | Dùng Container/code hiện tại làm strategic evidence |
| `mysql-optimization` | Schema, index, query shape, pagination, transaction, lock, write performance | Phân loại Subdomain |
| `db-bandwidth-optimization` | List/report/relation/data transfer, N+1, payload/bandwidth và pre-deploy cost risk | Thiết kế Context Map |

## Packet từ domain-expert sang domain-discovery

Tạo một packet cho **mỗi Subdomain/use case được chọn**:

```yaml
strategic_to_discovery:
  packet_id: "<id>"
  selected_scope:
    subdomain: "<tên Subdomain>"
    use_case: "<use case hoặc capability slice>"
    classification: "core | supporting | generic | unresolved"
    classification_certainty: "<certainty label>"
    confidence: "low | medium | high"
  business_context:
    business_domain_statement: "<statement>"
    outcome: "<outcome>"
    customer_value_evidence:
      - "<evidence hoặc missing>"
    organization: "<scope>"
    market: "<scope>"
    time_horizon: "<scope>"
  wiki_glossary_snapshot:
    source_ref: "<dossier glossary ref>"
    document_version: "<version>"
    publication_status: "APPROVED | DRAFT | DEPRECATED | BLOCKED"
    glossary_owner: "<owner>"
    effective_date: "<YYYY-MM-DD hoặc pending>"
    review_date: "<YYYY-MM-DD hoặc review event>"
    review_policy: "<cadence và điều kiện review>"
    change_triggers:
      - "<language/policy/market/organization/discovery trigger>"
    approval_change_history:
      - version: "<version>"
        date: "<YYYY-MM-DD>"
        change: "<change>"
        approved_by: "<authority hoặc pending>"
        decision_refs:
          - "<decision ref>"
    scoped_at: "<subdomain/use case/capability slice>"
    entries:
      - term: "<term>"
        definition: "<definition>"
        example: "<example>"
        counterexample: "<counterexample>"
        scope: "<scope>"
        entry_owner: "<owner duy trì entry>"
        authority:
          required: "<authority đúng definition claim>"
          confirmed_by: "<authority hoặc none>"
        synonyms:
          - "<synonym hoặc none>"
        homonyms_collision_notes:
          - term: "<same term hoặc none>"
            other_scope: "<scope>"
            other_meaning: "<meaning>"
            resolution_status: "resolved | unresolved | not_applicable"
        status: "APPROVED | DRAFT | DEPRECATED | BLOCKED"
        version: "<entry version>"
        effective_date: "<YYYY-MM-DD hoặc pending>"
        review_date: "<YYYY-MM-DD hoặc review event>"
        certainty: "<certainty label>"
        evidence_decision_refs:
          - "<ref>"
  gherkin_bdd_seeds:
    source_ref: "<dossier BDD ref>"
    version: "<version>"
    publication_status: "APPROVED | DRAFT | BLOCKED"
    owner: "<owner duy trì/xuất bản BDD artifact>"
    authority:
      required: "<authority xác nhận behavior>"
      confirmed_by: "<authority hoặc none>"
    review_date: "<YYYY-MM-DD hoặc review event>"
    selected_capabilities_rules:
      - capability_ref: "CAP-001"
        rule_refs:
          - "RULE-001"
    unresolved_language_markers:
      - "<term/collision marker hoặc none>"
    seeds:
      - id: "SEED-001"
        capability_ref: "CAP-001"
        rule:
          id: "RULE-001"
          statement: "<strategic business rule>"
        status: "APPROVED | DRAFT | BLOCKED"
        scenarios:
          - id: "SCN-001"
            coverage: "positive"
            status: "APPROVED | DRAFT | BLOCKED"
            glossary_entry_refs:
              - "<term@version>"
            evidence_decision_refs:
              - "<ref>"
            authority:
              required: "<authority đúng behavior claim>"
              confirmed_by: "<authority hoặc none>"
            gherkin: |
              Feature: <business capability/outcome>
                Rule: RULE-001 — <business rule>
                  Scenario: [SCN-001] <positive business example>
                    Given <business context>
                    When <business event/action>
                    Then <observable business outcome>
          - id: "SCN-002"
            coverage: "negative_counterexample"
            status: "APPROVED | DRAFT | BLOCKED"
            glossary_entry_refs:
              - "<term@version>"
            evidence_decision_refs:
              - "<ref>"
            authority:
              required: "<authority đúng behavior claim>"
              confirmed_by: "<authority hoặc none>"
            gherkin: |
              Feature: <business capability/outcome>
                Rule: RULE-001 — <business rule>
                  Scenario: [SCN-002] <negative/counterexample>
                    Given <business context không đủ điều kiện>
                    When <business event/action>
                    Then <business outcome bị từ chối hoặc thay đổi>
          - id: "SCN-003"
            coverage: "boundary"
            status: "APPROVED | DRAFT | BLOCKED"
            glossary_entry_refs:
              - "<term@version>"
            evidence_decision_refs:
              - "<ref>"
            authority:
              required: "<authority đúng behavior claim>"
              confirmed_by: "<authority hoặc none>"
            gherkin: |
              Feature: <business capability/outcome>
                Rule: RULE-001 — <business rule>
                  Scenario: [SCN-003] <boundary example>
                    Given <giá trị/trạng thái tại ranh giới>
                    When <business event/action>
                    Then <kết quả tại ranh giới>
  capability:
    id: "CAP-001"
    purpose: "<business purpose>"
    in_scope:
      - "<scope>"
    out_of_scope:
      - "<scope>"
    pivotal_events:
      - "<event>"
    dependencies:
      - "<upstream/downstream capability>"
  actor_and_authority_hints:
    actors:
      - "<actor hint>"
    authority_claims:
      - claim: "<claim cần xác nhận>"
        expected_authority: "<authority>"
        certainty: "<label>"
  candidate_context:
    name: "<candidate>"
    certainty: "Proposal"
    model_purpose: "<purpose>"
    boundary_evidence:
      - "<language/authority/ownership/change/pivotal-event evidence>"
    alternatives:
      - "<alternative>"
  context_relationships:
    as_is:
      - "<relation/coupling>"
    target_proposals:
      - "<relation Proposal>"
  unresolved_workflow_topics:
    - id: "WT-001"
      priority: "<high | medium | low với lý do định tính>"
      topic: "<một policy/workflow topic, không viết thành câu hỏi>"
      required_authority: "<authority>"
      strategic_impact: "<boundary/classification/relationship impact>"
  active_next_workflow_question:
    topic_ref: "WT-001"
    question: "<đúng một câu hỏi workflow độc lập>"
    required_authority: "<authority>"
    why_it_blocks: "<lý do>"
  accepted_assumptions:
    - claim: "<assumption hoặc none>"
      accepted_by: "<authority>"
      scope: "<bounded scope>"
      review_event: "<event>"
  strategic_constraints:
    - "Không đổi UL/classification/boundary im lặng"
    - "Không đổi behavior của Gherkin seed im lặng"
    - "Có thể refine/expand glossary và BDD nhưng mọi rename/behavior contradiction phải trả về domain-expert"
    - "Trả contradiction nếu workflow evidence không khớp strategic map"
```

`wiki_glossary_snapshot` chỉ chứa scope được chọn nhưng phải giữ nguyên toàn bộ governance, effective/review date, change triggers, approval history, per-entry owner/authority/certainty/status, collision marker và traceability. Handoff BDD phải giữ owner, authority, review date, selected `CAP-*`/`RULE-*`, seed/scenario status, unresolved language markers và scenario-level traceability.

Gherkin seeds là strategic behavior seed, chưa phải acceptance suite đầy đủ; `domain-discovery` refine representative positive, negative/counterexample và boundary scenarios bằng workflow evidence nhưng không đổi behavior im lặng. `unresolved_workflow_topics[]` chỉ chứa topic, không chứa câu hỏi literal. Packet có đúng một `active_next_workflow_question`; **chỉ câu này được phép hỏi trong response hiện tại**. Các `actor_and_authority_hints` chỉ là đầu mối, không thay thế discovery.

## Kết quả domain-discovery trả về

```yaml
discovery_to_strategic:
  packet_id: "<id gốc>"
  workflow_readiness: "READY | READY_WITH_ACCEPTED_ASSUMPTIONS | BLOCKED"
  workflow_dossier_ref: "<artifact/path hoặc nội dung>"
  confirmed:
    actors_and_authority:
      - "<claim>"
    workflow_and_states:
      - "<finding>"
    invariants_and_failures:
      - "<finding>"
    lifecycle_and_acceptance:
      - "<finding>"
  strategic_contradictions:
    - finding: "<workflow evidence>"
      contradicts: "<glossary term/BDD behavior/subdomain/classification/context claim>"
      impact: "<rename/change behavior/reclassify/split/merge/change relationship/reopen unresolved>"
      evidence: "<source>"
      required_authority: "<authority>"
  technical_triggers:
    schema_query_write_concurrency: true
    list_report_relation_data_transfer: true
```

Nếu `strategic_contradictions` không rỗng:

1. Dừng handoff kỹ thuật cho scope bị ảnh hưởng.
2. `domain-expert` mở lại certainty log và decision record.
3. Review Wiki Glossary, Gherkin behavior, Subdomain boundary, classification hoặc Context Map.
4. Hỏi đúng một strategic blocker mỗi lượt.
5. Chỉ phát packet mới sau khi contradiction được giải quyết hoặc ghi accepted assumption hợp lệ.

## Gate sang Apiato

Chỉ handoff `apiato-container-workflow` khi:

- Strategic Readiness Gate là `READY` hoặc `READY_WITH_ACCEPTED_ASSUMPTIONS`.
- Wiki Glossary pass chỉ khi mọi entry được dùng là `APPROVED` bởi authority đúng definition claim.
- Gherkin/BDD pass chỉ khi publication, mọi seed và mọi scenario được chọn đều `APPROVED` bởi authority đúng behavior claim, có đủ representative positive/negative-counterexample/boundary coverage.
- Bất kỳ artifact `DRAFT`, `BLOCKED`, stale, untraceable, conflicted/unresolved hoặc thiếu authority confirmation đều chặn final readiness.
- Workflow Readiness Gate của `domain-discovery` là `READY` hoặc `READY_WITH_ACCEPTED_ASSUMPTIONS`.
- Không còn contradiction chiến lược chưa xử lý.
- Candidate context/target relation cần triển khai đã được đúng authority chấp nhận ở mức cần thiết.
- Scope kỹ thuật có traceability tới Subdomain, use case, UL, invariant và acceptance.

Handoff kỹ thuật:

```yaml
discovery_to_apiato:
  repository:
    framework: "Laravel 9"
    architecture: "Apiato 11.x / Porto"
  strategic_refs:
    dossier: "<ref>"
    subdomain: "<subdomain>"
    wiki_glossary_snapshot: "<version/ref>"
    gherkin_bdd_seeds: "<seed refs>"
    candidate_context: "<accepted boundary ref>"
    context_relationships: "<contract refs>"
  workflow_refs:
    dossier: "<ref>"
    use_case: "<use case>"
    actors_authority: "<refs>"
    states_invariants_failures: "<refs>"
    acceptance: "<refs>"
  implementation_constraints:
    - "<constraint>"
  optimization_triggers:
    mysql:
      required: true
      reasons:
        - "<schema/query/write/concurrency trigger>"
    db_bandwidth:
      required: true
      reasons:
        - "<list/report/relation/data transfer/pre-deploy trigger>"
```

## Version boundary và cấm thiết kế sớm

- Repository target là Laravel 9 / Apiato 11.x.
- Strategic dossier, UL, Subdomain và Context Map phải framework-agnostic.
- Trước double readiness, không đề xuất tên Migration, Model, Repository, Task, Action, Request, Controller, Route, Transformer, Apiato Container, table hoặc endpoint.
- Bounded Context không tự động trở thành Apiato Container; mapping đó là quyết định implementation downstream.
- Không chọn CQRS, Event Sourcing, Kafka, microservice, Outbox, Saga hoặc Data Mesh nếu workflow/consistency/scale evidence chưa kích hoạt đánh giá.

## Trigger optimization

Gọi `mysql-optimization` khi scope có migration/schema/index, relation, repository/query, search/filter/sort, pagination, transaction, locking/concurrency, bulk write hoặc delete semantics.

Gọi `db-bandwidth-optimization` khi scope có list/report, relation loading, aggregate response, export/import, payload lớn, cross-service data transfer, N+1 risk hoặc trước deploy.

Các skill tối ưu nhận technical packet sau readiness; không được dùng kết quả tối ưu để đổi strategic classification theo chiều ngược lại.

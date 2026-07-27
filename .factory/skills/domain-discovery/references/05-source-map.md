# Source Map

## Claim-type authority routing

Không có một evidence tier toàn cục cho mọi claim. Chọn authority theo loại claim:

| Claim type | Authority/source quyết định | Vai trò của repository và tài liệu phụ |
|---|---|---|
| Business policy | PO/domain authority trong authority scope đã xác nhận; policy chuyên biệt cần đúng authority | Code/tests/tickets chỉ là implementation Observation hoặc evidence, không tự trở thành domain Decision |
| Framework/package behavior | Versioned official Laravel 9/Apiato 11.x/package docs, installed source và lock file | Repository cho biết project đang dùng API/pattern nào, không định nghĩa behavior ngoài version thực tế |
| Current implementation behavior | Repository code, tests, migrations, config an toàn và runtime evidence | Đây là Observation về hệ thống hiện tại; có thể là legacy/bug, không phải business policy |
| Database behavior | Official docs đúng MySQL/version, schema thực tế và reproducible query/test/EXPLAIN | Migration/query code là implementation evidence; benchmark ngoài ngữ cảnh chỉ tạo giả thuyết |
| Legal/compliance | Legal/compliance authority và nguồn pháp lý áp dụng cho jurisdiction/context | Framework docs, code, sách và blog không thể chốt legal basis hoặc retention |
| Methodology/pattern | Standard gốc, sách và nguồn chuyên môn phù hợp | Dùng để đặt câu hỏi, modeling và Proposal; không quyết định policy hay framework API |

Security retention/control cần security authority. Product lifecycle cần PO/domain authority trong giới hạn legal đã xác nhận. Book-derived docs là secondary learning references.

## Source intake method

Với mỗi claim:

1. Ghi nguyên claim và loại: domain, framework, database, operational hay legal/compliance.
2. Route đến authority/source đúng loại claim.
3. Kiểm tra authority scope, version, edition, date, jurisdiction và applicability.
4. Tìm counterexample hoặc điều kiện làm claim sai.
5. Gắn evidence ID và nhãn certainty; chỉ đúng authority biến policy thành Decision.
6. Gắn expiry/review condition nếu context có thể đổi.

Không dùng một source phương pháp luận để khẳng định API framework; không dùng framework docs để phát minh policy nghiệp vụ.

## Local Laravel 9 sources

Root:

```text
E:\documents\laravel-docs
```

Mirror hiện chủ yếu có getting-started/architecture. Khi cần validation, authorization, database transactions, events, queues, cache locks hoặc scheduling, đối chiếu official Laravel 9 docs và package/code thực tế.

Official:

- Laravel 9 documentation: https://laravel.com/docs/9.x

Các chủ đề baseline:

- Validation và Form Request authorization.
- Database transactions.
- Events/listeners và queued listeners.
- Queues/jobs, retry, timeout và failed jobs.
- Cache atomic locks.
- Task scheduling.

## Local Apiato 11.x sources

Root:

```text
E:\documents\apiato\versioned_docs\version-11.x
```

Relevant files:

```text
getting-started/software-architectural-patterns.md
getting-started/conventions-and-principles.md
main-components/actions.md
main-components/subactions.mdx
main-components/tasks.md
optional-components/events.md
optional-components/jobs.md
optional-components/migrations.md
```

Official:

- Apiato 11.x documentation: https://apiato.io/docs/11.x

Kiểm tra lại exact local path/file extension trước khi trích dẫn. Apiato docs mô tả component responsibility; chúng không thay thế business readiness gate.

## Official method sources

- Domain Storytelling: https://domainstorytelling.org/
- EventStorming: https://www.eventstorming.com/
- OMG BPMN specification/resources: https://www.omg.org/bpmn/

Các nguồn này hỗ trợ discovery/modeling. Không bắt buộc dùng đầy đủ ký pháp nếu một timeline và state matrix ngắn giúp BA/PO review tốt hơn.

## Recommended reading backlog

### Business Discovery

- *User Story Mapping* — Jeff Patton.
- *Domain Storytelling* — Stefan Hofer, Henning Schwentner.
- *Writing Effective Use Cases* — Alistair Cockburn.

### Domain Modeling

- *Learning Domain-Driven Design* — Vlad Khononov.
- *Introducing EventStorming* — Alberto Brandolini.
- *Implementing Domain-Driven Design* — Vaughn Vernon.
- *Patterns of Enterprise Application Architecture (POEAA)* — Martin Fowler.

### Specification

- *Specification by Example* — Gojko Adzic.
- *Writing Effective Use Cases* — đối chiếu use-case boundary và alternatives.

### Integration / Production

- *Enterprise Integration Patterns* — Gregor Hohpe, Bobby Woolf.
- *Release It!* — Michael Nygard.
- *Designing Data-Intensive Applications (DDIA)* — Martin Kleppmann.

### Data Systems

- *Designing Data-Intensive Applications (DDIA)* — consistency, replication, streams và trade-offs.
- *Patterns of Enterprise Application Architecture (POEAA)* — data mapping và transaction patterns.
- MySQL và Laravel 9 versioned official docs cho behavior triển khai cụ thể.

Backlog là danh sách học, không phải authority để áp dụng mọi pattern.

## Checklist ingest future converted book docs

Mỗi tài liệu/chương được thêm phải ghi:

| Field | Câu hỏi |
|---|---|
| Title/edition/year | Đúng edition nào, xuất bản khi nào? |
| Converted source | File local nào, conversion có mất table/code/footnote không? |
| Chapter/page/section | Claim nằm chính xác ở đâu? |
| Claim | Tác giả khẳng định điều gì, trong context nào? |
| Applicability | Áp dụng cho discovery, Laravel 9, Apiato 11.x, MySQL hay operations? |
| Counterexample | Khi nào claim sai hoặc gây hại? |
| Concern boundary | Application concern hay operational/infrastructure concern? |
| Version caveat | API/pattern nào đã đổi theo framework/database/runtime? |
| Evidence label | Observation, Inference hay Proposal? |
| Expiry/review | Khi nào phải kiểm tra lại? |

## Source hygiene red flags

- Trích blog không version để khẳng định Laravel 9 API.
- Trích sách DDD để chốt legal retention.
- Coi code hiện tại là business Decision dù có thể là bug/legacy.
- Dùng một benchmark ngoài ngữ cảnh để đặt capacity threshold.
- Chỉ lưu quote mà không lưu edition/chapter/counterexample.
- Dùng local converted docs mà không đối chiếu formatting hoặc official source khi claim quan trọng.

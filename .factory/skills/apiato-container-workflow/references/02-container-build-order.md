# Container Build Order

Đây là workflow tổng. Khi trả user phải lọc theo capability đã kích hoạt.

## Phase 0: Audit và contract

1. Đọc `AGENTS.md`, standards, skill Apiato/Laravel Dương/QA.
2. Kiểm tra Git status, không đụng user-owned changes.
3. Xác định expected/actual, actors, ownership và parent chain.
4. Chốt endpoints, payload, response, Hash ID, permissions.
5. Chốt aggregate root, child/pivot/log/history/snapshot.
6. Chốt delete semantics: hard, soft, inactive, detach, transfer, anonymize.
7. Chốt query/list/filter/sort/include và indexes.
8. Chốt side effects, consistency và runtime.
9. Đóng scope A thành A'.

## Phase 1: Dependency Containers

1. Tạo các Container business dependency trước.
2. Mỗi dependency phải đủ field/invariant mà A' sử dụng.
3. Hoàn thiện Model/Migration/Factory/Repository tối thiểu hợp lệ.
4. Chạy migration và tests độc lập.
5. Không thêm cross-container relation tại phase này.

## Phase 2: Generator

### CRUD đơn giản

```bash
php artisan create-api
```

Ghi rõ:

```txt
type
section
container
RESTful hoặc endpoint riêng
base URI
```

### Container cần skeleton Apiato đầy đủ

```bash
php artisan apiato:generate:container
```

Ghi rõ:

```txt
section
container
UI: API/WEB/BOTH
API version
doctype: private/public
base URI
controller: SAC/MAC
CRUD Events: yes/no
Listeners: yes/no nếu Events=yes
Tests: yes/no
```

Không chọn CRUD Events chỉ để lấy Provider. Nếu feature chỉ cần một custom event, sinh custom Event/Listener/Provider riêng.

Sau generator:

1. Kiểm tra file dư/sai tên.
2. Không coi stub là implementation.
3. Không tạo README nếu user không yêu cầu.

## Phase 3: Config

1. Tạo config khi có default/limit/status/MIME/provider mapping.
2. Chọn key có domain rõ.
3. Model getter, Request và Task dùng cùng config source.
4. Không hardcode lại config trong nhiều lớp.

## Phase 4: Model source of truth

1. Ship Parent.
2. `$table`.
3. `getTableName()`.
4. `$resourceKey`.
5. `$fillable`, không dùng `$guarded = []`.
6. `$casts`.
7. `$hidden`.
8. `SoftDeletes` nếu đã chốt restore semantics.
9. Accessor/mutator nếu cần normalize persisted value.
10. Domain getter/helper có tên rõ, không query/side effect ẩn.
11. Static methods dùng `static::` khi cần late static binding.
12. Tạo custom Pivot Model nếu pivot có nghiệp vụ.

## Phase 5: Migration

1. Import Model source-of-truth.
2. `Schema::create(Model::getTableName())`.
3. Primary key.
4. Business columns.
5. Snapshot columns.
6. JSON columns và version nếu cần.
7. Relation columns.
8. Explicit foreign keys.
9. `cascadeOnDelete()`, `nullOnDelete()` hoặc restrict theo lifecycle.
10. Unique/composite constraints.
11. Index cho FK/filter/sort/ownership/soft-delete query.
12. `$table->softDeletes()` nếu dùng SoftDelete.
13. Parent table trước child table.
14. `down()` child trước parent.
15. Production schema đổi theo Expand-Migrate-Contract.
16. Kiểm tra local/dev:

```bash
php artisan migrate
php artisan migrate:rollback
php artisan migrate
```

Không rollback tùy tiện trên production.

## Phase 6: Relations và model domain logic

Chỉ chạy khi cả hai đầu dependency đã tồn tại.

1. `belongsTo`.
2. `hasOne`/`hasMany`.
3. `belongsToMany` + custom Pivot.
4. `morphTo`/`morphOne`/`morphMany`.
5. Explicit morph map nếu public owner alias.
6. `withPivot`, `withTimestamps`, casts.
7. `canView/canEdit/canDelete` nếu có domain permission.
8. `booted()` chỉ cho lifecycle áp dụng mọi write path.
9. `updateQuietly()`/`saveQuietly()` khi callback tự update.
10. Named global scope chỉ khi query shape thật sự là default và phải tắt được.

## Phase 7: Factory, Seeder và permissions

1. Factory tạo record hợp lệ.
2. Factory states cho status/owner/trashed/relations.
3. Seeder idempotent cho default data.
4. Permission seeder đồng bộ Request và route docs.
5. Test rerun seeder nếu feature dựa vào seeded invariants.

## Phase 8: Repository và query contract

1. Model binding.
2. `$fieldSearchable` allowlist.
3. Criteria quyền/backend không cho client override.
4. Secondary repository dùng pattern hiện có, không gọi Model thô.
5. List dùng pagination/limit cap.
6. Eager load, `withCount`, `withExists`.
7. Không query/filter collection trong Transformer.

## Phase 9: Tasks

1. Liệt kê atomic operations trước.
2. Một Task cho một job.
3. Không nhận Request.
4. Dùng Repository.
5. Chuẩn hóa exceptions.
6. Tạo Task riêng cho:
   - resolve/find/lock owner;
   - create/update/delete;
   - sync relation/pivot;
   - store/delete file;
   - write snapshot/log/history;
   - batch query/reorder.
7. Không để Task gọi Action.

## Phase 10: Action/SubAction và transaction

1. Action nhận Request.
2. `sanitizeInput()` whitelist và server-owned fields.
3. Dùng SubAction khi reusable orchestration.
4. Chọn một transaction owner theo repo pattern.
5. Lock tài nguyên nếu limit/stock/balance/primary/reorder.
6. Core writes cùng consistency boundary.
7. Compensate file đã upload nếu DB rollback.
8. Commit trước external side effect.
9. Dispatch Event/Job/Notification after commit.
10. Rollback trước throw và không nuốt core exception.

## Phase 11: Request

1. `$access`.
2. `$decode`.
3. `$urlParameters`.
4. `rules(): array`.
5. `authorize(): bool`.
6. Object rules `Rule::exists/unique`.
7. String/file/list/batch limits.
8. Nested JSON shape.
9. Update dùng `sometimes`.
10. Request chỉ là endpoint gate, không chứa data access/business orchestration.

## Phase 12: Transformer

1. Public contract.
2. `getHashedKey()`.
3. Không secret/raw IDs/FQCN/internal disk path.
4. Includes chỉ khi relation thật đã có.
5. Heavy includes opt-in.
6. Ưu tiên relation đã load.
7. Không query DB trong collection transform.
8. Snapshot response không join lại dữ liệu live.

## Phase 13: Controller và Route

1. Controller nhận Request.
2. Gọi một Action.
3. Transform/response.
4. Route HTTP verb/URI.
5. Auth middleware.
6. Permission docs.
7. `@apiParam`, `@apiBody`, `@apiQuery`.
8. Không tạo endpoint generic nhận FQCN hoặc internal class từ client.

## Phase 14: Lifecycle/side effects

Kích hoạt module tương ứng:

- soft delete/restore/purge;
- Event/Listener/Provider;
- Storage cleanup;
- Notification/Mail;
- Job/Queue/Worker;
- Scheduler;
- Realtime;
- cache/rate limit.

Xem capability matrix và queue reference.

## Phase 15: Tests

1. Failing tests trước behavior.
2. Unit Task/Action/Model domain.
3. Functional Route.
4. 422/401/403/404.
5. Hash ID.
6. Permission/IDOR/cross-owner.
7. Transaction rollback.
8. Concurrent invariant.
9. Event/Queue/Notification/Mail/Storage fake.
10. Missing model, idempotency, retry.
11. Migration/schema/factory.
12. Cross-container integration.

## Phase 16: Docs, validators và runtime

```bash
composer validate --strict
vendor/bin/php-cs-fixer fix --config=php_cs.dist.php --dry-run --diff
vendor/bin/psalm --config=psalm.dist.xml
vendor/bin/phpunit
php artisan apiato:apidoc
```

Thêm khi relevant:

```txt
migrate status/up/down validation
event:list/event cache
queue worker/restart/failed jobs
scheduler health
storage/CDN smoke
config/cache refresh
rollback/runbook
```

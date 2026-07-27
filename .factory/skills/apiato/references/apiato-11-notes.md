# Apiato 11.x Notes

## Sources used

- Official Apiato 11.x docs:
  - `https://apiato.io/docs/11.x/core-features/query-parameters/`
  - `https://apiato.io/docs/11.x/main-components/requests/`
  - `https://apiato.io/docs/11.x/optional-components/repositories/`
  - `https://apiato.io/docs/11.x/optional-components/events/`
  - `https://apiato.io/docs/11.x/core-features/code-generator/`
  - `https://apiato.io/docs/11.x/core-features/hash-id/`
- Laravel events docs for listener queue/after-commit behavior.
- Local repo examples:
  - `app/Containers/AppSection/User/Data/Repositories/UserRepository.php`
  - `app/Containers/AppSection/User/Tasks/GetAllUsersTask.php`
  - `app/Containers/AppSection/User/Actions/UpdateUserAction.php`
- Local Notion export:
  - `C:/Users/VTOS/Downloads/notion APIATO/notion_apiato.txt`

## Container vs Model

- Container is a business domain / bounded context.
- Model is a DB table representation.
- One Container can have many Models, one Model, or no Model.
- Use one Container when entities share lifecycle/context.
- Create a new Container when the feature is independent and removable.

Examples:

- `Order` Container can contain `Order`, `OrderItem`, `OrderHistory`.
- `Payment` Container can be independent and may have no local Model if it only integrates third-party APIs.

## Request details

Key properties:

```php
protected array $access = [
    'permission' => '',
    'roles' => '',
];

protected array $decode = [];

protected array $urlParameters = [];
```

Notes:

- `$access`: endpoint permission/role requirements.
- `$decode`: fields containing hashed IDs.
- `$urlParameters`: route params for validation/access as Request data.
- `authorize()` commonly uses `$this->check(['hasAccess'])`.
- `sanitizeInput([...])` filters input fields and preserves values like `false` better than naive `array_filter`.
- `$fillable` is still required as model-level mass assignment protection.

List request validation starter:

```php
public function rules(): array
{
    return [
        'limit' => 'nullable|integer|min:1|max:100',
        'page' => 'nullable|integer|min:1',
        'search' => 'nullable|string',
        'searchFields' => 'nullable|string',
        'orderBy' => 'nullable|string',
        'sortedBy' => 'nullable|in:asc,desc,ASC,DESC',
        'filter' => 'nullable|string',
        'include' => 'nullable|string',
    ];
}
```

Create/update request reminder:

- Always set `max`.
- Use `required` for create, `sometimes` for partial update.
- Decode IDs before `exists:*` validation.

## Repo override: Action details

Official Apiato 11.x allows Action orchestration, SubActions, and `transactionalRun()`. The rules below are the stricter convention of this repository.

- Action is an endpoint-specific input boundary, not a reusable orchestration or query layer.
- Action may receive Request object.
- Create/update Actions should usually call `$request->sanitizeInput([...])`.
- Action must call exactly one main Task.
- Do not call SubAction, another Action, a second Task, Repository/Model, or transaction APIs from Action.
- Do not share Actions across Containers. Share the owning Container's Task.
- Official Apiato supports Action `transactionalRun()`, but this repo explicitly does not use it.

Example:

```php
$sanitizedData = $request->sanitizeInput([
    'name',
    'email',
]);

return app(UpdateUserTask::class)->run($sanitizedData, $request->id);
```

## Repo override: Task details

Practical rules:

1. Represent a coherent reusable business capability.
2. Do not accept Request object.
3. Do not call Action.
4. Use Repository for data access.
5. Catch low-level failures and throw standard exceptions.
6. A main Task may call child Tasks, including cross-Container Tasks.

Transaction note:

- Transaction is part of the guarantee of a reusable Task capability.
- A Task with dependent writes owns its transaction; read-only Tasks do not need one.
- Nested Task transactions are allowed on the same connection when savepoints are supported.
- Inner commit is not durable while an outer transaction remains active.
- Exceptions that require rollback must propagate to the outermost transaction Task; never catch-and-swallow them.
- Put deadlock retry at the outermost transaction owner and run external side effects only through after-commit/outbox with retry and idempotency.

CRUD patterns:

- Create: repository `create($data)`, catch `CreateResourceFailedException`.
- Update: repository `update($data, $id)`, catch not found/update failure.
- Delete: repository `delete($id)`, catch not found/delete failure.
- Find: repository `find($id)`, catch `NotFoundException`.
- GetAll: `addRequestCriteria()->repository->paginate()`.

## Repository and frontend-friendly API

`$fieldSearchable` is important because it turns backend repositories into a safe, reusable search surface for the frontend.

Example:

```php
protected $fieldSearchable = [
    'name' => 'like',
    'id' => '=',
    'email' => '=',
    'email_verified_at' => '=',
    'created_at' => 'like',
];
```

Frontend can then call:

```txt
GET /v1/users?search=name:john&searchFields=name:like&orderBy=created_at&sortedBy=desc&limit=20
```

Benefits:

- Less custom backend endpoint code.
- Frontend can search/sort/filter safely.
- Backend controls allowed fields and operators.

Risks:

- Do not expose sensitive or expensive fields.
- Index fields used often.
- Validate/cap list query params.
- Avoid letting `limit=0` or huge limits reach production unless intended.

Production defaults:

- Treat `$fieldSearchable` as an API contract for the frontend.
- Keep it intentionally small and indexed.
- Prefer `like` only for fields where partial search is truly needed.
- Prefer `=` for IDs, enum/status, email, and exact-match fields.
- If frontend needs a new search/sort field, add validation and DB index together.

## Transformer details

- Transformer hides DB shape.
- Return hashed ID.
- Use `ifAdmin` only for safe admin-only metadata.
- Define includes in Transformer before frontend uses `?include=...`.
- Avoid query work in Transformer.

Example response core:

```php
return [
    'object' => $book->getResourceKey(),
    'id' => $book->getHashedKey(),
    'title' => $book->title,
];
```

## Events and listeners

Apiato events are Laravel events with Apiato structure:

- Event class extends `App\Ship\Parents\Events\Event`.
- Listener class extends `App\Ship\Parents\Listeners\Listener`.
- Events live in `{Container}/Events` or `Ship/Events` for global cross-container events.
- Listeners live in `{Container}/Listeners`.
- Container event providers must be registered in the container `MainServiceProvider`.
- Parent `EventServiceProvider` has `$listen = []` and `shouldDiscoverEvents()` returns `false` in this repo, so explicit registration is safest.

### When to use events

Use events for side effects that should be decoupled from the main use case:

- notifications
- email/SMS
- audit/activity log
- cache invalidation
- broadcast/realtime
- webhook/third-party integration
- search indexing

Avoid events/listeners for required core state changes that must be completed synchronously as part of the transaction. Those belong in the main Task and its child Tasks.

### Where to dispatch

Apiato docs say events can be fired from Actions or Tasks and recommend choosing one place. Practical production rule:

- Dispatch domain events from the Task that owns the state change.
- If a main Task owns a transaction or calls nested transactional Tasks, dispatch only after the outermost transaction succeeds.
- External listeners/jobs must use `$afterCommit = true`, queue connection `after_commit`, or transactional outbox with retry/idempotency.

Example Task-owned transaction:

```php
class PayOrderTask extends ParentTask
{
    public function run(array $data): Order
    {
        $order = DB::transaction(function () use ($data) {
            $order = app(CreateOrderTask::class)->run($data);
            app(DeductWalletTask::class)->run($order->user_id, $order->total);

            return $order;
        });

        OrderPaidEvent::dispatch($order->id);

        return $order;
    }
}
```

### Event shape

Event should be a data carrier:

```php
class OrderPaidEvent extends ParentEvent
{
    public function __construct(
        public readonly int $orderId,
    ) {
    }
}
```

Guidance:

- Use minimal immutable payload.
- Prefer IDs for queued side effects, then reload fresh state in listener.
- Avoid putting business logic in Event.
- Avoid passing secrets/tokens.

### Listener shape

Listener handles one side effect:

```php
class SendOrderPaidNotificationListener extends ParentListener implements ShouldQueue
{
    public bool $afterCommit = true;
    public int $tries = 3;
    public string $queue = 'listeners';

    public function handle(OrderPaidEvent $event): void
    {
        app(SendOrderPaidNotificationTask::class)->run($event->orderId);
    }
}
```

Guidance:

- Slow/external side effects should queue.
- Listener must be idempotent due to retries.
- Use `failed()` for alert/log cleanup if needed.
- Set queue name, tries, timeout/retry policy for production.
- Do not swallow exceptions silently unless explicitly non-critical and logged.

### Registration shape

```php
protected $listen = [
    OrderPaidEvent::class => [
        SendOrderPaidNotificationListener::class,
        WriteOrderAuditLogListener::class,
    ],
];
```

Register container event provider in `MainServiceProvider`:

```php
public array $serviceProviders = [
    EventServiceProvider::class,
];
```

### Testing

- Use `Event::fake()` to assert the domain event dispatch.
- Test listener side effects separately.
- Use queued listener tests when `ShouldQueue` is used.
- Verify after-commit behavior for transaction-dependent listeners.

## Common pitfalls

- Creating Container per table.
- Reusing one Request for all CRUD endpoints.
- Forgetting `$decode` for hashed IDs.
- Forgetting `$urlParameters` for route `{id}` validation.
- Writing DB queries in Controller/Action.
- Task accepting Request.
- Missing `$fieldSearchable`, causing frontend search to require custom backend code.
- Missing `max` validation on strings.
- Returning raw `id`.
- List endpoint without pagination.
- N+1 through includes.
- Dispatching external side effects before transaction commit.
- Using listeners for mandatory core writes.
- Queued listeners without idempotency.
- Forgetting to register the container EventServiceProvider.
- Event payload includes secrets or too much mutable model state.

## Production-grade checklist

### Wireframe / contract

- Simple UI/table/form sketch.
- Field list.
- Endpoint list.
- Request payload.
- Response shape.
- Permissions.

### Migration

- Correct data types.
- `nullable`, `unique`, default values.
- Foreign keys.
- Delete behavior.
- Indexes for foreign keys, filters, sorts, compound query patterns.
- Soft delete for important business records.

### Model

- `$fillable`.
- `$casts`.
- `$hidden`.
- Relationships.
- No broad `$guarded = []` unless explicitly justified.

### Request

- Per-endpoint Request.
- `rules()`.
- `authorize()`.
- `$access`.
- `$decode`.
- `$urlParameters`.
- String `max`.
- List query caps.

### Porto layers

- Controller thin.
- Action sanitizes/enriches input and calls exactly one main Task.
- Action does not call SubAction or own a transaction.
- Task is the reusable business capability and transaction owner for dependent writes.
- Main Task may call child Tasks; nested exceptions propagate to the outermost transaction.
- Repository data access.
- Transformer response.
- Event/Listener for side effects only.

### Query

- Pagination/limit.
- No `all()` for user-facing lists.
- No PHP filtering after fetching all.
- N+1 checked.
- Eager loading/includes checked.
- Index exists before production.

### Security / production

- Hash IDs in Transformer.
- No sensitive fields.
- No raw SQL errors.
- No secret logging.
- Rate limits for abuse-prone endpoints.
- Cache only with safe invalidation and permission-aware keys.
- Queued listeners use after-commit when reading transactional data.
- Event listeners are idempotent and registered.
- Validator/test results recorded.

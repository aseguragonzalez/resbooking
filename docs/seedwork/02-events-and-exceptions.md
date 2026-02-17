# Events and exceptions

Seedwork provides a base for **domain events** and two exception types for the domain layer: **DomainException** and **ValueException**. Events live in `Seedwork\Domain`; exceptions live in `Seedwork\Domain\Exceptions`.

---

## DomainEvent

**Source:** `Domain/DomainEvent.php`

### Purpose

Base class for domain events: immutable records of something that happened in the domain. Events are raised by aggregates (via `addEvent()`), collected via `getEvents()`, and published to a `DomainEventsBus`. Subscribers (implementing `DomainEventHandler`) react to events for side effects (e.g. sending email, updating projections). The payload is read-only; consumers must not mutate it.

### Public API

| Member | Signature / Type | Description |
|--------|-------------------|-------------|
| Constructor | `__construct(EntityId $id, string $type = "DomainEvent", string $version = "1.0", array $payload = [], \DateTimeImmutable $createdAt = ...)` | **Protected.** Subclasses call it from a static factory (e.g. `new()`). |
| `id` | `EntityId` (readonly) | Unique identifier for this event instance. |
| `type` | `string` (readonly) | Event type; use the short name (e.g. `'RestaurantCreated'`) or FQCN. Used by the bus to route to handlers (FQCN recommended for `subscribe()`). |
| `version` | `string` (readonly) | Optional version; default `"1.0"`. |
| `payload` | `array<string, mixed>` (readonly) | Event data. Consumers must not mutate this array. |
| `createdAt` | `\DateTimeImmutable` (readonly) | When the event was created (default: UTC now). |
| `equals` | `equals(DomainEvent $other): bool` | Returns whether this event has the same `id` as `$other`. |

### Conventions

- Declare event classes as **readonly**.
- Use a **protected constructor** and a **static factory** `static new(...): self` that builds the event with a new `EntityId` for the event itself, a clear `type`, and a `payload` containing whatever subscribers need (e.g. aggregate id, snapshot, or references).
- Use the event **class name** (FQCN) as `$eventType` when subscribing to the bus (e.g. `RestaurantCreated::class`), so the bus can match events to handlers.
- Do not modify `payload` after creation; treat it as immutable.

### Example

See PHPDoc on `Seedwork\Domain\DomainEvent` and RestaurantCreated in the domain layer.

---

## DomainException

**Source:** `Domain/Exceptions/DomainException.php`

### Purpose

Base class for domain-level errors. Use it when a business rule is violated or a domain operation cannot be performed (e.g. entity not found, duplicate resource, invalid state transition). Extends `\Exception`.

### Public API

| Member | Signature | Description |
|--------|-----------|-------------|
| Constructor | `__construct(string $message, int $code = 0)` | Same as `\Exception`. |

### Conventions

- Create a **concrete subclass per domain error** (e.g. `RestaurantDoesNotExist`, `DiningAreaAlreadyExist`).
- Use **clear, business-oriented messages** so that callers (e.g. application or infrastructure) can log or map them to user-facing messages.
- Application or infrastructure catches these and translates them (e.g. to HTTP 404 or a form error).

### Example

See PHPDoc on `Seedwork\Domain\Exceptions\DomainException` and concrete domain exceptions in the domain layer.

---

## ValueException

**Source:** `Domain/Exceptions/ValueException.php`

### Purpose

Concrete exception for value object or invariant violations. Extends `DomainException`. Use it when a value is invalid (e.g. empty name, min greater than max) or when a value object constructor detects a broken invariant.

### Public API

| Member | Signature | Description |
|--------|-----------|-------------|
| Constructor | `__construct(string $message, int $code = 0)` | Same as `DomainException`. |

### Conventions

- Throw **from value object constructors** (or entity/aggregate constructors) when validation fails.
- Message should describe the invariant (e.g. `"Name is required"`, `"Min number of diners must be less than or equal to max number of diners"`).
- Callers that create value objects (e.g. application handlers or other domain code) should catch and handle or rethrow as appropriate.

### Example

See PHPDoc on `Seedwork\Domain\Exceptions\ValueException`. Throw from value object constructors when validation fails.

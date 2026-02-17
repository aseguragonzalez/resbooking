# Domain building blocks

Seedwork provides four base types for the domain layer: **AggregateRoot**, **Entity**, **EntityId**, and **ValueObject**. All live in the `Seedwork\Domain` namespace.

---

## AggregateRoot

**Source:** `Domain/AggregateRoot.php`

### Purpose

Base class for aggregate roots. Each aggregate has a unique identity and an internal buffer of domain events. Events are added when something meaningful happens and are drained (and cleared) when `getEvents()` is called—typically by the repository or by infrastructure that publishes them to the domain events bus.

### Public API

| Member | Signature | Description |
|--------|-----------|-------------|
| Constructor | `__construct(EntityId $id, array $domainEvents = [])` | Pass the aggregate identity and optionally initial events. Subclasses call `parent::__construct($id)` and do not pass events from outside. |
| `equals` | `equals(AggregateRoot $other): bool` | Returns whether this aggregate has the same identity as `$other`. |
| `getId` | `getId(): EntityId` | Returns the aggregate identity. |
| `getEvents` | `getEvents(): array` | Returns a copy of all buffered domain events and clears the buffer. Return type is `array<DomainEvent>`. |
| `addEvent` | `addEvent(DomainEvent $domainEvent): void` | **Protected.** Appends an event to the buffer. Call from within the aggregate when a domain-relevant change occurs. |

### Conventions

- Use a **private constructor** and static named constructors: `new()` for creation from scratch, `build()` when reconstituting from persistence (if needed).
- Call `addEvent()` only from inside the aggregate when something meaningful happens (e.g. entity added, settings changed).
- Do not call `getEvents()` from domain or application logic; let infrastructure (e.g. repository save, or a dedicated step) call it and then publish each event via `DomainEventsBus::publish()`.
- Subclasses typically hold value objects and entities; the root is the only entry point for changes that affect consistency.

### Example

See PHPDoc on `Seedwork\Domain\AggregateRoot` and the Restaurant aggregate in the domain layer.

---

## Entity

**Source:** `Domain/Entity.php`

### Purpose

Base class for domain entities that have an identity. Entities are equal when their identities are equal. Subclasses are typically readonly and expose their own properties.

### Public API

| Member | Signature | Description |
|--------|-----------|-------------|
| Constructor | `__construct(EntityId $id)` | **Protected.** Subclasses call `parent::__construct($id)`. The `id` is exposed as a public readonly property. |
| `equals` | `equals(Entity $other): bool` | Returns whether this entity has the same identity as `$other`. |

### Conventions

- Declare the class as **readonly**.
- Use a **protected constructor** and static factories: `new()` for creation, `build()` when reconstituting with a known id.
- Identity is always an `EntityId`; use `EntityId::new()` for new entities and `EntityId::fromString($id)` when loading from storage or commands.
- Entities may contain value objects and enforce invariants in the constructor (throwing `ValueException` or domain exceptions as needed).

### Example

See PHPDoc on `Seedwork\Domain\Entity` and the DiningArea entity in the domain layer.

---

## EntityId

**Source:** `Domain/EntityId.php`

### Purpose

Value type representing the unique identity of an aggregate or entity. Immutable and comparable.

### Public API

| Member | Signature | Description |
|--------|-----------|-------------|
| Constructor | `__construct(string $value)` | **Private.** Use static factories instead. |
| `new` | `new(): self` | Creates a new identity with a unique value (via `uniqid()`). |
| `fromString` | `fromString(string $value): self` | Creates an identity from an existing string (e.g. from database or command). |
| `equals` | `equals(EntityId $other): bool` | Returns whether `$this->value === $other->value`. |
| `__toString` | `__toString(): string` | Returns the underlying string value. |
| Property | `string $value` | Public readonly; the raw identity string. |

### Conventions

- Use **`EntityId::new()`** when creating a new aggregate or entity.
- Use **`EntityId::fromString($id)`** when loading from persistence or when the id comes from a command/query (primitives-only boundary).
- Do not use domain types in command/query DTOs; pass the id as `string` and build `EntityId::fromString($command->id)` in the handler.

### Example

See PHPDoc on `Seedwork\Domain\EntityId`.

---

## ValueObject

**Source:** `Domain/ValueObject.php`

### Purpose

Base class for immutable value objects. Value objects are defined by their attributes; equality is based on value comparison, not identity. Subclasses must implement `equals()`.

### Public API

| Member | Signature | Description |
|--------|-----------|-------------|
| `equals` | `equals(ValueObject $other): bool` | **Abstract.** Subclasses implement value-based comparison. |

There is no constructor contract; subclasses define their own readonly properties and constructor.

### Conventions

- Declare the class as **readonly** and keep all properties readonly.
- **Immutability:** Do not allow modification after construction; no setters.
- Implement **`equals(ValueObject $other): bool`** by comparing all relevant properties. Typically return `false` if `$other` is not the same class, then compare each field.
- Use for concepts that have no identity: email, capacity, settings, money, etc. Use **Entity** when the object has a stable identity over time.
- Validation in the constructor is encouraged; throw `Seedwork\Domain\Exceptions\ValueException` when invariants are violated.

### Example

See PHPDoc on `Seedwork\Domain\ValueObject` and the Settings value object in the domain layer.

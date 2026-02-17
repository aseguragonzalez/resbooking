# Application layer components

Seedwork provides markers for use cases (**CommandHandler**, **QueryHandler**) and the domain events bus (**DomainEventsBus**, **DomainEventHandler**, **DeferredDomainEventsBus**). All live in the `Seedwork\Application` namespace.

---

## CommandHandler

**Source:** `Application/CommandHandler.php`

### Purpose

Marker interface for write-only application use cases (command handlers). It does not declare methods; the concrete use case interface extends it and declares `execute(Command): void`. The generic parameter documents the command type for static analysis and DI.

### Public API

The interface has **no methods**. It is a marker with a PHPDoc generic:

- **`@template TCommand of object`** — the command DTO type that the use case handles.

Use case interfaces extend it and add the execution method; see PHPDoc on `Seedwork\Application\CommandHandler`.

### Conventions

- **One use case interface per write operation** (e.g. `CreateNewRestaurant`, `UpdateDiningArea`). The interface extends `CommandHandler<YourCommand>` and declares `execute(YourCommand $command): void`.
- **Command DTOs use only primitive types** (and arrays of primitives or documented array shapes). No domain types in the command class; the handler builds domain types (e.g. `EntityId::fromString($command->id)`) when calling domain or repositories.
- **Handler class** implements the use case interface and is named `<UseCase>Handler` (e.g. `CreateNewRestaurantHandler`).
- Controllers receive a request, build the command, and call `$useCase->execute($command)`.

---

## QueryHandler

**Source:** `Application/QueryHandler.php`

### Purpose

Marker interface for read-only application use cases (query handlers). It does not declare methods; the concrete use case interface extends it and declares `execute(Query): TResult`. The generic parameters document the query and result types.

### Public API

The interface has **no methods**. It is a marker with PHPDoc generics:

- **`@template TQuery of object`** — the query DTO type.
- **`@template TResult`** — the type returned by `execute()` (e.g. an entity or a DTO).

Use case interfaces extend it and add the execution method; see PHPDoc on `Seedwork\Application\QueryHandler`.

### Conventions

- **One use case interface per read operation** (e.g. `GetRestaurantById`). The interface extends `QueryHandler<YourQuery, YourResult>` and declares `execute(YourQuery $query): YourResult`.
- **Query DTOs use only primitive types**. No domain types in the query class; the handler builds domain types when calling repositories or domain services.
- **Handler class** implements the use case interface and is named `<UseCase>Handler`. It loads data via repositories and returns a domain object or a read model.
- Controllers build the query from the request and call `$useCase->execute($query)` to get the result.

---

## DomainEventsBus

**Source:** `Application/DomainEventsBus.php`

### Purpose

Interface for publishing and subscribing to domain events. Events are stored when published and delivered only when `notify()` is called. This allows the same transaction/request to complete (e.g. persist aggregates) before side effects run. Multiple handlers per event type are supported.

### Public API

| Method | Signature | Description |
|--------|-----------|-------------|
| `publish` | `publish(DomainEvent $event): void` | Stores the event for later delivery. Does not invoke handlers until `notify()` is called. |
| `subscribe` | `subscribe(string $eventType, DomainEventHandler $domainEventHandler): void` | Registers a handler for a specific event type. `$eventType` should be the event class name (FQCN), e.g. `RestaurantCreated::class`. Multiple handlers per type are allowed. |
| `notify` | `notify(): void` | Delivers all stored events to their subscribed handlers, then clears the buffer. |

### Conventions

- **Publish** events after they are collected from aggregates (e.g. in the repository’s `save()` method or a dedicated step that calls `getEvents()` and then `publish()` for each event).
- **Subscribe** at application bootstrap: for each event type, register one or more `DomainEventHandler` implementations.
- **Notify** after the request has been handled (e.g. in middleware after `$next->handleRequest()` returns).
- Use the event’s **FQCN** for `$eventType` so the bus can match `$event::class` to subscribers.

### Example

See PHPDoc on `Seedwork\Application\DomainEventsBus` and `DeferredDomainEventsBus`.

---

## DomainEventHandler

**Source:** `Application/DomainEventHandler.php`

### Purpose

Interface for reacting to domain events. Implementations are subscribed to the bus for specific event types and are invoked when the bus delivers events in `notify()`.

### Public API

| Method | Signature | Description |
|--------|-----------|-------------|
| `execute` | `execute(DomainEvent $event): void` | Called by the bus for each event of a type this handler is subscribed to. The handler may cast `$event` to the concrete event class to read `payload`. |

### Conventions

- **One handler class per side effect or concern** (e.g. send welcome email on `RestaurantCreated`, update a projection). Each handler implements `DomainEventHandler` and is registered with `DomainEventsBus::subscribe($eventType, $handler)`.
- Inside `execute()`, cast the event to the concrete type if needed and read from `$event->payload`. Do not mutate the payload.
- Handlers may depend on infrastructure (e.g. mailer); they are typically registered in the composition root.

### Example

See PHPDoc on `Seedwork\Application\DomainEventHandler`.

---

## DeferredDomainEventsBus

**Source:** `Application/DeferredDomainEventsBus.php`

### Purpose

Default implementation of `DomainEventsBus`. Events are appended to an internal buffer on `publish()` and delivered to subscribed handlers when `notify()` is called. Matching is done by event class name (`$event::class`).

### Public API

Same as the interface: `publish()`, `subscribe()`, `notify()`. No additional public methods.

### Conventions

- Use this class as the concrete `DomainEventsBus` in the application (e.g. in the container or middleware). Inject it where events are published (e.g. repository) and where `notify()` is called (e.g. `DomainEventsMiddleware`).
- **Flow:** During a request, aggregates raise events via `addEvent()`; when they are saved, someone calls `getEvents()` and then `$bus->publish($event)` for each. After the response is produced, middleware calls `$bus->notify()`, which runs all subscribed handlers and clears the buffer.

### Example

See PHPDoc on `Seedwork\Application\DeferredDomainEventsBus` and DomainEventsMiddleware in the infrastructure layer.

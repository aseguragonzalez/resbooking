# Repository

**Source:** `Domain/Repository.php`

## Purpose

Generic contract for persisting and loading aggregate roots. Repositories belong to the domain layer as interfaces; infrastructure provides concrete implementations (e.g. SQL, in-memory). Application handlers depend on the interface, not on the implementation. Only aggregate roots are saved and loaded through this contract.

## Public API

| Member | Signature | Description |
|--------|-----------|-------------|
| `save` | `save($aggregateRoot): void` | Persists the given aggregate root. The parameter type is the generic `T` (an `AggregateRoot`). Implementation may call `getEvents()` on the aggregate and publish them to the domain events bus before or after persisting. |
| `getById` | `getById(EntityId $id)` | Returns the aggregate root with the given identity, or `null` if not found. Return type is `T|null`. |

The interface is generic:

- **`@template T of AggregateRoot`** — the type of aggregate this repository manages.
- Domain repository interfaces extend `Repository` and document the aggregate in PHPDoc: `@extends Repository<Restaurant>`.

## Conventions

- **Define the interface in the domain** (e.g. `Domain\Restaurants\Repositories\RestaurantRepository`). It extends `Seedwork\Domain\Repository` and specifies the aggregate: `@extends Repository<Restaurant>`.
- **Implement in infrastructure** (e.g. `SqlRestaurantRepository` in `Infrastructure\Adapters\Repositories\Restaurants`). The implementation performs persistence and may publish domain events from `$aggregateRoot->getEvents()`.
- **Application handlers** depend on the domain repository interface (injected via constructor). They call `save()` after changing an aggregate and `getById()` (or other domain-defined methods) to load one.
- **Extra methods:** The Seedwork interface only requires `save()` and `getById()`. Domain interfaces may add more methods (e.g. `findByUserEmail(string $email): array`) for specific lookups; implementations fulfill those in the same class.

## Example

See PHPDoc on `Seedwork\Domain\Repository` and RestaurantRepository in the domain layer.

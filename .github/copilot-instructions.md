# 🧩 Copilot Project Instructions

These are project-wide coding and architectural guidelines.
**Copilot must always follow these rules** when suggesting code, generating tests, or completing functions.

---

## 1. Core Principles

- **Language:** PHP 8.4
  Use strict typing, readonly and promoted properties, and enums where appropriate.
- **Style:** Must fully comply with **PSR-12** coding standards.
- **Architecture:** Follow **Domain-Driven Design (DDD)** within a **Hexagonal Architecture** (Ports and Adapters).
- **Seedwork Alignment:** Always reuse and extend existing abstractions from `Seedwork\Domain`.
  Never duplicate or redefine their functionality.
- **Quality:** Prefer immutability, explicitness, and domain alignment over brevity.
- **Testing:** All code must have full **PHPUnit** test coverage.

---

## 2. Layering and Directory Structure

Copilot must respect this organization:

```
- src/
    - Application/
        - <BoundedContext>/
            - <UseCases>/
                - <UseCase>.php
                - <UseCase>Command.php
                - <UseCase>Service.php
    - Domain/
        - <BoundedContext>/
            - Entities/
                - <AggregateRoot>.php
                - <Entity>.php
            - ValueObjects/
                - <ValueObject>.php
            - Events/
                - <DomainEvent>.php
            - Exceptions/
                - <DomainException>.php
            - Repositories/
                - <AggregateRoot>Repository.php
    - Infrastructure/
        - Adapters/
            - Repositories/
                - <BoundedContext>/
                    - Models/
                        - **Model.php
                    - Sql<AggregateRoot>Repository.php
                    - <AggregateRoot>Mapper.php
        - Ports/
            - <MvcApp>/
                - Controllers/
                    - <FeatureController>Controller.php
                - Models/
                    - <Feature>/
                        - Pages/
                            - <FeaturePage>.php
                        - Requests/
                            - <FeatureRequest>.php
                - Views/
                    - <Feature>/
                        - <FeaturePage>.html
- tests/
    - Unit/
        - Application/
            - <BoundedContext>/
                - <UseCases>/
                    - <UseCase>Test.php
        - Domain/
            - <BoundedContext>/
                - Entities/
                    - <AggregateRoot>Test.php
                - ValueObjects/
                    - <ValueObject>Test.php
                - Events/
                    - <DomainEvent>Test.php
                - Exceptions/
                    - <DomainException>Test.php
                - Repositories/
                    - <AggregateRoot>RepositoryTest.php
        - Infrastructure/
            - Adapters/
                - Repositories/
                    - <BoundedContext>/
                        - Sql<AggregateRoot>RepositoryTest.php
            - Ports/
                - <MvcApp>/
                    - Controllers/
                        - <FeatureController>ControllerTest.php
                    - Models/
                        - <Feature>/
                            - Pages/
                                - <FeaturePage>Test.php
                            - Requests/
                                - <FeatureRequest>Test.php

```

- **Domain:** Pure business logic.
- **Application:** Use cases, orchestration, and application services.
- **Infrastructure:** Technical details behind ports and adapters (persistence, API, etc.).

---

## 3. Seedwork Components (Mandatory Use)

| Type | Namespace | Description |
|------|------------|-------------|
| `AggregateRoot` | `Seedwork\Domain\AggregateRoot` | Base for aggregates. Manages ID + domain events. |
| `Entity` | `Seedwork\Domain\Entity` | Base for domain entities. |
| `ValueObject` | `Seedwork\Domain\ValueObject` | Base for immutable, equality-based objects. |
| `DomainEvent` | `Seedwork\Domain\DomainEvent` | Base for all domain events. Must define static factories (`new()`, `build()`). |
| `DomainException` | `Seedwork\Domain\Exceptions\DomainException` | Base for domain exceptions. |
| `Repository` | `Seedwork\Domain\Repository` | Base interface for aggregate persistence. |
| `Controller` | `Seedwork\Infrastructure\Ports\Mvc\Controllers\Controller` | Base for MVC controllers. |
| `Command` | `Seedwork\Application\Command` | Base for application commands. |
| `ApplicationService` | `Seedwork\Application\ApplicationService` | Base for application services handling commands of type `T`. |

Always extend or implement these abstractions.
Example patterns:

```php
final class MyAggregate extends AggregateRoot { /* ... */ }
final class MyEntity extends Entity { /* ... */ }
final class MyValueObject extends ValueObject { /* ... */ }
final class MyEvent extends DomainEvent { /* ... */ }
final class MyException extends DomainException { /* ... */ }

/**
 * @extends Repository<MyAggregateRoot>
 */
interface MyAggregateRootRepository extends Repository { /* ... */ }


interface CreateNewProject { /* ... */ }
/**
 * @extends ApplicationService<CreateNewProjectCommand>
 */
final class CreateNewProjectService extends ApplicationService implements CreateNewProject { /* ... */ }
final class CreateNewProjectCommand extends Command { /* ... */ }

```

---

## 4. Domain Modeling Conventions

* Use **private constructors** + static named constructors (`new()`, `build()`).
* **Readonly properties** for value objects.
* Manage domain events with `$this->addEvent(...)`.
* Equality is defined via `equals()` or value comparison.
* Keep the **domain pure** — no infrastructure or framework code here.

---

## 5. Examples

### 5.1 AggregateRoot Implementation Style

```php
<?php

declare(strict_types=1);

namespace Domain\Projects\Entities;

use Domain\Projects\Events\ProjectCreated;
use Domain\Projects\ValueObjects\Settings;
use Domain\Shared\Email;
use Seedwork\Domain\AggregateRoot;
use Tuupola\Ksuid;

final class Project extends AggregateRoot
{
    private function __construct(
        string $id,
        private Settings $settings,
    ) {
        parent::__construct($id);
    }

    public static function new(string $email, ?string $id = null): self
    {
        $project = new self(
            $id ?? (string)new Ksuid(),
            new Settings(email: new Email($email), /* ... */)
        );

        $project->addEvent(ProjectCreated::new($project->getId(), $project));
        return $project;
    }
}
```

### 5.2 Entity Implementation Style

```php
<?php

declare(strict_types=1);

namespace Domain\Projects\Entities;

use Domain\Shared\Capacity;
use Seedwork\Domain\Entity;
use Seedwork\Domain\Exceptions\ValueException;
use Tuupola\Ksuid;

final class Place extends Entity
{
    private function __construct(
        string $id,
        public readonly Capacity $capacity,
        public readonly string $name,
    ) {
        parent::__construct(id: $id);

        if (empty($name)) {
            throw new ValueException('Name is required');
        }
    }

    public static function new(Capacity $capacity, string $name, ?string $id = null): self
    {
        return new self(
            id: $id ?? (string) new Ksuid(),
            capacity: $capacity,
            name: $name
        );
    }

    public static function build(string $id, Capacity $capacity, string $name): self
    {
        return new self($id, $capacity, $name);
    }
}
```

### 5.3 Value object Implementation Style

```php
<?php

declare(strict_types=1);

namespace Domain\Projects\ValueObjects;

use Seedwork\Domain\ValueObject;
use Seedwork\Domain\Exceptions\ValueException;
use Domain\Shared\Capacity;
use Domain\Shared\Email;
use Domain\Shared\Phone;

final class Settings extends ValueObject
{
    public function __construct(
        public readonly Email $email,
        public readonly bool $hasReminders,
        public readonly string $name,
        public readonly Capacity $maxNumberOfDiners,
        public readonly Capacity $minNumberOfDiners,
        public readonly Capacity $numberOfTables,
        public readonly Phone $phone,
    ) {
        $this->checkName();
        $this->checkMinMaxNumberOfDinners();
    }

    private function checkName(): void
    {
        if (empty($this->name)) {
            throw new ValueException('Name is required');
        }
    }

    private function checkMinMaxNumberOfDinners(): void
    {
        if ($this->minNumberOfDiners->value > $this->maxNumberOfDiners->value) {
            throw new ValueException('Min number of diners must be less than or equal to max number of diners');
        }
    }
}

```

### 5.4 Use case Implementation Style

```php
<?php

declare(strict_types=1);

namespace Application\Projects\CreateNewProject;

interface CreateNewProject
{
    public function execute(CreateNewProjectCommand $command): void;
}
```

### 5.5 Use case command Implementation Style

```php
<?php

declare(strict_types=1);

namespace Application\Projects\CreateNewProject;

use Seedwork\Application\Command;

final class CreateNewProjectCommand extends Command
{
    public function __construct(public readonly string $email)
    {
    }
}
```

### 5.6 Use case application service Implementation Style

```php
<?php

declare(strict_types=1);

namespace Application\Projects\CreateNewProject;

use Domain\Projects\ProjectRepository;
use Domain\Projects\Entities\Project;
use Seedwork\Application\ApplicationService;

/**
 * @extends ApplicationService<CreateNewProjectCommand>
 */
final class CreateNewProjectService extends ApplicationService implements CreateNewProject
{
    public function __construct(private readonly ProjectRepository $projectRepository)
    {
    }

    /**
     * @param CreateNewProjectCommand $command
     */
    public function execute($command): void
    {
        $project = Project::new(email: $command->email);

        $this->projectRepository->save($project);
    }
}
```

### 5.7 Feature page model Implementation Style

```php
<?php

declare(strict_types=1);

namespace Infrastructure\Ports\Dashboard\Models\Accounts\Pages;

use Infrastructure\Ports\Dashboard\Models\FormModel;

final class SignIn extends FormModel
{
    /**
     * @param array<string, string> $errors
     */
    protected function __construct(array $errors = [])
    {
        parent::__construct(pageTitle: '{{accounts.signin.title}}', errors: $errors);
    }

    public static function new(): self
    {
        return new self();
    }

    /**
     * @param array<string, string> $errors
     */
    public static function withErrors(array $errors): self
    {
        return new self(errors: $errors);
    }
}
```

### 5.8 Feature request model Implementation Style

```php
<?php

declare(strict_types=1);

namespace Infrastructure\Ports\Dashboard\Models\Accounts\Requests;

final class SignInRequest
{
    public function __construct(
        public readonly string $username,
        public readonly string $password,
        public readonly string $rememberMe = 'off',
    ) {
    }

    public function keepMeSignedIn(): bool
    {
        return $this->rememberMe === 'on';
    }

    /**
     * @return array<string, string>
     */
    public function validate(): array
    {
        $errors = [];

        if (empty($this->username)) {
            $errors['username'] = '{{accounts.signin.form.username.error.required}}';
        } elseif (!filter_var($this->username, FILTER_VALIDATE_EMAIL)) {
            $errors['username'] = '{{accounts.signin.form.username.error.invalid_email}}';
        }

        if (empty($this->password)) {
            $errors['password'] = '{{accounts.signin.form.password.error.required}}';
        }

        return $errors;
    }
}
```

---

## 6. Event Conventions

* Extend `DomainEvent`.
* Always provide:
  * `public static function new(...): self`
  * `public static function build(...): self`
* Use `Ksuid` for unique IDs.
* Event names follow the pattern `<Entity><Action>` (e.g., `ProjectCreated`, `UserRemoved`).
* Payload must include relevant identifiers and entities.

Example:

```php
final class ProjectCreated extends DomainEvent
{
    public static function new(string $projectId, Project $project): self
    {
        return new self(
            id: (string)new Ksuid(),
            type: 'ProjectCreated',
            payload: ['projectId' => $projectId, 'project' => $project]
        );
    }
}
```

---

## 7. Exception Conventions

* Extend `DomainException`.
* Provide clear, business-relevant messages.

Example:

```php
final class ProjectDoesNotExist extends DomainException
{
    public function __construct()
    {
        parent::__construct('Project does not exist');
    }
}
```

---

## 8. Repository Conventions

All repositories must extend the generic `Seedwork\Domain\Repository` interface.

```php
/**
 * @extends Repository<Project>
 */
interface ProjectRepository extends Repository
{
}
```

* Application handlers must depend on interfaces, **never concrete infrastructure classes**.

---

## 9. Testing Guidelines

* Use **PHPUnit**.
* Mirror source structure under `/tests`.
* Each class has a corresponding `*Test.php`.
* Use **Arrange - Act - Assert (AAA)** structure.
* Prefer **mocks/test doubles** over real infrastructure.
* Validate **domain invariants** and **emitted events**.

Example:

```php
final class ProjectTest extends TestCase
{
    public function testItCreatesANewProject(): void
    {
        // Given
        $email = 'test@example.com';

        // When
        $project = Project::new($email);

        // Then
        $this->assertNotEmpty($project->getId());
        $this->assertCount(1, $project->getEvents());
    }
}
```

---

## 10. General Style Rules

* Always declare `strict_types=1`.
* Use **constructor injection**.
* Prefer **final** classes unless extension is required.
* Explicit parameter and return types are mandatory.
* Use `===` and `!==` for comparisons.
* Avoid static utility classes unless defined in Seedwork.
* No side effects in constructors.

---

## 11. Copilot Behavior Expectations

When generating or completing code, Copilot must:

* Use the existing Seedwork components automatically.
* Follow DDD naming conventions (e.g., `UserCreated`, `Settings`, `ProjectRepository`).
* Respect domain boundaries.
* Suggest PSR-12 compliant code.
* Include meaningful **unit tests** by default.

> **Goal:** Generate *production-quality DDD code* aligned with our conventions and architecture.

---

## 12. Common Mistakes to Avoid

❌ Creating aggregates without extending `AggregateRoot`
❌ Missing static constructors (`new()`, `build()`)
❌ Using untyped or mixed parameters
❌ Writing code without tests
❌ Mixing domain and infrastructure logic
❌ Violating PSR-12 formatting rules

---

**All generated code must align with these principles.**
Copilot’s primary objective is to assist in writing maintainable, testable, domain-aligned PHP code that integrates perfectly with the project’s Seedwork.

## 13. Infrastructure Layer — Controller Conventions

Controllers in `Infrastructure\Ports\<PortName>\Controllers` act as **adapters** between the external interface (HTTP, CLI, etc.) and the Application layer.

### ✅ Responsibilities
- Receive and validate external requests (via `Request` models).
- Invoke **Application commands or services** (use cases).
- Handle **domain and application exceptions** gracefully.
- Map results to **Views** (`view()`) or **Redirects** (`redirectToAction()`).
- Manage **headers and cookies** through `ActionResponse`.
- Register **routes** statically via `getRoutes()` using `Route` and `Path`.

### ✅ Typical Structure

```php
final class SomeController extends Controller
{
    public function __construct(
        private readonly SomeApplicationService $service,
        private readonly IdentityManager $identityManager,
        private readonly Settings $settings
    ) {}

    public function someAction(SomeRequest $request): ActionResponse
    {
        $errors = $request->validate();
        if (!empty($errors)) {
            return $this->view("formView", model: SomeView::withErrors($errors));
        }

        try {
            $this->service->execute(new SomeCommand(/* ... */));
            return $this->redirectToAction("success");
        } catch (SomeDomainException) {
            return $this->view("formView", model: SomeView::withErrors([...]));
        }
    }

    /** @return array<Route> */
    public static function getRoutes(): array
    {
        return [
            Route::create(RouteMethod::Get, Path::create('/some/path'), self::class, 'someAction'),
            // ...
        ];
    }
}
```

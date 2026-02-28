---
title: MVC Architecture Copilot Instructions
description: These are project-wide coding conventions for the MVC architecture.
applyTo: [src/Infrastructure/Ports/*]
---

# 🧩 MVC Architecture Copilot Instructions

These are project-wide coding conventions for the MVC architecture.

---

## Code Samples for MVC Architecture

### Feature page model Implementation Style

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

### Feature Request Model Implementation Style

```php
<?php

declare(strict_types=1);

namespace Infrastructure\Ports\Dashboard\Models\Accounts\Requests;

final readonly class SignInRequest
{
    public function __construct(
        public string $username,
        public string $password,
        public string $rememberMe = 'off',
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

## Controller Conventions

Controllers in `Infrastructure\Ports\<PortName>\Controllers` act as **adapters**
    between the external interface (HTTP, CLI, etc.) and the Application layer.

### ✅ Responsibilities

- Receive and validate external requests (via `Request` models).
- Invoke **Application use cases** (command handlers or query handlers).
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
            Route::create(
                RouteMethod::Get,
                Path::create('/some/path'),
                self::class, 'someAction'
            ),
            // ...
        ];
    }
}
```

## HTML Template Conventions

All HTML templates in `Infrastructure\Ports\<PortName>\Views` must follow
consistent patterns for forms, tables, and layout structure. Templates use a
custom templating syntax with conditional blocks, loops, and variable interpolation.

### Template Layout Pattern

Every view template must start with `{{#layout layout:}}` to inherit the base
layout structure. The layout provides the HTML document structure, header
navigation, main content area, and footer.

**View Template Pattern:**

```html
{{#layout layout:}}
<!-- Your view content here -->
```

### Form Template Pattern

Forms must follow a consistent structure with proper accessibility attributes,
error handling, and semantic HTML.

**Basic Form Structure:**

```html
{{#layout layout:}}
<form action="/accounts/sign-in" method="post" class="form">
    <header>
        <h2>{{accounts.signin.form.title}}</h2>
    </header>

    {{#if errorSummary:}}
    <section role="alert" aria-live="assertive" class="error-summary" tabindex="-1">
        <h3>{{accounts.signin.form.error.summary}}</h3>
        <ul>
            {{#for entry in errorSummary:}}
            <li>
                <a href="#{{entry->field}}">{{entry->message}}</a>
            </li>
            {{#endfor errorSummary:}}
        </ul>
    </section>
    {{#endif errorSummary:}}

    <div class="form-control">
        <label for="username">
            {{accounts.signin.form.username.label}}
        </label>
        <input
            aria-describedby="username-help"
            autocomplete="off"
            id="username"
            name="username"
            placeholder="{{accounts.signin.form.username.placeholder}}"
            required
            type="email"
            {{#if errors->username:}}aria-invalid="true"{{#endif errors->username:}}
        />
        <p>
            <small id="username-help">{{accounts.signin.form.username.help}}</small>
        </p>
    </div>

    <div class="form-control">
        <button type="submit" class="primary-button">
            {{accounts.signin.form.submit}}
        </button>
    </div>

    <footer>
        <p>
            {{accounts.signin.form.forgot-password}}
            <a href="/accounts/reset-password">{{accounts.signin.form.reset}}</a>
        </p>
    </footer>
</form>

```

**Form with Field-Level Validation:**

```html
{{#layout layout:}}
<form action="/reservations/{{reservation->id}}" method="post" class="form">
    <header>
        <h2>{{reservation.edit.form.title}} | {{reservation->name}}</h2>
    </header>

    <input type="hidden" name="id" value="{{reservation->id}}" />
    <input type="hidden" name="backUrl" value="{{backUrl}}" />

    <div class="form-control">
        <label for="name">
            {{reservation.form.name.label}}
        </label>
        <input
            aria-describedby="name-help {{#if errors->name:}}name-error{{#endif errors->name:}}"
            autocomplete="off"
            id="name"
            name="name"
            placeholder="{{reservation.form.name.placeholder}}"
            required
            type="text"
            value="{{reservation->name}}"
            {{#if errors->name:}}aria-invalid="true"{{#endif errors->name:}}
        />
        <p>
            <small id="name-help">{{reservation.form.name.help}}</small>
            {{#if errors->name:}}
            <small id="name-error" class="error" role="alert">
                {{errors->name}}
            </small>
            {{#endif errors->name:}}
        </p>
    </div>

    <footer>
        <button type="submit">{{form.save}}</button>
        <button type="reset">{{form.cancel}}</button>
        <a href="{{backUrl}}">{{form.back}}</a>
    </footer>
</form>

```

**Checkbox Control Pattern:**

```html
<div class="form-control">
    <div class="checkbox-control">
        <label for="remember">
            {{accounts.signin.form.remember.label}}
        </label>
        <input
            id="remember"
            name="remember"
            type="checkbox"
            checked
        />
    </div>
</div>
```

**Form Requirements:**

- Use `class="form"` on the `<form>` element
- Include a `<header>` with `<h2>` for the form title
- Show error summary with `role="alert"` and `aria-live="assertive"` when errors
   exist
- Wrap each field in `<div class="form-control">`
- Always pair `<label>` with `for` attribute matching input `id`
- Use `aria-describedby` to reference help text and error messages
- Set `aria-invalid="true"` on inputs with validation errors
- Include help text in `<small>` elements with matching `id`
- Use `class="primary-button"` for submit buttons
- Use `class="checkbox-control"` wrapper for checkbox fields
- Include a `<footer>` section for additional links or actions

### Table Template Pattern

Tables must include navigation controls, proper structure, and action buttons
 for data management.

**Table with Navigation and Pagination:**

```html
{{#layout layout:}}
<section class="section">
    <nav class="table-nav">
        <form action="/reservations" method="get" id="filter-form">
            <div class="inline-form-control">
                <label for="from">{{reservations.from}}</label>
                <input
                    type="date"
                    id="from"
                    name="from"
                    value="{{date}}"
                    onchange="this.form.submit()"
                />
            </div>
        </form>
        <a href="/reservations/create" class="primary-button">
            {{reservations.new}}
        </a>
    </nav>

    {{#if hasReservations:}}
    <form id="update-status" method="post">
        <input type="hidden" name="offset" value="{{offset}}">
        <input type="hidden" name="from" value="{{date}}">
    </form>
    <table class="table">
        <thead>
            <tr>
                <th>{{reservations.header.time}}</th>
                <th>{{reservations.header.name}}</th>
                <th>{{reservations.header.phone}}</th>
                <th>{{reservations.header.email}}</th>
                <th>{{reservations.header.details}}</th>
            </tr>
        </thead>
        <tbody>
            {{#for reservation in reservations:}}
            <tr>
                <td>{{reservation->turn}}</td>
                <td>{{reservation->name}}</td>
                <td>{{reservation->phone}}</td>
                <td>{{reservation->email}}</td>
                <td class="control-group">
                    <button type="submit"
                     form="update-status"
                     name="status"
                     value="ACCEPTED"
                     formaction="/reservations/{{reservation->id}}/status"
                    >
                        {{reservations.accept}}
                    </button>
                    <button type="submit"
                        form="update-status"
                        name="status"
                        value="CANCELLED"
                        formaction="/reservations/{{reservation->id}}/status"
                    >
                        {{reservations.cancel}}
                    </button>
                    <a href="/reservations/{{reservation->id}}">{{reservations.details}}</a>
                </td>
            </tr>
            {{#endfor reservations:}}
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5">&nbsp;</td>
            </tr>
        </tfoot>
    </table>
    <nav class="table-pagination">
        <button form="filter-form" type="submit" name="offset" value="{{prev}}">
            {{reservations.prev}}
        </button>
        <button form="filter-form" type="submit" name="offset" value="{{next}}">
            {{reservations.next}}
        </button>
    </nav>
    {{#endif hasReservations:}}

    {{#if !hasReservations:}}
    <article class="card">
        <header>
            <h3>{{reservations.no_found}}</h3>
        </header>
        <p>
            {{reservations.no_found_description}}
        </p>
    </article>
    {{#endif !hasReservations:}}
</section>

```

**Table Requirements:**

- Wrap table section in `<section class="section">`
- Use `class="table-nav"` for navigation bar containing filters and action buttons
- Use `class="inline-form-control"` for inline form fields in navigation
- Use `class="table"` on the `<table>` element
- Always include `<thead>`, `<tbody>`, and `<tfoot>` elements
- Use `class="control-group"` for action button containers in table cells
- Use `class="table-pagination"` for pagination controls
- Use `form` attribute on buttons to reference forms outside the table
- Show empty state with `<article class="card">` when no data exists
- Use conditional blocks to show/hide table and empty state

### Template Syntax Reference

The templating system uses a custom syntax for conditionals, loops, and variable
 interpolation.

**Conditional Blocks:**

```html
{{#if condition:}}
    <!-- Content when condition is true -->
{{#endif condition:}}

{{#if !condition:}}
    <!-- Content when condition is false -->
{{#endif !condition:}}
```

**Loops:**

```html
{{#for item in collection:}}
    <!-- Loop content -->
    <p>{{item->property}}</p>
{{#endfor collection:}}
```

**Variable Interpolation:**

```html
<!-- Simple variable -->
{{variableName}}

<!-- Object property access -->
{{object->property}}

<!-- Nested property access -->
{{object->nested->property}}
```

**Template Variables:**

- `{{content}}` - Injected by layout template for view content
- `{{pageTitle}}` - Page title for `<title>` tag
- `{{layout.*}}` - Layout-specific translations/values
- `{{errors->fieldName}}` - Field-specific error messages
- `{{errorSummary}}` - Array of error summary entries

**Accessibility Guidelines:**

- Always include `aria-describedby` on form inputs linking to help text
- Use `aria-invalid="true"` on inputs with validation errors
- Include `role="alert"` and `aria-live="assertive"` on error summary sections
- Use semantic HTML elements (`<header>`, `<nav>`, `<main>`, `<footer>`, `<section>`)
- Ensure all form inputs have associated `<label>` elements with matching `for`/`id`
- Use `tabindex="-1"` on error summaries to allow programmatic focus

**CSS Class Conventions:**

- `form` - Main form container
- `form-control` - Individual form field wrapper
- `checkbox-control` - Checkbox field wrapper
- `primary-button` - Primary action button
- `error-summary` - Error summary section
- `table` - Table element
- `table-nav` - Table navigation bar
- `table-pagination` - Pagination controls
- `inline-form-control` - Inline form field in navigation
- `control-group` - Group of action buttons
- `section` - Section container
- `card` - Card component for empty states or content blocks

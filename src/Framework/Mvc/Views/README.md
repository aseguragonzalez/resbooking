# Framework MVC Views

The Views package provides an HTML template engine for the MVC layer: it loads `.html` templates, optionally applies a layout, and replaces placeholders and blocks using a view model (object or array) and the request context.

## Usage from the controller

Return a view from any controller action:

```php
return new View($viewPath, $data);
```

- **viewPath**: Path relative to the views directory configured in `HtmlViewEngineSettings`, without extension. Example: `'Dashboard/index'` resolves to `{path}/Dashboard/index.html`.
- **data**: The view model; may be an `array`, an `object`, or `null`. It is merged with the request context (e.g. `user.username`, `user.isAuthenticated`).

The engine always injects into the model:

- `user.username`
- `user.isAuthenticated`

(from `RequestContext`).

## Architecture

- **ViewEngine** (interface) → **HtmlViewEngine** (implementation): resolves the template path, reads the file, applies the layout, builds the merged model (view data + context), and delegates to a **ContentReplacer** (the pipeline).
- The replacement pipeline runs in fixed order: **ModelReplacer** → **BranchesReplacer** → **I18nReplacer**. Each step receives the output of the previous one.
- **ViewValueResolver**: resolves path expressions (`model->foo->bar`, `items[0]->name`, `items["key"]`) against the model; used by both ModelReplacer and BranchesReplacer.

## Template syntax

### Interpolation `{{path}}`

Placeholders are replaced by the value of a path on the model.

**Paths supported:**

- Properties: `{{name}}`, `{{model->title}}`, `{{model->customer->address->city}}`
- Numeric index: `{{users[0]->name}}`
- Associative key: `{{items["my-key"]->label}}` or `{{items['my-key']}}`

**Formatted types:** strings, numbers, `true`/`false` for booleans, dates in ISO8601. Non-scalar values are replaced with an empty string.

**Example:**

```html
<h1>{{model->title}}</h1>
<p>{{model->customer->address->street}}, {{model->customer->address->number}}</p>
<p>{{model->customer->address->city}}</p>
```

### Loops `{{#for var in path:}} ... {{#endfor path:}}`

- **path** must resolve to an array (e.g. `items`, `model->customer->transactions`).
- **var** is the loop variable name inside the block.
- Inside the block use `{{var}}` or `{{var->property}}` when the item is an object.
- Nested loops are supported: e.g. `{{#for item in section->items:}}` inside a block where `section` is the outer loop variable. The path in `#endfor` must match exactly the path in `#for` (e.g. `{{#endfor section->items:}}`).

**Example:**

```html
<ul>
    {{#for transaction in model->customer->transactions:}}
    <li>({{transaction->createdAt}}) {{transaction->amount}}€ | {{transaction->status}}</li>
    {{#endfor model->customer->transactions:}}
</ul>
```

### Conditionals `{{#if expression:}} ... {{#endif expression:}}`

- **expression**: Same path syntax as interpolation, optionally prefixed with `!` for negation.
- Nested conditionals are supported (processed from innermost to outermost).
- Examples: `{{#if model->isBooleanProperty:}}`, `{{#if !model->nullProperty:}}`, `{{#if arrayProperty["my-key"]->name:}}`, and method calls: `{{#if model->isBranch():}}`.
- The expression in `#endif` must be identical to the one in `#if`.

**Example:**

```html
{{#if hasItems:}}
<div>
    <p>Show if items</p>
    <ul>
        {{#for item in items:}}
        <li>{{item}}</li>
        {{#endfor items:}}
    </ul>
</div>
{{#endif hasItems:}}
```

### Layout

- In the view: start the content with `{{#layout layoutName:}}`; the rest of the view content is injected into the layout.
- In the layout file (e.g. `layout.html`): include exactly `{{content}}` where the view body should go; indentation of the injected content is preserved based on the position of `{{content}}`.

**Example view (view_with_layout.html):**

```html
{{#layout layout:}}
<section>
    <header>
        <h1>View with layout</h1>
    </header>
    <article>
        <ol>
            <li>Name: {{model->name}}</li>
            <li>Age: {{model->age}}</li>
            <li>Height: {{model->height}}</li>
        </ol>
    </article>
    <footer>
        <p>Footer content</p>
    </footer>
</section>
```

**Example layout (layout.html):**

```html
<!DOCTYPE html>
<html>
    <head>
        <title>{{pageTitle}}</title>
    </head>
    <body>
        <header>
            <h1>{{model->title}}</h1>
        </header>
        <main>
            {{content}}
        </main>
        <footer>
            <p>Footer content</p>
        </footer>
    </body>
</html>
```

### Internationalization (I18n)

Placeholders `{{key}}` where **key** exists in the language JSON file (e.g. `{locale}.json`) are replaced in the last pipeline step. They can override model placeholders if the same key exists in the i18n file.

## Processing order

1. Load the view file: `{path}/{viewPath}.html`.
2. If `{{#layout name:}}` is present, load `{path}/{name}.html` and inject the view content into `{{content}}`.
3. Run the pipeline on the result: (1) **ModelReplacer**: `{{path}}` and `{{#for}}`; (2) **BranchesReplacer**: `{{#if}}`; (3) **I18nReplacer**: language keys.
4. Strip empty lines from the final output.

## Model and context

The model passed to the template is the merge of `view->data` (array or object) with the request context (e.g. `user`). If `data` is `null`, only the context is used.

The model may be an associative array (e.g. from an API or JSON); paths work the same way (`model->key` or array key access).

## Relevant files

**Interfaces and classes:** `ViewEngine`, `HtmlViewEngine`, `ContentReplacer`, `ContentReplacerPipeline`, `ModelReplacer`, `BranchesReplacer`, `I18nReplacer`, `ViewValueResolver`.

**Configuration:** `HtmlViewEngineSettings` (views path), `LanguageSettings` (i18n). The pipeline is wired in `MvcWebApp::configureMvc()`.

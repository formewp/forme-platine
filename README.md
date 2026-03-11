# Platine

A pure PHP templating library with layouts, components and slots.

Platine is the default templating library for [Forme](https://formewp.github.io), a WordPress MVC framework, but can be used in any PHP project.

## Background

Platine is a hard fork of Plates v4 alpha by RJ Garcia ([@ragboyjr](https://github.com/ragboyjr)). V4 alpha introduced some substantial improvements over v3 — a functional extension system, a composable render pipeline, a render context object (`$v`), and a component/slot system.

Unfortunately the v4 branch was reverted and the changes were never released. The Plates repository returned to v3 and v4 development was abandoned. Some features are being ported back but the file-based component and slot system was shelved in favour of an inline function approach.

Platine preserves and continues that v4 work. The core architecture is intact and the library is maintained as a standalone package independent of the PHP League.

If you were using or interested in Plates v4, this is its continuation.

## Requirements

- PHP 8.3+

## Installation

```bash
composer require forme/platine
```

## Getting Started

Create a view instance with a base directory for your templates:

```php
use Forme\Platine\Engine;

$view = Engine::create('/path/to/views', 'plate.php');
```

Render a template, passing in an array of data:

```php
echo $view->render('home', ['title' => 'Hello World']);
```

## Templates

Template files are plain PHP files. The render context object is available as `$v` within every template.

```php
// views/home.plate.php
<h1><?=$v->e($title)?></h1>
```

Use `$v->e()` to escape output:

```php
<?=$v->e($userInput)?>
```

## Layouts

Declare a layout at the top of your template:

```php
<?php $v->layout('layouts/main')?>
<p>This is the page content.</p>
```

In the layout, use `$v->section('content')` to render the child content:

```php
// views/layouts/main.plate.php
<html>
<body>
    <?=$v->section('content')?>
</body>
</html>
```

Pass variables up to the layout:

```php
<?php $v->layout('layouts/main', ['title' => 'My Page'])?>
```

Layouts can be nested:

```php
// views/layouts/wrapper.plate.php
<?php $v->layout('layouts/main')?>
<div class="wrapper">
    <?=$v->section('content')?>
</div>
```

## Insert

Include another template using `insert()`:

```php
<?=$v->insert('partials/header')?>
```

Pass data to the partial:

```php
<?=$v->insert('partials/header', ['title' => 'My Page'])?>
```

## Components

Components are isolated templates rendered with an explicit data scope. Use `component()` with `end()` to capture slot content:

```php
// calling side
<?php $v->component('components/card', ['title' => 'My Card'])?>
    <p>This becomes the default slot.</p>
<?php $v->end()?>
```

```php
// views/components/card.plate.php
<div class="card">
    <div class="card-header"><?=$v->e($title)?></div>
    <div class="card-body"><?=$slot?></div>
</div>
```

### Named Slots

Use `slot()` inside a component call to capture named regions:

```php
<?php $v->component('components/card', ['title' => 'My Card'])?>
    <?php $v->slot('footer')?>
        <p>Footer content</p>
    <?php $v->end()?>
    <p>Default slot content</p>
<?php $v->end()?>
```

```php
// views/components/card.plate.php
<div class="card">
    <div class="card-header"><?=$v->e($title)?></div>
    <div class="card-body"><?=$slot?></div>
    <div class="card-footer"><?=$footer?></div>
</div>
```

## Template References

Leave off the file extension when referencing templates. Both slash and dot notation work:

```php
$view->render('partials/foo');
$view->render('partials.foo');
```

Templates named `index` can be referenced with a trailing slash or dot:

```php
$view->render('partials/foo/');
$view->render('partials.foo.');
```

## Custom Functions

Register custom functions on the engine using `registerFunction()`:

```php
$view->registerFunction('truncate', function(string $value, int $length = 100): string {
    return mb_strimwidth($value, 0, $length, '...');
});
```

Then use them in templates via `$v`:

```php
<?=$v->truncate($longString, 50)?>
```

## Render Context Variable Name

The render context is injected into templates as `$v` by default. This is configurable:

```php
$view = Engine::createWithConfig([
    'base_dir' => '/path/to/views',
    'render_context_var_name' => 'view',
]);
```

## Validation

Platine ships with a `Validator` that enforces template coding standards. See `Validator::validateFile()`.

### Git Hook

If you use [CaptainHook](https://github.com/captainhookphp/captainhook), you can lint templates automatically on commit. First require CaptainHook:

```bash
composer require --dev captainhook/captainhook
```

Then add to your `captainhook.json`:

```json
{
    "pre-commit": {
        "actions": [
            {
                "action": "\\Forme\\Platine\\CaptainHook\\Lint"
            }
        ]
    }
}
```

## Todo

- Add test suite
- Component prop isolation and interface declaration
- Improve type coverage throughout
- Add `declare(strict_types=1)` once type coverage is sufficient

## Acknowledgements

Original Plates v4 alpha work by RJ Garcia. See [https://github.com/thephpleague/plates](https://github.com/thephpleague/plates). Both are MIT licensed — copyright notices preserved in LICENSE.

## License

MIT. See [LICENSE](LICENSE).

# Laminas Navigation for Mezzio

This demo shows how to integrate Laminas Navigation with a Mezzio application, including:

- A custom `Route` page type for Mezzio's router
- ACL-based menu filtering
- Native view helpers (`menu()`, `breadcrumbs()`)

## Quick Start

```bash
composer install
php -S localhost:8080 -t public
```

Visit http://localhost:8080 and use the role switcher to see ACL filtering in action.

## Setup Guide

### 1. Install Dependencies

```bash
composer require laminas/laminas-navigation laminas/laminas-permissions-acl mezzio/mezzio-session mezzio/mezzio-session-ext
```

### 2. Register View Helper Delegator

**config/autoload/dependencies.global.php**

Registers the navigation view helper with Mezzio's view layer.

```php
return [
    'dependencies' => [
        'delegators' => [
            \Laminas\View\HelperPluginManager::class => [
                \Laminas\Navigation\View\ViewHelperManagerDelegatorFactory::class,
            ],
        ],
    ],
];
```

### 3. Create the Route Page Type

**src/App/Navigation/Page/Route.php**

A Mezzio-compatible page type that uses `RouterInterface` for URL generation and `RouteResult` for active state detection. This is the equivalent of `Laminas\Navigation\Page\Mvc` for Mezzio.

### 4. Create Navigation Middleware

**src/App/Middleware/NavigationMiddleware.php**

Configures the `Route` page type with the router and current route result. Registers a factory with `AbstractPage` so pages with a `route` key automatically use the `Route` page type.

### 5. Define Navigation Structure

**config/autoload/navigation.global.php**

```php
return [
    'navigation' => [
        'default' => [
            [
                'label'    => 'Home',
                'route'    => 'home',
                'resource' => 'page:home',
            ],
            [
                'label'    => 'Admin',
                'route'    => 'admin',
                'resource' => 'page:admin',
                'privilege' => 'access',
            ],
        ],
    ],
];
```

### 6. Define ACL Rules

**config/autoload/acl.global.php**

```php
return [
    'acl' => [
        'roles' => [
            'guest' => null,
            'admin' => 'guest',
        ],
        'resources' => [
            'page:home',
            'page:admin',
        ],
        'allow' => [
            ['guest', 'page:home', null],
            ['admin', 'page:admin', 'access'],
        ],
    ],
];
```

### 7. Configure Middleware Pipeline

**config/pipeline.php**

```php
$app->pipe(SessionMiddleware::class);
$app->pipe(RoleMiddleware::class);
$app->pipe(NavigationMiddleware::class);
```

### 8. Use in Templates

**templates/layout/default.phtml**

```php
<?= $this->navigation('default')->menu() ?>
<?= $this->navigation('default')->breadcrumbs()->setMinDepth(0) ?>
```

## Key Files

| File | Purpose |
|------|---------|
| `src/App/Navigation/Page/Route.php` | Mezzio-compatible page type |
| `src/App/Middleware/NavigationMiddleware.php` | Configures Route page defaults |
| `src/App/Middleware/RoleMiddleware.php` | Manages role and ACL integration |
| `src/App/Factory/NavigationFactory.php` | Creates Navigation container |
| `src/App/Factory/AclFactory.php` | Creates ACL from config |
| `config/autoload/navigation.global.php` | Navigation structure |
| `config/autoload/acl.global.php` | ACL roles, resources, rules |

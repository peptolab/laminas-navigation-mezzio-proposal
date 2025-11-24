<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Navigation\Page\Route;
use Laminas\Navigation\Page\AbstractPage;
use Mezzio\Router\RouteResult;
use Mezzio\Router\RouterInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Middleware that configures the Route page type with the current router and route result
 *
 * This enables Laminas Navigation to work with Mezzio routes by:
 * 1. Setting the default router for URL generation
 * 2. Setting the default RouteResult for active state detection
 * 3. Registering a factory so pages with 'route' key (without 'controller') use our Route page type
 */
class NavigationMiddleware implements MiddlewareInterface
{
    private static bool $factoryRegistered = false;

    public function __construct(
        private readonly RouterInterface $router
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // Set the default router for URL generation
        Route::setDefaultRouter($this->router);

        // Set the default RouteResult for active state detection
        $routeResult = $request->getAttribute(RouteResult::class);
        Route::setDefaultRouteResult($routeResult);

        // Register factory once (for auto-detection of Route pages)
        if (!self::$factoryRegistered) {
            AbstractPage::addFactory(function (array $options): ?Route {
                // Only handle if 'route' is set but NOT 'controller' or 'action' (those are MVC pages)
                if (isset($options['route']) && !isset($options['controller']) && !isset($options['action'])) {
                    return new Route($options);
                }
                return null;
            });
            self::$factoryRegistered = true;
        }

        return $handler->handle($request);
    }
}
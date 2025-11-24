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

class NavigationMiddleware implements MiddlewareInterface
{
    private static bool $factoryRegistered = false;

    public function __construct(
        private readonly RouterInterface $router
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        Route::setDefaultRouter($this->router);

        $routeResult = $request->getAttribute(RouteResult::class);
        Route::setDefaultRouteResult($routeResult);

        if (! self::$factoryRegistered) {
            AbstractPage::addFactory(function (array $options): ?Route {
                if (isset($options['route']) && ! isset($options['controller']) && ! isset($options['action'])) {
                    return new Route($options);
                }
                return null;
            });
            self::$factoryRegistered = true;
        }

        return $handler->handle($request);
    }
}

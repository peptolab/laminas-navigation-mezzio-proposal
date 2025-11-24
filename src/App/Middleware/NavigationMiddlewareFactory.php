<?php

declare(strict_types=1);

namespace App\Middleware;

use Mezzio\Router\RouterInterface;
use Psr\Container\ContainerInterface;

class NavigationMiddlewareFactory
{
    public function __invoke(ContainerInterface $container): NavigationMiddleware
    {
        return new NavigationMiddleware(
            $container->get(RouterInterface::class)
        );
    }
}
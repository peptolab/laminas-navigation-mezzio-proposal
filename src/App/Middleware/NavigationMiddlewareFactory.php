<?php

declare(strict_types=1);

namespace App\Middleware;

use Laminas\Navigation\Navigation;
use Laminas\Permissions\Acl\Acl;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;

class NavigationMiddlewareFactory
{
    public function __invoke(ContainerInterface $container): NavigationMiddleware
    {
        return new NavigationMiddleware(
            $container->get(TemplateRendererInterface::class),
            $container->get(Navigation::class),
            $container->get(Acl::class)
        );
    }
}

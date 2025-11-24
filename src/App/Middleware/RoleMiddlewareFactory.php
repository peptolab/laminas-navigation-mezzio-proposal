<?php

declare(strict_types=1);

namespace App\Middleware;

use Laminas\Permissions\Acl\Acl;
use Laminas\View\HelperPluginManager;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;

class RoleMiddlewareFactory
{
    public function __invoke(ContainerInterface $container): RoleMiddleware
    {
        return new RoleMiddleware(
            $container->get(TemplateRendererInterface::class),
            $container->get(HelperPluginManager::class),
            $container->get(Acl::class)
        );
    }
}

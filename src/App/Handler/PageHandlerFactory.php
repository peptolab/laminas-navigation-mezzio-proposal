<?php

declare(strict_types=1);

namespace App\Handler;

use Laminas\Permissions\Acl\Acl;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;

class PageHandlerFactory
{
    public function __construct(
        private readonly string $pageName,
        private readonly string $pageTitle,
        private readonly string $pageDescription,
        private readonly ?string $resource = null,
        private readonly ?string $privilege = null
    ) {
    }

    public function __invoke(ContainerInterface $container): PageHandler
    {
        return new PageHandler(
            $container->get(TemplateRendererInterface::class),
            $container->get(Acl::class),
            $this->pageName,
            $this->pageTitle,
            $this->pageDescription,
            $this->resource,
            $this->privilege
        );
    }
}

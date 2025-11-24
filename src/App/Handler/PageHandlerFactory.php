<?php

declare(strict_types=1);

namespace App\Handler;

use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;

class PageHandlerFactory
{
    private string $pageName;
    private string $pageTitle;
    private string $pageDescription;

    public function __construct(string $pageName, string $pageTitle, string $pageDescription)
    {
        $this->pageName = $pageName;
        $this->pageTitle = $pageTitle;
        $this->pageDescription = $pageDescription;
    }

    public function __invoke(ContainerInterface $container): PageHandler
    {
        return new PageHandler(
            $container->get(TemplateRendererInterface::class),
            $this->pageName,
            $this->pageTitle,
            $this->pageDescription
        );
    }
}

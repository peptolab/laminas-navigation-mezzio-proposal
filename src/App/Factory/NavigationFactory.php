<?php

declare(strict_types=1);

namespace App\Factory;

use Laminas\Navigation\Navigation;
use Laminas\Permissions\Acl\Acl;
use Mezzio\Router\RouterInterface;
use Psr\Container\ContainerInterface;

class NavigationFactory
{
    public function __invoke(ContainerInterface $container): Navigation
    {
        $config = $container->get('config')['navigation']['default'] ?? [];
        $router = $container->get(RouterInterface::class);

        // Process the config to convert route names to URIs
        $pages = $this->processPages($config, $router);

        return new Navigation($pages);
    }

    private function processPages(array $pages, RouterInterface $router): array
    {
        $processed = [];

        foreach ($pages as $page) {
            if (isset($page['route'])) {
                try {
                    $page['uri'] = $router->generateUri($page['route']);
                } catch (\Exception $e) {
                    $page['uri'] = '#';
                }
            }

            if (isset($page['pages']) && is_array($page['pages'])) {
                $page['pages'] = $this->processPages($page['pages'], $router);
            }

            $processed[] = $page;
        }

        return $processed;
    }
}

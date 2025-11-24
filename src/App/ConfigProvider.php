<?php

declare(strict_types=1);

namespace App;

use App\Factory\AclFactory;
use App\Factory\NavigationFactory;
use App\Handler\PageHandlerFactory;
use App\Middleware\NavigationMiddleware;
use App\Middleware\NavigationMiddlewareFactory;
use App\Middleware\RoleMiddleware;
use App\Middleware\RoleMiddlewareFactory;
use Laminas\Navigation\Navigation;
use Laminas\Permissions\Acl\Acl;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
            'templates'    => $this->getTemplates(),
        ];
    }

    public function getDependencies(): array
    {
        return [
            'factories' => [
                Acl::class                             => AclFactory::class,
                Navigation::class                      => NavigationFactory::class,
                NavigationMiddleware::class            => NavigationMiddlewareFactory::class,
                RoleMiddleware::class                  => RoleMiddlewareFactory::class,
                'handler.home'                         => fn($c) => (new PageHandlerFactory(
                    'home',
                    'Home',
                    'Welcome to our Mezzio site with Laminas Navigation!',
                    'page:home'
                ))($c),
                'handler.products'                     => fn($c) => (new PageHandlerFactory(
                    'products',
                    'Products',
                    'Browse our extensive product catalog.',
                    'page:products'
                ))($c),
                'handler.products.servers'             => fn($c) => (new PageHandlerFactory(
                    'products.servers',
                    'Servers',
                    'Enterprise-grade server solutions.',
                    'page:products.servers'
                ))($c),
                'handler.products.software'            => fn($c) => (new PageHandlerFactory(
                    'products.software',
                    'Software',
                    'Cutting-edge software products.',
                    'page:products.software'
                ))($c),
                'handler.products.software.enterprise' => fn($c) => (new PageHandlerFactory(
                    'products.software.enterprise',
                    'Enterprise Software',
                    'Business software solutions for large organizations.',
                    'page:products.software.enterprise'
                ))($c),
                'handler.products.software.consumer'   => fn($c) => (new PageHandlerFactory(
                    'products.software.consumer',
                    'Consumer Software',
                    'User-friendly software for everyday use.',
                    'page:products.software.consumer'
                ))($c),
                'handler.company'                      => fn($c) => (new PageHandlerFactory(
                    'company',
                    'Company',
                    'Learn more about our company.',
                    'page:company'
                ))($c),
                'handler.company.about'                => fn($c) => (new PageHandlerFactory(
                    'company.about',
                    'About Us',
                    'Discover our story and mission.',
                    'page:company.about'
                ))($c),
                'handler.company.careers'              => fn($c) => (new PageHandlerFactory(
                    'company.careers',
                    'Careers',
                    'Join our team and grow with us.',
                    'page:company.careers'
                ))($c),
                'handler.company.investors'            => fn($c) => (new PageHandlerFactory(
                    'company.investors',
                    'Investors',
                    'Financial information for stakeholders. (Requires member access)',
                    'page:company.investors',
                    'view'
                ))($c),
                'handler.community'                    => fn($c) => (new PageHandlerFactory(
                    'community',
                    'Community',
                    'Connect with our community.',
                    'page:community'
                ))($c),
                'handler.admin'                        => fn($c) => (new PageHandlerFactory(
                    'admin',
                    'Admin Panel',
                    'Administrative dashboard. (Requires admin access)',
                    'page:admin',
                    'access'
                ))($c),
            ],
        ];
    }

    public function getTemplates(): array
    {
        return [
            'paths' => [
                'app'    => ['templates/app'],
                'error'  => ['templates/error'],
                'layout' => ['templates/layout'],
            ],
        ];
    }
}

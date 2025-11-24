<?php

declare(strict_types=1);

namespace App;

use App\Factory\AclFactory;
use App\Factory\NavigationFactory;
use App\Handler\PageHandlerFactory;
use App\Middleware\NavigationMiddleware;
use App\Middleware\NavigationMiddlewareFactory;
use Laminas\Navigation\Navigation;
use Laminas\Permissions\Acl\Acl;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
            'templates' => $this->getTemplates(),
        ];
    }

    public function getDependencies(): array
    {
        return [
            'factories' => [
                Acl::class => AclFactory::class,
                Navigation::class => NavigationFactory::class,
                NavigationMiddleware::class => NavigationMiddlewareFactory::class,

                // Page handlers
                'handler.home' => fn($c) => (new PageHandlerFactory(
                    'home',
                    'Home',
                    'Welcome to our Mezzio site with Laminas Navigation!'
                ))($c),

                'handler.products' => fn($c) => (new PageHandlerFactory(
                    'products',
                    'Products',
                    'Browse our extensive product catalog.'
                ))($c),

                'handler.products.servers' => fn($c) => (new PageHandlerFactory(
                    'products.servers',
                    'Servers',
                    'Enterprise-grade server solutions.'
                ))($c),

                'handler.products.software' => fn($c) => (new PageHandlerFactory(
                    'products.software',
                    'Software',
                    'Cutting-edge software products.'
                ))($c),

                'handler.products.software.enterprise' => fn($c) => (new PageHandlerFactory(
                    'products.software.enterprise',
                    'Enterprise Software',
                    'Business software solutions for large organizations.'
                ))($c),

                'handler.products.software.consumer' => fn($c) => (new PageHandlerFactory(
                    'products.software.consumer',
                    'Consumer Software',
                    'User-friendly software for everyday use.'
                ))($c),

                'handler.company' => fn($c) => (new PageHandlerFactory(
                    'company',
                    'Company',
                    'Learn more about our company.'
                ))($c),

                'handler.company.about' => fn($c) => (new PageHandlerFactory(
                    'company.about',
                    'About Us',
                    'Discover our story and mission.'
                ))($c),

                'handler.company.careers' => fn($c) => (new PageHandlerFactory(
                    'company.careers',
                    'Careers',
                    'Join our team and grow with us.'
                ))($c),

                'handler.company.investors' => fn($c) => (new PageHandlerFactory(
                    'company.investors',
                    'Investors',
                    'Financial information for stakeholders. (Requires member access)'
                ))($c),

                'handler.community' => fn($c) => (new PageHandlerFactory(
                    'community',
                    'Community',
                    'Connect with our community.'
                ))($c),

                'handler.admin' => fn($c) => (new PageHandlerFactory(
                    'admin',
                    'Admin Panel',
                    'Administrative dashboard. (Requires admin access)'
                ))($c),
            ],
        ];
    }

    public function getTemplates(): array
    {
        return [
            'paths' => [
                'app' => ['templates/app'],
                'error' => ['templates/error'],
                'layout' => ['templates/layout'],
            ],
        ];
    }
}

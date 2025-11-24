<?php

declare(strict_types=1);

use Mezzio\Application;
use Mezzio\MiddlewareFactory;
use Psr\Container\ContainerInterface;

return static function (Application $app, MiddlewareFactory $factory, ContainerInterface $container): void {
    // Home page
    $app->get('/', 'handler.home', 'home');

    // Products section
    $app->get('/products', 'handler.products', 'products');
    $app->get('/products/servers', 'handler.products.servers', 'products.servers');
    $app->get('/products/software', 'handler.products.software', 'products.software');
    $app->get('/products/software/enterprise', 'handler.products.software.enterprise', 'products.software.enterprise');
    $app->get('/products/software/consumer', 'handler.products.software.consumer', 'products.software.consumer');

    // Company section
    $app->get('/company', 'handler.company', 'company');
    $app->get('/company/about', 'handler.company.about', 'company.about');
    $app->get('/company/careers', 'handler.company.careers', 'company.careers');
    $app->get('/company/investors', 'handler.company.investors', 'company.investors');

    // Community
    $app->get('/community', 'handler.community', 'community');

    // Admin
    $app->get('/admin', 'handler.admin', 'admin');
};

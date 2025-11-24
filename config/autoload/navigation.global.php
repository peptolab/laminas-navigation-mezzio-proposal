<?php

declare(strict_types=1);

return [
    'navigation' => [
        'default' => [
            [
                'label'    => 'Home',
                'route'    => 'home',
                'resource' => 'page:home',
            ],
            [
                'label'    => 'Products',
                'route'    => 'products',
                'resource' => 'page:products',
                'pages'    => [
                    [
                        'label'    => 'Servers',
                        'route'    => 'products.servers',
                        'resource' => 'page:products.servers',
                    ],
                    [
                        'label'    => 'Software',
                        'route'    => 'products.software',
                        'resource' => 'page:products.software',
                        'pages'    => [
                            [
                                'label'    => 'Enterprise',
                                'route'    => 'products.software.enterprise',
                                'resource' => 'page:products.software.enterprise',
                            ],
                            [
                                'label'    => 'Consumer',
                                'route'    => 'products.software.consumer',
                                'resource' => 'page:products.software.consumer',
                            ],
                        ],
                    ],
                ],
            ],
            [
                'label'    => 'Company',
                'route'    => 'company',
                'resource' => 'page:company',
                'pages'    => [
                    [
                        'label'    => 'About Us',
                        'route'    => 'company.about',
                        'resource' => 'page:company.about',
                    ],
                    [
                        'label'    => 'Careers',
                        'route'    => 'company.careers',
                        'resource' => 'page:company.careers',
                    ],
                    [
                        'label'     => 'Investors',
                        'route'     => 'company.investors',
                        'resource'  => 'page:company.investors',
                        'privilege' => 'view',
                    ],
                ],
            ],
            [
                'label'    => 'Community',
                'route'    => 'community',
                'resource' => 'page:community',
            ],
            [
                'label'     => 'Admin',
                'route'     => 'admin',
                'resource'  => 'page:admin',
                'privilege' => 'access',
            ],
        ],
    ],
];

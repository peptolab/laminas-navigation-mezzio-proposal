<?php

declare(strict_types=1);

return [
    'acl' => [
        'roles'     => [
            'guest'  => null,
            'member' => 'guest',
            'admin'  => 'member',
        ],
        'resources' => [
            'page:home',
            'page:products',
            'page:products.servers',
            'page:products.software',
            'page:products.software.enterprise',
            'page:products.software.consumer',
            'page:company',
            'page:company.about',
            'page:company.careers',
            'page:company.investors',
            'page:community',
            'page:admin',
        ],
        'allow'     => [
            ['guest', 'page:home', null],
            ['guest', 'page:products', null],
            ['guest', 'page:products.servers', null],
            ['guest', 'page:products.software', null],
            ['guest', 'page:products.software.enterprise', null],
            ['guest', 'page:products.software.consumer', null],
            ['guest', 'page:company', null],
            ['guest', 'page:company.about', null],
            ['guest', 'page:company.careers', null],
            ['guest', 'page:community', null],
            ['member', 'page:company.investors', 'view'],
            ['admin', 'page:admin', 'access'],
        ],
        'deny'      => [],
    ],
];

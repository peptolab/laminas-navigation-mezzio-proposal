<?php

declare(strict_types=1);

use Laminas\Navigation\View\ViewHelperManagerDelegatorFactory;
use Laminas\View\HelperPluginManager;

return [
    'dependencies' => [
        'delegators' => [
            HelperPluginManager::class => [
                ViewHelperManagerDelegatorFactory::class,
            ],
        ],
    ],
];

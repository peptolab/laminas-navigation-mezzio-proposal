<?php

declare(strict_types=1);

namespace App\Factory;

use Laminas\Permissions\Acl\Acl;
use Psr\Container\ContainerInterface;

class AclFactory
{
    public function __invoke(ContainerInterface $container): Acl
    {
        $config = $container->get('config')['acl'] ?? [];
        $acl    = new Acl();

        foreach ($config['roles'] ?? [] as $role => $parent) {
            $acl->addRole($role, $parent);
        }

        foreach ($config['resources'] ?? [] as $resource) {
            $acl->addResource($resource);
        }

        foreach ($config['allow'] ?? [] as $rule) {
            $acl->allow($rule[0], $rule[1], $rule[2] ?? null);
        }

        foreach ($config['deny'] ?? [] as $rule) {
            $acl->deny($rule[0], $rule[1], $rule[2] ?? null);
        }

        return $acl;
    }
}

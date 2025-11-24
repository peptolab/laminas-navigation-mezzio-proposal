<?php

declare(strict_types=1);

namespace App\Middleware;

use Laminas\Permissions\Acl\Acl;
use Laminas\View\Helper\Navigation as NavigationHelper;
use Laminas\View\HelperPluginManager;
use Mezzio\Session\SessionInterface;
use Mezzio\Session\SessionMiddleware;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function in_array;

class RoleMiddleware implements MiddlewareInterface
{
    public const ROLE_ATTRIBUTE = 'user_role';
    private const SESSION_KEY   = 'user_role';

    private const ALLOWED_ROLES = ['guest', 'member', 'admin'];
    private const DEFAULT_ROLE  = 'guest';

    public function __construct(
        private readonly TemplateRendererInterface $template,
        private readonly HelperPluginManager $viewHelpers,
        private readonly Acl $acl
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        /** @var SessionInterface $session */
        $session = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);

        $queryRole = $request->getQueryParams()['role'] ?? null;

        if ($queryRole !== null && in_array($queryRole, self::ALLOWED_ROLES, true)) {
            $session->set(self::SESSION_KEY, $queryRole);
            $role = $queryRole;
        } else {
            $role = $session->get(self::SESSION_KEY, self::DEFAULT_ROLE);

            if (! in_array($role, self::ALLOWED_ROLES, true)) {
                $role = self::DEFAULT_ROLE;
                $session->set(self::SESSION_KEY, $role);
            }
        }

        $this->template->addDefaultParam(
            TemplateRendererInterface::TEMPLATE_ALL,
            'role',
            $role
        );

        /** @var NavigationHelper $navigationHelper */
        $navigationHelper = $this->viewHelpers->get('navigation');
        $navigationHelper->setAcl($this->acl);
        $navigationHelper->setRole($role);

        return $handler->handle(
            $request->withAttribute(self::ROLE_ATTRIBUTE, $role)
        );
    }
}

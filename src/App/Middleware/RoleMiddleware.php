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

/**
 * Demo middleware that manages user role via session
 *
 * - Reads role from session
 * - Allows changing role via ?role= query parameter (persists to session)
 * - Configures navigation view helper with ACL
 *
 * In a real application, the role would come from authentication
 */
class RoleMiddleware implements MiddlewareInterface
{
    public const ROLE_ATTRIBUTE = 'user_role';
    private const SESSION_KEY = 'user_role';

    private const ALLOWED_ROLES = ['guest', 'member', 'admin'];
    private const DEFAULT_ROLE = 'guest';

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

        // Check if role is being changed via query parameter
        $queryRole = $request->getQueryParams()['role'] ?? null;

        if ($queryRole !== null && in_array($queryRole, self::ALLOWED_ROLES, true)) {
            // Update session with new role
            $session->set(self::SESSION_KEY, $queryRole);
            $role = $queryRole;
        } else {
            // Get role from session, default to guest
            $role = $session->get(self::SESSION_KEY, self::DEFAULT_ROLE);

            // Validate stored role
            if (!in_array($role, self::ALLOWED_ROLES, true)) {
                $role = self::DEFAULT_ROLE;
                $session->set(self::SESSION_KEY, $role);
            }
        }

        // Make role available to all templates
        $this->template->addDefaultParam(
            TemplateRendererInterface::TEMPLATE_ALL,
            'role',
            $role
        );

        // Configure navigation view helper with ACL and role
        /** @var NavigationHelper $navigationHelper */
        $navigationHelper = $this->viewHelpers->get('navigation');
        $navigationHelper->setAcl($this->acl);
        $navigationHelper->setRole($role);

        return $handler->handle(
            $request->withAttribute(self::ROLE_ATTRIBUTE, $role)
        );
    }
}

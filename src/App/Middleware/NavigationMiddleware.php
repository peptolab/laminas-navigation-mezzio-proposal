<?php

declare(strict_types=1);

namespace App\Middleware;

use Laminas\Navigation\Navigation;
use Laminas\Permissions\Acl\Acl;
use Mezzio\Router\RouteResult;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class NavigationMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly TemplateRendererInterface $template,
        private readonly Navigation $navigation,
        private readonly Acl $acl
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // Get role from query parameter (for demo purposes)
        $role = $request->getQueryParams()['role'] ?? 'guest';
        if (!in_array($role, ['guest', 'member', 'admin'], true)) {
            $role = 'guest';
        }

        // Get current route
        $routeResult = $request->getAttribute(RouteResult::class);
        $currentRoute = $routeResult?->getMatchedRouteName() ?? '';

        // Set active page in navigation based on current route
        $this->setActivePage($this->navigation, $currentRoute);

        // Generate navigation HTML with ACL
        $navigationHtml = $this->renderNavigation($this->navigation, $role, $currentRoute);
        $breadcrumbHtml = $this->renderBreadcrumb($this->navigation, $currentRoute);

        // Add to template
        $this->template->addDefaultParam(TemplateRendererInterface::TEMPLATE_ALL, 'navigation', $navigationHtml);
        $this->template->addDefaultParam(TemplateRendererInterface::TEMPLATE_ALL, 'breadcrumb', $breadcrumbHtml);
        $this->template->addDefaultParam(TemplateRendererInterface::TEMPLATE_ALL, 'currentRole', $role);

        return $handler->handle($request);
    }

    private function setActivePage(Navigation $navigation, string $currentRoute): void
    {
        foreach ($navigation as $page) {
            $this->setActiveRecursive($page, $currentRoute);
        }
    }

    private function setActiveRecursive($page, string $currentRoute): bool
    {
        $isActive = false;

        // Check if this page matches the current route
        if ($page->get('route') === $currentRoute) {
            $isActive = true;
        }

        // Check children
        if ($page->hasPages()) {
            foreach ($page->getPages() as $childPage) {
                if ($this->setActiveRecursive($childPage, $currentRoute)) {
                    $isActive = true;
                }
            }
        }

        $page->setActive($isActive);
        return $isActive;
    }

    private function renderNavigation(Navigation $navigation, string $role, string $currentRoute): string
    {
        $html = '<ul class="nav flex-column">';

        foreach ($navigation as $page) {
            $html .= $this->renderPage($page, $role, $currentRoute, 0);
        }

        $html .= '</ul>';
        return $html;
    }

    private function renderPage($page, string $role, string $currentRoute, int $depth): string
    {
        $resource = $page->get('resource');
        $privilege = $page->get('privilege');

        // Check ACL
        $hasAccess = true;
        $isRestricted = false;

        if ($resource) {
            try {
                $hasAccess = $this->acl->isAllowed($role, $resource, $privilege);
                if (!$hasAccess) {
                    $isRestricted = true;
                }
            } catch (\Exception $e) {
                $hasAccess = false;
                $isRestricted = true;
            }
        }

        $label = htmlspecialchars($page->getLabel(), ENT_QUOTES, 'UTF-8');
        $uri = $page->get('uri') ?: '#';
        $isActive = $page->isActive();
        $hasChildren = $page->hasPages();

        // Build CSS classes
        $linkClasses = ['nav-link'];
        if ($isActive && $page->get('route') === $currentRoute) {
            $linkClasses[] = 'active';
        }
        if ($isRestricted) {
            $linkClasses[] = 'restricted-link';
        }

        $html = '<li class="nav-item">';

        if ($isRestricted && !$hasAccess) {
            // Show restricted item but not clickable
            $html .= '<span class="' . implode(' ', $linkClasses) . '" title="Restricted - requires higher privileges">';
            $html .= '<i class="text-warning">&#128274;</i> ' . $label;
            $html .= '</span>';
        } else {
            $html .= '<a class="' . implode(' ', $linkClasses) . '" href="' . htmlspecialchars($uri, ENT_QUOTES, 'UTF-8') . '">';
            $html .= $label;
            $html .= '</a>';
        }

        // Render children
        if ($hasChildren) {
            $html .= '<ul class="nav flex-column ms-3">';
            foreach ($page->getPages() as $childPage) {
                $html .= $this->renderPage($childPage, $role, $currentRoute, $depth + 1);
            }
            $html .= '</ul>';
        }

        $html .= '</li>';

        return $html;
    }

    private function renderBreadcrumb(Navigation $navigation, string $currentRoute): string
    {
        $breadcrumbs = $this->findBreadcrumbPath($navigation, $currentRoute);

        if (empty($breadcrumbs)) {
            return '<nav aria-label="breadcrumb"><ol class="breadcrumb mb-0"><li class="breadcrumb-item active">Home</li></ol></nav>';
        }

        $html = '<nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">';

        $count = count($breadcrumbs);
        foreach ($breadcrumbs as $index => $page) {
            $label = htmlspecialchars($page->getLabel(), ENT_QUOTES, 'UTF-8');
            $uri = $page->get('uri') ?: '#';
            $isLast = ($index === $count - 1);

            if ($isLast) {
                $html .= '<li class="breadcrumb-item active" aria-current="page">' . $label . '</li>';
            } else {
                $html .= '<li class="breadcrumb-item"><a href="' . htmlspecialchars($uri, ENT_QUOTES, 'UTF-8') . '">' . $label . '</a></li>';
            }
        }

        $html .= '</ol></nav>';
        return $html;
    }

    private function findBreadcrumbPath(Navigation $navigation, string $currentRoute): array
    {
        foreach ($navigation as $page) {
            $path = $this->findPagePath($page, $currentRoute);
            if (!empty($path)) {
                return $path;
            }
        }
        return [];
    }

    private function findPagePath($page, string $currentRoute, array $path = []): array
    {
        $path[] = $page;

        if ($page->get('route') === $currentRoute) {
            return $path;
        }

        if ($page->hasPages()) {
            foreach ($page->getPages() as $childPage) {
                $result = $this->findPagePath($childPage, $currentRoute, $path);
                if (!empty($result)) {
                    return $result;
                }
            }
        }

        return [];
    }
}

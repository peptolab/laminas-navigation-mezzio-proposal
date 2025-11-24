<?php

declare(strict_types=1);

namespace App\Handler;

use App\Middleware\RoleMiddleware;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Permissions\Acl\Acl;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class PageHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly TemplateRendererInterface $template,
        private readonly Acl $acl,
        private readonly string $pageName,
        private readonly string $pageTitle,
        private readonly string $pageDescription,
        private readonly ?string $resource = null,
        private readonly ?string $privilege = null
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        // Check ACL if resource is defined
        if ($this->resource !== null) {
            $role = $request->getAttribute(RoleMiddleware::ROLE_ATTRIBUTE, 'guest');

            if (!$this->acl->isAllowed($role, $this->resource, $this->privilege)) {
                return new HtmlResponse(
                    $this->template->render('error::403', [
                        'pageTitle' => '403 Forbidden',
                        'resource' => $this->resource,
                        'privilege' => $this->privilege,
                        'role' => $role,
                    ]),
                    403
                );
            }
        }

        return new HtmlResponse($this->template->render('app::page', [
            'pageName' => $this->pageName,
            'pageTitle' => $this->pageTitle,
            'pageDescription' => $this->pageDescription,
        ]));
    }
}

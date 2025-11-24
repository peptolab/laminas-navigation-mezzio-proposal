<?php

declare(strict_types=1);

namespace App\Navigation\Page;

use Laminas\Navigation\Exception;
use Laminas\Navigation\Page\AbstractPage;
use Mezzio\Router\RouteResult;
use Mezzio\Router\RouterInterface;

use function array_intersect_assoc;
use function array_merge;
use function count;
use function http_build_query;

/**
 * Represents a page defined using a Mezzio route name and route params
 *
 * This is the Mezzio equivalent of Laminas\Navigation\Page\Mvc
 */
class Route extends AbstractPage
{
    /**
     * Route name to use when assembling URL
     */
    protected ?string $route = null;

    /**
     * Params to use when assembling URL
     *
     * @var array<string, mixed>
     */
    protected array $params = [];

    /**
     * URL query part to use when assembling URL
     *
     * @var array<string, mixed>|null
     */
    protected ?array $query = null;

    /**
     * Cached href
     */
    protected ?string $hrefCache = null;

    /**
     * RouteResult for matching active state
     */
    protected ?RouteResult $routeResult = null;

    /**
     * Router for assembling URLs
     */
    protected ?RouterInterface $router = null;

    /**
     * Default router to be used if router is not given
     */
    protected static ?RouterInterface $defaultRouter = null;

    /**
     * Default RouteResult for active state detection
     */
    protected static ?RouteResult $defaultRouteResult = null;

    /**
     * Returns whether page should be considered active or not
     *
     * Compares the page's route and params against the RouteResult.
     *
     * @param bool $recursive
     */
    public function isActive($recursive = false): bool
    {
        if (! $this->active) {
            $routeResult = $this->routeResult ?? static::$defaultRouteResult;

            if ($routeResult instanceof RouteResult && $routeResult->isSuccess()) {
                $matchedRouteName = $routeResult->getMatchedRouteName();
                $matchedParams    = $routeResult->getMatchedParams();

                // Check if route name matches
                if ($this->getRoute() === $matchedRouteName) {
                    // If we have specific params, they must also match
                    if (empty($this->params)) {
                        $this->active = true;
                        return true;
                    }

                    // Check if page params are a subset of matched params
                    if (count(array_intersect_assoc($matchedParams, $this->params)) === count($this->params)) {
                        $this->active = true;
                        return true;
                    }
                }
            }
        }

        return parent::isActive($recursive);
    }

    /**
     * Returns href for this page
     *
     * Uses RouterInterface to assemble the href based on route and params.
     *
     * @throws Exception\DomainException If no router is set.
     */
    public function getHref(): string
    {
        if ($this->hrefCache !== null) {
            return $this->hrefCache;
        }

        $router = $this->router ?? static::$defaultRouter;

        if (! $router instanceof RouterInterface) {
            throw new Exception\DomainException(
                __METHOD__ . ' cannot execute as no Mezzio\Router\RouterInterface instance is composed'
            );
        }

        $route = $this->getRoute();
        if ($route === null) {
            throw new Exception\DomainException(
                __METHOD__ . ' cannot execute as no route name is set'
            );
        }

        // Generate the URI
        $uri = $router->generateUri($route, $this->getParams());

        // Add query string if present
        if ($this->query !== null && ! empty($this->query)) {
            $uri .= '?' . http_build_query($this->query);
        }

        // Add fragment if present
        $fragment = $this->getFragment();
        if ($fragment !== null) {
            $uri .= '#' . $fragment;
        }

        return $this->hrefCache = $uri;
    }

    /**
     * Sets route name to use when assembling URL
     *
     * @throws Exception\InvalidArgumentException If invalid route name is given.
     */
    public function setRoute(?string $route): self
    {
        if ($route !== null && $route === '') {
            throw new Exception\InvalidArgumentException(
                'Invalid argument: $route must be a non-empty string or null'
            );
        }

        $this->route     = $route;
        $this->hrefCache = null;

        return $this;
    }

    /**
     * Returns route name
     */
    public function getRoute(): ?string
    {
        return $this->route;
    }

    /**
     * Sets params to use when assembling URL
     *
     * @param array<string, mixed>|null $params
     */
    public function setParams(?array $params = null): self
    {
        $this->params    = $params ?? [];
        $this->hrefCache = null;

        return $this;
    }

    /**
     * Returns params
     *
     * @return array<string, mixed>
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * Sets URL query part
     *
     * @param array<string, mixed>|null $query
     */
    public function setQuery(?array $query): self
    {
        $this->query     = $query;
        $this->hrefCache = null;

        return $this;
    }

    /**
     * Returns URL query part
     *
     * @return array<string, mixed>|null
     */
    public function getQuery(): ?array
    {
        return $this->query;
    }

    /**
     * Sets the RouteResult for active state detection
     */
    public function setRouteResult(?RouteResult $routeResult): self
    {
        $this->routeResult = $routeResult;

        return $this;
    }

    /**
     * Returns the RouteResult
     */
    public function getRouteResult(): ?RouteResult
    {
        return $this->routeResult;
    }

    /**
     * Sets the router for URL generation
     */
    public function setRouter(?RouterInterface $router): self
    {
        $this->router    = $router;
        $this->hrefCache = null;

        return $this;
    }

    /**
     * Returns the router
     */
    public function getRouter(): ?RouterInterface
    {
        return $this->router;
    }

    /**
     * Sets the default router for all Route pages
     */
    public static function setDefaultRouter(?RouterInterface $router): void
    {
        static::$defaultRouter = $router;
    }

    /**
     * Gets the default router
     */
    public static function getDefaultRouter(): ?RouterInterface
    {
        return static::$defaultRouter;
    }

    /**
     * Sets the default RouteResult for all Route pages
     */
    public static function setDefaultRouteResult(?RouteResult $routeResult): void
    {
        static::$defaultRouteResult = $routeResult;
    }

    /**
     * Gets the default RouteResult
     */
    public static function getDefaultRouteResult(): ?RouteResult
    {
        return static::$defaultRouteResult;
    }

    /**
     * Returns an array representation of the page
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return array_merge(
            parent::toArray(),
            [
                'route'  => $this->getRoute(),
                'params' => $this->getParams(),
                'query'  => $this->getQuery(),
            ]
        );
    }
}

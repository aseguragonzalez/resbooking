<?php

declare(strict_types=1);

namespace Framework\Mvc\Routes;

final class Router
{
    /**
     * @param Route[] $routes
     */
    public function __construct(private array $routes = [])
    {
    }

    /**
     * @return Route[]
     */
    public function getRoutes(): array
    {
        return array_values($this->routes);
    }

    public function register(Route $route): void
    {
        if (!empty(array_filter($this->routes, fn (Route $currentRoute) => $currentRoute->equals($route)))) {
            throw new DuplicatedRouteException($route);
        }
        $this->routes[] = $route;
    }

    public function get(RouteMethod $method, string $path): Route
    {
        $matches = array_values(array_filter($this->routes, fn (Route $route) => $route->match($method, $path)));
        if (empty($matches)) {
            throw new RouteDoesNotFoundException($method, $path);
        }
        return $matches[0];
    }

    public function getFromControllerAndAction(string $controller, string $action): ?Route
    {
        $matches = array_values(
            array_filter(
                $this->routes,
                fn (Route $route) => $route->controller === $controller && $route->action === $action
            )
        );

        if (empty($matches)) {
            return null;
        }
        return $matches[0];
    }
}

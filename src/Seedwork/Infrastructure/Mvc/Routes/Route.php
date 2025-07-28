<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Mvc\Routes;

use Seedwork\Infrastructure\Mvc\Controllers\Controller;

final class Route
{
    private const PARAM_PATTERN = '/\{(int:|uuid:|ksuid:|float:)?([^\}]+)\}/';

    /**
     * @param class-string $controller
     */
    private function __construct(
        public readonly RouteMethod $method,
        public readonly Path $path,
        public readonly string $controller,
        public readonly string $action,
    ) {
    }

    public function match(RouteMethod $method, string $path): bool
    {
        return $this->isSameMethod($method) && $this->isEquivalentPath($path);
    }

    private function isSameMethod(RouteMethod $method): bool
    {
        return $this->method === $method;
    }

    private function isEquivalentPath(string $path): bool
    {
        return preg_match($this->getMatchPattern(), $path) ? true : false;
    }

    private function getMatchPattern(): string
    {
        $pattern = preg_replace_callback(Route::PARAM_PATTERN, function ($matches) {
            return match ($matches[1]) {
                'int:' => '(\d+)',
                'uuid:' => '([0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12})',
                'ksuid:' => '([0-9a-zA-Z]{27})',
                default => '([^/]+)',
            };
        }, $this->path->value()) ?? '';
        return '/^' . str_replace('/', '\/', $pattern) . '$/';
    }

    /**
     * @return array<string, string|int|float> Associative array of argument name to argument value
     */
    public function getArgs(string $path): array
    {
        $args = [];
        if (preg_match($this->getMatchPattern(), $path, $matches)) {
            preg_match_all(Route::PARAM_PATTERN, $this->path->value(), $paramNames);
            foreach ($paramNames[2] as $index => $name) {
                $args[$name] = match ($paramNames[1][$index]) {
                    'int:' => (int)$matches[$index + 1],
                    'float:' => (float)$matches[$index + 1],
                    default => $matches[$index + 1],
                };
            }
        }
        return $args;
    }

    public function equals(Route $other): bool
    {
        return $this->method === $other->method &&
            $this->path->equals($other->path) &&
            $this->controller === $other->controller &&
            $this->action === $other->action;
    }

    public function __toString(): string
    {
        return "{$this->method->value} {$this->path}";
    }

    /**
     * @param class-string $controller
     */
    public static function create(
        RouteMethod $method,
        Path $path,
        string $controller,
        string $action,
    ): Route {
        if (!class_exists($controller) || !is_subclass_of($controller, Controller::class)) {
            throw new InvalidController($controller);
        }
        if (!method_exists($controller, $action)) {
            throw new InvalidAction($controller, $action);
        }
        return new Route($method, $path, $controller, $action);
    }
}

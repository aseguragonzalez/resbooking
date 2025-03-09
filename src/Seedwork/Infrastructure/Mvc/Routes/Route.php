<?php

declare(strict_types=1);

namespace App\Seedwork\Infrastructure\Mvc\Routes;

final class Route
{
    private const PARAM_PATTERN = '/\{(int:|uuid:|ksuid:)?([^\}]+)\}/';

    private function __construct(
        public readonly RouteMethod $method,
        public readonly string $path,
        public readonly string $controller,
        public readonly string $action,
        public readonly string $request
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
        }, $this->path) ?? '';
        return '/^' . str_replace('/', '\/', $pattern) . '$/';
    }

    /**
     * @return array<string, string|int> Associative array of argument name to argument value
     */
    public function getArgs(string $path): array
    {
        $args = [];
        if (preg_match($this->getMatchPattern(), $path, $matches)) {
            preg_match_all(Route::PARAM_PATTERN, $this->path, $paramNames);
            foreach ($paramNames[2] as $index => $name) {
                $args[$name] = match ($paramNames[1][$index]) {
                    'int:' => (int)$matches[$index + 1],
                    default => $matches[$index + 1],
                };
            }
        }
        return $args;
    }

    public function equals(Route $other): bool
    {
        return $this->method === $other->method &&
            $this->path === $other->path &&
            $this->controller === $other->controller &&
            $this->action === $other->action &&
            $this->request === $other->request;
    }

    public function __toString(): string
    {
        return "{$this->method->value} {$this->path}";
    }

    public static function create(
        RouteMethod $method,
        string $path,
        string $controller,
        string $action,
        string $request
    ): Route {
        return new Route($method, $path, $controller, $action, $request);
    }
}

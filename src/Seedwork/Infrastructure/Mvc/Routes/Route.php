<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Mvc\Routes;

use Seedwork\Infrastructure\Mvc\Controllers\Controller;
use Seedwork\Infrastructure\Mvc\Security\Identity;

final class Route
{
    private const PARAM_PATTERN = '/\{(int:|uuid:|ksuid:|float:)?([^\}]+)\}/';

    /**
     * @param class-string $controller
     * @param array<string> $roles
     */
    private function __construct(
        public readonly RouteMethod $method,
        public readonly Path $path,
        public readonly string $controller,
        public readonly string $action,
        private readonly bool $authRequired,
        private readonly array $roles,
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

    /**
     * @param array<string, bool|string|int|float|null> $args
     */
    public function getPathFromArgs(array $args): Path
    {
        $rawPath = $this->path->value();
        preg_match_all(self::PARAM_PATTERN, $rawPath, $paramNames);
        $usedNames = $paramNames[2];

        // Replace path parameters using preg_replace_callback
        /** @var string $path */
        $path = preg_replace_callback(
            self::PARAM_PATTERN,
            function ($matches) use ($args) {
                $name = $matches[2];
                if (array_key_exists($name, $args)) {
                    return (string)$args[$name];
                }
                return $matches[0];
            },
            $rawPath
        );

        // Build query string for unused args
        $queryArgs = array_filter(
            $args,
            fn ($key) => !in_array($key, $usedNames, true) && $args[$key] !== null,
            ARRAY_FILTER_USE_KEY
        );

        if (!empty($queryArgs)) {
            $query = array_map(fn ($value) => is_scalar($value) ? (string)$value : '', $queryArgs);
            $path .= '?' . http_build_query($query);
        }

        return Path::create($path);
    }

    public function equals(Route $other): bool
    {
        return $this->method === $other->method &&
            $this->path->equals($other->path) &&
            $this->controller === $other->controller &&
            $this->action === $other->action;
    }

    public function ensureAuthenticated(Identity $identity): void
    {
        if ($this->authRequired && !$identity->isAuthenticated()) {
            throw new AuthenticationRequiredException($this);
        }
    }

    public function ensureAuthorized(Identity $identity): void
    {
        if (empty($this->roles)) {
            return;
        }
        if (!array_intersect($this->roles, $identity->getRoles())) {
            throw new AccessDeniedException($this);
        }
    }

    public function __toString(): string
    {
        return "{$this->method->value} {$this->path}";
    }

    /**
     * @param class-string $controller
     * @param array<string> $roles
     */
    public static function create(
        RouteMethod $method,
        Path $path,
        string $controller,
        string $action,
        bool $authRequired = false,
        array $roles = [],
    ): Route {
        if (!class_exists($controller) || !is_subclass_of($controller, Controller::class)) {
            throw new InvalidController($controller);
        }
        if (!method_exists($controller, $action)) {
            throw new InvalidAction($controller, $action);
        }
        return new Route($method, $path, $controller, $action, $authRequired, $roles);
    }
}

<?php

declare(strict_types=1);

namespace App\Seedwork\Infrastructure\Mvc\Routes;

final class Route
{
    private const PARAM_PATTERN = '/\{(int:|uuid:|ksuid:)?([^\}]+)\}/';

    private function __construct(
        public readonly string $path,
        public readonly string $controller,
        public readonly string $action,
        public readonly string $request
    ) {
    }

    public function match(string $path): bool
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

    public static function create(string $path, string $controller, string $action, string $request): Route
    {
        return new Route($path, $controller, $action, $request);
    }
}

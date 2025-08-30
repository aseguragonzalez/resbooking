<?php

declare(strict_types=1);

namespace Tests\Unit\Seedwork\Infrastructure\Mvc\Routes;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Seedwork\Infrastructure\Mvc\Routes\{InvalidAction, InvalidController, Path, Route, RouteMethod};
use Tests\Unit\Seedwork\Infrastructure\Mvc\Fixtures\Routes\Route\RouteController;

final class RouteTest extends TestCase
{
    /**
     * @param array<string, bool|string|int|float|null> $args
     * @param string $expectedPath
     */
    #[DataProvider('getPathFromArgsProvider')]
    public function testGetPathFromArgs(string $routePath, array $args, string $expectedPath): void
    {
        $route = Route::create(RouteMethod::Get, Path::create($routePath), RouteController::class, 'get');
        $result = $route->getPathFromArgs($args);
        $this->assertSame($expectedPath, $result->value());
    }

    /**
     * @return array<array{string, array<string, bool|string|int|float|null>, string}>
     */
    public static function getPathFromArgsProvider(): array
    {
        return [
            // Simple path param
            [ '/foo/{id}', ['id' => 123], '/foo/123' ],
            // Multiple path params
            [ '/foo/{id}/bar/{name}', ['id' => 1, 'name' => 'alice'], '/foo/1/bar/alice' ],
            // Path param + query param
            [ '/foo/{id}', ['id' => 42, 'extra' => 'test'], '/foo/42?extra=test' ],
            // Multiple query params
            [ '/foo/{id}', ['id' => 42, 'a' => 'x', 'b' => 2], '/foo/42?a=x&b=2' ],
            // Null query param is skipped
            [ '/foo/{id}', ['id' => 42, 'a' => null, 'b' => 'ok'], '/foo/42?b=ok' ],
            // Scalar conversion
            [ '/foo/{id}', ['id' => true, 'q' => false], '/foo/1?q=' ],
            // No params
            [ '/foo', [], '/foo' ],
            // Path param with type
            [ '/foo/{int:id}', ['id' => 7], '/foo/7' ],
            // Path param with float type
            [ '/foo/{float:amount}', ['amount' => 3.14], '/foo/3.14' ],
            // No matching arg for placeholder, should keep original
            [ '/foo/{id}/bar/{missing}', ['id' => 5], '/foo/5/bar/{missing}' ],
        ];
    }

    public function testCreate(): void
    {
        $route = Route::create(RouteMethod::Get, Path::create('/foo'), RouteController::class, 'get');

        $this->assertSame(RouteMethod::Get, $route->method);
        $this->assertSame('/foo', $route->path->value());
        $this->assertSame(RouteController::class, $route->controller);
        $this->assertSame('get', $route->action);
    }

    public function testCreateFailWhenControllerIsInvalid(): void
    {
        $this->expectException(InvalidController::class);
        $this->expectExceptionMessage("Controller " . RouteTest::class . " is not a valid controller");
        Route::create(RouteMethod::Get, Path::create('/foo'), RouteTest::class, 'invalidAction');
    }

    public function testCreateFailWhenActionIsInvalid(): void
    {
        $this->expectException(InvalidAction::class);
        $this->expectExceptionMessage(
            "Action 'invalidAction' is not a valid action for controller " . RouteController::class
        );
        Route::create(RouteMethod::Get, Path::create('/foo'), RouteController::class, 'invalidAction');
    }

    public function testEquals(): void
    {
        $route1 = Route::create(RouteMethod::Get, Path::create('/foo'), RouteController::class, 'get');
        $route2 = Route::create(RouteMethod::Get, Path::create('/foo'), RouteController::class, 'get');
        $route3 = Route::create(RouteMethod::Get, Path::create('/bar'), RouteController::class, 'get');

        $this->assertTrue($route1->equals($route2));
        $this->assertFalse($route1->equals($route3));
    }

    #[DataProvider('routeProvider')]
    public function testMatch(string $path, string $testPath, bool $expected): void
    {
        $route = Route::create(RouteMethod::Get, Path::create($path), RouteController::class, 'get');

        $this->assertTrue($route->match(method: RouteMethod::Get, path: $testPath) === $expected);
    }

    /**
     * @param array<string, string|float|int> $args
     */
    #[DataProvider('argsProvider')]
    public function testGetArgs(string $path, string $testPath, array $args): void
    {
        $route = Route::create(RouteMethod::Get, Path::create($path), RouteController::class, 'get');

        $this->assertSame($args, $route->getArgs($testPath));
    }

    /**
     * @return array<array{string, string, bool}>
     */
    public static function routeProvider(): array
    {
        return [
            ['/foo', '/foo', true],
            ['/bar', '/foo', false],
            ['/foo/{id}', '/foo/1', true],
            ['/foo/{id}', '/foo/1', true],
            ['/foo/{id}', '/foo/name', true],
            ['/foo/{int:id}', '/foo/name', false],
            ['/foo/{int:id}', '/foo/1', true],
            ['/foo/{int:id}/bar', '/foo/1/bar', true],
            ['/foo/{int:id}/bar', '/foo/1/baz', false],
            ['/foo/{int:id}/bar/{name}', '/foo/1/bar/baz', true],
            ['/foo/{int:id}/bar/{name}', '/foo/1/bar/1', true],
            ['/foo/{int:id}/bar/{name}', '/foo/1/baz/1', false],
            ['/foo/{uuid:id}', '/foo/1', false],
            ['/foo/{uuid:id}/bar', '/foo/123e4567-e89b-12d3-a456-426614174000/bar', true],
            ['/foo/{ksuid:id}', '/foo/1', false],
            ['/foo/{ksuid:id}/bar', '/foo/1Bz8dJH3y7d9K3a4Q2w5X6Z7Y8V/bar', true],
            [
                '/foo/{ksuid:id}/bar/{uuid:id2}',
                '/foo/1Bz8dJH3y7d9K3a4Q2w5X6Z7Y8V/bar/123e4567-e89b-12d3-a456-426614174000',
                true
            ],
        ];
    }

    /**
     * @return array<array{string, string, array<string, string|float|int>}>
     */
    public static function argsProvider(): array
    {
        return [
            ['/foo/{id}', '/foo/1', ['id' => '1']],
            ['/foo/{id}/bar', '/foo/1/bar', ['id' => '1']],
            ['/foo/{id}/bar/{float:amount}', '/foo/1/bar/10.01', ['id' => '1', 'amount' => 10.01]],
            ['/foo/{id}/bar/{name}', '/foo/1/bar/peter', ['id' => '1', 'name' => 'peter']],
            ['/foo/{int:id}/bar/{name}', '/foo/1/bar/peter', ['id' => 1, 'name' => 'peter']],
            [
                '/foo/{int:id}/bar/{uuid:token}',
                '/foo/10/bar/123e4567-e89b-12d3-a456-426614174000',
                [ 'id' => 10, 'token' => '123e4567-e89b-12d3-a456-426614174000'],
            ],
        ];
    }
}

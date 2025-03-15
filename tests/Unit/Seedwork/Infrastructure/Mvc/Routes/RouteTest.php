<?php

declare(strict_types=1);

namespace Tests\Unit\Seedwork\Infrastructure\Mvc\Routes;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Seedwork\Infrastructure\Mvc\Routes\{InvalidAction, InvalidController, Path, Route, RouteMethod};
use Tests\Unit\Seedwork\Infrastructure\Mvc\Fixtures\Routes\Route\RouteController;

final class RouteTest extends TestCase
{
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
     * @param array<string, string|int> $args
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
     * @return array<array{string, string, array<string, string|int>}>
     */
    public static function argsProvider(): array
    {
        return [
            ['/foo/{id}', '/foo/1', ['id' => '1']],
            ['/foo/{id}/bar', '/foo/1/bar', ['id' => '1']],
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

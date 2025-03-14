<?php

declare(strict_types=1);

namespace Tests\Unit\Seedwork\Infrastructure\Mvc\Routes;

use PHPUnit\Framework\TestCase;
use Seedwork\Infrastructure\Mvc\Routes\{
    DuplicatedRouteException,
    Path,
    Route,
    RouteMethod,
    Router,
    RouteDoesNotFoundException
};
use Tests\Unit\Seedwork\Infrastructure\Mvc\Fixtures\ExampleController;

final class RouterTest extends TestCase
{
    protected function setUp(): void
    {
    }

    protected function tearDown(): void
    {
    }

    public function testRegister(): void
    {
        $route = Route::create(RouteMethod::Get, Path::create('/test'), ExampleController::class, 'get');
        $router = new Router();

        $router->register($route);

        $this->assertEquals([$route], $router->getRoutes());
    }

    public function testRegisterFailsWhenRouteIsDuplicated(): void
    {
        $route = Route::create(RouteMethod::Get, Path::create('/test'), ExampleController::class, 'get');
        $router = new Router();
        $router->register($route);

        $this->expectException(DuplicatedRouteException::class);
        $this->expectExceptionMessage('Route already registered: GET /test');
        $router->register($route);
    }

    public function testGet(): void
    {
        $route = Route::create(RouteMethod::Get, Path::create('/test'), ExampleController::class, 'get');
        $router = new Router(routes: [
            $route,
            Route::create(RouteMethod::Get, Path::create('/other1'), ExampleController::class, 'get'),
            Route::create(RouteMethod::Get, Path::create('/other2'), ExampleController::class, 'get'),
        ]);

        $this->assertEquals($route, $router->get(RouteMethod::Get, '/test'));
    }

    public function testGetFailsWhenRouteIsNotFound(): void
    {
        $router = new Router();

        $this->expectException(RouteDoesNotFoundException::class);
        $this->expectExceptionMessage('Route not found: GET /test');
        $router->get(RouteMethod::Get, '/test');
    }
}

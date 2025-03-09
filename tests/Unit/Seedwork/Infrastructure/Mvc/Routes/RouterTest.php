<?php

declare(strict_types=1);

namespace Tests\Unit\Seedwork\Infrastructure\Mvc\Routes;

use App\Seedwork\Infrastructure\Mvc\Routes\{
    DuplicatedRouteException,
    Route,
    RouteMethod,
    Router,
    RouteDoesNotFoundException
};
use PHPUnit\Framework\TestCase;

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
        $route = Route::create(RouteMethod::Get, '/test', 'controllerName', 'actionName', 'requestName');
        $router = new Router();

        $router->register($route);

        $this->assertEquals([$route], $router->getRoutes());
    }

    public function testRegisterFailsWhenRouteIsDuplicated(): void
    {
        $route = Route::create(RouteMethod::Get, '/test', 'controllerName', 'actionName', 'requestName');
        $router = new Router();
        $router->register($route);

        $this->expectException(DuplicatedRouteException::class);
        $this->expectExceptionMessage('Route already registered: GET /test');
        $router->register($route);
    }

    public function testGet(): void
    {
        $route = Route::create(RouteMethod::Get, '/test', 'controllerName', 'actionName', 'requestName');
        $router = new Router(routes: [
            $route,
            Route::create(RouteMethod::Get, '/other1', 'controllerName', 'actionName', 'requestName'),
            Route::create(RouteMethod::Get, '/other2', 'controllerName', 'actionName', 'requestName'),
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

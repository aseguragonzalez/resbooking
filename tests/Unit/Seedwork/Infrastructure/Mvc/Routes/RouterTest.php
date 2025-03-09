<?php

declare(strict_types=1);

namespace Tests\Unit\Seedwork\Infrastructure\Mvc\Routes;

use App\Seedwork\Infrastructure\Mvc\Routes\{
    DuplicatedRouteException,
    Route,
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
        $route = Route::create('GET', '/test', 'controllerName', 'actionName', 'requestName');
        $router = new Router();

        $router->register($route);

        $this->assertEquals([$route], $router->getRoutes());
    }

    public function testRegisterFailsWhenRouteIsDuplicated(): void
    {
        $route = Route::create('GET', '/test', 'controllerName', 'actionName', 'requestName');
        $router = new Router();
        $router->register($route);

        $this->expectException(DuplicatedRouteException::class);
        $this->expectExceptionMessage('Route already registered: GET /test');
        $router->register($route);
    }

    public function testGet(): void
    {
        $route = Route::create('GET', '/test', 'controllerName', 'actionName', 'requestName');
        $router = new Router(routes: [
            $route,
            Route::create('GET', '/other1', 'controllerName', 'actionName', 'requestName'),
            Route::create('GET', '/other2', 'controllerName', 'actionName', 'requestName'),
        ]);

        $this->assertEquals($route, $router->get('GET', '/test'));
    }

    public function testGetFailsWhenRouteIsNotFound(): void
    {
        $router = new Router();

        $this->expectException(RouteDoesNotFoundException::class);
        $this->expectExceptionMessage('Route not found: GET /test');
        $router->get('GET', '/test');
    }
}

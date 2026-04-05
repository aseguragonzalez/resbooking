<?php

declare(strict_types=1);

namespace Tests\Unit\Framework\Web\Routes;

use PHPUnit\Framework\TestCase;
use Framework\Web\Routes\DuplicatedRouteException;
use Framework\Web\Routes\Path;
use Framework\Web\Routes\Route;
use Framework\Web\Routes\RouteMethod;
use Framework\Web\Routes\Router;
use Framework\Web\Routes\RouteDoesNotFoundException;
use Tests\Unit\Framework\Fixtures\Routes\Router\RouterController;

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
        $route = Route::create(RouteMethod::Get, Path::create('/test'), RouterController::class, 'get');
        $router = new Router();

        $router->register($route);

        $this->assertEquals([$route], $router->getRoutes());
    }

    public function testRegisterFailsWhenRouteIsDuplicated(): void
    {
        $route = Route::create(RouteMethod::Get, Path::create('/test'), RouterController::class, 'get');
        $router = new Router();
        $router->register($route);

        $this->expectException(DuplicatedRouteException::class);
        $this->expectExceptionMessage('Route already registered: GET /test');
        $router->register($route);
    }

    public function testGet(): void
    {
        $route = Route::create(RouteMethod::Get, Path::create('/test'), RouterController::class, 'get');
        $router = new Router(routes: [
            $route,
            Route::create(RouteMethod::Get, Path::create('/other1'), RouterController::class, 'get'),
            Route::create(RouteMethod::Get, Path::create('/other2'), RouterController::class, 'get'),
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

    public function testGetFromControllerAndActionReturnsRoute(): void
    {
        $route = Route::create(RouteMethod::Get, Path::create('/test'), RouterController::class, 'get');
        $router = new Router(routes: [
            $route,
            Route::create(RouteMethod::Get, Path::create('/other1'), RouterController::class, 'get'),
        ]);

        $result = $router->getFromControllerAndAction(RouterController::class, 'get');
        $this->assertEquals($route, $result);
    }

    public function testGetFromControllerAndActionReturnsNullWhenNotFound(): void
    {
        $router = new Router(routes: [
            Route::create(RouteMethod::Get, Path::create('/other1'), RouterController::class, 'get'),
        ]);

        $result = $router->getFromControllerAndAction(RouterController::class, 'nonexistent');
        $this->assertNull($result);
    }
}

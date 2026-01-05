<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Ports\Dashboard\Controllers;

use Infrastructure\Ports\Dashboard\Controllers\DashboardController;
use Infrastructure\Ports\Dashboard\Middlewares\RestaurantContextSettings;
use PHPUnit\Framework\TestCase;
use Seedwork\Infrastructure\Mvc\Actions\Responses\View;
use Seedwork\Infrastructure\Mvc\Requests\RequestContext;
use Seedwork\Infrastructure\Mvc\Routes\RouteMethod;

final class DashboardControllerTest extends TestCase
{
    private RequestContext $requestContext;
    private RestaurantContextSettings $settings;
    private DashboardController $controller;

    protected function setUp(): void
    {
        $this->requestContext = new RequestContext();
        $this->settings = new RestaurantContextSettings();
        $this->controller = new DashboardController(
            $this->requestContext,
            $this->settings,
        );
    }

    public function testIndexReturnsDashboardView(): void
    {
        $response = $this->controller->index();
        $this->assertEquals(200, $response->statusCode->value);
        $this->assertInstanceOf(View::class, $response);
        /** @var View $view */
        $view = $response;
        $this->assertEquals('Dashboard/index', $view->viewPath);
        $this->assertObjectHasProperty('pageTitle', $view->data);
        $this->assertObjectHasProperty('model', $view->data);
    }

    public function testGetRoutesConfiguration(): void
    {
        $routes = DashboardController::getRoutes();

        $this->assertCount(1, $routes);
        $route = $routes[0];
        $this->assertEquals(RouteMethod::Get->name, $route->method->name);
        $this->assertEquals('/', $route->path->value());
        $this->assertEquals(DashboardController::class, $route->controller);
        $this->assertEquals('index', $route->action);
    }
}

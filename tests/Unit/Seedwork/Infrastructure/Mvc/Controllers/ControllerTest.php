<?php

declare(strict_types=1);

namespace Tests\Unit\Seedwork\Infrastructure\Mvc;

use PHPUnit\Framework\TestCase;
use Seedwork\Infrastructure\Mvc\Controllers\LocalRedirectTo;
use Seedwork\Infrastructure\Mvc\Responses\StatusCode;
use Seedwork\Infrastructure\Mvc\Views\View;
use Tests\Unit\Seedwork\Infrastructure\Mvc\Fixtures\ExampleController;

final class ControllerTest extends TestCase
{
    private ExampleController $controller;

    protected function setUp(): void
    {
        $this->controller = new ExampleController();
    }

    protected function tearDown(): void
    {
    }

    public function testGetDefaultView(): void
    {
        $view = $this->controller->getDefaultView();

        $this->assertSame(StatusCode::Ok, $view->statusCode);
        $this->assertSame([], $view->headers);
        $this->assertInstanceOf(\stdClass::class, $view->data);
        if ($view instanceof View) {
            $this->assertSame('Example/getDefaultView', $view->viewPath);
        }
    }

    public function testGetViewByName(): void
    {
        $viewName = "index";

        $view = $this->controller->getCustomView($viewName);

        if ($view instanceof View) {
            $this->assertSame("Example/{$viewName}", $view->viewPath);
        }
    }

    public function testGetViewWithStatusCode(): void
    {
        $statusCode = StatusCode::NotFound;

        $view = $this->controller->getCustomStatusCode($statusCode);

        $this->assertSame($statusCode, $view->statusCode);
    }

    public function testGetViewWithModel(): void
    {
        $model = new \stdClass();
        $model->name = "John Doe";

        $view = $this->controller->getCustomModel($model);

        $this->assertSame($model, $view->data);
    }

    public function testRedirectToAction(): void
    {
        $action = 'index';
        $args = new \stdClass();
        $args->offset = 1;
        $args->limit = 10;

        $response = $this->controller->customRedirectToAction($action, $args);

        $this->assertSame(StatusCode::Found, $response->statusCode);
        $this->assertSame([], $response->headers);
        $this->assertInstanceOf(\stdClass::class, $response->data);
        $this->assertSame($args, $response->data);

        if ($response instanceof LocalRedirectTo) {
            $this->assertSame('ExampleController', $response->controller);
            $this->assertSame($action, $response->action);
        }
    }

    public function testRedirectToActionWithController(): void
    {
        $controller = 'HomeController';
        $action = 'index';
        $args = new \stdClass();
        $args->offset = 1;
        $args->limit = 10;

        $response = $this->controller->customRedirectToControllerAction(
            controller: $controller,
            action: $action,
            args: $args
        );

        $this->assertSame(StatusCode::Found, $response->statusCode);
        $this->assertSame([], $response->headers);
        $this->assertInstanceOf(\stdClass::class, $response->data);
        $this->assertSame($args, $response->data);

        if ($response instanceof LocalRedirectTo) {
            $this->assertSame($controller, $response->controller);
            $this->assertSame($action, $response->action);
        }
    }

    public function testRedirectToUrl(): void
    {
        $url = 'https://example.com';
        $args = new \stdClass();
        $args->offset = 1;
        $args->limit = 10;

        $response = $this->controller->customRedirectToUrl($url, $args);

        $this->assertSame(StatusCode::Found, $response->statusCode);
        $this->assertSame(['Location' => "{$url}?offset={$args->offset}&limit={$args->limit}"], $response->headers);
    }

    public function testRedirectToUrlWithInvalidUrl(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $url = '/home';
        $args = new \stdClass();
        $args->offset = 1;
        $args->limit = 10;

        $this->controller->customRedirectToUrl($url, $args);
    }
}

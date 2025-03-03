<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Mvc;

use PHPUnit\Framework\TestCase;
use Seedwork\Infrastructure\Mvc\{StatusCode, ViewResponse, LocalRedirectToResponse};

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

        $this->assertEquals(StatusCode::Ok, $view->statusCode);
        $this->assertEquals([], $view->headers);
        $this->assertInstanceOf(\stdClass::class, $view->data);
        if ($view instanceof ViewResponse) {
            $this->assertEquals('Example/getDefaultView', $view->viewPath);
        }
    }

    public function testGetViewByName(): void
    {
        $viewName = "index";

        $view = $this->controller->getCustomView($viewName);

        if ($view instanceof ViewResponse) {
            $this->assertEquals("Example/{$viewName}", $view->viewPath);
        }
    }

    public function testGetViewWithStatusCode(): void
    {
        $statusCode = StatusCode::NotFound;

        $view = $this->controller->getCustomStatusCode($statusCode);

        $this->assertEquals($statusCode, $view->statusCode);
    }

    public function testGetViewWithModel(): void
    {
        $model = new \stdClass();
        $model->name = "John Doe";

        $view = $this->controller->getCustomModel($model);

        $this->assertEquals($model, $view->data);
    }

    public function testRedirectToAction(): void
    {
        $action = 'index';
        $args = new \stdClass();
        $args->offset = 1;
        $args->limit = 10;

        $response = $this->controller->customRedirectToAction($action, $args);

        $this->assertEquals(StatusCode::Found, $response->statusCode);
        $this->assertEquals([], $response->headers);
        $this->assertInstanceOf(\stdClass::class, $response->data);
        $this->assertEquals($args, $response->data);

        if ($response instanceof LocalRedirectToResponse) {
            $this->assertEquals('ExampleController', $response->controller);
            $this->assertEquals($action, $response->action);
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

        $this->assertEquals(StatusCode::Found, $response->statusCode);
        $this->assertEquals([], $response->headers);
        $this->assertInstanceOf(\stdClass::class, $response->data);
        $this->assertEquals($args, $response->data);

        if ($response instanceof LocalRedirectToResponse) {
            $this->assertEquals($controller, $response->controller);
            $this->assertEquals($action, $response->action);
        }
    }

    public function testRedirectToUrl(): void
    {
        $url = 'https://example.com';
        $args = new \stdClass();
        $args->offset = 1;
        $args->limit = 10;

        $response = $this->controller->customRedirectToUrl($url, $args);

        $this->assertEquals(StatusCode::Found, $response->statusCode);
        $this->assertEquals(['Location' => "{$url}?offset={$args->offset}&limit={$args->limit}"], $response->headers);
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

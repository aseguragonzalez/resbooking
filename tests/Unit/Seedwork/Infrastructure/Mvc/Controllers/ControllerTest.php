<?php

declare(strict_types=1);

namespace Tests\Unit\Seedwork\Infrastructure\Mvc\Controllers;

use PHPUnit\Framework\TestCase;
use Seedwork\Infrastructure\Mvc\Controllers\LocalRedirectTo;
use Seedwork\Infrastructure\Mvc\Responses\Headers\{AccessControlAllowMethods, ContentType, Location};
use Seedwork\Infrastructure\Mvc\Responses\StatusCode;
use Seedwork\Infrastructure\Mvc\Views\View;
use Tests\Unit\Seedwork\Infrastructure\Mvc\Fixtures\Controllers\TestController;

final class ControllerTest extends TestCase
{
    private TestController $controller;

    protected function setUp(): void
    {
        $this->controller = new TestController();
    }

    protected function tearDown(): void
    {
    }

    public function testGetDefaultView(): void
    {
        $view = $this->controller->getDefaultView();

        $this->assertSame(StatusCode::Ok, $view->statusCode);
        $this->assertEquals([ContentType::html()], $view->headers);
        $this->assertInstanceOf(\stdClass::class, $view->data);
        if ($view instanceof View) {
            $this->assertSame('Test/getDefaultView', $view->viewPath);
        }
    }

    public function testGetViewByName(): void
    {
        $viewName = "index";

        $view = $this->controller->getCustomView($viewName);

        if ($view instanceof View) {
            $this->assertSame("Test/{$viewName}", $view->viewPath);
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
            $this->assertSame('TestController', $response->controller);
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
        $this->assertEquals(
            [Location::new("{$url}?offset={$args->offset}&limit={$args->limit}"), ContentType::html()],
            $response->headers
        );
    }

    public function testRedirectToUrlWithInvalidUrl(): void
    {
        $url = '/home';
        $args = new \stdClass();
        $args->offset = 1;
        $args->limit = 10;
        $this->expectException(\InvalidArgumentException::class);

        $this->controller->customRedirectToUrl($url, $args);
    }

    public function testAddHeaders(): void
    {
        $header = new AccessControlAllowMethods(
            get: true,
            post: true,
            put: false,
            delete: false,
            options: false,
            head: false,
            patch: false,
            connect: false,
            trace: false
        );

        $response = $this->controller->customHeader($header);

        $this->assertSame(StatusCode::Ok, $response->statusCode);
        $this->assertEquals([$header, ContentType::html()], $response->headers);
    }
}

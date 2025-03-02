<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Mvc;

use PHPUnit\Framework\TestCase;
use Seedwork\Infrastructure\Mvc\{StatusCode, ViewResponse};

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

    public function testControllerShouldUseDefaultValues(): void
    {
        $view = $this->controller->getDefaultView();

        $this->assertEquals(StatusCode::Ok, $view->statusCode);
        $this->assertEquals([], $view->headers);
        $this->assertInstanceOf(\stdClass::class, $view->data);
        if ($view instanceof ViewResponse) {
            $this->assertEquals('Example/getDefaultView', $view->name);
        }
    }

    public function testControllerShouldUseCustomViewName(): void
    {
        $viewName = "index";

        $view = $this->controller->getCustomView($viewName);

        if ($view instanceof ViewResponse) {
            $this->assertEquals("Example/{$viewName}", $view->name);
        }
    }

    public function testControllerShouldUseCustomStatusCode(): void
    {
        $statusCode = StatusCode::NotFound;

        $view = $this->controller->getCustomStatusCode($statusCode);

        $this->assertEquals($statusCode, $view->statusCode);
    }

    public function testControllerShouldUseCustomModel(): void
    {
        $model = new \stdClass();
        $model->name = "John Doe";

        $view = $this->controller->getCustomModel($model);

        $this->assertEquals($model, $view->data);
    }
}

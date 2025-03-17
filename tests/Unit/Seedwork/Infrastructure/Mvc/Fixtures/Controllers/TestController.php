<?php

declare(strict_types=1);

namespace Tests\Unit\Seedwork\Infrastructure\Mvc\Fixtures\Controllers;

use Seedwork\Infrastructure\Mvc\Actions\Responses\ActionResponse;
use Seedwork\Infrastructure\Mvc\Controllers\Controller;
use Seedwork\Infrastructure\Mvc\Responses\Headers\Header;
use Seedwork\Infrastructure\Mvc\Responses\StatusCode;

class TestController extends Controller
{
    public function index(): ActionResponse
    {
        return $this->view();
    }

    public function getDefaultView(): ActionResponse
    {
        return $this->view();
    }

    public function getCustomView(string $viewName): ActionResponse
    {
        return $this->view($viewName);
    }

    public function getCustomStatusCode(StatusCode $statusCode): ActionResponse
    {
        return $this->view(statusCode: $statusCode);
    }

    public function getCustomModel(object $model): ActionResponse
    {
        return $this->view(model: $model);
    }

    public function customRedirectToAction(string $action, object $args): ActionResponse
    {
        return $this->redirectToAction($action, args: $args);
    }

    /**
     * @param class-string $controller
     */
    public function redirectToControllerAction(string $controller, string $action, object $args): ActionResponse
    {
        return $this->redirectToAction($action, $controller, $args);
    }

    /**
     * @param array<string, mixed> $args
     */
    public function customRedirectToUrl(string $url, array $args): ActionResponse
    {
        return $this->redirectTo(url: $url, args: $args);
    }

    public function customHeader(Header $header): ActionResponse
    {
        $this->addHeader($header);

        return $this->view();
    }
}

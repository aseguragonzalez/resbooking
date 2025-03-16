<?php

declare(strict_types=1);

namespace Tests\Unit\Seedwork\Infrastructure\Mvc\Fixtures\Controllers;

use Seedwork\Infrastructure\Mvc\Actions\Responses\ActionResponse;
use Seedwork\Infrastructure\Mvc\Controllers\Controller;
use Seedwork\Infrastructure\Mvc\Responses\Headers\Header;
use Seedwork\Infrastructure\Mvc\Responses\StatusCode;

final class TestController extends Controller
{
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

    public function customRedirectToControllerAction(string $controller, string $action, object $args): ActionResponse
    {
        return $this->redirectToAction(action: $action, controller: $controller, args: $args);
    }

    public function customRedirectToUrl(string $url, object $args): ActionResponse
    {
        return $this->redirectTo(url: $url, args: $args);
    }

    public function customHeader(Header $header): ActionResponse
    {
        $this->addHeader($header);

        return $this->view();
    }
}

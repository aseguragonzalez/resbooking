<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Mvc;

use Seedwork\Infrastructure\Mvc\{Controller, StatusCode, Response};

class ExampleController extends Controller
{
    public function getDefaultView(): Response
    {
        return $this->view();
    }

    public function getCustomView(string $viewName): Response
    {
        return $this->view($viewName);
    }

    public function getCustomStatusCode(StatusCode $statusCode): Response
    {
        return $this->view(statusCode: $statusCode);
    }

    public function getCustomModel(object $model): Response
    {
        return $this->view(model: $model);
    }

    public function customRedirectToAction(string $action, object $args): Response
    {
        return $this->redirectToAction($action, args: $args);
    }

    public function customRedirectToControllerAction(string $controller, string $action, object $args): Response
    {
        return $this->redirectToAction(action: $action, controller: $controller, args: $args);
    }

    public function customRedirectToUrl(string $url, object $args): Response
    {
        return $this->redirectTo(url: $url, args: $args);
    }
}

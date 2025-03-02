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
}

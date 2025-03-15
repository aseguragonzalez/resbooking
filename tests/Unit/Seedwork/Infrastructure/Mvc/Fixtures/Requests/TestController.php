<?php

declare(strict_types=1);

namespace Tests\Unit\Seedwork\Infrastructure\Mvc\Fixtures\Requests;

use Seedwork\Infrastructure\Mvc\Controllers\Controller;
use Seedwork\Infrastructure\Mvc\Responses\Response;

final class TestController extends Controller
{
    public function index(): Response
    {
        return $this->view();
    }

    public function find(FindRequest $request): Response
    {
        return $this->view(model: $request);
    }

    public function list(ListRequest $request): Response
    {
        return $this->view(model: $request);
    }

    public function save(): Response
    {
        return $this->view();
    }
}

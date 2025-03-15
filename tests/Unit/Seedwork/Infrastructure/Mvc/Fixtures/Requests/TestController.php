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

    public function edit(EditRequest $request): Response
    {
        return $this->view(model: $request);
    }

    public function save(EditRequest $request, FindRequest $query): Response
    {
        $model = new \stdClass();
        $model->name = $request->name;
        $model->email = $request->email;
        $model->id = $request->id;
        $model->offset = $query->offset;
        $model->limit = $query->limit;
        return $this->view(model: $model);
    }
}

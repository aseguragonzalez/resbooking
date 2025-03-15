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

    public function get(int $offset, int $limit): Response
    {
        $model = new \stdClass();
        $model->offset = $offset;
        $model->limit = $limit;
        return $this->view(model: $model);
    }

    public function search(int $offset, int $limit, SearchRequest $request): Response
    {
        $model = new \stdClass();
        $model->offset = $offset;
        $model->limit = $limit;
        $model->name = $request->name;
        $model->email = $request->email;
        return $this->view(model: $model);
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

    public function delete(): Response
    {
        return $this->view();
    }
}

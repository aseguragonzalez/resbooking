<?php

declare(strict_types=1);

namespace Tests\Unit\Seedwork\Infrastructure\Mvc\Fixtures\Requests;

use Seedwork\Infrastructure\Mvc\Actions\Responses\ActionResponse;
use Seedwork\Infrastructure\Mvc\Controllers\Controller;

final class TestController extends Controller
{
    public function index(): ActionResponse
    {
        return $this->view();
    }

    public function redirect(): ActionResponse
    {
        return $this->redirectTo('http://test.com');
    }

    public function get(int $offset, int $limit): ActionResponse
    {
        $model = new \stdClass();
        $model->offset = $offset;
        $model->limit = $limit;
        return $this->view(model: $model);
    }

    public function search(int $offset, int $limit, SearchRequest $request): ActionResponse
    {
        $model = new \stdClass();
        $model->offset = $offset;
        $model->limit = $limit;
        $model->name = $request->name;
        $model->email = $request->email;
        return $this->view(model: $model);
    }

    public function find(FindRequest $request): ActionResponse
    {
        return $this->view(model: $request);
    }

    public function list(ListRequest $request): ActionResponse
    {
        return $this->view(model: $request);
    }

    public function edit(EditRequest $request): ActionResponse
    {
        return $this->view(model: $request);
    }

    public function save(EditRequest $request, FindRequest $query): ActionResponse
    {
        $model = new \stdClass();
        $model->name = $request->name;
        $model->email = $request->email;
        $model->id = $request->id;
        $model->offset = $query->offset;
        $model->limit = $query->limit;
        return $this->view(model: $model);
    }

    public function delete(): ActionResponse
    {
        return $this->view();
    }
}

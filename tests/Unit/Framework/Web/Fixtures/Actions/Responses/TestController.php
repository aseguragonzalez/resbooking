<?php

declare(strict_types=1);

namespace Tests\Unit\Framework\Fixtures\Actions\Responses;

use Framework\Actions\MvcAction;
use Framework\Actions\Responses\ActionResponse;
use Framework\Controllers\Controller;

final class TestController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    #[MvcAction]
    public function index(): ActionResponse
    {
        return $this->view('Test/index');
    }

    #[MvcAction]
    public function list(int $offset, int $limit): ActionResponse
    {
        $model = new \stdClass();
        $model->offset = $offset;
        $model->limit = $limit;

        return $this->view('Test/list', model: $model);
    }

    #[MvcAction]
    public function search(int $offset, int $limit, SearchRequest $request): ActionResponse
    {
        $model = new \stdClass();
        $model->offset = $offset;
        $model->limit = $limit;
        $model->request = $request;

        return $this->view('Test/search', model: $model);
    }
}

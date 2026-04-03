<?php

declare(strict_types=1);

namespace Tests\Unit\Framework\Mvc\Fixtures\Routes\Router;

use Framework\Mvc\Actions\Responses\ActionResponse;
use Framework\Mvc\Controllers\Controller;

final class RouterController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get(): ActionResponse
    {
        return $this->view();
    }
}

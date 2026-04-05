<?php

declare(strict_types=1);

namespace Tests\Unit\Framework\Fixtures\Routes\Router;

use Framework\Actions\MvcAction;
use Framework\Actions\Responses\ActionResponse;
use Framework\Controllers\Controller;

final class RouterController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    #[MvcAction]
    public function get(): ActionResponse
    {
        return $this->view('Router/get');
    }
}

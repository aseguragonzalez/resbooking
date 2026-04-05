<?php

declare(strict_types=1);

namespace Tests\Unit\Framework\Fixtures\Routes\Route;

use Framework\Web\Actions\MvcAction;
use Framework\Web\Actions\Responses\ActionResponse;
use Framework\Web\Controllers\Controller;

final class RouteController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    #[MvcAction]
    public function get(): ActionResponse
    {
        return $this->view('Route/get');
    }
}

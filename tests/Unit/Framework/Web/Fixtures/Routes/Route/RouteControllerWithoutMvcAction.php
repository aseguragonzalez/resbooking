<?php

declare(strict_types=1);

namespace Tests\Unit\Framework\Fixtures\Routes\Route;

use Framework\Web\Actions\Responses\ActionResponse;
use Framework\Web\Controllers\Controller;

/**
 * Intentionally no #[MvcAction] on get() for Route::create validation tests.
 */
final class RouteControllerWithoutMvcAction extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get(): ActionResponse
    {
        return $this->view('Route/get');
    }
}

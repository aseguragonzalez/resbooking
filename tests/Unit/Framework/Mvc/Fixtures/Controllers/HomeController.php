<?php

declare(strict_types=1);

namespace Tests\Unit\Framework\Mvc\Fixtures\Controllers;

use Framework\Mvc\Actions\Responses\ActionResponse;
use Framework\Mvc\Controllers\Controller;

final class HomeController extends Controller
{
    public function index(): ActionResponse
    {
        return $this->view();
    }
}

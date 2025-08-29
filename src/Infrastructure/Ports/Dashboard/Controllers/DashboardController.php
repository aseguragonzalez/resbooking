<?php

declare(strict_types=1);

namespace Infrastructure\Ports\Dashboard\Controllers;

use Seedwork\Infrastructure\Mvc\Actions\Responses\ActionResponse;
use Seedwork\Infrastructure\Mvc\Controllers\Controller;

class DashboardController extends Controller
{
    public function index(): ActionResponse
    {
        return $this->view(model: (object)[
            'pageTitle' => 'Dashboard',
            'model' => (object)[
                'title' => 'Welcome to the Dashboard',
            ],
        ]);
    }
}

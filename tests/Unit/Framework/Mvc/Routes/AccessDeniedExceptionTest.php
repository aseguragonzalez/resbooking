<?php

declare(strict_types=1);

namespace Tests\Unit\Framework\Mvc\Routes;

use Framework\Mvc\Routes\AccessDeniedException;
use Framework\Mvc\Routes\Path;
use Framework\Mvc\Routes\Route;
use Framework\Mvc\Routes\RouteMethod;
use PHPUnit\Framework\TestCase;
use Tests\Unit\Framework\Mvc\Fixtures\Routes\Route\RouteController;

final class AccessDeniedExceptionTest extends TestCase
{
    public function testExceptionHasExpectedMessage(): void
    {
        $route = Route::create(
            RouteMethod::Get,
            Path::create('/admin/users'),
            RouteController::class,
            'get',
            false,
            []
        );

        $exception = new AccessDeniedException($route);

        $this->assertStringContainsString('Access denied for route:', $exception->getMessage());
        $this->assertStringContainsString('GET /admin/users', $exception->getMessage());
    }
}

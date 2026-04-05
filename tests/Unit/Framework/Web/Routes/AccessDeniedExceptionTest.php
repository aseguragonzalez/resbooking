<?php

declare(strict_types=1);

namespace Tests\Unit\Framework\Routes;

use Framework\Routes\AccessDeniedException;
use Framework\Routes\Path;
use Framework\Routes\Route;
use Framework\Routes\RouteMethod;
use PHPUnit\Framework\TestCase;
use Tests\Unit\Framework\Fixtures\Routes\Route\RouteController;

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

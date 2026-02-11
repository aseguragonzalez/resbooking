<?php

declare(strict_types=1);

namespace Tests\Unit\Framework\Mvc\Routes;

use Framework\Mvc\Routes\AuthenticationRequiredException;
use Framework\Mvc\Routes\Path;
use Framework\Mvc\Routes\Route;
use Framework\Mvc\Routes\RouteMethod;
use PHPUnit\Framework\TestCase;
use Tests\Unit\Framework\Mvc\Fixtures\Routes\Route\RouteController;

final class AuthenticationRequiredExceptionTest extends TestCase
{
    public function testExceptionHasExpectedMessage(): void
    {
        $route = Route::create(
            RouteMethod::Get,
            Path::create('/dashboard'),
            RouteController::class,
            'get',
            false,
            []
        );

        $exception = new AuthenticationRequiredException($route);

        $this->assertStringContainsString('Authentication required for route:', $exception->getMessage());
        $this->assertStringContainsString('GET /dashboard', $exception->getMessage());
    }
}

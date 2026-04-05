<?php

declare(strict_types=1);

namespace Tests\Unit\Framework\Web\Routes;

use Framework\Web\Routes\AuthenticationRequiredException;
use Framework\Web\Routes\Path;
use Framework\Web\Routes\Route;
use Framework\Web\Routes\RouteMethod;
use PHPUnit\Framework\TestCase;
use Tests\Unit\Framework\Fixtures\Routes\Route\RouteController;

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

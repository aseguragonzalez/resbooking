<?php

declare(strict_types=1);

namespace Tests\Unit\Framework\Routes;

use Framework\Routes\DuplicatedRouteException;
use Framework\Routes\Path;
use Framework\Routes\Route;
use Framework\Routes\RouteMethod;
use PHPUnit\Framework\TestCase;
use Tests\Unit\Framework\Fixtures\Routes\Route\RouteController;

final class DuplicatedRouteExceptionTest extends TestCase
{
    public function testExceptionHasExpectedMessage(): void
    {
        $route = Route::create(
            RouteMethod::Post,
            Path::create('/api/resource'),
            RouteController::class,
            'get',
            false,
            []
        );

        $exception = new DuplicatedRouteException($route);

        $this->assertStringContainsString('Route already registered:', $exception->getMessage());
        $this->assertStringContainsString('POST /api/resource', $exception->getMessage());
    }
}

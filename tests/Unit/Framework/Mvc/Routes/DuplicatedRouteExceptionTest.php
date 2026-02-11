<?php

declare(strict_types=1);

namespace Tests\Unit\Framework\Mvc\Routes;

use Framework\Mvc\Routes\DuplicatedRouteException;
use Framework\Mvc\Routes\Path;
use Framework\Mvc\Routes\Route;
use Framework\Mvc\Routes\RouteMethod;
use PHPUnit\Framework\TestCase;
use Tests\Unit\Framework\Mvc\Fixtures\Routes\Route\RouteController;

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

<?php

declare(strict_types=1);

namespace Tests\Unit\Framework\Web\Routes;

use Framework\Web\Routes\DuplicatedRouteException;
use Framework\Web\Routes\Path;
use Framework\Web\Routes\Route;
use Framework\Web\Routes\RouteMethod;
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

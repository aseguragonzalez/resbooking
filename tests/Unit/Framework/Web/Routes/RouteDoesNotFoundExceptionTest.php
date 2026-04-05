<?php

declare(strict_types=1);

namespace Tests\Unit\Framework\Web\Routes;

use Framework\Web\Routes\Path;
use Framework\Web\Routes\RouteDoesNotFoundException;
use Framework\Web\Routes\RouteMethod;
use PHPUnit\Framework\TestCase;

final class RouteDoesNotFoundExceptionTest extends TestCase
{
    public function testExceptionHasExpectedMessage(): void
    {
        $exception = new RouteDoesNotFoundException(RouteMethod::Get, '/unknown/path');

        $this->assertSame('Route not found: GET /unknown/path', $exception->getMessage());
    }

    public function testExceptionIncludesMethodAndPath(): void
    {
        $exception = new RouteDoesNotFoundException(RouteMethod::Post, '/api/v2/resource');

        $this->assertStringContainsString('POST', $exception->getMessage());
        $this->assertStringContainsString('/api/v2/resource', $exception->getMessage());
    }
}

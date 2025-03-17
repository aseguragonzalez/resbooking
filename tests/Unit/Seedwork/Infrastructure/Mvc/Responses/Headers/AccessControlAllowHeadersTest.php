<?php

declare(strict_types=1);

namespace Tests\Unit\Seedwork\Infrastructure\Mvc\Responses\Headers;

use PHPUnit\Framework\TestCase;
use Seedwork\Infrastructure\Mvc\Responses\Headers\AccessControlAllowHeaders;

final class AccessControlAllowHeadersTest extends TestCase
{
    public function testDefaultHeaders(): void
    {
        $header = new AccessControlAllowHeaders();

        $this->assertSame('Access-Control-Allow-Headers', $header->name);
        $this->assertSame('*', $header->value);
    }

    public function testCustomHeaders(): void
    {
        $header = new AccessControlAllowHeaders(
            contentTypes: false,
            authorization: false,
            accept: false,
            xRequestedWith: false,
            origin: false,
            cacheControl: false,
            contentLength: false,
            acceptEncoding: false,
            acceptLanguage: false,
            userAgent: false
        );

        $this->assertSame('Access-Control-Allow-Headers', $header->name);
        $this->assertSame('', $header->value);
    }

    public function testPartialHeaders(): void
    {
        $header = new AccessControlAllowHeaders(
            accept: false,
            xRequestedWith: false,
            origin: false,
            cacheControl: false,
            contentLength: false,
            acceptEncoding: false,
            acceptLanguage: false
        );

        $this->assertSame('Access-Control-Allow-Headers', $header->name);
        $this->assertStringContainsString('Content-Type', $header->value);
        $this->assertStringContainsString('Authorization', $header->value);
    }

    public function testSingleHeader(): void
    {
        $header = new AccessControlAllowHeaders(
            contentTypes: false,
            authorization: false,
            accept: false,
            xRequestedWith: false,
            cacheControl: false,
            contentLength: false,
            acceptEncoding: false,
            acceptLanguage: false,
            userAgent: false
        );

        $this->assertSame('Access-Control-Allow-Headers', $header->name);
        $this->assertSame('Origin', $header->value);
    }

    public function testToString(): void
    {
        $header = new AccessControlAllowHeaders(
            contentTypes: true,
            authorization: true,
            accept: false,
            xRequestedWith: false,
            origin: true,
            cacheControl: false,
            contentLength: false,
            acceptEncoding: false,
            acceptLanguage: false,
            userAgent: false
        );

        $this->assertSame('Access-Control-Allow-Headers: Content-Type, Authorization, Origin', (string) $header);
    }
}

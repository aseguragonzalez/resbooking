<?php

declare(strict_types=1);

namespace Tests\Unit\Framework\Mvc\Web\Config;

use Framework\Mvc\Config\PublicApplicationUrl;
use PHPUnit\Framework\TestCase;

final class PublicApplicationUrlTest extends TestCase
{
    public function testOriginIsTrimmedWithoutTrailingSlash(): void
    {
        $url = new PublicApplicationUrl('https://app.example.com/');
        $this->assertSame('https://app.example.com', $url->origin());
    }

    public function testAbsoluteUrlForPath(): void
    {
        $url = new PublicApplicationUrl('https://app.example.com');
        $this->assertSame('https://app.example.com/foo/bar', $url->absoluteUrlForPath('/foo/bar'));
        $this->assertSame('https://app.example.com/foo', $url->absoluteUrlForPath('foo'));
    }

    public function testEmptyStringThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new PublicApplicationUrl('');
    }

    public function testNonHttpSchemeThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new PublicApplicationUrl('ftp://example.com');
    }

    public function testPathSegmentThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new PublicApplicationUrl('https://example.com/dashboard');
    }
}

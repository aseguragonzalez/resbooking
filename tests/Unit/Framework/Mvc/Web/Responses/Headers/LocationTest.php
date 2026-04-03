<?php

declare(strict_types=1);

namespace Tests\Unit\Framework\Mvc\Responses\Headers;

use PHPUnit\Framework\TestCase;
use Framework\Mvc\Responses\Headers\Location;

class LocationTest extends TestCase
{
    public function testLocationHeaderIsSetCorrectly(): void
    {
        $url = 'https://example.com';
        $location = Location::toUrl(url: $url);

        $this->assertSame('Location', $location->name);
        $this->assertSame($url, $location->value);
    }

    public function testLocationHeaderWithEmptyUrl(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Location::toUrl('');
    }

    public function testLocationHeaderWithSpecialCharacters(): void
    {
        $url = 'https://example.com/path?query=param&another=param';
        $location = Location::toUrl(url: $url);

        $this->assertSame('Location', $location->name);
        $this->assertSame($url, $location->value);
    }

    public function testLocationHeaderToString(): void
    {
        $url = 'https://example.com';
        $location = Location::toUrl(url: $url);

        $this->assertSame('Location: https://example.com', (string) $location);
    }

    public function testLocationHeaderThrowsExceptionForInvalidUrl(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Location::toUrl('ftp://invalid-url.com');
    }
}

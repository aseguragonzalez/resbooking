<?php

declare(strict_types=1);

namespace Tests\Unit\Seedwork\Infrastructure\Mvc\Responses\Headers;

use PHPUnit\Framework\TestCase;
use Seedwork\Infrastructure\Mvc\Responses\Headers\Location;

class LocationTest extends TestCase
{
    public function testLocationHeaderIsSetCorrectly(): void
    {
        $url = 'https://example.com';
        $location = new Location($url);

        $this->assertSame('Location', $location->name);
        $this->assertSame($url, $location->value);
    }

    public function testLocationHeaderWithEmptyUrl(): void
    {
        $url = '';
        $location = new Location($url);

        $this->assertSame('Location', $location->name);
        $this->assertSame($url, $location->value);
    }

    public function testLocationHeaderWithSpecialCharacters(): void
    {
        $url = 'https://example.com/path?query=param&another=param';
        $location = new Location($url);

        $this->assertSame('Location', $location->name);
        $this->assertSame($url, $location->value);
    }
}

<?php

declare(strict_types=1);

namespace Tests\Unit\Seedwork\Infrastructure\Mvc\Responses;

use PHPUnit\Framework\TestCase;
use Seedwork\Infrastructure\Mvc\Responses\Headers\{ContentType, Location};
use Seedwork\Infrastructure\Mvc\Responses\{RedirectTo, StatusCode};

final class RedirectToTest extends TestCase
{
    public function testSetLocationUrl(): void
    {
        $data = new \stdClass();
        $data->offset = 1;
        $data->limit = 10;

        $response = new RedirectTo('Books/Index', $data);

        $this->assertSame(StatusCode::Found, $response->statusCode);
        $this->assertCount(2, $response->headers);
        $this->assertEquals(
            [Location::new(url: '/books/index?offset=1&limit=10'), ContentType::html()],
            $response->headers
        );
        $this->assertSame($data, $response->data);
    }

    public function testSetLocationWithoutArgs(): void
    {
        $response = new RedirectTo('Books/Index');

        $this->assertCount(2, $response->headers);
        $this->assertEquals(
            [Location::new(url: '/books/index'), ContentType::html()],
            $response->headers
        );
    }

    public function testSetHeadersAndKeepPrevious(): void
    {
        $expected = [ContentType::html(), Location::new(url: '/books/index')];
        $response = new RedirectTo('Books/Index', headers: [ContentType::html()]);

        $this->assertEquals($expected, $response->headers);
    }
}

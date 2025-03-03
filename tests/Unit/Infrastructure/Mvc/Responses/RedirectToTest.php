<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Mvc\Responses;

use PHPUnit\Framework\TestCase;
use Seedwork\Infrastructure\Mvc\Responses\{RedirectTo, StatusCode};

final class RedirectToTest extends TestCase
{
    public function testSetLocationUrl(): void
    {
        $data = new \stdClass();
        $data->offset = 1;
        $data->limit = 10;

        $response = new RedirectTo('Books/Index', $data);

        $this->assertEquals(StatusCode::Found, $response->statusCode);
        $this->assertEquals(['Location' => '/books/index?offset=1&limit=10'], $response->headers);
        $this->assertEquals($data, $response->data);
    }

    public function testSetLocationWithoutArgs(): void
    {
        $response = new RedirectTo('Books/Index');

        $this->assertEquals(['Location' => '/books/index?'], $response->headers);
    }

    public function testSetHeadersAndKeepPrevious(): void
    {
        $expected = ['Content-Type' => 'application/json', 'Location' => '/books/index?'];
        $response = new RedirectTo('Books/Index', headers: ['Content-Type' => 'application/json']);

        $this->assertEquals($expected, $response->headers);
    }
}

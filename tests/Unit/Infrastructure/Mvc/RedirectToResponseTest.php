<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Mvc;

use PHPUnit\Framework\TestCase;
use Seedwork\Infrastructure\Mvc\{RedirectToResponse, StatusCode};

final class RedirectToResponseTest extends TestCase
{
    public function testRedirectToResponseShouldSetLocationUrl(): void
    {
        $data = new \stdClass();
        $data->offset = 1;
        $data->limit = 10;

        $response = new RedirectToResponse('Books/Index', $data);

        $this->assertEquals(StatusCode::Found, $response->statusCode);
        $this->assertEquals(['Location' => '/books/index?offset=1&limit=10'], $response->headers);
        $this->assertEquals($data, $response->data);
    }

    public function testRedirectToResponseShouldSetLocationWithoutArgs(): void
    {
        $response = new RedirectToResponse('Books/Index');

        $this->assertEquals(['Location' => '/books/index?'], $response->headers);
    }

    public function testRedirectToResponseShouldKeepHeaders(): void
    {
        $expected = ['Content-Type' => 'application/json', 'Location' => '/books/index?'];
        $response = new RedirectToResponse('Books/Index', headers: ['Content-Type' => 'application/json']);

        $this->assertEquals($expected, $response->headers);
    }
}

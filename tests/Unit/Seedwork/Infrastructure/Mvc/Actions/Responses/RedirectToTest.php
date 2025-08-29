<?php

declare(strict_types=1);

namespace Tests\Unit\Seedwork\Infrastructure\Mvc\Actions\Responses;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Seedwork\Infrastructure\Mvc\Actions\Responses\RedirectTo;
use Seedwork\Infrastructure\Mvc\Responses\Headers\{AccessControlAllowMethods, ContentType, Location};
use Seedwork\Infrastructure\Mvc\Responses\StatusCode;

final class RedirectToTest extends TestCase
{
    #[DataProvider('schemeProvider')]
    public function testCreate(string $scheme): void
    {
        $expectedHeaders = [Location::new(url: "$scheme://books/index"), ContentType::html()];

        $response = RedirectTo::create("$scheme://Books/Index");

        $this->assertSame(StatusCode::Found, $response->statusCode);
        $this->assertCount(count($expectedHeaders), $response->headers);
        $this->assertEquals($expectedHeaders, $response->headers);
        $this->assertEquals(new \stdClass(), $response->data);
    }

    #[DataProvider('schemeProvider')]
    public function testCreateWithArgsAndHeaders(string $scheme): void
    {
        $args = [
            'offset' => 1,
            'limit' => 10,
        ];
        $header = new AccessControlAllowMethods(put: false, delete: false);
        $expectedHeaders = [
            $header,
            Location::new(url: "$scheme://books/index?offset=1&limit=10"),
            ContentType::html()
        ];

        $response = RedirectTo::create("$scheme://Books/Index", args: $args, headers: [$header]);

        $this->assertSame(StatusCode::Found, $response->statusCode);
        $this->assertCount(count($expectedHeaders), $response->headers);
        $this->assertEquals($expectedHeaders, $response->headers);
        $this->assertEquals(new \stdClass(), $response->data);
    }

    #[DataProvider('fakeUrlProvider')]
    public function testCreateFailsWhenUrlIsInvalid(string $url): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid URL');
        RedirectTo::create($url);
    }

    /**
     * @return array<array{string}>
     */
    public static function schemeProvider(): array
    {
        return [
            ['http'],
            ['https'],
        ];
    }

    /**
     * @return array<array{string}>
     */
    public static function fakeUrlProvider(): array
    {
        return [
            ['/fake-path'],
            ['ftp://domain.com'],
            ['file://domain.com'],
            ['mailto://domain.com'],
            ['tel://domain.com'],
            ['data://domain.com'],
            ['javascript://domain.com'],
            ['ws://domain.com'],
            ['wss://domain.com'],
            ['sftp://domain.com'],
            ['ssh://domain.com'],
            ['svn://domain.com'],
            ['git://domain.com'],
            ['gopher://domain.com'],
            ['imap://domain.com'],
            ['pop3://domain.com'],
            ['smtp://domain.com'],
            ['rtsp://domain.com'],
            ['rtmp://domain.com'],
            ['rtmpt://domain.com'],
            ['rtmpe://domain.com'],
            ['rtmpte://domain.com'],
            ['rtmps://domain.com'],
        ];
    }
}

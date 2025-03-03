<?php

declare(strict_types=1);

namespace Tests\Unit\Seedwork\Infrastructure\Mvc\Responses\Headers;

use PHPUnit\Framework\TestCase;
use Seedwork\Infrastructure\Mvc\Responses\Headers\ContentEncoding;

final class ContentEncodingTest extends TestCase
{
    public function testGzip(): void
    {
        $header = ContentEncoding::gzip();

        $this->assertInstanceOf(ContentEncoding::class, $header);
        $this->assertSame('Content-Encoding', $header->name);
        $this->assertSame('gzip', $header->value);
    }

    public function testDeflate(): void
    {
        $header = ContentEncoding::deflate();

        $this->assertInstanceOf(ContentEncoding::class, $header);
        $this->assertSame('Content-Encoding', $header->name);
        $this->assertSame('deflate', $header->value);
    }

    public function testBr(): void
    {
        $header = ContentEncoding::br();

        $this->assertInstanceOf(ContentEncoding::class, $header);
        $this->assertSame('Content-Encoding', $header->name);
        $this->assertSame('br', $header->value);
    }

    public function testIdentity(): void
    {
        $header = ContentEncoding::identity();

        $this->assertInstanceOf(ContentEncoding::class, $header);
        $this->assertSame('Content-Encoding', $header->name);
        $this->assertSame('identity', $header->value);
    }

    public function testCompress(): void
    {
        $header = ContentEncoding::compress();

        $this->assertInstanceOf(ContentEncoding::class, $header);
        $this->assertSame('Content-Encoding', $header->name);
        $this->assertSame('x-compress', $header->value);
    }

    public function testXGzip(): void
    {
        $header = ContentEncoding::xGzip();

        $this->assertInstanceOf(ContentEncoding::class, $header);
        $this->assertSame('Content-Encoding', $header->name);
        $this->assertSame('x-gzip', $header->value);
    }
}

<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Mvc\Responses\Headers;

final class ContentEncoding extends Header
{
    public function __construct(string $value)
    {
        parent::__construct('Content-Encoding', $value);
    }

    public static function gzip(): Header
    {
        return new ContentEncoding('gzip');
    }

    public static function deflate(): Header
    {
        return new ContentEncoding('deflate');
    }

    public static function br(): Header
    {
        return new ContentEncoding('br');
    }

    public static function identity(): Header
    {
        return new ContentEncoding('identity');
    }

    public static function compress(): Header
    {
        return new ContentEncoding('x-compress');
    }

    public static function xGzip(): Header
    {
        return new ContentEncoding('x-gzip');
    }
}

<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Mvc\Responses\Headers;

final class ContentType extends Header
{
    public function __construct(string $value)
    {
        parent::__construct('Content-Type', $value);
    }

    public static function json(): Header
    {
        return new ContentType('application/json');
    }

    public static function xml(): Header
    {
        return new ContentType('application/xml');
    }

    public static function html(): Header
    {
        return new ContentType('text/html');
    }

    public static function text(): Header
    {
        return new ContentType('text/plain');
    }

    public static function css(): Header
    {
        return new ContentType('text/css');
    }

    public static function javascript(): Header
    {
        return new ContentType('application/javascript');
    }

    public static function formUrlEncoded(): Header
    {
        return new ContentType('application/x-www-form-urlencoded');
    }

    public static function formData(): Header
    {
        return new ContentType('multipart/form-data');
    }

    public static function octetStream(): Header
    {
        return new ContentType('application/octet-stream');
    }

    public static function pdf(): Header
    {
        return new ContentType('application/pdf');
    }

    public static function zip(): Header
    {
        return new ContentType('application/zip');
    }

    public static function tar(): Header
    {
        return new ContentType('application/x-tar');
    }

    public static function gzip(): Header
    {
        return new ContentType('application/gzip');
    }

    public static function rar(): Header
    {
        return new ContentType('application/x-rar-compressed');
    }

    public static function sevenZip(): Header
    {
        return new ContentType('application/x-7z-compressed');
    }

    public static function png(): Header
    {
        return new ContentType('image/png');
    }

    public static function jpeg(): Header
    {
        return new ContentType('image/jpeg');
    }

    public static function gif(): Header
    {
        return new ContentType('image/gif');
    }

    public static function bmp(): Header
    {
        return new ContentType('image/bmp');
    }

    public static function webp(): Header
    {
        return new ContentType('image/webp');
    }

    public static function svg(): Header
    {
        return new ContentType('image/svg+xml');
    }

    public static function mpeg(): Header
    {
        return new ContentType('audio/mpeg');
    }

    public static function audioOgg(): Header
    {
        return new ContentType('audio/ogg');
    }

    public static function wav(): Header
    {
        return new ContentType('audio/wav');
    }

    public static function mp4(): Header
    {
        return new ContentType('video/mp4');
    }

    public static function videoOgg(): Header
    {
        return new ContentType('video/ogg');
    }

    public static function webm(): Header
    {
        return new ContentType('video/webm');
    }
}

<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Mvc\Responses\Headers;

final class AccessControlAllowOrigin extends Header
{
    public function __construct(string $value)
    {
        parent::__construct('Access-Control-Allow-Origin', $value);
    }

    public static function any(): Header
    {
        return new AccessControlAllowOrigin('*');
    }

    public static function none(): Header
    {
        return new AccessControlAllowOrigin('null');
    }

    public static function specific(string $origin): Header
    {
        return new AccessControlAllowOrigin($origin);
    }
}

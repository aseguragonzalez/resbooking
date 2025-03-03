<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Mvc\Responses\Headers;

final class ContentLength extends Header
{
    public function __construct(int $length)
    {
        parent::__construct('Content-Length', (string)$length);
    }
}

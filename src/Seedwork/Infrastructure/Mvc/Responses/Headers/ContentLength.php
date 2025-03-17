<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Mvc\Responses\Headers;

final class ContentLength extends Header
{
    public function __construct(int $length)
    {
        if ($length < 0) {
            throw new \InvalidArgumentException('Content length must be a non-negative integer.');
        }
        parent::__construct('Content-Length', (string)$length);
    }
}

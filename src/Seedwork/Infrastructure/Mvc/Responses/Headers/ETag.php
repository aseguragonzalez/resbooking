<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Mvc\Responses\Headers;

final class ETag extends Header
{
    public function __construct(string $value)
    {
        parent::__construct('ETag', $value);
    }
}

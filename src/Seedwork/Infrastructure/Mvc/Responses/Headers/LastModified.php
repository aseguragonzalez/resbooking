<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Mvc\Responses\Headers;

final class LastModified extends Header
{
    public function __construct(\DateTimeImmutable $lastModified)
    {
        $value = $lastModified->setTimezone(new \DateTimeZone('UTC'))->format('D, d M Y H:i:s \G\M\T');
        parent::__construct('Last-Modified', $value);
    }
}

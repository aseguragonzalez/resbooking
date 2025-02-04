<?php

declare(strict_types=1);

namespace App\Domain\Shared\Exceptions;

use App\Seedwork\Domain\Exceptions\DomainException;

final class OpenCloseEventOutOfRange extends DomainException
{
    public function __construct()
    {
        parent::__construct('Open-close event date is out of range');
    }
}

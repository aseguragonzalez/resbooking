<?php

declare(strict_types=1);

namespace Domain\Projects\Exceptions;

use Seedwork\Domain\Exceptions\DomainException;

final class OpenCloseEventOutOfRange extends DomainException
{
    public function __construct()
    {
        parent::__construct('Open-close event date is out of range');
    }
}

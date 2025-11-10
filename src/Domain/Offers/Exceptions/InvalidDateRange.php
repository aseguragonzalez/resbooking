<?php

declare(strict_types=1);

namespace Domain\Offers\Exceptions;

use Seedwork\Domain\Exceptions\DomainException;

final class InvalidDateRange extends DomainException
{
    public function __construct()
    {
        parent::__construct(message: 'Start date must be less than end date');
    }
}

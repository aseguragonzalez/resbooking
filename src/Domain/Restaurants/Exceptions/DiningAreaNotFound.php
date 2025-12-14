<?php

declare(strict_types=1);

namespace Domain\Restaurants\Exceptions;

use Seedwork\Domain\Exceptions\DomainException;

final class DiningAreaNotFound extends DomainException
{
    public function __construct(string $diningAreaId)
    {
        parent::__construct("Dining area not found: {$diningAreaId}");
    }
}

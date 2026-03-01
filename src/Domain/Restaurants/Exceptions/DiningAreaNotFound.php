<?php

declare(strict_types=1);

namespace Domain\Restaurants\Exceptions;

use Domain\Restaurants\ValueObjects\DiningAreaId;
use SeedWork\Domain\Exceptions\DomainException;

final class DiningAreaNotFound extends DomainException
{
    public function __construct(DiningAreaId $diningAreaId)
    {
        parent::__construct("Dining area not found: {$diningAreaId->value}");
    }
}

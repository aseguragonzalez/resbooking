<?php

declare(strict_types=1);

namespace Domain\Restaurants\Exceptions;

use SeedWork\Domain\Exceptions\DomainException;

final class DiningAreaDoesNotExist extends DomainException
{
    public function __construct()
    {
        parent::__construct('Dining area does not exists in restaurant');
    }
}

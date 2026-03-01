<?php

declare(strict_types=1);

namespace Domain\Restaurants\Exceptions;

use SeedWork\Domain\Exceptions\DomainException;

final class DiningAreaAlreadyExist extends DomainException
{
    public function __construct()
    {
        parent::__construct('Dining area already exists in restaurant');
    }
}

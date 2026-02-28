<?php

declare(strict_types=1);

namespace Domain\Restaurants\Exceptions;

use SeedWork\Domain\Exceptions\DomainException;

final class RestaurantDoesNotExist extends DomainException
{
    public function __construct()
    {
        parent::__construct('Restaurant does not exists');
    }
}

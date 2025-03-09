<?php

declare(strict_types=1);

namespace App\Domain\Offers\Exceptions;

use Seedwork\Domain\Exceptions\DomainException;

final class OfferAlreadyDisabled extends DomainException
{
    public function __construct()
    {
        parent::__construct('Offer is already disabled');
    }
}

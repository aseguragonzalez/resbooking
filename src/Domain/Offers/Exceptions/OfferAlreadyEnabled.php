<?php

declare(strict_types=1);

namespace Domain\Offers\Exceptions;

use Seedwork\Domain\Exceptions\DomainException;

final class OfferAlreadyEnabled extends DomainException
{
    public function __construct()
    {
        parent::__construct('Offer is already enabled');
    }
}

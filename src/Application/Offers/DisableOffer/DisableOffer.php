<?php

declare(strict_types=1);

namespace App\Application\Offers\DisableOffer;

use App\Seedwork\Application\UseCase;
use App\Seedwork\Exceptions\NotImplementedException;

class DisableOffer extends UseCase
{
    public function execute(DisableOfferRequest $request): void
    {
        throw new NotImplementedException();
    }
}

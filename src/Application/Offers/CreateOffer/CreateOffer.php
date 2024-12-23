<?php

declare(strict_types=1);

namespace App\Application\Offers\CreateOffer;

use App\Seedwork\Application\UseCase;
use App\Seedwork\Exception\NotImplementedException;

class CreateOffer extends UseCase
{
    public function execute(CreateOfferRequest $request): void
    {
        throw new NotImplementedException();
    }
}

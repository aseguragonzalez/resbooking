<?php

declare(strict_types=1);

namespace App\Application\Offers\EnableOffer;

use App\Seedwork\Application\UseCase;
use App\Seedwork\Exception\NotImplementedException;

class EnableOffer extends UseCase
{
    public function execute(EnableOfferRequest $request): void
    {
        throw new NotImplementedException();
    }
}

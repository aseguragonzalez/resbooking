<?php

declare(strict_types=1);

namespace App\Application\Offers\ModifyOffer;

use App\Seedwork\Application\UseCase;
use App\Seedwork\Exceptions\NotImplementedException;

class ModifyOffer extends UseCase
{
    public function execute(ModifyOfferRequest $request): void
    {
        throw new NotImplementedException();
    }
}

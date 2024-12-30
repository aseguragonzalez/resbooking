<?php

declare(strict_types=1);

namespace App\Application\Offers\ModifyOffer;

use App\Domain\Offers\OffersRepository;
use App\Seedwork\Application\UseCase;
use App\Seedwork\Exceptions\NotImplementedException;

/**
 * @extends UseCase<ModifyOfferRequest>
 */
class ModifyOffer extends UseCase
{
    public function __construct(OffersRepository $offersRepository)
    {
        $this->offersRepository = $offersRepository;
    }

    public function execute(ModifyOfferRequest $request): void
    {
        throw new NotImplementedException();
    }
}

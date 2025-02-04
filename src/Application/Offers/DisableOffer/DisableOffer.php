<?php

declare(strict_types=1);

namespace App\Application\Offers\DisableOffer;

use App\Domain\Offers\OffersRepository;
use App\Seedwork\Application\UseCase;
use App\Seedwork\Exceptions\NotImplementedException;

/**
 * @extends UseCase<DisableOfferRequest>
 */
class DisableOffer extends UseCase
{
    public function __construct(OffersRepository $offersRepository)
    {
        $this->offersRepository = $offersRepository;
    }

    public function execute(DisableOfferRequest $request): void
    {
        throw new NotImplementedException();
    }
}

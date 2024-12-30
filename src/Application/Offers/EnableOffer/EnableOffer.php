<?php

declare(strict_types=1);

namespace App\Application\Offers\EnableOffer;

use App\Domain\Offers\OffersRepository;
use App\Seedwork\Application\UseCase;
use App\Seedwork\Exceptions\NotImplementedException;

/**
 * @extends UseCase<EnableOfferRequest>
 */
class EnableOffer extends UseCase
{
    public function __construct(OffersRepository $offersRepository)
    {
        $this->offersRepository = $offersRepository;
    }

    public function execute(EnableOfferRequest $request): void
    {
        throw new NotImplementedException();
    }
}

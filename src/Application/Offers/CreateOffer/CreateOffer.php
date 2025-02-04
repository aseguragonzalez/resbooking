<?php

declare(strict_types=1);

namespace App\Application\Offers\CreateOffer;

use App\Domain\Offers\OffersRepository;
use App\Seedwork\Application\UseCase;
use App\Seedwork\Exceptions\NotImplementedException;

/**
 * @extends UseCase<CreateOfferRequest>
 */
class CreateOffer extends UseCase
{
    public function __construct(OffersRepository $offersRepository)
    {
        $this->offersRepository = $offersRepository;
    }

    public function execute(CreateOfferRequest $request): void
    {
        throw new NotImplementedException();
    }
}

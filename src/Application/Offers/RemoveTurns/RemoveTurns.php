<?php

declare(strict_types=1);

namespace App\Application\Offers\RemoveTurns;

use App\Domain\Offers\OffersRepository;
use App\Seedwork\Application\UseCase;
use App\Seedwork\Exceptions\NotImplementedException;

/**
 * @extends UseCase<RemoveTurnsRequest>
 */
class RemoveTurns extends UseCase
{
    public function __construct(OffersRepository $offersRepository)
    {
        $this->offersRepository = $offersRepository;
    }

    public function execute(RemoveTurnsRequest $request): void
    {
        throw new NotImplementedException();
    }
}

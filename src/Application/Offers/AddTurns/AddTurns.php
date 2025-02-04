<?php

declare(strict_types=1);

namespace App\Application\Offers\AddTurns;

use App\Domain\Offers\OffersRepository;
use App\Seedwork\Application\UseCase;
use App\Seedwork\Exceptions\NotImplementedException;

/**
 * @extends UseCase<AddTurnsRequest>
 */
class AddTurns extends UseCase
{
    public function __construct(OffersRepository $offersRepository)
    {
        $this->offersRepository = $offersRepository;
    }

    public function execute(AddTurnsRequest $request): void
    {
        throw new NotImplementedException();
    }
}

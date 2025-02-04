<?php

declare(strict_types=1);

namespace App\Application\Offers\AddOpenCloseEvent;

use App\Domain\Offers\OffersRepository;
use App\Seedwork\Application\UseCase;
use App\Seedwork\Exceptions\NotImplementedException;

/**
 * @extends UseCase<AddOpenCloseEventRequest>
 */
class AddOpenCloseEvent extends UseCase
{
    public function __construct(OffersRepository $offersRepository)
    {
        $this->offersRepository = $offersRepository;
    }

    public function execute(AddOpenCloseEventRequest $request): void
    {
        throw new NotImplementedException();
    }
}

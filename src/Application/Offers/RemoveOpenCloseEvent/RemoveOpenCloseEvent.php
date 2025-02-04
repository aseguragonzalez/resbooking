<?php

declare(strict_types=1);

namespace App\Application\Offers\RemoveOpenCloseEvent;

use App\Domain\Offers\OffersRepository;
use App\Seedwork\Application\UseCase;
use App\Seedwork\Exceptions\NotImplementedException;

/**
 * @extends UseCase<RemoveOpenCloseEventRequest>
 */
class RemoveOpenCloseEvent extends UseCase
{
    public function __construct(OffersRepository $offersRepository)
    {
        $this->offersRepository = $offersRepository;
    }

    public function execute(RemoveOpenCloseEventRequest $request): void
    {
        throw new NotImplementedException();
    }
}

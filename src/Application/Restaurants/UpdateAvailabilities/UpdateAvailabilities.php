<?php

declare(strict_types=1);

namespace Application\Restaurants\UpdateAvailabilities;

interface UpdateAvailabilities
{
    public function execute(UpdateAvailabilitiesCommand $command): void;
}

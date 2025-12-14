<?php

declare(strict_types=1);

namespace Application\Restaurants\UpdateSettings;

interface UpdateSettings
{
    public function execute(UpdateSettingsCommand $command): void;
}

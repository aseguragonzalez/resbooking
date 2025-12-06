<?php

declare(strict_types=1);

namespace Application\Projects\UpdateSettings;

interface UpdateSettings
{
    public function execute(UpdateSettingsCommand $command): void;
}

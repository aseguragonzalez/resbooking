<?php

declare(strict_types=1);

namespace Application\Restaurants\RemoveDiningArea;

interface RemoveDiningArea
{
    public function execute(RemoveDiningAreaCommand $command): void;
}

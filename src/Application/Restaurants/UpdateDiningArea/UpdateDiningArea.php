<?php

declare(strict_types=1);

namespace Application\Restaurants\UpdateDiningArea;

interface UpdateDiningArea
{
    public function execute(UpdateDiningAreaCommand $command): void;
}

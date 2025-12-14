<?php

declare(strict_types=1);

namespace Application\Restaurants\AddDiningArea;

interface AddDiningArea
{
    public function execute(AddDiningAreaCommand $command): void;
}

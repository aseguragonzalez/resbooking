<?php

declare(strict_types=1);

namespace Application\Projects\AddPlace;

interface AddPlace
{
    public function execute(AddPlaceCommand $command): void;
}

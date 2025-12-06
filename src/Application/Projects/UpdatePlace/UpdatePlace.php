<?php

declare(strict_types=1);

namespace Application\Projects\UpdatePlace;

interface UpdatePlace
{
    public function execute(UpdatePlaceCommand $command): void;
}

<?php

declare(strict_types=1);

namespace Application\Projects\RemovePlace;

interface RemovePlace
{
    public function execute(RemovePlaceCommand $command): void;
}

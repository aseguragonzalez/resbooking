<?php

declare(strict_types=1);

namespace Application\Projects\RemoveTurn;

interface RemoveTurn
{
    public function execute(RemoveTurnCommand $command): void;
}

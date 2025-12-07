<?php

declare(strict_types=1);

namespace Application\Projects\UpdateTurns;

interface UpdateTurns
{
    public function execute(UpdateTurnsCommand $command): void;
}

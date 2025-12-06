<?php

declare(strict_types=1);

namespace Application\Projects\AddTurns;

interface AddTurns
{
    public function execute(AddTurnsCommand $command): void;
}

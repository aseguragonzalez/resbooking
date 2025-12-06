<?php

declare(strict_types=1);

namespace Application\Projects\AddOpenCloseEvent;

interface AddOpenCloseEvent
{
    public function execute(AddOpenCloseEventCommand $command): void;
}

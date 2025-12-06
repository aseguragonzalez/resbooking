<?php

declare(strict_types=1);

namespace Application\Projects\RemoveOpenCloseEvent;

interface RemoveOpenCloseEvent
{
    public function execute(RemoveOpenCloseEventCommand $command): void;
}

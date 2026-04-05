<?php

declare(strict_types=1);

namespace Framework\Module\Security\Application\SignOut;

interface SignOut
{
    public function execute(SignOutCommand $command): void;
}

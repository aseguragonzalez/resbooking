<?php

declare(strict_types=1);

namespace Framework\Security\Application\SignOut;

interface SignOut
{
    public function execute(SignOutCommand $command): void;
}

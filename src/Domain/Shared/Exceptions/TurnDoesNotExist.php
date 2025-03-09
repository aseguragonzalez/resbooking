<?php

declare(strict_types=1);

namespace App\Domain\Shared\Exceptions;

use Seedwork\Domain\Exceptions\DomainException;

final class TurnDoesNotExist extends DomainException
{
    public function __construct()
    {
        parent::__construct('Turn does not exists in project');
    }
}

<?php

declare(strict_types=1);

namespace App\Domain\Shared\Exceptions;

use App\Seedwork\Domain\Exceptions\DomainException;

final class OpenCloseEventDoesNotExist extends DomainException
{
    public function __construct()
    {
        parent::__construct(message: 'Open-close event does not exists in project');
    }
}

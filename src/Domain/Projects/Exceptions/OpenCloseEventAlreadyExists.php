<?php

declare(strict_types=1);

namespace App\Domain\Projects\Exceptions;

use App\Seedwork\Domain\Exceptions\DomainException;

final class OpenCloseEventAlreadyExists extends DomainException
{
    public function __construct()
    {
        parent::__construct(message: 'OpenCloseEvent already exists in project');
    }
}

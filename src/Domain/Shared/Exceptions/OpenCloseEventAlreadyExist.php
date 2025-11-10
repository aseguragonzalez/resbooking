<?php

declare(strict_types=1);

namespace Domain\Shared\Exceptions;

use Seedwork\Domain\Exceptions\DomainException;

final class OpenCloseEventAlreadyExist extends DomainException
{
    public function __construct()
    {
        parent::__construct(message: 'OpenCloseEvent already exists in project');
    }
}

<?php

declare(strict_types=1);

namespace Domain\Projects\Exceptions;

use Seedwork\Domain\Exceptions\DomainException;

final class ProjectDoesNotExist extends DomainException
{
    public function __construct()
    {
        parent::__construct('Project does not exists');
    }
}

<?php

declare(strict_types=1);

namespace Application\Projects\CreateNewProject;

use Seedwork\Application\Command;

final class CreateNewProjectCommand extends Command
{
    public function __construct(public readonly string $email)
    {
    }
}

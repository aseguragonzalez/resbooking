<?php

declare(strict_types=1);

namespace Application\Projects\CreateNewProject;

final class CreateNewProjectCommand
{
    public function __construct(public readonly string $email)
    {
    }
}

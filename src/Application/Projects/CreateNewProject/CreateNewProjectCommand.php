<?php

declare(strict_types=1);

namespace Application\Projects\CreateNewProject;

final readonly class CreateNewProjectCommand
{
    public function __construct(public string $email)
    {
    }
}

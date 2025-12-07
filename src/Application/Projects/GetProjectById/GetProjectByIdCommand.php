<?php

declare(strict_types=1);

namespace Application\Projects\GetProjectById;

final readonly class GetProjectByIdCommand
{
    public function __construct(public string $id)
    {
    }
}

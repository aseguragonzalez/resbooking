<?php

declare(strict_types=1);

namespace App\Domain\Offers\ValueObjects;

use Seedwork\Domain\ValueObject;
use Seedwork\Domain\Exceptions\ValueException;

final class Project extends ValueObject
{
    public function __construct(public readonly string $id)
    {
        if (empty($id)) {
            throw new ValueException('Project id is required');
        }
    }

    public function equals(Project $project): bool
    {
        return $this->id === $project->id;
    }
}

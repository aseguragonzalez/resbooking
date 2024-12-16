<?php

declare(strict_types=1);

namespace App\Domain\Projects;

use App\Domain\Projects\ValueObjects\Settings;
use App\Seedwork\Domain\AggregateRoot;

final class Project extends AggregateRoot
{
    public function __construct(
        public ?int $id,
        public readonly Settings $settings,
        public readonly array $users = [],
        public readonly array $places = [],
        public readonly array $turns = [],
    ) { }

    public function addUser(User $user): void
    {
        $this->users[] = $user;
    }

    public function removeUser(User $user): void
    {
        $this->users = array_filter(
            $this->users,
            fn (User $s) => $s->equals($user)
        );
    }

    public function addPlace(Place $place): void
    {
        $this->places[] = $place;
    }

    public function removePlace(Place $place): void
    {
        $this->places = array_filter(
            $this->places,
            fn (Place $s) => $s->equals($place)
        );
    }
}

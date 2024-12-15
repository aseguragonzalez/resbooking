<?php

declare(strict_types=1);

namespace App\Domain\Projects;

use App\Domain\Projects\ValueObjects\Settings;

final class Project
{
    public function __construct(
        public int $id,
        public readonly Settings $settings,
        public readonly array $offers = [],
        public readonly array $users = [],
        public readonly array $places = [],
        public readonly array $turns = [],
    ) { }

    public function addOffer(Offer $offer): void
    {
        $this->offers[] = $offer;
    }

    public function removeOffer(Offer $offer): void
    {
        $this->offers = array_filter(
            $this->offers,
            fn (Offer $s) => $s->equals($offer)
        );
    }

    public function modifyOffer(Offer $offer): void
    {
        $this->offers = array_map(
            fn (Offer $s) => $s->equals($offer) ? $offer : $s,
            $this->offers
        );
    }

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

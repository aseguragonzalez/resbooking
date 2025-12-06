<?php

declare(strict_types=1);

namespace Infrastructure\Ports\Dashboard\Models\Places\Pages;

use Infrastructure\Ports\Dashboard\Models\PageModel;
use Infrastructure\Ports\Dashboard\Models\Places\Place;

final class PlacesList extends PageModel
{
    public readonly bool $hasPlaces;

    /**
     * @param array<Place> $places
     */
    private function __construct(public readonly array $places)
    {
        parent::__construct('{{places.title}}');
        $this->hasPlaces = !empty($this->places);
    }

    /**
     * @param array<Place> $places
     */
    public static function create(array $places = []): PlacesList
    {
        return new PlacesList(places: $places);
    }
}

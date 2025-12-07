<?php

declare(strict_types=1);

namespace Infrastructure\Ports\Dashboard\Models\Places\Pages;

use Infrastructure\Ports\Dashboard\Models\FormModel;
use Infrastructure\Ports\Dashboard\Models\Places\Requests\AddPlaceRequest;
use Infrastructure\Ports\Dashboard\Models\Places\Requests\UpdatePlaceRequest;

final readonly class EditPlace extends FormModel
{
    public ?string $placeId;
    public string $backUrl;
    public string $actionUrl;

    /**
     * @param array<string, string> $errors
     */
    private function __construct(
        public AddPlaceRequest|UpdatePlaceRequest $place,
        array $errors = [],
        ?string $placeId = null,
        string $backUrl = '/places',
    ) {
        parent::__construct(
            pageTitle: $placeId === null
                ? '{{places.create.form.title}}'
                : '{{places.edit.form.title}}',
            errors: $errors
        );
        $this->placeId = $placeId;
        $this->backUrl = $backUrl;
        $this->actionUrl = $placeId === null ? '/places' : "/places/{$placeId}";
    }

    public static function new(string $backUrl = '/places'): self
    {
        return new self(
            place: new AddPlaceRequest(),
            errors: [],
            placeId: null,
            backUrl: $backUrl
        );
    }

    public static function fromPlace(string $placeId, string $name, int $capacity, string $backUrl = '/places'): self
    {
        return new self(
            place: new UpdatePlaceRequest(name: $name, capacity: $capacity),
            errors: [],
            placeId: $placeId,
            backUrl: $backUrl
        );
    }

    /**
     * @param array<string, string> $errors
     */
    public static function withErrors(
        AddPlaceRequest|UpdatePlaceRequest $request,
        array $errors,
        ?string $placeId = null,
        string $backUrl = '/places'
    ): self {
        return new self(
            place: $request,
            errors: $errors,
            placeId: $placeId,
            backUrl: $backUrl
        );
    }
}

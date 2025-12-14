<?php

declare(strict_types=1);

namespace Infrastructure\Ports\Dashboard\Models\DiningAreas\Pages;

use Infrastructure\Ports\Dashboard\Models\DiningAreas\Requests\AddDiningAreaRequest;
use Infrastructure\Ports\Dashboard\Models\DiningAreas\Requests\UpdateDiningAreaRequest;
use Infrastructure\Ports\Dashboard\Models\FormModel;

final readonly class EditDiningArea extends FormModel
{
    public ?string $diningAreaId;
    public string $backUrl;
    public string $actionUrl;

    /**
     * @param array<string, string> $errors
     */
    private function __construct(
        public AddDiningAreaRequest|UpdateDiningAreaRequest $diningArea,
        array $errors = [],
        ?string $diningAreaId = null,
        string $backUrl = '/dining-areas',
    ) {
        parent::__construct(
            pageTitle: $diningAreaId === null
                ? '{{dining-areas.create.form.title}}'
                : '{{dining-areas.edit.form.title}}',
            errors: $errors
        );
        $this->diningAreaId = $diningAreaId;
        $this->backUrl = $backUrl;
        $this->actionUrl = $diningAreaId === null ? '/dining-areas' : "/dining-areas/{$diningAreaId}";
    }

    public static function new(string $backUrl = '/dining-areas'): self
    {
        return new self(
            diningArea: new AddDiningAreaRequest(),
            errors: [],
            diningAreaId: null,
            backUrl: $backUrl
        );
    }

    public static function fromDiningArea(
        string $diningAreaId,
        string $name,
        int $capacity,
        string $backUrl = '/dining-areas'
    ): self {
        return new self(
            diningArea: new UpdateDiningAreaRequest(name: $name, capacity: $capacity),
            errors: [],
            diningAreaId: $diningAreaId,
            backUrl: $backUrl
        );
    }

    /**
     * @param array<string, string> $errors
     */
    public static function withErrors(
        AddDiningAreaRequest|UpdateDiningAreaRequest $request,
        array $errors,
        ?string $diningAreaId = null,
        string $backUrl = '/dining-areas'
    ): self {
        return new self(
            diningArea: $request,
            errors: $errors,
            diningAreaId: $diningAreaId,
            backUrl: $backUrl
        );
    }
}

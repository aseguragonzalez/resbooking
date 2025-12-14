<?php

declare(strict_types=1);

namespace Infrastructure\Ports\Dashboard\Models\DiningAreas\Pages;

use Infrastructure\Ports\Dashboard\Models\PageModel;
use Infrastructure\Ports\Dashboard\Models\DiningAreas\DiningArea;

final readonly class DiningAreasList extends PageModel
{
    public bool $hasDiningAreas;

    /**
     * @param array<DiningArea> $diningAreas
     */
    private function __construct(public readonly array $diningAreas)
    {
        parent::__construct('{{dining-areas.title}}');
        $this->hasDiningAreas = !empty($this->diningAreas);
    }

    /**
     * @param array<DiningArea> $diningAreas
     */
    public static function create(array $diningAreas = []): DiningAreasList
    {
        return new DiningAreasList(diningAreas: $diningAreas);
    }
}

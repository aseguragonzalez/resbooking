<?php

declare(strict_types=1);

namespace Infrastructure\Ports\Dashboard\Models\Turns\Pages;

use Domain\Projects\ValueObjects\TurnAvailability;
use Infrastructure\Ports\Dashboard\Models\PageModel;
use Infrastructure\Ports\Dashboard\Models\Turns\Turn;

final class TurnsList extends PageModel
{
    /**
     * @param array<Turn> $turns
     */
    private function __construct(public readonly array $turns)
    {
        parent::__construct(pageTitle: '{{turns.title}}');
    }

    /**
     * @param array<TurnAvailability> $turnAvailables
     */
    public static function create(array $turnAvailables = []): TurnsList
    {
        $turns = array_map(
            fn (TurnAvailability $turnAvailable) => new Turn(
                time: substr($turnAvailable->turn->toString(), 0, 5),
                dayOfWeekId: $turnAvailable->dayOfWeek->value,
                turnId: $turnAvailable->turn->value,
                capacity: $turnAvailable->capacity->value,
            ),
            $turnAvailables
        );

        usort($turns, function (Turn $a, Turn $b): int {
            $turnComparison = $a->turnId <=> $b->turnId;
            if ($turnComparison !== 0) {
                return $turnComparison;
            }
            return $a->dayOfWeekId <=> $b->dayOfWeekId;
        });

        return new TurnsList(turns: $turns);
    }
}

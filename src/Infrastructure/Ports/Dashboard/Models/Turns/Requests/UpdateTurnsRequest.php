<?php

declare(strict_types=1);

namespace Infrastructure\Ports\Dashboard\Models\Turns\Requests;

use Application\Projects\UpdateTurns\TurnAvailability;
use Psr\Http\Message\ServerRequestInterface;

final class UpdateTurnsRequest
{
    /**
     * @var array<TurnAvailability> $turns
     */
    public readonly array $turns;

    public function __construct(ServerRequestInterface $request)
    {
        // TODO: Validate the request body
        $parsedBody = $request->getParsedBody();
        if (!is_array($parsedBody)) {
            throw new \InvalidArgumentException('Invalid request body');
        }

        $turns = [];
        foreach ($parsedBody as $key => $value) {
            $parts = explode('_', (string)$key);
            $turnId = intval($parts[0]);
            $dayOfWeekId = intval($parts[1]);
            $turns[] = new TurnAvailability(
                turnId: $turnId,
                dayOfWeekId: $dayOfWeekId,
                capacity: is_int($value) ? intval($value) : 0
            );
        }

        $this->turns = $turns;
    }
}

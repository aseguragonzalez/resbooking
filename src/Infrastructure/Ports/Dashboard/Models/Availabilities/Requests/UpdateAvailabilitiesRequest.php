<?php

declare(strict_types=1);

namespace Infrastructure\Ports\Dashboard\Models\Availabilities\Requests;

use Application\Restaurants\UpdateAvailabilities\Availability;
use Psr\Http\Message\ServerRequestInterface;

final readonly class UpdateAvailabilitiesRequest
{
    /**
     * @var array<Availability> $availabilities
     */
    public array $availabilities;

    public function __construct(ServerRequestInterface $request)
    {
        // TODO: Validate the request body
        $parsedBody = $request->getParsedBody();
        if (!is_array($parsedBody)) {
            throw new \InvalidArgumentException('Invalid request body');
        }

        $availabilities = [];
        foreach ($parsedBody as $key => $value) {
            $parts = explode('_', (string)$key);
            $timeSlotId = intval($parts[0]);
            $dayOfWeekId = intval($parts[1]);
            $availabilities[] = new Availability(
                timeSlotId: $timeSlotId,
                dayOfWeekId: $dayOfWeekId,
                capacity: is_numeric($value) ? intval($value) : 0
            );
        }

        $this->availabilities = $availabilities;
    }
}

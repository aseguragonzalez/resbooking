<?php

declare(strict_types=1);

namespace Infrastructure\Ports\Dashboard\Models\Availabilities\Requests;

use Psr\Http\Message\ServerRequestInterface;

final readonly class UpdateAvailabilitiesRequest
{
    /**
     * @var array<int, array{dayOfWeekId: int, timeSlotId: int, capacity: int}>
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
            $timeSlotId = (int) $parts[0];
            $dayOfWeekId = (int) $parts[1];
            $availabilities[] = [
                'dayOfWeekId' => $dayOfWeekId,
                'timeSlotId' => $timeSlotId,
                'capacity' => is_numeric($value) ? (int) $value : 0,
            ];
        }

        $this->availabilities = $availabilities;
    }
}

<?php

declare(strict_types=1);

namespace Infrastructure\Ports\Dashboard\Controllers;

use Infrastructure\Ports\Dashboard\Models\Pages\Reservations;
use Infrastructure\Ports\Dashboard\Models\Shared\Reservation;
use Infrastructure\Ports\Dashboard\Models\UpdateStatusRequest;
use Psr\Http\Message\ServerRequestInterface;
use Seedwork\Infrastructure\Mvc\Actions\Responses\ActionResponse;
use Seedwork\Infrastructure\Mvc\Controllers\Controller;

final class ReservationsController extends Controller
{
    public function index(int $offset = 0, string $from = 'now'): ActionResponse
    {
        $reservations = [
            new Reservation("id", "10:00", "John Doe", "555-555-555", "john.doe@gmail.com"),
            new Reservation("id", "10:00", "John Doe", "555-555-555", "john.doe@gmail.com"),
            new Reservation("id", "10:00", "John Doe", "555-555-555", "john.doe@gmail.com"),
            new Reservation("id", "10:00", "John Doe", "555-555-555", "john.doe@gmail.com"),
            new Reservation("id", "10:00", "John Doe", "555-555-555", "john.doe@gmail.com"),
            new Reservation("id", "10:00", "John Doe", "555-555-555", "john.doe@gmail.com"),
            new Reservation("id", "10:00", "John Doe", "555-555-555", "john.doe@gmail.com"),
            new Reservation("id", "10:00", "John Doe", "555-555-555", "john.doe@gmail.com"),
            new Reservation("id", "10:00", "John Doe", "555-555-555", "john.doe@gmail.com"),
        ];
        $model = Reservations::create(
            from: $from,
            current: $offset,
            reservations: $reservations
        );

        return $this->view(model: $model);
    }

    public function updateStatus(UpdateStatusRequest $request): ActionResponse
    {
        $args = (object)[
            'offset' => $request->offset,
            'from' => $request->from
        ];
        return $this->redirectToAction(action: 'index', args: $args);
    }

    public function edit(string $id, ServerRequestInterface $request): ActionResponse
    {
        $backUrl = $request->getHeaderLine('Referer') ?: '/reservations';
        $model = (object)[
            "reservation" => new Reservation($id, "10:00", "John Doe", "555-555-555", "john.doe@gmail.com"),
            "backUrl" => $backUrl,
            "errors" => (object)[
                "name" => false,
                "email" => "Email is invalid",
                "phone" => "Phone number is required",
            ],
        ];
        return $this->view(model: $model);
    }

    public function update(string $id, ?string $backUrl): ActionResponse
    {
        $model = (object)[
            "reservation" => new Reservation($id, "10:00", "John Doe", "555-555-555", "john.doe@gmail.com"),
            "backUrl" => $backUrl ?? '/reservations',
        ];
        return $this->view(name: 'edit', model: $model);
    }
}

<?php

declare(strict_types=1);

namespace Infrastructure\Ports\Dashboard\Controllers;

use Infrastructure\Ports\Dashboard\Models\Pages\Reservations;
use Infrastructure\Ports\Dashboard\Models\Shared\Reservation;
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

    public function edit(string $id): ActionResponse
    {
        $model = new Reservation($id, "10:00", "John Doe", "555-555-555", "john.doe@gmail.com");
        return $this->view(model: $model);
    }

    public function update(string $id): ActionResponse
    {
        $model = new Reservation($id, "10:00", "John Doe", "555-555-555", "john.doe@gmail.com");
        return $this->view(name: 'edit', model: $model);
    }
}

<?php

declare(strict_types=1);

namespace Infrastructure\Ports\Dashboard\Controllers;

use Infrastructure\Ports\Dashboard\Models\Reservation;
use Infrastructure\Ports\Dashboard\Models\ReservationsModel;
use Seedwork\Infrastructure\Mvc\Actions\Responses\ActionResponse;
use Seedwork\Infrastructure\Mvc\Controllers\Controller;

class ReservationsController extends Controller
{
    public function index(int $offset = 0, string $from = 'now'): ActionResponse
    {
        $model = ReservationsModel::create(
            from: $from,
            current: $offset,
            reservations: [
                new Reservation("id", "10:00", "John Doe", "555-555-555", "john.doe@gmail.com"),
                new Reservation("id", "10:00", "John Doe", "555-555-555", "john.doe@gmail.com"),
                new Reservation("id", "10:00", "John Doe", "555-555-555", "john.doe@gmail.com"),
                new Reservation("id", "10:00", "John Doe", "555-555-555", "john.doe@gmail.com"),
                new Reservation("id", "10:00", "John Doe", "555-555-555", "john.doe@gmail.com"),
                new Reservation("id", "10:00", "John Doe", "555-555-555", "john.doe@gmail.com"),
                new Reservation("id", "10:00", "John Doe", "555-555-555", "john.doe@gmail.com"),
                new Reservation("id", "10:00", "John Doe", "555-555-555", "john.doe@gmail.com"),
                new Reservation("id", "10:00", "John Doe", "555-555-555", "john.doe@gmail.com"),
            ]
        );

        return $this->view(name: $model->hasReservations ? "index" : "empty", model: $model);
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

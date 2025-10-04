<?php

declare(strict_types=1);

namespace Infrastructure\Ports\Dashboard\Controllers;

use Infrastructure\Ports\Dashboard\Models\Reservations\Pages\Reservations;
use Infrastructure\Ports\Dashboard\Models\Reservations\Requests\UpdateReservationRequest;
use Infrastructure\Ports\Dashboard\Models\Reservations\Requests\UpdateStatusRequest;
use Infrastructure\Ports\Dashboard\Models\Reservations\Reservation;
use Psr\Http\Message\ServerRequestInterface;
use Seedwork\Infrastructure\Mvc\Actions\Responses\ActionResponse;
use Seedwork\Infrastructure\Mvc\Controllers\Controller;
use Faker\Factory as FakerFactory;

final class ReservationsController extends Controller
{
    /**
     * @return Reservation[]
     */
    private function getFakeReservations(int $count): array
    {
        $faker = FakerFactory::create();
        return array_map(fn () => new Reservation(
            id: $faker->uuid(),
            turn: $faker->time(),
            name: $faker->name(),
            phone: $faker->phoneNumber(),
            email: $faker->email()
        ), range(1, $count));
    }

    public function index(int $offset = 0, string $from = 'now'): ActionResponse
    {
        $reservations = $this->getFakeReservations(10);
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

    public function create(ServerRequestInterface $request): ActionResponse
    {
        $backUrl = $request->getHeaderLine('Referer') ?: '/reservations';
        $model = (object)[
            "pageTitle" => "{{reservation.create.title}}",
            "reservation" => new Reservation("", "", "", "", ""),
            "backUrl" => $backUrl,
        ];
        return $this->view("edit", model: $model);
    }

    public function edit(string $id, ServerRequestInterface $request): ActionResponse
    {
        $reservation = $this->getFakeReservations(1)[0];
        $backUrl = $request->getHeaderLine('Referer') ?: '/reservations';
        $model = (object)[
            "pageTitle" => "{{reservation.edit.title}} | {$reservation->name}",
            "reservation" => $reservation,
            "backUrl" => $backUrl,
        ];
        return $this->view(model: $model);
    }

    public function update(string $id, UpdateReservationRequest $request): ActionResponse
    {
        $reservation = $this->getFakeReservations(1)[0];
        $model = (object)[
            "pageTitle" => "{{reservation.edit.title}} | {$reservation->name}",
            "reservation" => new Reservation(
                id: $id,
                turn: $reservation->turn,
                name: $request->name,
                phone: $request->phone,
                email: $request->email
            ),
            "backUrl" => $request->backUrl,
            "errors" => (object)[
                "name" => "{{reservation.form.name.error.required}}",
                "email" => "{{reservation.form.email.error.invalid}}",
                "phone" => "{{reservation.form.phone.error.required}}",
            ],
        ];
        return $this->view('edit', model: $model);
    }
}

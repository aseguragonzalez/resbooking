<?php

declare(strict_types=1);

namespace Infrastructure\Ports\Dashboard\Controllers;

use Faker\Factory as FakerFactory;
use Infrastructure\Ports\Dashboard\Middlewares\RestaurantContextSettings;
use Infrastructure\Ports\Dashboard\Models\Reservations\Pages\Reservations;
use Infrastructure\Ports\Dashboard\Models\Reservations\Requests\UpdateReservationRequest;
use Infrastructure\Ports\Dashboard\Models\Reservations\Requests\UpdateStatusRequest;
use Infrastructure\Ports\Dashboard\Models\Reservations\Reservation;
use Psr\Http\Message\ServerRequestInterface;
use Seedwork\Infrastructure\Mvc\Actions\Responses\ActionResponse;
use Seedwork\Infrastructure\Mvc\Requests\RequestContext;
use Seedwork\Infrastructure\Mvc\Routes\Path;
use Seedwork\Infrastructure\Mvc\Routes\Route;
use Seedwork\Infrastructure\Mvc\Routes\RouteMethod;

final class ReservationsController extends RestaurantBaseController
{
    public function __construct(
        RequestContext $requestContext,
        RestaurantContextSettings $settings,
    ) {
        parent::__construct($requestContext, $settings);
    }

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

    /**
     * @return array<Route>
     */
    public static function getRoutes(): array
    {
        return [
            Route::create(
                method: RouteMethod::Get,
                path: Path::create('/reservations'),
                controller: ReservationsController::class,
                action: 'index',
                authRequired: true
            ),
            Route::create(
                method: RouteMethod::Get,
                path: Path::create('/reservations/create'),
                controller: ReservationsController::class,
                action: 'create',
                authRequired: true,
                roles: ['admin']
            ),
            Route::create(
                method: RouteMethod::Get,
                path: Path::create('/reservations/{id}'),
                controller: ReservationsController::class,
                action: 'edit',
                authRequired: true,
            ),
            Route::create(
                method: RouteMethod::Post,
                path: Path::create('/reservations/{id}'),
                controller: ReservationsController::class,
                action: 'update',
                authRequired: true,
                roles: ['admin']
            ),
            Route::create(
                method: RouteMethod::Post,
                path: Path::create('/reservations/{id}/status'),
                controller: ReservationsController::class,
                action: 'updateStatus',
                authRequired: true,
                roles: ['admin']
            ),
        ];
    }
}

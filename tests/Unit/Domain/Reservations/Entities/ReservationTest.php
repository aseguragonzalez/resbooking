<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Reservations\Entities;

use Domain\Reservations\Entities\Reservation;
use Domain\Reservations\Events\ReservationCreated;
use Domain\Reservations\Events\ReservationStatusUpdated;
use Domain\Reservations\Events\ReservationUpdated;
use Domain\Reservations\ValueObjects\ReservationStatus;
use Domain\Shared\Capacity;
use Domain\Shared\Email;
use Domain\Shared\Phone;
use Domain\Shared\Turn;
use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
use PHPUnit\Framework\TestCase;

final class ReservationTest extends TestCase
{
    private Faker $faker;

    protected function setUp(): void
    {
        $this->faker = FakerFactory::create();
    }

    protected function tearDown(): void
    {
    }

    public function testCreateNewReservation(): void
    {
        $projectId = $this->faker->uuid();
        $date = new \DateTimeImmutable('2024-12-25');
        $turn = Turn::H1900;
        $name = $this->faker->name();
        $email = new Email($this->faker->email());
        $phone = new Phone($this->faker->phoneNumber());
        $numberOfDiners = new Capacity(4);

        $reservation = Reservation::new(
            projectId: $projectId,
            date: $date,
            turn: $turn,
            name: $name,
            email: $email,
            phone: $phone,
            numberOfDiners: $numberOfDiners
        );

        $this->assertInstanceOf(Reservation::class, $reservation);
        $this->assertNotEmpty($reservation->getId());
        $this->assertSame($projectId, $reservation->getProjectId());
        $this->assertSame($date, $reservation->getDate());
        $this->assertSame($turn, $reservation->getTurn());
        $this->assertSame($name, $reservation->getName());
        $this->assertSame($email, $reservation->getEmail());
        $this->assertSame($phone, $reservation->getPhone());
        $this->assertSame($numberOfDiners, $reservation->getNumberOfDiners());
        $this->assertSame(ReservationStatus::PENDING, $reservation->getStatus());

        $events = $reservation->getEvents();
        $this->assertCount(1, $events);
        $this->assertInstanceOf(ReservationCreated::class, $events[0]);
        $event = $events[0];
        $this->assertSame($reservation, $event->getPayload()['reservation']);
        $this->assertSame($reservation->getId(), $event->getPayload()['reservationId']);
    }

    public function testCreateReservationWithId(): void
    {
        $id = $this->faker->uuid();
        $projectId = $this->faker->uuid();
        $date = new \DateTimeImmutable('2024-12-25');
        $turn = Turn::H2000;
        $name = $this->faker->name();
        $email = new Email($this->faker->email());
        $phone = new Phone($this->faker->phoneNumber());
        $numberOfDiners = new Capacity(2);

        $reservation = Reservation::new(
            projectId: $projectId,
            date: $date,
            turn: $turn,
            name: $name,
            email: $email,
            phone: $phone,
            numberOfDiners: $numberOfDiners,
            id: $id
        );

        $this->assertSame($id, $reservation->getId());
    }

    public function testBuildReservation(): void
    {
        $id = $this->faker->uuid();
        $projectId = $this->faker->uuid();
        $date = new \DateTimeImmutable('2024-12-25');
        $turn = Turn::H1930;
        $name = $this->faker->name();
        $email = new Email($this->faker->email());
        $phone = new Phone($this->faker->phoneNumber());
        $numberOfDiners = new Capacity(6);
        $status = ReservationStatus::ACCEPTED;

        $reservation = Reservation::build(
            id: $id,
            projectId: $projectId,
            date: $date,
            turn: $turn,
            name: $name,
            email: $email,
            phone: $phone,
            numberOfDiners: $numberOfDiners,
            status: $status
        );

        $this->assertSame($id, $reservation->getId());
        $this->assertSame($projectId, $reservation->getProjectId());
        $this->assertSame($date, $reservation->getDate());
        $this->assertSame($turn, $reservation->getTurn());
        $this->assertSame($name, $reservation->getName());
        $this->assertSame($email, $reservation->getEmail());
        $this->assertSame($phone, $reservation->getPhone());
        $this->assertSame($numberOfDiners, $reservation->getNumberOfDiners());
        $this->assertSame($status, $reservation->getStatus());
        $this->assertEmpty($reservation->getEvents());
    }

    public function testUpdateReservationDetails(): void
    {
        $reservation = $this->createReservation();
        $newName = $this->faker->name();
        $newEmail = new Email($this->faker->email());
        $newPhone = new Phone($this->faker->phoneNumber());

        $reservation->updateDetails(
            name: $newName,
            email: $newEmail,
            phone: $newPhone
        );

        $this->assertSame($newName, $reservation->getName());
        $this->assertSame($newEmail, $reservation->getEmail());
        $this->assertSame($newPhone, $reservation->getPhone());

        $events = $reservation->getEvents();
        $this->assertCount(2, $events);
        $this->assertInstanceOf(ReservationUpdated::class, $events[1]);
        $event = $events[0];
        $this->assertSame($reservation, $event->getPayload()['reservation']);
        $this->assertSame($reservation->getId(), $event->getPayload()['reservationId']);
    }

    public function testUpdateReservationStatus(): void
    {
        $reservation = $this->createReservation();
        $oldStatus = $reservation->getStatus();
        $newStatus = ReservationStatus::ACCEPTED;

        $reservation->updateStatus($newStatus);

        $this->assertSame($newStatus, $reservation->getStatus());

        $events = $reservation->getEvents();
        $this->assertCount(2, $events);
        $this->assertInstanceOf(ReservationStatusUpdated::class, $events[1]);
        $event = $events[1];
        $this->assertSame($reservation, $event->getPayload()['reservation']);
        $this->assertSame($reservation->getId(), $event->getPayload()['reservationId']);
        $this->assertSame($oldStatus->value, $event->getPayload()['oldStatus']);
        $this->assertSame($newStatus->value, $event->getPayload()['newStatus']);
    }

    private function createReservation(): Reservation
    {
        return Reservation::new(
            projectId: $this->faker->uuid(),
            date: new \DateTimeImmutable('2024-12-25'),
            turn: Turn::H1900,
            name: $this->faker->name(),
            email: new Email($this->faker->email()),
            phone: new Phone($this->faker->phoneNumber()),
            numberOfDiners: new Capacity(4)
        );
    }
}

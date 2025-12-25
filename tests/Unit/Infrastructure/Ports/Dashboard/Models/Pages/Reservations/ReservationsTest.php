<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Ports\Dashboard\Models\Reservations\Pages;

use PHPUnit\Framework\TestCase;
use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
use Infrastructure\Ports\Dashboard\Models\Reservations\Pages\Reservations;
use Infrastructure\Ports\Dashboard\Models\Reservations\Reservation;

final class ReservationsTest extends TestCase
{
    private Faker $faker;

    protected function setUp(): void
    {
        $this->faker = FakerFactory::create();
    }

    public function testCreateWithDefaultParameters(): void
    {
        $reservations = Reservations::create();

        $this->assertSame('{{reservations.title}}', $reservations->pageTitle);
        $this->assertMatchesRegularExpression('/\d{4}-\d{2}-\d{2}/', $reservations->date);
        $this->assertSame(0, $reservations->offset);
        $this->assertSame(0, $reservations->prev);
        $this->assertSame(1, $reservations->next);
        $this->assertEmpty($reservations->reservations);
        $this->assertFalse($reservations->hasReservations);
        $this->assertTrue($reservations->prevDisabled);
    }

    public function testCreateWithValidDateString(): void
    {
        $date = '2024-01-15';
        $reservations = Reservations::create(from: $date);

        $this->assertSame($date, $reservations->date);
    }

    public function testCreateWithInvalidDateStringUsesNow(): void
    {
        $reservations = Reservations::create(from: 'invalid-date');

        $this->assertMatchesRegularExpression('/\d{4}-\d{2}-\d{2}/', $reservations->date);
    }

    public function testCreateWithCurrentOffset(): void
    {
        $current = 5;
        $reservations = Reservations::create(current: $current);

        $this->assertSame($current, $reservations->offset);
        $this->assertSame(4, $reservations->prev);
        $this->assertSame(6, $reservations->next);
        $this->assertFalse($reservations->prevDisabled);
    }

    public function testCreateWithZeroOffsetHasPrevDisabled(): void
    {
        $reservations = Reservations::create(current: 0);

        $this->assertSame(0, $reservations->offset);
        $this->assertSame(0, $reservations->prev);
        $this->assertTrue($reservations->prevDisabled);
    }

    public function testCreateWithReservationsSetsHasReservationsTrue(): void
    {
        $reservation = new Reservation(
            id: $this->faker->uuid,
            turn: '12:00',
            name: $this->faker->name,
            phone: $this->faker->phoneNumber,
            email: $this->faker->email
        );

        $reservations = Reservations::create(reservations: [$reservation]);

        $this->assertTrue($reservations->hasReservations);
        $this->assertCount(1, $reservations->reservations);
        $this->assertSame($reservation, $reservations->reservations[0]);
    }

    public function testCreateWithMultipleReservations(): void
    {
        $reservation1 = new Reservation(
            id: $this->faker->uuid,
            turn: '12:00',
            name: $this->faker->name,
            phone: $this->faker->phoneNumber,
            email: $this->faker->email
        );
        $reservation2 = new Reservation(
            id: $this->faker->uuid,
            turn: '13:00',
            name: $this->faker->name,
            phone: $this->faker->phoneNumber,
            email: $this->faker->email
        );

        $reservations = Reservations::create(reservations: [$reservation1, $reservation2]);

        $this->assertTrue($reservations->hasReservations);
        $this->assertCount(2, $reservations->reservations);
    }

    public function testCreateWithAllParameters(): void
    {
        $date = '2024-03-20';
        $current = 3;
        $reservation = new Reservation(
            id: $this->faker->uuid,
            turn: '18:00',
            name: $this->faker->name,
            phone: $this->faker->phoneNumber,
            email: $this->faker->email
        );

        $reservations = Reservations::create(
            from: $date,
            current: $current,
            reservations: [$reservation]
        );

        $this->assertSame($date, $reservations->date);
        $this->assertSame($current, $reservations->offset);
        $this->assertSame(2, $reservations->prev);
        $this->assertSame(4, $reservations->next);
        $this->assertFalse($reservations->prevDisabled);
        $this->assertTrue($reservations->hasReservations);
    }
}

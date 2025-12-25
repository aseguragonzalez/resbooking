<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Ports\Dashboard\Models\Reservations\Requests;

use PHPUnit\Framework\TestCase;
use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
use Infrastructure\Ports\Dashboard\Models\Reservations\Requests\UpdateReservationRequest;

final class UpdateReservationRequestTest extends TestCase
{
    private Faker $faker;

    protected function setUp(): void
    {
        $this->faker = FakerFactory::create();
    }

    public function testConstructorSetsProperties(): void
    {
        $id = $this->faker->uuid;
        $backUrl = $this->faker->url;
        $name = $this->faker->name;
        $email = $this->faker->email;
        $phone = $this->faker->phoneNumber;

        $request = new UpdateReservationRequest(
            id: $id,
            backUrl: $backUrl,
            name: $name,
            email: $email,
            phone: $phone
        );

        $this->assertSame($id, $request->id);
        $this->assertSame($backUrl, $request->backUrl);
        $this->assertSame($name, $request->name);
        $this->assertSame($email, $request->email);
        $this->assertSame($phone, $request->phone);
    }

    public function testConstructorSetsDefaultValues(): void
    {
        $id = $this->faker->uuid;
        $backUrl = $this->faker->url;

        $request = new UpdateReservationRequest(
            id: $id,
            backUrl: $backUrl
        );

        $this->assertSame($id, $request->id);
        $this->assertSame($backUrl, $request->backUrl);
        $this->assertSame('', $request->name);
        $this->assertSame('', $request->email);
        $this->assertSame('', $request->phone);
    }
}

<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Ports\Dashboard\Models\Reservations\Requests;

use PHPUnit\Framework\TestCase;
use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
use Infrastructure\Ports\Dashboard\Models\Reservations\Requests\UpdateStatusRequest;

final class UpdateStatusRequestTest extends TestCase
{
    private Faker $faker;

    protected function setUp(): void
    {
        $this->faker = FakerFactory::create();
    }

    public function testConstructorSetsProperties(): void
    {
        $id = $this->faker->uuid;
        $status = 'ACCEPTED';
        $offset = $this->faker->numberBetween(0, 100);
        $from = '2024-01-15';

        $request = new UpdateStatusRequest(
            id: $id,
            status: $status,
            offset: $offset,
            from: $from
        );

        $this->assertSame($id, $request->id);
        $this->assertSame($status, $request->status);
        $this->assertSame($offset, $request->offset);
        $this->assertSame($from, $request->from);
    }

    public function testConstructorSetsDefaultValues(): void
    {
        $id = $this->faker->uuid;
        $status = 'CANCELLED';

        $request = new UpdateStatusRequest(
            id: $id,
            status: $status
        );

        $this->assertSame($id, $request->id);
        $this->assertSame($status, $request->status);
        $this->assertSame(0, $request->offset);
        $this->assertSame('now', $request->from);
    }
}

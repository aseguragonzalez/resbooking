<?php

declare(strict_types=1);

namespace Tests\Unit\Seedwork\Infrastructure\Mvc;

use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
use PHPUnit\Framework\TestCase;
use Seedwork\Infrastructure\Mvc\RequestBuilder;
use Tests\Unit\Seedwork\Infrastructure\Mvc\Fixtures\RequestObject;

final class RequestBuilderTest extends TestCase
{
    private Faker $faker;

    public function setUp(): void
    {
        $this->faker = FakerFactory::create();
    }

    public function tearDown(): void
    {
    }

    public function testBuild(): void
    {
        $requestBuilder = new RequestBuilder();
        $requestBuilder->withRequestType(RequestObject::class);
        $body = [
            'id' => $this->faker->randomNumber(),
            'amount' => $this->faker->randomFloat(),
            'name' => $this->faker->name,
            'uuid' => $this->faker->uuid,
            'ksuid' => new \Tuupola\Ksuid(),
            'date' => '2021-01-01 00:00:00',
            'dateImmutable' => '2021-01-01 00:00:00',
            'active' => $this->faker->boolean(),
        ];
        $requestBuilder->withBody(array_map(fn ($item) => (string)$item, $body));

        $requestObject = $requestBuilder->build();

        $this->assertInstanceOf(RequestObject::class, $requestObject);
        $this->assertSame($body['id'], $requestObject->id);
        $this->assertSame($body['amount'], $requestObject->amount);
        $this->assertSame($body['name'], $requestObject->name);
        $this->assertSame($body['uuid'], $requestObject->uuid);
        $this->assertEquals($body['ksuid'], $requestObject->ksuid);
        $this->assertSame($body['date'], $requestObject->date ? $requestObject->date->format('Y-m-d H:i:s') : null);
        $this->assertSame(
            $body['dateImmutable'],
            $requestObject->dateImmutable ? $requestObject->dateImmutable->format('Y-m-d H:i:s') : null
        );
        $this->assertSame($body['active'], $requestObject->active);
    }

    public function testBuildWithDefaultValues(): void
    {
        $requestBuilder = new RequestBuilder();
        $requestBuilder->withRequestType(RequestObject::class);
        $requestBuilder->withBody([]);

        $requestObject = $requestBuilder->build();

        $this->assertInstanceOf(RequestObject::class, $requestObject);
        $this->assertSame(0, $requestObject->id);
        $this->assertSame(0.0, $requestObject->amount);
        $this->assertSame('', $requestObject->name);
        $this->assertSame('', $requestObject->uuid);
        $this->assertNull($requestObject->ksuid);
        $this->assertNull($requestObject->date);
        $this->assertNull($requestObject->dateImmutable);
        $this->assertFalse($requestObject->active);
    }
}

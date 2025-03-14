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
            'active' => $this->faker->boolean()
        ];
        $requestBuilder->withArgs(array_map(fn ($item) => (string)$item, $body));

        $requestObject = $requestBuilder->build();

        $this->assertInstanceOf(RequestObject::class, $requestObject);
        $this->assertSame($body['id'], $requestObject->id);
        $this->assertEqualsWithDelta($body['amount'], $requestObject->amount, 0.1);
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

    public function testBuildWithBuiltInTypeArray(): void
    {
        $requestBuilder = new RequestBuilder();
        $requestBuilder->withRequestType(RequestObject::class);
        $body = [
            'id' => $this->faker->randomNumber(),
            'items[0]' => $this->faker->randomNumber(),
            'items[1]' => $this->faker->randomNumber(),
            'items[2]' => $this->faker->randomNumber(),
            'items[3]' => $this->faker->randomNumber(),
        ];
        $requestBuilder->withArgs(array_map(fn ($item) => (string)$item, $body));

        $requestObject = $requestBuilder->build();

        $this->assertInstanceOf(RequestObject::class, $requestObject);
        $this->assertSame($body['id'], $requestObject->id);
        $this->assertSame($body['items[0]'], $requestObject->items[0]);
        $this->assertSame($body['items[1]'], $requestObject->items[1]);
        $this->assertSame($body['items[2]'], $requestObject->items[2]);
        $this->assertSame($body['items[3]'], $requestObject->items[3]);
    }

    public function testBuildWithClassTypeArray(): void
    {
        $requestBuilder = new RequestBuilder();
        $requestBuilder->withRequestType(RequestObject::class);
        $body = [
            'id' => $this->faker->randomNumber(),
            'ksuidArray[0]' => new \Tuupola\Ksuid(),
            'ksuidArray[1]' => new \Tuupola\Ksuid(),
            'ksuidArray[2]' => new \Tuupola\Ksuid(),
            'ksuidArray[3]' => new \Tuupola\Ksuid(),
        ];
        $requestBuilder->withArgs(array_map(fn ($item) => (string)$item, $body));

        $requestObject = $requestBuilder->build();

        $this->assertInstanceOf(RequestObject::class, $requestObject);
        $this->assertSame($body['id'], $requestObject->id);
        $this->assertEquals($body['ksuidArray[0]'], $requestObject->ksuidArray[0]);
        $this->assertEquals($body['ksuidArray[1]'], $requestObject->ksuidArray[1]);
        $this->assertEquals($body['ksuidArray[2]'], $requestObject->ksuidArray[2]);
        $this->assertEquals($body['ksuidArray[3]'], $requestObject->ksuidArray[3]);
    }

    public function testBuildWithCustomClassTypeArray(): void
    {
        $requestBuilder = new RequestBuilder();
        $requestBuilder->withRequestType(RequestObject::class);
        $body = [
            'id' => $this->faker->randomNumber(),
            'customClassType[0].id' => $this->faker->randomNumber(),
            'customClassType[0].name' => $this->faker->name,
            'customClassType[0].createdAt' => '2021-01-01 00:00:00',
            'customClassType[0].active' => $this->faker->boolean(),
            'customClassType[1].id' => $this->faker->randomNumber(),
            'customClassType[1].name' => $this->faker->name,
            'customClassType[1].createdAt' => '2021-01-02 01:01:00',
            'customClassType[1].active' => $this->faker->boolean(),
            'customClassType[2].id' => $this->faker->randomNumber(),
            'customClassType[2].name' => $this->faker->name,
            'customClassType[2].createdAt' => '2021-01-03 02:02:00',
            'customClassType[2].active' => $this->faker->boolean(),
        ];
        $requestBuilder->withArgs(array_map(fn ($item) => (string)$item, $body));

        $requestObject = $requestBuilder->build();

        $this->assertInstanceOf(RequestObject::class, $requestObject);
        $this->assertSame($body['id'], $requestObject->id);
        $this->assertEquals($body['customClassType[0].id'], $requestObject->customClassType[0]->id);
        $this->assertEquals($body['customClassType[0].name'], $requestObject->customClassType[0]->name);
        $this->assertEquals(
            $body['customClassType[0].createdAt'],
            $requestObject->customClassType[0]->createdAt->format('Y-m-d H:i:s')
        );
        $this->assertEquals($body['customClassType[0].active'], $requestObject->customClassType[0]->active);
        $this->assertEquals($body['customClassType[1].id'], $requestObject->customClassType[1]->id);
        $this->assertEquals($body['customClassType[1].name'], $requestObject->customClassType[1]->name);
        $this->assertEquals(
            $body['customClassType[1].createdAt'],
            $requestObject->customClassType[1]->createdAt->format('Y-m-d H:i:s')
        );
        $this->assertEquals($body['customClassType[1].active'], $requestObject->customClassType[1]->active);
        $this->assertEquals($body['customClassType[2].id'], $requestObject->customClassType[2]->id);
        $this->assertEquals($body['customClassType[2].name'], $requestObject->customClassType[2]->name);
        $this->assertEquals(
            $body['customClassType[2].createdAt'],
            $requestObject->customClassType[2]->createdAt->format('Y-m-d H:i:s')
        );
        $this->assertEquals($body['customClassType[2].active'], $requestObject->customClassType[2]->active);
    }

    public function testBuildWithEmbeddedObject(): void
    {
        $requestBuilder = new RequestBuilder();
        $requestBuilder->withRequestType(RequestObject::class);
        $body = [
            'id' => $this->faker->randomNumber(),
            'innerTypeObject.id' => $this->faker->randomNumber(),
            'innerTypeObject.name' => $this->faker->name,
            'innerTypeObject.createdAt' => '2021-01-01 00:00:00',
            'innerTypeObject.active' => $this->faker->boolean(),
        ];
        $requestBuilder->withArgs(array_map(fn ($item) => (string)$item, $body));

        $requestObject = $requestBuilder->build();

        $this->assertInstanceOf(RequestObject::class, $requestObject);
        $this->assertSame($body['id'], $requestObject->id);
        $this->assertNotNull($requestObject->innerTypeObject);
        $this->assertSame($body['innerTypeObject.id'], $requestObject->innerTypeObject->id);
        $this->assertSame($body['innerTypeObject.name'], $requestObject->innerTypeObject->name);
        $this->assertSame(
            $body['innerTypeObject.createdAt'],
            $requestObject->innerTypeObject->createdAt->format('Y-m-d H:i:s')
        );
        $this->assertSame($body['innerTypeObject.active'], $requestObject->innerTypeObject->active);
    }

    public function testBuildWithDefaultValues(): void
    {
        $requestBuilder = new RequestBuilder();
        $requestBuilder->withRequestType(RequestObject::class);
        $requestBuilder->withArgs([]);

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

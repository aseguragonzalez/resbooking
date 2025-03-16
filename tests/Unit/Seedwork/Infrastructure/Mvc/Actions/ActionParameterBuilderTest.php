<?php

declare(strict_types=1);

namespace Tests\Unit\Seedwork\Infrastructure\Mvc\Actions;

use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Seedwork\Infrastructure\Mvc\Actions\ActionParameterBuilder;
use Tests\Unit\Seedwork\Infrastructure\Mvc\Fixtures\Actions\RequestObject;

final class ActionParameterBuilderTest extends TestCase
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
        $requestBuilder = new ActionParameterBuilder();
        $args = [
            'id' => $this->faker->randomNumber(),
            'amount' => $this->faker->randomFloat(),
            'name' => $this->faker->name,
            'uuid' => $this->faker->uuid,
            'ksuid' => new \Tuupola\Ksuid(),
            'date' => '2021-01-01 00:00:00',
            'dateImmutable' => '2021-01-01 00:00:00',
            'active' => $this->faker->boolean()
        ];
        $requestBuilder->withArgs(array_map(fn ($item) => (string)$item, $args));

        $requestObject = $requestBuilder->build(RequestObject::class);

        $this->assertInstanceOf(RequestObject::class, $requestObject);
        $this->assertSame($args['id'], $requestObject->id);
        $this->assertEqualsWithDelta($args['amount'], $requestObject->amount, 0.1);
        $this->assertSame($args['name'], $requestObject->name);
        $this->assertSame($args['uuid'], $requestObject->uuid);
        $this->assertEquals($args['ksuid'], $requestObject->getKsuid());
        $this->assertSame($args['date'], $requestObject->date ? $requestObject->date->format('Y-m-d H:i:s') : null);
        $this->assertSame(
            $args['dateImmutable'],
            $requestObject->dateImmutable ? $requestObject->dateImmutable->format('Y-m-d H:i:s') : null
        );
        $this->assertSame($args['active'], $requestObject->active);
    }

    /**
     * @param array<string, float|int> $args
     */
    #[DataProvider('buildInNumbersArrayProvider')]
    public function testBuildWithBuiltInNumbersArray(array $args): void
    {
        $requestBuilder = new ActionParameterBuilder();
        $requestBuilder->withArgs($args);

        $requestObject = $requestBuilder->build(RequestObject::class);

        $this->assertInstanceOf(RequestObject::class, $requestObject);
        $this->assertEqualsWithDelta($args['items[0]'], $requestObject->items[0], 1);
        $this->assertEqualsWithDelta($args['items[1]'], $requestObject->items[1], 1);
        $this->assertEqualsWithDelta($args['items[2]'], $requestObject->items[2], 1);
        $this->assertEqualsWithDelta($args['items[3]'], $requestObject->items[3], 1);
    }

    /**
     * @param array<string, string> $args
     */
    #[DataProvider('buildInNotNumbersArrayProvider')]
    public function testBuildWithBuiltInNotNumberArray(array $args): void
    {
        $requestBuilder = new ActionParameterBuilder();
        $requestBuilder->withArgs($args);

        $requestObject = $requestBuilder->build(RequestObject::class);

        $this->assertInstanceOf(RequestObject::class, $requestObject);
        $this->assertSame($args['items[0]'], $requestObject->items[0]);
        $this->assertSame($args['items[1]'], $requestObject->items[1]);
        $this->assertSame($args['items[2]'], $requestObject->items[2]);
        $this->assertSame($args['items[3]'], $requestObject->items[3]);
    }

    public function testBuildWithClassTypeArray(): void
    {
        $requestBuilder = new ActionParameterBuilder();
        $args = [
            'id' => $this->faker->randomNumber(),
            'ksuidArray[0]' => new \Tuupola\Ksuid(),
            'ksuidArray[1]' => new \Tuupola\Ksuid(),
            'ksuidArray[2]' => new \Tuupola\Ksuid(),
            'ksuidArray[3]' => new \Tuupola\Ksuid(),
        ];
        $requestBuilder->withArgs(array_map(fn ($item) => (string)$item, $args));

        $requestObject = $requestBuilder->build(RequestObject::class);

        $this->assertInstanceOf(RequestObject::class, $requestObject);
        $this->assertSame($args['id'], $requestObject->id);
        $this->assertEquals($args['ksuidArray[0]'], $requestObject->ksuidArray[0]);
        $this->assertEquals($args['ksuidArray[1]'], $requestObject->ksuidArray[1]);
        $this->assertEquals($args['ksuidArray[2]'], $requestObject->ksuidArray[2]);
        $this->assertEquals($args['ksuidArray[3]'], $requestObject->ksuidArray[3]);
    }

    public function testBuildWithCustomClassTypeArray(): void
    {
        $requestBuilder = new ActionParameterBuilder();
        $args = [
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
        $requestBuilder->withArgs(array_map(fn ($item) => (string)$item, $args));

        $requestObject = $requestBuilder->build(RequestObject::class);

        $this->assertInstanceOf(RequestObject::class, $requestObject);
        $this->assertSame($args['id'], $requestObject->id);
        $this->assertEquals($args['customClassType[0].id'], $requestObject->customClassType[0]->id);
        $this->assertEquals($args['customClassType[0].name'], $requestObject->customClassType[0]->name);
        $this->assertEquals(
            $args['customClassType[0].createdAt'],
            $requestObject->customClassType[0]->createdAt->format('Y-m-d H:i:s')
        );
        $this->assertEquals($args['customClassType[0].active'], $requestObject->customClassType[0]->active);
        $this->assertEquals($args['customClassType[1].id'], $requestObject->customClassType[1]->id);
        $this->assertEquals($args['customClassType[1].name'], $requestObject->customClassType[1]->name);
        $this->assertEquals(
            $args['customClassType[1].createdAt'],
            $requestObject->customClassType[1]->createdAt->format('Y-m-d H:i:s')
        );
        $this->assertEquals($args['customClassType[1].active'], $requestObject->customClassType[1]->active);
        $this->assertEquals($args['customClassType[2].id'], $requestObject->customClassType[2]->id);
        $this->assertEquals($args['customClassType[2].name'], $requestObject->customClassType[2]->name);
        $this->assertEquals(
            $args['customClassType[2].createdAt'],
            $requestObject->customClassType[2]->createdAt->format('Y-m-d H:i:s')
        );
        $this->assertEquals($args['customClassType[2].active'], $requestObject->customClassType[2]->active);
    }

    public function testBuildWithEmbeddedObject(): void
    {
        $requestBuilder = new ActionParameterBuilder();
        $args = [
            'id' => $this->faker->randomNumber(),
            'innerTypeObject.id' => $this->faker->randomNumber(),
            'innerTypeObject.name' => $this->faker->name,
            'innerTypeObject.createdAt' => '2021-01-01 00:00:00',
            'innerTypeObject.active' => $this->faker->boolean(),
        ];
        $requestBuilder->withArgs(array_map(fn ($item) => (string)$item, $args));

        $requestObject = $requestBuilder->build(RequestObject::class);

        $this->assertInstanceOf(RequestObject::class, $requestObject);
        $this->assertSame($args['id'], $requestObject->id);
        $this->assertNotNull($requestObject->innerTypeObject);
        $this->assertSame($args['innerTypeObject.id'], $requestObject->innerTypeObject->id);
        $this->assertSame($args['innerTypeObject.name'], $requestObject->innerTypeObject->name);
        $this->assertSame(
            $args['innerTypeObject.createdAt'],
            $requestObject->innerTypeObject->createdAt->format('Y-m-d H:i:s')
        );
        $this->assertSame($args['innerTypeObject.active'], $requestObject->innerTypeObject->active);
    }

    public function testBuildWithDefaultValues(): void
    {
        $requestBuilder = new ActionParameterBuilder();
        $requestBuilder->withArgs([]);

        $requestObject = $requestBuilder->build(RequestObject::class);

        $this->assertInstanceOf(RequestObject::class, $requestObject);
        $this->assertSame(0, $requestObject->id);
        $this->assertSame(0.0, $requestObject->amount);
        $this->assertSame('', $requestObject->name);
        $this->assertSame('', $requestObject->uuid);
        $this->assertNull($requestObject->getKsuid());
        $this->assertNull($requestObject->date);
        $this->assertNull($requestObject->dateImmutable);
        $this->assertFalse($requestObject->active);
    }

    /**
     * @return array<array{array<string, float|int>}>
     */
    public static function buildInNumbersArrayProvider(): array
    {
        $faker = FakerFactory::create();
        $intArgs = [
            'items[0]' => $faker->randomNumber(),
            'items[1]' => $faker->randomNumber(),
            'items[2]' => $faker->randomNumber(),
            'items[3]' => $faker->randomNumber(),
        ];
        $floatArgs = [
            'items[0]' => $faker->randomFloat(),
            'items[1]' => $faker->randomFloat(),
            'items[2]' => $faker->randomFloat(),
            'items[3]' => $faker->randomFloat(),
        ];
        return [
            [$intArgs],
            [$floatArgs],
        ];
    }

    /**
     * @return array<array{array<string, string>}>
     */
    public static function buildInNotNumbersArrayProvider(): array
    {
        $faker = FakerFactory::create();
        $stringArgs = [
            'items[0]' => $faker->name,
            'items[1]' => $faker->name,
            'items[2]' => $faker->name,
            'items[3]' => $faker->name,
        ];
        $boolArgs = [
            'items[0]' => $faker->boolean() ? 'true' : 'false',
            'items[1]' => $faker->boolean() ? 'true' : 'false',
            'items[2]' => $faker->boolean() ? 'true' : 'false',
            'items[3]' => $faker->boolean() ? 'true' : 'false',
        ];
        $dateTime = [
            'items[0]' => $faker->dateTime->format('Y-m-d H:i:s'),
            'items[1]' => $faker->dateTime->format('Y-m-d H:i:s'),
            'items[2]' => $faker->dateTime->format('Y-m-d H:i:s'),
            'items[3]' => $faker->dateTime->format('Y-m-d H:i:s'),
        ];
        $dateTimeImmutable = [
            'items[0]' => (new \DateTimeImmutable())->format('Y-m-d H:i:s'),
            'items[1]' => (new \DateTimeImmutable())->format('Y-m-d H:i:s'),
            'items[2]' => (new \DateTimeImmutable())->format('Y-m-d H:i:s'),
            'items[3]' => (new \DateTimeImmutable())->format('Y-m-d H:i:s'),
        ];
        return [
            [$stringArgs],
            [$boolArgs],
            [$dateTime],
            [$dateTimeImmutable],
        ];
    }
}

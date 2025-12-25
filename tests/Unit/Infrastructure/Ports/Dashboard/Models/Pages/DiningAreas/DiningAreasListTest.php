<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Ports\Dashboard\Models\DiningAreas\Pages;

use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
use Infrastructure\Ports\Dashboard\Models\DiningAreas\DiningArea;
use Infrastructure\Ports\Dashboard\Models\DiningAreas\Pages\DiningAreasList;
use PHPUnit\Framework\TestCase;

final class DiningAreasListTest extends TestCase
{
    private Faker $faker;

    protected function setUp(): void
    {
        $this->faker = FakerFactory::create();
    }

    public function testCreateWithEmptyArray(): void
    {
        $list = DiningAreasList::create([]);

        $this->assertSame('{{dining-areas.title}}', $list->pageTitle);
        $this->assertEmpty($list->diningAreas);
        $this->assertFalse($list->hasDiningAreas);
    }

    public function testCreateWithSingleDiningArea(): void
    {
        $diningArea = new DiningArea(
            id: $this->faker->uuid,
            name: $this->faker->name,
            capacity: $this->faker->numberBetween(1, 50)
        );

        $list = DiningAreasList::create([$diningArea]);

        $this->assertSame('{{dining-areas.title}}', $list->pageTitle);
        $this->assertCount(1, $list->diningAreas);
        $this->assertSame($diningArea, $list->diningAreas[0]);
        $this->assertTrue($list->hasDiningAreas);
    }

    public function testCreateWithMultipleDiningAreas(): void
    {
        $diningArea1 = new DiningArea(
            id: $this->faker->uuid,
            name: $this->faker->name,
            capacity: $this->faker->numberBetween(1, 50)
        );
        $diningArea2 = new DiningArea(
            id: $this->faker->uuid,
            name: $this->faker->name,
            capacity: $this->faker->numberBetween(1, 50)
        );

        $list = DiningAreasList::create([$diningArea1, $diningArea2]);

        $this->assertSame('{{dining-areas.title}}', $list->pageTitle);
        $this->assertCount(2, $list->diningAreas);
        $this->assertSame($diningArea1, $list->diningAreas[0]);
        $this->assertSame($diningArea2, $list->diningAreas[1]);
        $this->assertTrue($list->hasDiningAreas);
    }

    public function testHasDiningAreasIsFalseWhenEmpty(): void
    {
        $list = DiningAreasList::create([]);
        $this->assertFalse($list->hasDiningAreas);
    }

    public function testHasDiningAreasIsTrueWhenNotEmpty(): void
    {
        $diningArea = new DiningArea(
            id: $this->faker->uuid,
            name: $this->faker->name,
            capacity: $this->faker->numberBetween(1, 50)
        );

        $list = DiningAreasList::create([$diningArea]);
        $this->assertTrue($list->hasDiningAreas);
    }
}

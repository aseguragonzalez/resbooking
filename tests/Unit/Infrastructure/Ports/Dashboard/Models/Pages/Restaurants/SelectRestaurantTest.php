<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Ports\Dashboard\Models\Restaurants\Pages;

use PHPUnit\Framework\TestCase;
use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
use Infrastructure\Ports\Dashboard\Models\Restaurants\Pages\SelectRestaurant;

final class SelectRestaurantTest extends TestCase
{
    private Faker $faker;

    protected function setUp(): void
    {
        $this->faker = FakerFactory::create();
    }

    public function testWithRestaurantsSetsProperties(): void
    {
        $restaurants = [
            (object)['id' => $this->faker->uuid, 'name' => $this->faker->company],
            (object)['id' => $this->faker->uuid, 'name' => $this->faker->company],
        ];
        $backUrl = $this->faker->url;

        $page = SelectRestaurant::withRestaurants($restaurants, $backUrl);

        $this->assertSame('{{restaurants.select.title}}', $page->pageTitle);
        $this->assertSame($restaurants, $page->restaurants);
        $this->assertSame($backUrl, $page->backUrl);
        $this->assertFalse($page->hasNoRestaurants);
        $this->assertEquals((object)[], $page->errors);
        $this->assertEquals([], $page->errorSummary);
    }

    public function testWithNoRestaurantsSetsHasNoRestaurantsTrue(): void
    {
        $backUrl = $this->faker->url;

        $page = SelectRestaurant::withNoRestaurants($backUrl);

        $this->assertSame('{{restaurants.select.title}}', $page->pageTitle);
        $this->assertEmpty($page->restaurants);
        $this->assertSame($backUrl, $page->backUrl);
        $this->assertTrue($page->hasNoRestaurants);
        $this->assertEquals((object)[], $page->errors);
        $this->assertEquals([], $page->errorSummary);
    }

    public function testWithErrorsSetsErrors(): void
    {
        $errors = [
            'restaurantId' => $this->faker->sentence,
        ];
        $backUrl = $this->faker->url;

        $page = SelectRestaurant::withErrors($errors, $backUrl);

        $this->assertSame('{{restaurants.select.title}}', $page->pageTitle);
        $this->assertEmpty($page->restaurants);
        $this->assertSame($backUrl, $page->backUrl);
        $this->assertFalse($page->hasNoRestaurants);
        $this->assertEquals((object)$errors, $page->errors);
        $this->assertCount(1, $page->errorSummary);
        $this->assertSame('restaurantId', $page->errorSummary[0]->field);
        $this->assertSame($errors['restaurantId'], $page->errorSummary[0]->message);
    }

    public function testWithRestaurantsSetsDefaultBackUrl(): void
    {
        $restaurants = [
            (object)['id' => $this->faker->uuid, 'name' => $this->faker->company],
        ];

        $page = SelectRestaurant::withRestaurants($restaurants, '/');

        $this->assertSame('/', $page->backUrl);
    }

    public function testWithRestaurantsWithMultipleErrors(): void
    {
        $errors = [
            'restaurantId' => $this->faker->sentence,
            'other' => $this->faker->sentence,
        ];
        $backUrl = $this->faker->url;

        $page = SelectRestaurant::withErrors($errors, $backUrl);

        $this->assertCount(2, $page->errorSummary);
        $this->assertSame('restaurantId', $page->errorSummary[0]->field);
        $this->assertSame('other', $page->errorSummary[1]->field);
    }
}

<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Ports\Dashboard\Models\Restaurants\Requests;

use PHPUnit\Framework\TestCase;
use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
use Infrastructure\Ports\Dashboard\Models\Restaurants\Requests\SelectRestaurantRequest;

final class SelectRestaurantRequestTest extends TestCase
{
    private Faker $faker;

    protected function setUp(): void
    {
        $this->faker = FakerFactory::create();
    }

    public function testConstructorSetsProperties(): void
    {
        $restaurantId = $this->faker->uuid;
        $backUrl = $this->faker->url;

        $request = new SelectRestaurantRequest(restaurantId: $restaurantId, backUrl: $backUrl);

        $this->assertSame($restaurantId, $request->restaurantId);
        $this->assertSame($backUrl, $request->backUrl);
    }

    public function testConstructorSetsDefaultBackUrl(): void
    {
        $restaurantId = $this->faker->uuid;

        $request = new SelectRestaurantRequest(restaurantId: $restaurantId);

        $this->assertSame($restaurantId, $request->restaurantId);
        $this->assertSame('/', $request->backUrl);
    }

    public function testValidateReturnsErrorForEmptyRestaurantId(): void
    {
        $request = new SelectRestaurantRequest(restaurantId: '');
        $errors = $request->validate();

        $this->assertArrayHasKey('restaurantId', $errors);
        $this->assertSame('{{restaurants.select.form.restaurantId.error.required}}', $errors['restaurantId']);
    }

    public function testValidateReturnsNoErrorsForValidRestaurantId(): void
    {
        $request = new SelectRestaurantRequest(restaurantId: $this->faker->uuid);
        $errors = $request->validate();

        $this->assertEmpty($errors);
    }
}

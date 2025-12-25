<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Ports\Dashboard\Models\DiningAreas\Pages;

use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
use Infrastructure\Ports\Dashboard\Models\DiningAreas\Pages\EditDiningArea;
use Infrastructure\Ports\Dashboard\Models\DiningAreas\Requests\AddDiningAreaRequest;
use Infrastructure\Ports\Dashboard\Models\DiningAreas\Requests\UpdateDiningAreaRequest;
use PHPUnit\Framework\TestCase;

final class EditDiningAreaTest extends TestCase
{
    private Faker $faker;

    protected function setUp(): void
    {
        $this->faker = FakerFactory::create();
    }

    public function testNewCreatesDefaultForm(): void
    {
        $page = EditDiningArea::new();

        $this->assertSame('{{dining-areas.create.form.title}}', $page->pageTitle);
        $this->assertInstanceOf(AddDiningAreaRequest::class, $page->diningArea);
        $this->assertNull($page->diningAreaId);
        $this->assertSame('/dining-areas', $page->backUrl);
        $this->assertSame('/dining-areas', $page->actionUrl);
        $this->assertEquals((object)[], $page->errors);
        $this->assertEquals([], $page->errorSummary);
    }

    public function testNewWithCustomBackUrl(): void
    {
        $backUrl = $this->faker->url;
        $page = EditDiningArea::new(backUrl: $backUrl);

        $this->assertSame($backUrl, $page->backUrl);
    }

    public function testFromDiningAreaCreatesEditForm(): void
    {
        $diningAreaId = $this->faker->uuid;
        $name = $this->faker->name;
        $capacity = $this->faker->numberBetween(1, 50);

        $page = EditDiningArea::fromDiningArea(
            diningAreaId: $diningAreaId,
            name: $name,
            capacity: $capacity
        );

        $this->assertSame('{{dining-areas.edit.form.title}}', $page->pageTitle);
        $this->assertInstanceOf(UpdateDiningAreaRequest::class, $page->diningArea);
        $this->assertSame($diningAreaId, $page->diningAreaId);
        $this->assertSame($name, $page->diningArea->name);
        $this->assertSame($capacity, $page->diningArea->capacity);
        $this->assertSame("/dining-areas/{$diningAreaId}", $page->actionUrl);
        $this->assertSame('/dining-areas', $page->backUrl);
        $this->assertEquals((object)[], $page->errors);
        $this->assertEquals([], $page->errorSummary);
    }

    public function testFromDiningAreaWithCustomBackUrl(): void
    {
        $backUrl = $this->faker->url;
        $page = EditDiningArea::fromDiningArea(
            diningAreaId: $this->faker->uuid,
            name: $this->faker->name,
            capacity: 10,
            backUrl: $backUrl
        );

        $this->assertSame($backUrl, $page->backUrl);
    }

    public function testWithErrorsSetsErrors(): void
    {
        $errors = [
            'name' => $this->faker->sentence,
            'capacity' => $this->faker->sentence,
        ];
        $request = new AddDiningAreaRequest(
            name: $this->faker->name,
            capacity: 10
        );

        $page = EditDiningArea::withErrors(
            request: $request,
            errors: $errors
        );

        $this->assertSame('{{dining-areas.create.form.title}}', $page->pageTitle);
        $this->assertSame($request, $page->diningArea);
        $this->assertNull($page->diningAreaId);
        $this->assertSame('/dining-areas', $page->actionUrl);
        $this->assertEquals((object)$errors, $page->errors);
        $this->assertCount(2, $page->errorSummary);
        $this->assertSame('name', $page->errorSummary[0]->field);
        $this->assertSame('capacity', $page->errorSummary[1]->field);
    }

    public function testWithErrorsWithDiningAreaIdCreatesEditForm(): void
    {
        $diningAreaId = $this->faker->uuid;
        $errors = ['name' => $this->faker->sentence];
        $request = new UpdateDiningAreaRequest(
            name: $this->faker->name,
            capacity: 15
        );

        $page = EditDiningArea::withErrors(
            request: $request,
            errors: $errors,
            diningAreaId: $diningAreaId
        );

        $this->assertSame('{{dining-areas.edit.form.title}}', $page->pageTitle);
        $this->assertSame($diningAreaId, $page->diningAreaId);
        $this->assertSame("/dining-areas/{$diningAreaId}", $page->actionUrl);
        $this->assertEquals((object)$errors, $page->errors);
    }

    public function testWithErrorsWithCustomBackUrl(): void
    {
        $backUrl = $this->faker->url;
        $request = new AddDiningAreaRequest();
        $errors = ['name' => $this->faker->sentence];

        $page = EditDiningArea::withErrors(
            request: $request,
            errors: $errors,
            backUrl: $backUrl
        );

        $this->assertSame($backUrl, $page->backUrl);
    }
}

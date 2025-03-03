<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Projects\UpdateSettings;

use App\Application\Projects\UpdateSettings\{UpdateSettings, UpdateSettingsRequest};
use App\Domain\Projects\ProjectRepository;
use App\Domain\Projects\ValueObjects\Settings;
use App\Domain\Shared\{Capacity, Email, Phone};
use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Tests\Unit\ProjectBuilder;

final class UpdateSettingsTest extends TestCase
{
    private Faker $faker;
    private ProjectBuilder $projectBuilder;
    private MockObject&ProjectRepository $projectRepository;

    protected function setUp(): void
    {
        $this->faker = FakerFactory::create();
        $this->projectBuilder = new ProjectBuilder($this->faker);
        $this->projectRepository = $this->createMock(ProjectRepository::class);
    }

    protected function tearDown(): void
    {
    }

    public function testUpdateSettingsShouldUpdateProjectSettings(): void
    {
        $settings = new Settings(
            email: new Email($this->faker->email),
            hasRemainders: $this->faker->boolean,
            name: $this->faker->name,
            maxNumberOfDiners: new Capacity(10),
            minNumberOfDiners: new Capacity(10),
            numberOfTables: new Capacity(10),
            phone: new Phone($this->faker->phoneNumber)
        );

        $project = $this->projectBuilder->withSettings($settings)->build();
        $this->projectRepository
            ->expects($this->once())
            ->method('getById')
            ->willReturn($project);
        $this->projectRepository
            ->expects($this->once())
            ->method('save')
            ->with($project);
        $request = new UpdateSettingsRequest(
            projectId: $this->faker->uuid,
            email: new Email($this->faker->email),
            hasRemainders: $this->faker->boolean,
            name: $this->faker->name,
            maxNumberOfDiners: new Capacity(10),
            minNumberOfDiners: new Capacity(10),
            numberOfTables: new Capacity(10),
            phone: new Phone($this->faker->phoneNumber)
        );
        $useCase = new UpdateSettings(projectRepository: $this->projectRepository);

        $useCase->execute($request);

        $currentSettings = $project->getSettings();
        $this->assertSame($request->email, $currentSettings->email);
        $this->assertSame($request->hasRemainders, $currentSettings->hasRemainders);
        $this->assertSame($request->name, $currentSettings->name);
        $this->assertSame($request->maxNumberOfDiners, $currentSettings->maxNumberOfDiners);
        $this->assertSame($request->minNumberOfDiners, $currentSettings->minNumberOfDiners);
        $this->assertSame($request->numberOfTables, $currentSettings->numberOfTables);
        $this->assertSame($request->phone, $currentSettings->phone);
    }
}

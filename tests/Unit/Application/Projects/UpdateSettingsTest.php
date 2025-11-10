<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Projects\UpdateSettings;

use Application\Projects\UpdateSettings\UpdateSettings;
use Application\Projects\UpdateSettings\UpdateSettingsCommand;
use Domain\Projects\ProjectRepository;
use Domain\Projects\ValueObjects\Settings;
use Domain\Shared\Capacity;
use Domain\Shared\Email;
use Domain\Shared\Phone;
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

    public function testUpdateProjectSettings(): void
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
        $request = new UpdateSettingsCommand(
            projectId: $this->faker->uuid,
            email: new Email($this->faker->email),
            hasRemainders: $this->faker->boolean,
            name: $this->faker->name,
            maxNumberOfDiners: new Capacity(10),
            minNumberOfDiners: new Capacity(10),
            numberOfTables: new Capacity(10),
            phone: new Phone($this->faker->phoneNumber)
        );
        $ApplicationService = new UpdateSettings(projectRepository: $this->projectRepository);

        $ApplicationService->execute($request);

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

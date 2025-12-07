<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Projects\UpdateSettings;

use Application\Projects\UpdateSettings\UpdateSettingsCommand;
use Application\Projects\UpdateSettings\UpdateSettingsService;
use Domain\Projects\Repositories\ProjectRepository;
use Domain\Projects\Services\ProjectObtainer;
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
    private MockObject&ProjectObtainer $projectObtainer;

    protected function setUp(): void
    {
        $this->faker = FakerFactory::create();
        $this->projectBuilder = new ProjectBuilder($this->faker);
        $this->projectRepository = $this->createMock(ProjectRepository::class);
        $this->projectObtainer = $this->createMock(ProjectObtainer::class);
    }

    public function testUpdateProjectSettings(): void
    {
        $settings = new Settings(
            email: new Email($this->faker->email),
            hasReminders: $this->faker->boolean,
            name: $this->faker->name,
            maxNumberOfDiners: new Capacity(10),
            minNumberOfDiners: new Capacity(10),
            numberOfTables: new Capacity(10),
            phone: new Phone($this->faker->phoneNumber)
        );
        $project = $this->projectBuilder->withSettings($settings)->build();
        $this->projectObtainer->expects($this->once())
            ->method('obtain')
            ->with($this->isString())
            ->willReturn($project);
        $this->projectRepository
            ->expects($this->once())
            ->method('save')
            ->with($project);
        $request = new UpdateSettingsCommand(
            projectId: $this->faker->uuid,
            email: $this->faker->email,
            hasReminders: $this->faker->boolean,
            name: $this->faker->name,
            maxNumberOfDiners: 10,
            minNumberOfDiners: 10,
            numberOfTables: 10,
            phone: $this->faker->phoneNumber
        );
        $ApplicationService = new UpdateSettingsService($this->projectObtainer, $this->projectRepository);

        $ApplicationService->execute($request);

        $currentSettings = $project->getSettings();
        $this->assertSame($request->email, $currentSettings->email->value);
        $this->assertSame($request->hasReminders, $currentSettings->hasReminders);
        $this->assertSame($request->name, $currentSettings->name);
        $this->assertSame($request->maxNumberOfDiners, $currentSettings->maxNumberOfDiners->value);
        $this->assertSame($request->minNumberOfDiners, $currentSettings->minNumberOfDiners->value);
        $this->assertSame($request->numberOfTables, $currentSettings->numberOfTables->value);
        $this->assertSame($request->phone, $currentSettings->phone->value);
    }
}

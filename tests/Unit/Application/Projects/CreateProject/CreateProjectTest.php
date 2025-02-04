<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Projects\CreateProject;

use Faker\Factory as FakerFactory;
use PHPUnit\Framework\TestCase;
use App\Domain\Projects\Entities\Project;
use App\Domain\Projects\ProjectRepository;
use Tests\Unit\ProjectBuilder;

final class CreateProjectTest extends TestCase
{
    private $faker = null;
    private $projectBuilder = null;
    private ?ProjectRepository $projectRepository = null;

    protected function setUp(): void
    {
        $this->faker = FakerFactory::create();
        $this->projectBuilder = new ProjectBuilder($this->faker);
        $this->projectRepository = $this->createMock(ProjectRepository::class);
    }

    protected function tearDown(): void
    {
        $this->faker = null;
        $this->projectBuilder = null;
        $this->projectRepository = null;
    }

    public function testFake(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}

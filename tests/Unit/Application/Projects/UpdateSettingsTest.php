<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Projects;

use Faker\Factory as FakerFactory;
use PHPUnit\Framework\TestCase;
use App\Domain\Projects\ProjectRepository;

final class UpdateSettingsTest extends TestCase
{
    private $faker = null;
    private ProjectRepository $projectRepository;

    protected function setUp(): void
    {
        $this->faker = FakerFactory::create();
        $this->projectRepository = $this->createMock(ProjectRepository::class);
    }

    protected function tearDown(): void
    {
        $this->faker = null;
        $this->projectRepository = null;
    }

    public function testUpdateSettingsShouldUpdateProjectSettings(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function testUpdateSettingsShouldFailWhenProjectDoesNotExist(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function testUpdateSettingsShouldFailWhenUserIsNotAuthorized(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function testUpdateSettingsShouldFailWhenDinersLimitAreInvalid(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function testUpdateSettingsShouldFailWhenNameIsInvalid(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function testUpdateSettingsShouldFailWhenClaimIsInvalid(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function testUpdateSettingsShouldFailWhenEmailIsInvalid(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function testUpdateSettingsShouldFailWhenPhoneIsInvalid(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function testUpdateSettingsShouldFailWhenAddressIsInvalid(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}

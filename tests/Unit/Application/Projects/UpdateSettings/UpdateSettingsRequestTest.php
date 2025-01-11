<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Projects\UpdateSettings;

use Faker\Factory as FakerFactory;
use PHPUnit\Framework\TestCase;

final class UpdateSettingsRequestTest extends TestCase
{
    private $faker = null;

    protected function setUp(): void
    {
        $this->faker = FakerFactory::create();
    }

    protected function tearDown(): void
    {
        $this->faker = null;
    }

    public function testUpdateSettingsShouldFailWhenDinersLimitAreInvalid(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function testUpdateSettingsShouldFailWhenNameIsInvalid(): void
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
}

<?php

declare(strict_types=1);

namespace Tests\Unit\Framework\Apps\Migrations;

use Framework\Apps\Migrations\MigrationApp;
use PHPUnit\Framework\TestCase;

final class MigrationAppTest extends TestCase
{
    public function testStripMigrationsBaseFromArgvRemovesFlagAndReturnsCleanArgv(): void
    {
        [$override, $clean] = MigrationApp::stripMigrationsBaseFromArgv([
            '--migrations-base=/var/migrations',
            '--test=20260123183421',
        ]);

        $this->assertSame('/var/migrations', $override);
        $this->assertSame(['--test=20260123183421'], $clean);
    }

    public function testStripMigrationsBaseFromArgvReturnsNullOverrideWhenAbsent(): void
    {
        [$override, $clean] = MigrationApp::stripMigrationsBaseFromArgv(['--test=abc']);

        $this->assertNull($override);
        $this->assertSame(['--test=abc'], $clean);
    }

    public function testStripMigrationsBaseFromArgvTreatsEmptyValueAsNullOverride(): void
    {
        [$override, $clean] = MigrationApp::stripMigrationsBaseFromArgv(['--migrations-base=', 'x']);

        $this->assertNull($override);
        $this->assertSame(['x'], $clean);
    }
}

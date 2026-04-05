<?php

declare(strict_types=1);

namespace Tests\Unit\Framework\Cli\Commands;

use Framework\Cli\Commands\MigrationsLeafPathResolver;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

final class MigrationsLeafPathResolverTest extends TestCase
{
    public function testResolvesNestedMigrationsDirectory(): void
    {
        vfsStream::setup('app', null, [
            'mvc.config.json' => json_encode([
                'migrationsFolderPath' => './Migrations',
                'migrationsEnabled' => true,
            ], JSON_THROW_ON_ERROR),
            'Migrations' => [
                'index.php' => '<?php',
                'migrations' => [],
            ],
        ]);

        $leaf = MigrationsLeafPathResolver::resolveLeafMigrationsDir(vfsStream::url('app'));

        $this->assertSame(vfsStream::url('app/Migrations/migrations'), $leaf);
    }

    public function testResolvesFlatModuleWhenMigrationsSubdirMissing(): void
    {
        vfsStream::setup('flat', null, [
            'mvc.config.json' => json_encode([
                'migrationsFolderPath' => './Adapters/Migrations',
                'migrationsEnabled' => true,
            ], JSON_THROW_ON_ERROR),
            'Adapters' => [
                'Migrations' => [
                    'index.php' => '<?php',
                    '20260123183421' => [
                        '0001_migration.sql' => '',
                    ],
                ],
            ],
        ]);

        $leaf = MigrationsLeafPathResolver::resolveLeafMigrationsDir(vfsStream::url('flat'));

        $this->assertSame(vfsStream::url('flat/Adapters/Migrations'), $leaf);
    }

    public function testReturnsNullWhenMigrationsDisabledUnlessIgnoreDisabled(): void
    {
        vfsStream::setup('off', null, [
            'mvc.config.json' => json_encode([
                'migrationsFolderPath' => './Migrations',
                'migrationsEnabled' => false,
            ], JSON_THROW_ON_ERROR),
            'Migrations' => [
                'index.php' => '<?php',
                'migrations' => [],
            ],
        ]);

        $app = vfsStream::url('off');
        $this->assertNull(MigrationsLeafPathResolver::resolveLeafMigrationsDir($app));
        $this->assertSame(
            vfsStream::url('off/Migrations/migrations'),
            MigrationsLeafPathResolver::resolveLeafMigrationsDir($app, ignoreDisabled: true),
        );
    }
}

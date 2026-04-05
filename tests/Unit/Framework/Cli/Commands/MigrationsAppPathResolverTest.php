<?php

declare(strict_types=1);

namespace Tests\Unit\Framework\Commands;

use Framework\Commands\MigrationsAppPathResolver;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

final class MigrationsAppPathResolverTest extends TestCase
{
    public function testResolveIndexPathFromLeafDirWhenLeafIsMigrationsSubfolder(): void
    {
        vfsStream::setup('app', null, [
            'Migrations' => [
                'index.php' => '<?php',
                'migrations' => [],
            ],
        ]);

        $leaf = vfsStream::url('app/Migrations/migrations');
        $index = MigrationsAppPathResolver::resolveIndexPathFromLeafDir($leaf);

        $this->assertSame(vfsStream::url('app/Migrations/index.php'), $index);
    }

    public function testResolveIndexPathFromLeafDirWhenFlatModuleRoot(): void
    {
        vfsStream::setup('mod', null, [
            'index.php' => '<?php',
            '20260123183421' => [],
        ]);

        $leaf = vfsStream::url('mod');
        $index = MigrationsAppPathResolver::resolveIndexPathFromLeafDir($leaf);

        $this->assertSame(vfsStream::url('mod/index.php'), $index);
    }

    public function testResolveIndexPathFromLeafDirReturnsNullWhenIndexMissing(): void
    {
        vfsStream::setup('bad', null, [
            'migrations' => [],
        ]);

        $leaf = vfsStream::url('bad/migrations');
        $index = MigrationsAppPathResolver::resolveIndexPathFromLeafDir($leaf);

        $this->assertNull($index);
    }

    public function testResolveIndexPathUsesMvcConfigModuleFolder(): void
    {
        vfsStream::setup('root', null, [
            'mvc.config.json' => json_encode([
                'migrationsFolderPath' => './DbMigrations',
                'migrationsEnabled' => true,
            ], JSON_THROW_ON_ERROR),
            'DbMigrations' => [
                'index.php' => '<?php',
                'migrations' => [],
            ],
        ]);

        $app = vfsStream::url('root');
        $index = MigrationsAppPathResolver::resolveIndexPath($app);

        $this->assertSame(vfsStream::url('root/DbMigrations/index.php'), $index);
    }
}

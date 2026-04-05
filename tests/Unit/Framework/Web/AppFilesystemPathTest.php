<?php

declare(strict_types=1);

namespace Tests\Unit\Framework\Web;

use Framework\Web\AppFilesystemPath;
use PHPUnit\Framework\TestCase;

final class AppFilesystemPathTest extends TestCase
{
    public function testJoinTrimsRedundantSeparators(): void
    {
        $this->assertSame(
            '/var/app/Views',
            AppFilesystemPath::join('/var/app/', '/Views')
        );
    }

    public function testJoinPreservesTrailingSlashOnRelativeSegment(): void
    {
        $this->assertSame(
            '/var/app/assets/i18n/',
            AppFilesystemPath::join('/var/app', 'assets/i18n/')
        );
    }

    public function testJoinWithEmptyBaseReturnsRelativeOnly(): void
    {
        $this->assertSame('Views/', AppFilesystemPath::join('', 'Views/'));
    }
}

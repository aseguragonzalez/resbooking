<?php

declare(strict_types=1);

namespace Tests\Unit\Framework\Mvc\Config;

use Framework\Mvc\Config\MvcConfig;
use Framework\Mvc\UiAssetsSettings;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

final class MvcConfigTest extends TestCase
{
    public function testLoadReturnsDefaultsWhenConfigFileIsMissing(): void
    {
        vfsStream::setup('project', null, [
            // no mvc.config.json
        ]);

        $config = MvcConfig::load(vfsStream::url('project'));

        $this->assertSame('./assets/scripts', $config->jsAssetsPath);
        $this->assertSame('main.min.js', $config->mainJsBundler);
        $this->assertSame('./assets/styles', $config->cssAssetsPath);
        $this->assertSame('main.min.css', $config->mainCssBundler);
        $this->assertSame('./assets/i18n', $config->i18nPath);
        $this->assertSame('', $config->migrationsFolderPath);
        $this->assertNull($config->migrationsEnabled);
        $this->assertTrue($config->isMigrationsEnabled());
        $this->assertSame('Migrations', $config->effectiveMigrationsModuleRelativePath());
        $this->assertSame('', $config->backgroundTasksFolderPath);
        $this->assertNull($config->authenticationEnabled);
        $this->assertFalse($config->isAuthenticationEnabled());
    }

    public function testNormalizationRemovesDotSlashAndTrailingSlashes(): void
    {
        vfsStream::setup('project', null, [
            MvcConfig::CONFIG_FILENAME => json_encode([
                'jsAssetsPath' => './assets/scripts/',
                'mainJsBundler' => '/main.min.js',
                'cssAssetsPath' => 'assets/styles/',
                'mainCssBundler' => '/main.min.css',
                'i18nPath' => './assets/i18n',
                'migrationsFolderPath' => '',
                'migrationsEnabled' => false,
                'backgroundTasksFolderPath' => './BackgroundTasks/',
                'authenticationEnabled' => true,
            ], JSON_THROW_ON_ERROR),
        ]);

        $config = MvcConfig::load(vfsStream::url('project'));

        $this->assertSame('assets/i18n/', $config->normalizedI18nAssetsPathForLanguageSettings());
        $this->assertSame('', $config->normalizedMigrationsFolderPath());
        $this->assertFalse($config->isMigrationsEnabled());
        $this->assertSame('Migrations', $config->effectiveMigrationsModuleRelativePath());
        $this->assertSame('BackgroundTasks', $config->normalizedBackgroundTasksFolderPath());
        $this->assertTrue($config->authenticationEnabled);
        $this->assertTrue($config->isAuthenticationEnabled());

        $uiAssets = UiAssetsSettings::fromConfig($config);
        $this->assertSame('/assets/scripts', $uiAssets->jsAssetsPathUrl);
        $this->assertSame('main.min.js', $uiAssets->mainJsBundler);
        $this->assertSame('/assets/styles', $uiAssets->cssAssetsPathUrl);
        $this->assertSame('main.min.css', $uiAssets->mainCssBundler);
    }

    public function testWriteMergedToAppPersistsAuthenticationEnabled(): void
    {
        vfsStream::setup('app', null, [
            'index.php' => '<?php',
            MvcConfig::CONFIG_FILENAME => json_encode([
                'jsAssetsPath' => './assets/scripts',
                'mainJsBundler' => 'main.min.js',
                'cssAssetsPath' => './assets/styles',
                'mainCssBundler' => 'main.min.css',
                'i18nPath' => './assets/i18n',
                'migrationsFolderPath' => '',
                'migrationsEnabled' => false,
                'backgroundTasksFolderPath' => '',
                'authenticationEnabled' => false,
            ], JSON_THROW_ON_ERROR),
        ]);

        $base = vfsStream::url('app');
        MvcConfig::writeMergedToApp($base, ['authenticationEnabled' => true]);

        $config = MvcConfig::load($base);
        $this->assertTrue($config->isAuthenticationEnabled());
    }
}

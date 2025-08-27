<?php

declare(strict_types=1);

namespace Tests\Unit\Seedwork\Infrastructure\Mvc\Views;

use PHPUnit\Framework\TestCase;
use Seedwork\Infrastructure\Mvc\Views\I18nReplacer;
use Seedwork\Infrastructure\Mvc\Requests\RequestContext;
use Seedwork\Infrastructure\Mvc\Requests\RequestContextKeys;
use Seedwork\Infrastructure\Mvc\Settings;

final class I18nReplacerTest extends TestCase
{
    private string $i18nDir;

    private I18nReplacer $i18nReplacer;

    protected function setUp(): void
    {
        $this->i18nDir = sys_get_temp_dir() . '/i18n_test_' . uniqid();
        mkdir($this->i18nDir);
        $settings = new Settings(basePath: '', i18nPath: $this->i18nDir, viewPath: '');
        $this->i18nReplacer = new I18nReplacer($settings);
    }

    protected function tearDown(): void
    {
        $files = glob($this->i18nDir . '/*.json');
        if ($files) {
            foreach ($files as $file) {
                unlink($file);
            }
        }
        rmdir($this->i18nDir);
    }

    /**
     * @param array<string, string> $dict
     */
    private function createLangFile(string $lang, array $dict): void
    {
        file_put_contents("{$this->i18nDir}/{$lang}.json", json_encode($dict));
    }

    public function testReplacesKeysWithDictionaryValues(): void
    {
        $this->createLangFile('en', [
            'greeting' => 'Hello',
            'name' => 'Peter',
        ]);
        $context = new RequestContext([RequestContextKeys::LANGUAGE->value => 'en']);
        $template = '{{greeting}}, {{name}}!';

        $result = $this->i18nReplacer->replace((object)[], $template, $context);

        $this->assertSame('Hello, Peter!', $result);
    }

    public function testThrowsExceptionIfLanguageFileNotFound(): void
    {
        $context = new RequestContext([RequestContextKeys::LANGUAGE->value => 'fr']);
        $template = '{{greeting}}';

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageMatches('/Language file not found/');
        $this->i18nReplacer->replace((object)[], $template, $context);
    }

    public function testThrowsExceptionIfLanguageFileIsInvalidJson(): void
    {
        file_put_contents("{$this->i18nDir}/en.json", '{invalid json');
        $context = new RequestContext([RequestContextKeys::LANGUAGE->value => 'en']);
        $template = '{{greeting}}';

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageMatches('/Failed to decode language file/');
        $this->i18nReplacer->replace((object)[], $template, $context);
    }

    public function testReplacesWithEmptyDictionary(): void
    {
        $this->createLangFile('en', []);
        $context = new RequestContext([RequestContextKeys::LANGUAGE->value => 'en']);
        $template = 'No keys here. {{some-key}}';

        $result = $this->i18nReplacer->replace((object)[], $template, $context);

        $this->assertSame('No keys here. {{some-key}}', $result);
    }

    public function testReplacesWithMissingKeysInDictionary(): void
    {
        $this->createLangFile('en', ['greeting' => 'Hello']);
        $context = new RequestContext([RequestContextKeys::LANGUAGE->value => 'en']);
        $template = '{{greeting}}, {{name}}!';

        $result = $this->i18nReplacer->replace((object)[], $template, $context);

        $this->assertSame('Hello, {{name}}!', $result);
    }
}

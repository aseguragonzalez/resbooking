<?php

declare(strict_types=1);

namespace Tests\Unit\Seedwork\Infrastructure\Mvc\Views;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Seedwork\Infrastructure\Files\FileManager;
use Seedwork\Infrastructure\Mvc\Views\I18nReplacer;
use Seedwork\Infrastructure\Mvc\Requests\RequestContext;
use Seedwork\Infrastructure\Mvc\Requests\RequestContextKeys;
use Seedwork\Infrastructure\Mvc\Settings;

final class I18nReplacerTest extends TestCase
{
    private FileManager&MockObject $fileManager;
    private I18nReplacer $i18nReplacer;

    protected function setUp(): void
    {
        $settings = new Settings(basePath: '', i18nPath: './', viewPath: '');
        $this->fileManager = $this->createMock(FileManager::class);
        $this->i18nReplacer = new I18nReplacer($settings, $this->fileManager);
    }

    public function testReplacesKeysWithDictionaryValues(): void
    {
        $this->fileManager
            ->method('readKeyValueJson')
            ->willReturn([
                'greeting' => 'Hello',
                'name' => 'Peter',
            ]);
        $context = new RequestContext([RequestContextKeys::Language->value => 'en']);
        $template = '{{greeting}}, {{name}}!';
        $result = $this->i18nReplacer->replace((object)[], $template, $context);
        $this->assertSame('Hello, Peter!', $result);
    }

    public function testReplacesWithEmptyDictionary(): void
    {
        $this->fileManager
            ->method('readKeyValueJson')
            ->willReturn([]);
        $context = new RequestContext([RequestContextKeys::Language->value => 'en']);
        $template = 'No keys here. {{some-key}}';
        $result = $this->i18nReplacer->replace((object)[], $template, $context);
        $this->assertSame('No keys here. {{some-key}}', $result);
    }

    public function testReplacesWithMissingKeysInDictionary(): void
    {
        $this->fileManager
            ->method('readKeyValueJson')
            ->willReturn(['greeting' => 'Hello']);
        $context = new RequestContext([RequestContextKeys::Language->value => 'en']);
        $template = '{{greeting}}, {{name}}!';
        $result = $this->i18nReplacer->replace((object)[], $template, $context);
        $this->assertSame('Hello, {{name}}!', $result);
    }
}

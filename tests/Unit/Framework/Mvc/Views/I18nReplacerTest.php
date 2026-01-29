<?php

declare(strict_types=1);

namespace Tests\Unit\Framework\Mvc\Views;

use Framework\Files\FileManager;
use Framework\Mvc\LanguageSettings;
use Framework\Mvc\Requests\RequestContext;
use Framework\Mvc\Requests\RequestContextKeys;
use Framework\Mvc\Views\BranchesReplacer;
use Framework\Mvc\Views\I18nReplacer;
use Framework\Mvc\Views\ModelReplacer;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

final class I18nReplacerTest extends TestCase
{
    private FileManager&Stub $fileManager;
    private I18nReplacer $i18nReplacer;
    private RequestContext $context;

    protected function setUp(): void
    {
        $settings = new LanguageSettings(basePath: __DIR__);
        $branchesReplacer = new BranchesReplacer(new ModelReplacer());
        $this->fileManager = $this->createStub(FileManager::class);
        $this->i18nReplacer = new I18nReplacer($settings, $this->fileManager, $branchesReplacer);
        $this->context = new RequestContext([RequestContextKeys::Language->value => 'en']);
    }

    public function testReplacesKeysWithDictionaryValues(): void
    {
        $this->fileManager
            ->method('readKeyValueJson')
            ->willReturn([
                'greeting' => 'Hello',
                'name' => 'Peter',
            ]);

        $result = $this->i18nReplacer->replace((object)[], '{{greeting}}, {{name}}!', $this->context);

        $this->assertSame('Hello, Peter!', $result);
    }

    public function testReplacesWithEmptyDictionary(): void
    {
        $this->fileManager->method('readKeyValueJson')->willReturn([]);

        $result = $this->i18nReplacer->replace((object)[], 'No keys here. {{some-key}}', $this->context);

        $this->assertSame('No keys here. {{some-key}}', $result);
    }

    public function testReplacesWithMissingKeysInDictionary(): void
    {
        $this->fileManager->method('readKeyValueJson')->willReturn(['greeting' => 'Hello']);

        $result = $this->i18nReplacer->replace((object)[], '{{greeting}}, {{name}}!', $this->context);

        $this->assertSame('Hello, {{name}}!', $result);
    }
}

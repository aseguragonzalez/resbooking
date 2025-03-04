<?php

declare(strict_types=1);

namespace Tests\Unit\Seedwork\Infrastructure\Mvc\Responses\Headers;

use PHPUnit\Framework\TestCase;
use Seedwork\Infrastructure\Mvc\Responses\Headers\ContentLanguage;

final class ContentLanguageTest extends TestCase
{
    public function testSingleLanguage(): void
    {
        $header = new ContentLanguage(english: true);

        $this->assertSame('Content-Language', $header->name);
        $this->assertSame('en', $header->value);
    }

    public function testMultipleLanguages(): void
    {
        $header = new ContentLanguage(english: true, french: true, spanish: true);

        $this->assertSame('Content-Language', $header->name);
        $this->assertSame('en, fr, es', $header->value);
    }

    public function testNoLanguage(): void
    {
        $header = new ContentLanguage();

        $this->assertSame('Content-Language', $header->name);
        $this->assertSame('', $header->value);
    }

    public function testAllLanguages(): void
    {
        $header = new ContentLanguage(
            english: true,
            french: true,
            spanish: true,
            portuguese: true,
            german: true,
            italian: true,
            dutch: true,
            russian: true
        );

        $this->assertSame('Content-Language', $header->name);
        $this->assertSame('en, fr, es, pt, de, it, nl, ru', $header->value);
    }
}

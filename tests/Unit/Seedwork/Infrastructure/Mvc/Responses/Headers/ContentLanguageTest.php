<?php

declare(strict_types=1);

namespace Tests\Unit\Seedwork\Infrastructure\Mvc\Responses\Headers;

use PHPUnit\Framework\TestCase;
use Seedwork\Infrastructure\Mvc\Responses\Headers\ContentLanguage;
use Seedwork\Infrastructure\Mvc\Responses\Headers\ContentType;

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

    public function testToStringReturnsFormattedHeader(): void
    {
        $header = new ContentLanguage(english: true);

        $this->assertSame('Content-Language: en', (string) $header);
    }

    public function testCreateFromCurrentLanguageWithEnglish(): void
    {
        $header = ContentLanguage::createFromCurrentLanguage('en');

        $this->assertSame('Content-Language', $header->name);
        $this->assertSame('en', $header->value);
    }

    public function testCreateFromCurrentLanguageWithFrench(): void
    {
        $header = ContentLanguage::createFromCurrentLanguage('fr');

        $this->assertSame('Content-Language', $header->name);
        $this->assertSame('fr', $header->value);
    }

    public function testCreateFromCurrentLanguageWithSpanish(): void
    {
        $header = ContentLanguage::createFromCurrentLanguage('es');

        $this->assertSame('Content-Language', $header->name);
        $this->assertSame('es', $header->value);
    }

    public function testCreateFromCurrentLanguageWithPortuguese(): void
    {
        $header = ContentLanguage::createFromCurrentLanguage('pt');

        $this->assertSame('Content-Language', $header->name);
        $this->assertSame('pt', $header->value);
    }

    public function testCreateFromCurrentLanguageWithGerman(): void
    {
        $header = ContentLanguage::createFromCurrentLanguage('de');

        $this->assertSame('Content-Language', $header->name);
        $this->assertSame('de', $header->value);
    }

    public function testCreateFromCurrentLanguageWithItalian(): void
    {
        $header = ContentLanguage::createFromCurrentLanguage('it');

        $this->assertSame('Content-Language', $header->name);
        $this->assertSame('it', $header->value);
    }

    public function testCreateFromCurrentLanguageWithDutch(): void
    {
        $header = ContentLanguage::createFromCurrentLanguage('nl');

        $this->assertSame('Content-Language', $header->name);
        $this->assertSame('nl', $header->value);
    }

    public function testCreateFromCurrentLanguageWithRussian(): void
    {
        $header = ContentLanguage::createFromCurrentLanguage('ru');

        $this->assertSame('Content-Language', $header->name);
        $this->assertSame('ru', $header->value);
    }

    public function testCreateFromCurrentLanguageThrowsExceptionForInvalidLanguage(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid language: ja');

        ContentLanguage::createFromCurrentLanguage('ja');
    }

    public function testEqualsReturnsTrueForSameHeaders(): void
    {
        $header1 = new ContentLanguage(english: true, french: true);
        $header2 = new ContentLanguage(english: true, french: true);

        $this->assertTrue($header1->equals($header2));
    }

    public function testEqualsReturnsFalseForHeadersWithDifferentValues(): void
    {
        $header1 = new ContentLanguage(english: true);
        $header2 = new ContentLanguage(french: true);

        $this->assertFalse($header1->equals($header2));
    }

    public function testEqualsReturnsFalseForHeadersWithDifferentNames(): void
    {
        $contentLanguage = new ContentLanguage(english: true);
        $contentType = ContentType::json();

        $this->assertFalse($contentLanguage->equals($contentType));
    }

    public function testToStringWithMultipleLanguages(): void
    {
        $header = new ContentLanguage(english: true, french: true, spanish: true);

        $this->assertSame('Content-Language: en, fr, es', (string) $header);
    }

    public function testToStringWithEmptyValue(): void
    {
        $header = new ContentLanguage();

        $this->assertSame('Content-Language: ', (string) $header);
    }

    public function testFrenchLanguage(): void
    {
        $header = new ContentLanguage(french: true);

        $this->assertSame('Content-Language', $header->name);
        $this->assertSame('fr', $header->value);
    }

    public function testSpanishLanguage(): void
    {
        $header = new ContentLanguage(spanish: true);

        $this->assertSame('Content-Language', $header->name);
        $this->assertSame('es', $header->value);
    }

    public function testPortugueseLanguage(): void
    {
        $header = new ContentLanguage(portuguese: true);

        $this->assertSame('Content-Language', $header->name);
        $this->assertSame('pt', $header->value);
    }

    public function testGermanLanguage(): void
    {
        $header = new ContentLanguage(german: true);

        $this->assertSame('Content-Language', $header->name);
        $this->assertSame('de', $header->value);
    }

    public function testItalianLanguage(): void
    {
        $header = new ContentLanguage(italian: true);

        $this->assertSame('Content-Language', $header->name);
        $this->assertSame('it', $header->value);
    }

    public function testDutchLanguage(): void
    {
        $header = new ContentLanguage(dutch: true);

        $this->assertSame('Content-Language', $header->name);
        $this->assertSame('nl', $header->value);
    }

    public function testRussianLanguage(): void
    {
        $header = new ContentLanguage(russian: true);

        $this->assertSame('Content-Language', $header->name);
        $this->assertSame('ru', $header->value);
    }

    public function testTwoLanguagesCombination(): void
    {
        $header = new ContentLanguage(german: true, italian: true);

        $this->assertSame('Content-Language', $header->name);
        $this->assertSame('de, it', $header->value);
    }

    public function testToStringWithAllLanguages(): void
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

        $this->assertSame('Content-Language: en, fr, es, pt, de, it, nl, ru', (string) $header);
    }
}

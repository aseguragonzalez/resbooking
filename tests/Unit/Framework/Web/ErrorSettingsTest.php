<?php

declare(strict_types=1);

namespace Tests\Unit\Framework\Web;

use Framework\Web\ErrorSettings;
use PHPUnit\Framework\TestCase;

final class ErrorSettingsTest extends TestCase
{
    public function testFrameworkDefaultHasEmptyMapAnd500Fallback(): void
    {
        $settings = ErrorSettings::frameworkDefault();

        $this->assertSame([], $settings->errorsMapping);
        $this->assertSame(500, $settings->errorsMappingDefaultValue->statusCode);
        $this->assertSame('Shared/500', $settings->errorsMappingDefaultValue->templateName);
        $this->assertSame('Internal Server Error', $settings->errorsMappingDefaultValue->pageTitle);
    }
}

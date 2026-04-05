<?php

declare(strict_types=1);

namespace Framework\Web\Actions;

/**
 * Marks a public controller instance method as invokable by the MVC router.
 */
#[\Attribute(\Attribute::TARGET_METHOD)]
final class MvcAction
{
}

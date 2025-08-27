<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Mvc\Views;

use Seedwork\Infrastructure\Mvc\Requests\RequestContext;

final class BranchesReplacer extends ContentReplacerBase
{
    public function __construct(?ContentReplacer $nextReplacer = null)
    {
        parent::__construct($nextReplacer);
    }

    protected function customReplace(object $model, string $template, RequestContext $context): string
    {
        $branchesToReplace = $model == null ? [] : $this->replaceBranches(model: $model, template: $template);

        return str_replace(array_keys($branchesToReplace), array_values($branchesToReplace), $template);
    }

    /**
     * @return array<string, string>
     */
    private function replaceBranches(object $model, string $template): array
    {
        // TODO: allow nested if statements
        $expressionsToReplace = [];
        preg_match_all(
            "/\{\{#if (.*?):\}\}(.*?)\{\{#endif \\1:\}\}/s",
            $template,
            $matches,
            PREG_SET_ORDER
        );
        foreach ($matches as $match) {
            $expression = $match[1];
            $blockContent = $match[2];
            $expressionsToReplace[$match[0]] = $this->checkExpression($expression, $model) ? $blockContent : '';
        }
        return $expressionsToReplace;
    }

    private function checkExpression(string $expression, object $model): bool
    {
        $parts = explode('->', trim(str_replace(['!', '()'], ['', ''], $expression)));
        $path = $model;
        foreach ($parts as $next) {
            // @phpstan-ignore-next-line
            if (property_exists($path, $next)) {
                $path = $path->$next;
            // @phpstan-ignore-next-line
            } elseif (method_exists($path, $next)) {
                $path = $path->$next();
            } elseif (preg_match('/^(\w+)\[(.*?)\]$/', $next, $arrayMatches)) {
                // NOTE: check if next is an key-value dict (array)
                $property = $arrayMatches[1];
                $key = $arrayMatches[2];
                $cleanKey = str_replace(['"', "'"], '', $key);
                if (
                    // @phpstan-ignore-next-line
                    property_exists($path, $property)
                    && is_array($path->$property)
                    && array_key_exists($cleanKey, $path->$property)
                ) {
                    $path = $path->$property[$cleanKey];
                }
            } else {
                $path = null;
                break;
            }
        }
        return str_starts_with($expression, '!') ? !(bool)$path : (bool)$path;
    }
}

<?php

declare(strict_types=1);

namespace Tests\Unit\Seedwork\Infrastructure\Mvc\Views;

use PHPUnit\Framework\TestCase;
use Seedwork\Infrastructure\Mvc\Views\BranchesReplacer;

final class BranchesReplacerTest extends TestCase
{
    public function testReplacesIfBranchWithTrueProperty(): void
    {
        $model = (object)['isVisible' => true];
        $template = 'Hello {{#if isVisible:}}World{{#endif isVisible:}}!';
        $replacer = new BranchesReplacer();
        $result = $replacer->replace($model, $template);
        $this->assertSame('Hello World!', $result);
    }

    public function testReplacesIfBranchWithFalseProperty(): void
    {
        $model = (object)['isVisible' => false];
        $template = 'Hello {{#if isVisible:}}World{{#endif isVisible:}}!';
        $replacer = new BranchesReplacer();
        $result = $replacer->replace($model, $template);
        $this->assertSame('Hello !', $result);
    }

    public function testReplacesIfBranchWithNegation(): void
    {
        $model = (object)['isVisible' => false];
        $template = 'Hello {{#if !isVisible:}}Hidden{{#endif !isVisible:}}!';
        $replacer = new BranchesReplacer();
        $result = $replacer->replace($model, $template);
        $this->assertSame('Hello Hidden!', $result);
    }

    public function testReplacesIfBranchWithNestedProperty(): void
    {
        $model = (object)['user' => (object)['active' => true]];
        $template = 'User is {{#if user->active:}}active{{#endif user->active:}}.';
        $replacer = new BranchesReplacer();
        $result = $replacer->replace($model, $template);
        $this->assertSame('User is active.', $result);
    }

    public function testReplacesIfBranchWithArrayProperty(): void
    {
        $model = (object)['items' => ['foo' => true]];
        $template = 'Item: {{#if items["foo"]:}}exists{{#endif items["foo"]:}}.';
        $replacer = new BranchesReplacer();
        $result = $replacer->replace($model, $template);
        $this->assertSame('Item: exists.', $result);
    }

    public function testDoesNotReplaceIfBranchWhenModelIsNull(): void
    {
        $template = 'Hello {{#if isVisible:}}World{{#endif isVisible:}}!';
        $replacer = new BranchesReplacer();
        $result = $replacer->replace((object)[], $template);
        $this->assertSame('Hello !', $result);
    }

    public function testReplacesMultipleBranches(): void
    {
        $model = (object)['a' => true, 'b' => false];
        $template = '{{#if a:}}A{{#endif a:}},{{#if b:}}B{{#endif b:}}';
        $replacer = new BranchesReplacer();
        $result = $replacer->replace($model, $template);
        $this->assertSame('A,', $result);
    }
}

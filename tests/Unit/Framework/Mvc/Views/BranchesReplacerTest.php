<?php

declare(strict_types=1);

namespace Tests\Unit\Framework\Mvc\Views;

use PHPUnit\Framework\TestCase;
use Framework\Mvc\Requests\RequestContext;
use Framework\Mvc\Views\BranchesReplacer;
use Framework\Mvc\Views\ModelReplacer;

final class BranchesReplacerTest extends TestCase
{
    private ModelReplacer $modelReplacer;
    private BranchesReplacer $branchesReplacer;

    public function setUp(): void
    {
        $this->modelReplacer = new ModelReplacer();
        $this->branchesReplacer = new BranchesReplacer($this->modelReplacer);
    }

    public function testReplacesIfBranchWithTrueProperty(): void
    {
        $model = (object)['isVisible' => true];
        $template = 'Hello {{#if isVisible:}}World{{#endif isVisible:}}!';

        $result = $this->branchesReplacer->replace($model, $template, new RequestContext());

        $this->assertSame('Hello World!', $result);
    }

    public function testReplacesIfBranchWithFalseProperty(): void
    {
        $model = (object)['isVisible' => false];
        $template = 'Hello {{#if isVisible:}}World{{#endif isVisible:}}!';

        $result = $this->branchesReplacer->replace($model, $template, new RequestContext());

        $this->assertSame('Hello !', $result);
    }

    public function testReplacesIfBranchWithNegation(): void
    {
        $model = (object)['isVisible' => false];
        $template = 'Hello {{#if !isVisible:}}Hidden{{#endif !isVisible:}}!';

        $result = $this->branchesReplacer->replace($model, $template, new RequestContext());

        $this->assertSame('Hello Hidden!', $result);
    }

    public function testReplacesIfBranchWithNestedProperty(): void
    {
        $model = (object)['user' => (object)['active' => true]];
        $template = 'User is {{#if user->active:}}active{{#endif user->active:}}.';

        $result = $this->branchesReplacer->replace($model, $template, new RequestContext());

        $this->assertSame('User is active.', $result);
    }

    public function testReplacesIfBranchWithArrayProperty(): void
    {
        $model = (object)['items' => ['foo' => true]];
        $template = 'Item: {{#if items["foo"]:}}exists{{#endif items["foo"]:}}.';

        $result = $this->branchesReplacer->replace($model, $template, new RequestContext());

        $this->assertSame('Item: exists.', $result);
    }

    public function testDoesNotReplaceIfBranchWhenModelIsNull(): void
    {
        $template = 'Hello {{#if isVisible:}}World{{#endif isVisible:}}!';

        $result = $this->branchesReplacer->replace((object)[], $template, new RequestContext());

        $this->assertSame('Hello !', $result);
    }

    public function testReplacesMultipleBranches(): void
    {
        $model = (object)['a' => true, 'b' => false];
        $template = '{{#if a:}}A{{#endif a:}},{{#if b:}}B{{#endif b:}}';

        $result = $this->branchesReplacer->replace($model, $template, new RequestContext());

        $this->assertSame('A,', $result);
    }
}

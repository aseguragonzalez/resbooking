<?php

declare(strict_types=1);

namespace Tests\Unit\Seedwork\Infrastructure\Mvc\Views;

use PHPUnit\Framework\TestCase;
use Seedwork\Infrastructure\Mvc\Views\ModelReplacer;

final class ModelReplacerTest extends TestCase
{
    public function testReplacesPrimitiveProperties(): void
    {
        $model = (object)[
            'name' => 'Peter Parker',
            'age' => 25,
            'height' => 1.75,
            'isStudent' => true,
            'isEmployed' => false,
            'createdAt' => new \DateTimeImmutable('2025-01-02T12:01:02.000Z'),
        ];
        $template = 'Name: {{name}}, Age: {{age}}, Height: {{height}},' .
             ' Student: {{isStudent}}, Employed: {{isEmployed}}, Created: {{createdAt}}';
        $replacer = new ModelReplacer();
        $result = $replacer->replace($model, $template);
        $this->assertStringContainsString('Peter Parker', $result);
        $this->assertStringContainsString('25', $result);
        $this->assertStringContainsString('1.75', $result);
        $this->assertStringContainsString('true', $result);
        $this->assertStringContainsString('false', $result);
        $this->assertStringContainsString('2025-01-02T12:01:02+00:00', $result);
    }

    public function testReplacesNestedObjectProperties(): void
    {
        $address = (object)[
            'street' => 'Elm Street',
            'number' => 123,
            'city' => 'Springwood',
        ];
        $model = (object)[
            'name' => 'Freddy Krueger',
            'address' => $address,
        ];
        $template = 'Name: {{name}}, Street: {{address->street}}, City: {{address->city}}';
        $replacer = new ModelReplacer();
        $result = $replacer->replace($model, $template);
        $this->assertStringContainsString('Freddy Krueger', $result);
        $this->assertStringContainsString('Elm Street', $result);
        $this->assertStringContainsString('Springwood', $result);
    }

    public function testReplacesArrayOfStrings(): void
    {
        $model = (object)[
            'items' => ['foo', 'bar', 'baz']
        ];
        $template = '{{#for item in items:}}Item: {{item}}; {{#endfor items:}}';
        $replacer = new ModelReplacer();
        $result = $replacer->replace($model, $template);
        $this->assertStringContainsString('Item: foo;', $result);
        $this->assertStringContainsString('Item: bar;', $result);
        $this->assertStringContainsString('Item: baz;', $result);
    }

    public function testReplacesArrayOfObjects(): void
    {
        $user1 = (object)['id' => '1', 'name' => 'Peter'];
        $user2 = (object)['id' => '2', 'name' => 'Freddy'];
        $model = (object)[
            'users' => [$user1, $user2]
        ];
        $template = '{{#for user in users:}}User: {{user->name}}; {{#endfor users:}}';
        $replacer = new ModelReplacer();
        $result = $replacer->replace($model, $template);
        $this->assertStringContainsString('User: Peter;', $result);
        $this->assertStringContainsString('User: Freddy;', $result);
    }

    public function testHandlesEmptyModel(): void
    {
        $model = (object)[];
        $template = 'Nothing to replace here.';
        $replacer = new ModelReplacer();
        $result = $replacer->replace($model, $template);
        $this->assertSame('Nothing to replace here.', $result);
    }

    public function testHandlesMissingProperty(): void
    {
        $model = (object)['name' => 'Peter'];
        $template = 'Name: {{name}}, Age: {{age}}';
        $replacer = new ModelReplacer();
        $result = $replacer->replace($model, $template);
        $this->assertStringContainsString('Peter', $result);
        $this->assertStringContainsString('Age: ', $result);
    }
}

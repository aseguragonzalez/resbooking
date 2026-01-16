<?php

declare(strict_types=1);

namespace Tests\Unit\Framework\Migrations\Domain;

use PHPUnit\Framework\TestCase;
use Framework\Migrations\Domain\ValueObjects\ColumnDefinition;
use Framework\Migrations\Domain\ValueObjects\ForeignKeyDefinition;
use Framework\Migrations\Domain\ValueObjects\IndexDefinition;
use Framework\Migrations\Domain\ValueObjects\SchemaSnapshot;
use Framework\Migrations\Domain\ValueObjects\TableDefinition;

final class SchemaSnapshotTest extends TestCase
{
    public function testItCreatesASchemaSnapshot(): void
    {
        $columns = [
            ColumnDefinition::new('id', 'int(11)', false, null, true, 'auto_increment'),
        ];
        $indexes = [];
        $foreignKeys = [];
        $table = TableDefinition::new('users', $columns, $indexes, $foreignKeys);
        $tables = [$table];

        $snapshot = SchemaSnapshot::new($tables);

        $this->assertCount(1, $snapshot->tables);
        $this->assertSame($table, $snapshot->tables[0]);
    }

    public function testItCreatesASchemaSnapshotWithMultipleTables(): void
    {
        $columns1 = [
            ColumnDefinition::new('id', 'int(11)', false, null, true, 'auto_increment'),
        ];
        $table1 = TableDefinition::new('users', $columns1, [], []);

        $columns2 = [
            ColumnDefinition::new('id', 'int(11)', false, null, true, 'auto_increment'),
            ColumnDefinition::new('user_id', 'int(11)', false, null, false, null),
        ];
        $table2 = TableDefinition::new('posts', $columns2, [], []);

        $snapshot = SchemaSnapshot::new([$table1, $table2]);

        $this->assertCount(2, $snapshot->tables);
    }
}

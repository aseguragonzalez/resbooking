<?php

declare(strict_types=1);

namespace Tests\Unit\Framework\Migrations\Domain;

use PHPUnit\Framework\TestCase;
use Framework\Migrations\Domain\Services\SchemaComparatorHandler;
use Framework\Migrations\Domain\ValueObjects\ColumnDefinition;
use Framework\Migrations\Domain\ValueObjects\ForeignKeyDefinition;
use Framework\Migrations\Domain\ValueObjects\IndexDefinition;
use Framework\Migrations\Domain\ValueObjects\SchemaSnapshot;
use Framework\Migrations\Domain\ValueObjects\TableDefinition;

final class SchemaComparatorServiceTest extends TestCase
{
    private SchemaComparatorHandler $comparator;

    protected function setUp(): void
    {
        $this->comparator = new SchemaComparatorHandler();
    }

    public function testItReturnsEqualWhenSchemasAreIdentical(): void
    {
        $columns = [
            ColumnDefinition::new('id', 'int(11)', false, null, true, 'auto_increment'),
            ColumnDefinition::new('name', 'varchar(255)', false, null, false, null),
        ];
        $table = TableDefinition::new('users', $columns, [], []);
        $snapshot = SchemaSnapshot::new([$table]);

        $result = $this->comparator->compare($snapshot, $snapshot);

        $this->assertTrue($result->areEqual);
        $this->assertEmpty($result->differences);
    }

    public function testItDetectsMissingTable(): void
    {
        $columns = [
            ColumnDefinition::new('id', 'int(11)', false, null, true, 'auto_increment'),
        ];
        $table = TableDefinition::new('users', $columns, [], []);
        $initialSnapshot = SchemaSnapshot::new([$table]);
        $finalSnapshot = SchemaSnapshot::new([]);

        $result = $this->comparator->compare($initialSnapshot, $finalSnapshot);

        $this->assertFalse($result->areEqual);
        $this->assertContains("Table 'users' was removed", $result->differences);
    }

    public function testItDetectsAddedTable(): void
    {
        $columns = [
            ColumnDefinition::new('id', 'int(11)', false, null, true, 'auto_increment'),
        ];
        $table = TableDefinition::new('users', $columns, [], []);
        $initialSnapshot = SchemaSnapshot::new([]);
        $finalSnapshot = SchemaSnapshot::new([$table]);

        $result = $this->comparator->compare($initialSnapshot, $finalSnapshot);

        $this->assertFalse($result->areEqual);
        $this->assertContains("Table 'users' was added", $result->differences);
    }

    public function testItDetectsMissingColumn(): void
    {
        $initialColumns = [
            ColumnDefinition::new('id', 'int(11)', false, null, true, 'auto_increment'),
            ColumnDefinition::new('name', 'varchar(255)', false, null, false, null),
        ];
        $finalColumns = [
            ColumnDefinition::new('id', 'int(11)', false, null, true, 'auto_increment'),
        ];
        $initialTable = TableDefinition::new('users', $initialColumns, [], []);
        $finalTable = TableDefinition::new('users', $finalColumns, [], []);
        $initialSnapshot = SchemaSnapshot::new([$initialTable]);
        $finalSnapshot = SchemaSnapshot::new([$finalTable]);

        $result = $this->comparator->compare($initialSnapshot, $finalSnapshot);

        $this->assertFalse($result->areEqual);
        $this->assertContains("Table 'users': Column 'name' was removed", $result->differences);
    }

    public function testItDetectsColumnTypeChange(): void
    {
        $initialColumns = [
            ColumnDefinition::new('id', 'int(11)', false, null, true, 'auto_increment'),
        ];
        $finalColumns = [
            ColumnDefinition::new('id', 'bigint(20)', false, null, true, 'auto_increment'),
        ];
        $initialTable = TableDefinition::new('users', $initialColumns, [], []);
        $finalTable = TableDefinition::new('users', $finalColumns, [], []);
        $initialSnapshot = SchemaSnapshot::new([$initialTable]);
        $finalSnapshot = SchemaSnapshot::new([$finalTable]);

        $result = $this->comparator->compare($initialSnapshot, $finalSnapshot);

        $this->assertFalse($result->areEqual);
        $this->assertContains(
            "Table 'users': Column 'id' type changed from 'int(11)' to 'bigint(20)'",
            $result->differences,
        );
    }

    public function testItDetectsNullableChange(): void
    {
        $initialColumns = [
            ColumnDefinition::new('name', 'varchar(255)', false, null, false, null),
        ];
        $finalColumns = [
            ColumnDefinition::new('name', 'varchar(255)', true, null, false, null),
        ];
        $initialTable = TableDefinition::new('users', $initialColumns, [], []);
        $finalTable = TableDefinition::new('users', $finalColumns, [], []);
        $initialSnapshot = SchemaSnapshot::new([$initialTable]);
        $finalSnapshot = SchemaSnapshot::new([$finalTable]);

        $result = $this->comparator->compare($initialSnapshot, $finalSnapshot);

        $this->assertFalse($result->areEqual);
        $this->assertContains(
            "Table 'users': Column 'name' changed from not nullable to nullable",
            $result->differences,
        );
    }

    public function testItDetectsIndexChanges(): void
    {
        $columns = [
            ColumnDefinition::new('id', 'int(11)', false, null, true, 'auto_increment'),
        ];
        $initialIndexes = [
            IndexDefinition::new('idx_name', 'users', ['name'], false),
        ];
        $finalIndexes = [];
        $initialTable = TableDefinition::new('users', $columns, $initialIndexes, []);
        $finalTable = TableDefinition::new('users', $columns, $finalIndexes, []);
        $initialSnapshot = SchemaSnapshot::new([$initialTable]);
        $finalSnapshot = SchemaSnapshot::new([$finalTable]);

        $result = $this->comparator->compare($initialSnapshot, $finalSnapshot);

        $this->assertFalse($result->areEqual);
        $this->assertContains("Table 'users': Index 'idx_name' was removed", $result->differences);
    }
}

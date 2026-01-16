<?php

declare(strict_types=1);

namespace Framework\Migrations\Infrastructure;

use PDO;
use Framework\Migrations\Domain\Services\SchemaSnapshotExecutor;
use Framework\Migrations\Domain\ValueObjects\ColumnDefinition;
use Framework\Migrations\Domain\ValueObjects\ForeignKeyDefinition;
use Framework\Migrations\Domain\ValueObjects\IndexDefinition;
use Framework\Migrations\Domain\ValueObjects\SchemaSnapshot;
use Framework\Migrations\Domain\ValueObjects\TableDefinition;

final readonly class SqlSchemaSnapshotExecutor implements SchemaSnapshotExecutor
{
    public function __construct(private PDO $db)
    {
    }

    public function capture(): SchemaSnapshot
    {
        $databaseName = $this->getDatabaseName();
        $tables = $this->getTables($databaseName);

        $tableDefinitions = [];
        foreach ($tables as $tableName) {
            $columns = $this->getColumns($databaseName, $tableName);
            $indexes = $this->getIndexes($databaseName, $tableName);
            $foreignKeys = $this->getForeignKeys($databaseName, $tableName);

            $tableDefinitions[] = TableDefinition::new(
                name: $tableName,
                columns: $columns,
                indexes: $indexes,
                foreignKeys: $foreignKeys,
            );
        }

        // Sort tables by name for consistent comparison
        usort($tableDefinitions, fn (TableDefinition $a, TableDefinition $b) => strcmp($a->name, $b->name));

        return SchemaSnapshot::new($tableDefinitions);
    }

    private function getDatabaseName(): string
    {
        $stmt = $this->db->query('SELECT DATABASE() as db_name');
        if ($stmt === false) {
            throw new \RuntimeException('Failed to query database name');
        }
        /** @var array<string, string>|false $result */
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result === false) {
            return '';
        }
        return (string) $result['db_name'];
    }

    /**
     * @return array<string>
     */
    private function getTables(string $databaseName): array
    {
        $stmt = $this->db->prepare(
            'SELECT TABLE_NAME
            FROM information_schema.TABLES
            WHERE TABLE_SCHEMA = :database_name
            AND TABLE_TYPE = \'BASE TABLE\'
            ORDER BY TABLE_NAME'
        );
        $stmt->execute(['database_name' => $databaseName]);
        /** @var array<int, array<string, string>> $tables */
        $tables = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(function (array $row): string {
            /** @var array<string, string> $row */
            return (string) $row['TABLE_NAME'];
        }, $tables);
    }

    /**
     * @return array<ColumnDefinition>
     */
    private function getColumns(string $databaseName, string $tableName): array
    {
        $stmt = $this->db->prepare(
            'SELECT
                COLUMN_NAME,
                COLUMN_TYPE,
                IS_NULLABLE,
                COLUMN_DEFAULT,
                COLUMN_KEY,
                EXTRA
            FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = :database_name
            AND TABLE_NAME = :table_name
            ORDER BY ORDINAL_POSITION'
        );
        $stmt->execute([
            'database_name' => $databaseName,
            'table_name' => $tableName,
        ]);

        $columns = [];
        while (($row = $stmt->fetch(PDO::FETCH_ASSOC)) !== false) {
            /** @var array<string, string|null> $row */
            $columns[] = ColumnDefinition::new(
                name: (string) $row['COLUMN_NAME'],
                type: (string) $row['COLUMN_TYPE'],
                isNullable: (string) ($row['IS_NULLABLE'] ?? 'NO') === 'YES',
                defaultValue: $row['COLUMN_DEFAULT'] !== null ? (string) $row['COLUMN_DEFAULT'] : null,
                isPrimaryKey: (string) ($row['COLUMN_KEY'] ?? '') === 'PRI',
                extra: $row['EXTRA'] !== null && $row['EXTRA'] !== '' ? (string) $row['EXTRA'] : null,
            );
        }

        return $columns;
    }

    /**
     * @return array<IndexDefinition>
     */
    private function getIndexes(string $databaseName, string $tableName): array
    {
        $stmt = $this->db->prepare(
            'SELECT
                INDEX_NAME,
                COLUMN_NAME,
                NON_UNIQUE
            FROM information_schema.STATISTICS
            WHERE TABLE_SCHEMA = :database_name
            AND TABLE_NAME = :table_name
            ORDER BY INDEX_NAME, SEQ_IN_INDEX'
        );
        $stmt->execute([
            'database_name' => $databaseName,
            'table_name' => $tableName,
        ]);

        /** @var array<string, array{name: string, columns: array<string>, isUnique: bool}> $indexData */
        $indexData = [];
        while (($row = $stmt->fetch(PDO::FETCH_ASSOC)) !== false) {
            /** @var array<string, string> $row */
            $indexName = (string) $row['INDEX_NAME'];
            if ($indexName === 'PRIMARY') {
                continue; // Skip primary key as it's already in column definition
            }

            if (!isset($indexData[$indexName])) {
                $indexData[$indexName] = [
                    'name' => $indexName,
                    'columns' => [],
                    'isUnique' => (string) $row['NON_UNIQUE'] === '0',
                ];
            }

            $indexData[$indexName]['columns'][] = (string) $row['COLUMN_NAME'];
        }

        $indexes = [];
        foreach ($indexData as $data) {
            $indexes[] = IndexDefinition::new(
                name: $data['name'],
                tableName: $tableName,
                columns: $data['columns'],
                isUnique: $data['isUnique'],
            );
        }

        // Sort indexes by name for consistent comparison
        usort($indexes, fn (IndexDefinition $a, IndexDefinition $b) => strcmp($a->name, $b->name));

        return $indexes;
    }

    /**
     * @return array<ForeignKeyDefinition>
     */
    private function getForeignKeys(string $databaseName, string $tableName): array
    {
        $stmt = $this->db->prepare(
            'SELECT
                kcu.CONSTRAINT_NAME,
                kcu.COLUMN_NAME,
                kcu.REFERENCED_TABLE_NAME,
                kcu.REFERENCED_COLUMN_NAME
            FROM information_schema.KEY_COLUMN_USAGE kcu
            INNER JOIN information_schema.REFERENTIAL_CONSTRAINTS rc
                ON kcu.CONSTRAINT_NAME = rc.CONSTRAINT_NAME
                AND kcu.TABLE_SCHEMA = rc.CONSTRAINT_SCHEMA
            WHERE kcu.TABLE_SCHEMA = :database_name
            AND kcu.TABLE_NAME = :table_name
            AND kcu.REFERENCED_TABLE_NAME IS NOT NULL
            ORDER BY kcu.CONSTRAINT_NAME, kcu.ORDINAL_POSITION'
        );
        $stmt->execute([
            'database_name' => $databaseName,
            'table_name' => $tableName,
        ]);

        /** @var array<string, array{name: string, columns: array<string>, referencedTable: string, referencedColumns: array<string>}> $fkData */
        $fkData = [];
        while (($row = $stmt->fetch(PDO::FETCH_ASSOC)) !== false) {
            /** @var array<string, string> $row */
            $constraintName = (string) $row['CONSTRAINT_NAME'];
            if (!isset($fkData[$constraintName])) {
                $fkData[$constraintName] = [
                    'name' => $constraintName,
                    'columns' => [],
                    'referencedTable' => (string) $row['REFERENCED_TABLE_NAME'],
                    'referencedColumns' => [],
                ];
            }

            $fkData[$constraintName]['columns'][] = (string) $row['COLUMN_NAME'];
            $fkData[$constraintName]['referencedColumns'][] = (string) $row['REFERENCED_COLUMN_NAME'];
        }

        $foreignKeys = [];
        foreach ($fkData as $data) {
            $foreignKeys[] = ForeignKeyDefinition::new(
                name: $data['name'],
                tableName: $tableName,
                columns: $data['columns'],
                referencedTableName: $data['referencedTable'],
                referencedColumns: $data['referencedColumns'],
            );
        }

        // Sort foreign keys by name for consistent comparison
        usort($foreignKeys, fn (ForeignKeyDefinition $a, ForeignKeyDefinition $b) => strcmp($a->name, $b->name));

        return $foreignKeys;
    }
}

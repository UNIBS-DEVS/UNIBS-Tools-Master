<?php

namespace App\Services;

use Illuminate\Support\Facades\Schema;

class DatabaseInspectorService
{
    public function getTables(): array
    {
        $databaseName = \Illuminate\Support\Facades\DB::connection('dynamic')->getDatabaseName();
        $tables = Schema::connection('dynamic')->getTables($databaseName);
        $result = [];

        foreach ($tables as $table) {
            $name = $table['name'];

            // Skip phpMyAdmin system/configuration tables
            if (str_starts_with($name, 'pma__')) {
                continue;
            }

            try {
                // Get approximate or exact row count
                $rowCount = \Illuminate\Support\Facades\DB::connection('dynamic')->table($name)->count();
            } catch (\Exception $e) {
                $rowCount = 0;
            }

            $result[] = [
                'name' => $name,
                'rows' => $rowCount,
            ];
        }

        return $result;
    }

    public function getTableSchema(string $tableName): array
    {
        if (str_starts_with($tableName, 'pma__') || !Schema::connection('dynamic')->hasTable($tableName)) {
            return [];
        }
        return Schema::connection('dynamic')->getColumns($tableName);
    }

    public function getTableRelations(string $tableName): array
    {
        if (str_starts_with($tableName, 'pma__') || !Schema::connection('dynamic')->hasTable($tableName)) {
            return ['outgoing' => [], 'incoming' => []];
        }

        $outgoing = Schema::connection('dynamic')->getForeignKeys($tableName);
        $incoming = [];

        // Find incoming relations from all other tables
        $databaseName = \Illuminate\Support\Facades\DB::connection('dynamic')->getDatabaseName();
        $tables = Schema::connection('dynamic')->getTables($databaseName);
        foreach ($tables as $table) {
            $otherTable = $table['name'];
            if ($otherTable === $tableName || str_starts_with($otherTable, 'pma__')) {
                continue;
            }

            try {
                $fks = Schema::connection('dynamic')->getForeignKeys($otherTable);
                foreach ($fks as $fk) {
                    if ($fk['foreign_table'] === $tableName) {
                        $incoming[] = [
                            'table' => $otherTable,
                            'columns' => $fk['columns'],
                            'foreign_columns' => $fk['foreign_columns'],
                            'name' => $fk['name'] ?? null,
                            'on_update' => $fk['on_update'] ?? null,
                            'on_delete' => $fk['on_delete'] ?? null,
                        ];
                    }
                }
            } catch (\Exception $e) {
                // Ignore errors reading foreign keys of single tables
            }
        }

        return [
            'outgoing' => $outgoing,
            'incoming' => $incoming,
        ];
    }

    public function getTableData(string $tableName, ?string $search = null, int $perPage = 15)
    {
        if (str_starts_with($tableName, 'pma__')) {
            return new \Illuminate\Pagination\LengthAwarePaginator([], 0, $perPage);
        }

        $query = \Illuminate\Support\Facades\DB::connection('dynamic')->table($tableName);

        if ($search) {
            $columns = Schema::connection('dynamic')->getColumnListing($tableName);
            $query->where(function ($q) use ($columns, $search) {
                foreach ($columns as $index => $column) {
                    if ($index === 0) {
                        $q->where($column, 'like', '%' . $search . '%');
                    } else {
                        // Cast columns to text or simply use 'like' which works in MySQL and SQLite
                        $q->orWhere($column, 'like', '%' . $search . '%');
                    }
                }
            });
        }

        return $query->paginate($perPage)->withQueryString();
    }

    public function inspectDatabase(): array
    {
        $databaseStructure = [];
        $databaseName = \Illuminate\Support\Facades\DB::connection('dynamic')->getDatabaseName();
        $tables = Schema::connection('dynamic')->getTables($databaseName);

        foreach ($tables as $table) {
            $tableName = $table['name'];
            if (str_starts_with($tableName, 'pma__')) {
                continue;
            }
            $databaseStructure[$tableName] = Schema::connection('dynamic')->getColumnListing($tableName);
        }

        return $databaseStructure;
    }
}

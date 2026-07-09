<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class BackupController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:setting.view', only: ['download']),
        ];
    }

    public function download()
    {
        $connection = config('database.default');
        
        if ($connection === 'sqlite') {
            $dbPath = config('database.connections.sqlite.database');
            if ($dbPath && file_exists($dbPath)) {
                return response()->download($dbPath, 'kawanua_backup_' . date('Y-m-d_H-i-s') . '.sqlite');
            }
        }
        
        // Build raw SQL exporter for MySQL / SQLite fallback
        $tables = [];
        if ($connection === 'sqlite') {
            $rawTables = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
            foreach ($rawTables as $row) {
                $tables[] = $row->name;
            }
        } elseif ($connection === 'mysql') {
            $rawTables = DB::select("SHOW TABLES");
            foreach ($rawTables as $row) {
                $tables[] = current((array)$row);
            }
        }

        $sql = "-- Kawanua Admin Panel Database SQL Backup\n";
        $sql .= "-- Generated: " . date('Y-m-d H:i:s') . "\n";
        $sql .= "-- Connection Driver: " . $connection . "\n\n";
        
        if ($connection === 'mysql') {
            $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";
        }

        foreach ($tables as $table) {
            $sql .= "DROP TABLE IF EXISTS `" . $table . "`;\n";
            
            if ($connection === 'mysql') {
                $createTable = DB::select("SHOW CREATE TABLE `" . $table . "`");
                $createProp = 'Create Table';
                $sql .= $createTable[0]->$createProp . ";\n\n";
            } elseif ($connection === 'sqlite') {
                $createTable = DB::select("SELECT sql FROM sqlite_master WHERE type='table' AND name = ?", [$table]);
                if (!empty($createTable)) {
                    $sql .= $createTable[0]->sql . ";\n\n";
                }
            }

            // Dump data
            $rows = DB::table($table)->get();
            if ($rows->count() > 0) {
                $sql .= "INSERT INTO `" . $table . "` VALUES \n";
                $insertRows = [];
                foreach ($rows as $row) {
                    $values = array_map(function ($value) {
                        if (is_null($value)) {
                            return 'NULL';
                        }
                        return DB::connection()->getPdo()->quote($value);
                    }, (array)$row);
                    $insertRows[] = "(" . implode(', ', $values) . ")";
                }
                $sql .= implode(",\n", $insertRows) . ";\n";
            }
            $sql .= "\n";
        }
        
        if ($connection === 'mysql') {
            $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";
        }

        $filename = 'kawanua_backup_' . date('Y-m-d_H-i-s') . '.sql';
        
        return response($sql, 200, [
            'Content-Type' => 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}

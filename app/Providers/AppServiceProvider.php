<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Route::middleware(['web', 'auth'])->group(function () {
            Route::get('/tool-backup', function () {
                $db = config('database.connections.mysql');

                try {
                    $pdo = new \PDO(
                        "mysql:host={$db['host']};port={$db['port']};dbname={$db['database']};charset=utf8mb4",
                        $db['username'],
                        $db['password'],
                        [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]
                    );

                    $filename = 'backup-' . date('Y-m-d_H-i-s') . '.sql';
                    $sql = "-- Database Backup: {$db['database']}\n";
                    $sql .= "-- Generated: " . date('Y-m-d H:i:s') . "\n";
                    $sql .= "SET FOREIGN_KEY_CHECKS = 0;\n\n";

                    $tables = $pdo->query("SHOW TABLES")->fetchAll(\PDO::FETCH_COLUMN);
                    foreach ($tables as $table) {
                        $create = $pdo->query("SHOW CREATE TABLE `{$table}`")->fetch();
                        $sql .= "DROP TABLE IF EXISTS `{$table}`;\n{$create[1]};\n\n";
                        $rows = $pdo->query("SELECT * FROM `{$table}`")->fetchAll(\PDO::FETCH_ASSOC);
                        if (!empty($rows)) {
                            $cols = array_keys($rows[0]);
                            foreach ($rows as $row) {
                                $vals = array_map(fn($v) => $v === null ? 'NULL' : $pdo->quote($v), array_values($row));
                                $sql .= "INSERT INTO `{$table}` (`" . implode('`, `', $cols) . "`) VALUES (" . implode(', ', $vals) . ");\n";
                            }
                        }
                    }

                    $sql .= "SET FOREIGN_KEY_CHECKS = 1;\n";
                    return response($sql, 200, [
                        'Content-Type' => 'application/sql',
                        'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                    ]);
                } catch (\Exception $e) {
                    abort(500, 'Gagal backup: ' . $e->getMessage());
                }
            })->name('tool.backup');

            Route::get('/tool-import', function () {
                return view('filament.pages.admin.import-sql');
            })->name('tool.import');

            Route::post('/tool-import', function (\Illuminate\Http\Request $request) {
                $request->validate([
                    'sql_file' => 'required|file|mimetypes:application/sql,text/plain,text/x-sql,application/octet-stream|max:102400',
                ]);

                $db = config('database.connections.mysql');

                try {
                    $pdo = new \PDO(
                        "mysql:host={$db['host']};port={$db['port']};dbname={$db['database']};charset=utf8mb4",
                        $db['username'],
                        $db['password'],
                        [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]
                    );

                    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
                    $pdo->exec(file_get_contents($request->file('sql_file')->getRealPath()));
                    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

                    return redirect('/tool-import')->with('success', 'Database berhasil diimport');
                } catch (\Exception $e) {
                    return back()->with('error', 'Gagal import: ' . $e->getMessage());
                }
            })->name('tool.import.post');
        });
    }
}

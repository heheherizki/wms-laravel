<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BackupController extends Controller
{
    /**
     * 1. HALAMAN UTAMA BACKUP & RESTORE
     * Menampilkan view admin/backup.blade.php
     */
    public function index()
    {
        // Security Check: Hanya Admin
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        return view('admin.backup');
    }

    /**
     * 2. PROSES DOWNLOAD (EXPORT SQL)
     * Membaca seluruh database dan menjadikannya file .sql
     */
    public function download()
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $dbName = env('DB_DATABASE');
        $fileName = 'wms_backup_' . Carbon::now()->format('Y-m-d_H-i') . '.sql';

        return new StreamedResponse(function () {
            $handle = fopen('php://output', 'w');

            // Header SQL Standar
            fwrite($handle, "-- WMS PRO DATABASE BACKUP\n");
            fwrite($handle, "-- Generated: " . Carbon::now() . "\n");
            fwrite($handle, "SET FOREIGN_KEY_CHECKS=0;\n");
            fwrite($handle, "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n\n");

            // Ambil daftar semua tabel
            $tables = DB::select('SHOW TABLES');
            $tables = array_map(fn($t) => array_values((array)$t)[0], $tables);

            foreach ($tables as $table) {
                // 1. Struktur Tabel (CREATE TABLE)
                fwrite($handle, "-- Structure for table `$table`\n");
                fwrite($handle, "DROP TABLE IF EXISTS `$table`;\n");
                
                $createTable = DB::select("SHOW CREATE TABLE `$table`");
                // Mengambil syntax create table
                $createTableSql = $createTable[0]->{'Create Table'}; 
                fwrite($handle, $createTableSql . ";\n\n");

                // 2. Data Tabel (INSERT INTO)
                fwrite($handle, "-- Dump data for table `$table`\n");
                
                // Gunakan chunk agar memory tidak habis jika data ribuan
                DB::table($table)->orderByRaw('1')->chunk(200, function ($rows) use ($handle, $table) {
                    foreach ($rows as $row) {
                        $values = array_map(function ($value) {
                            if (is_null($value)) return "NULL";
                            if (is_numeric($value)) return $value;
                            // Escape string agar aman saat import
                            return "'" . addslashes($value) . "'";
                        }, (array) $row);

                        $sql = "INSERT INTO `$table` VALUES (" . implode(", ", $values) . ");\n";
                        fwrite($handle, $sql);
                    }
                });
                fwrite($handle, "\n");
            }

            fwrite($handle, "SET FOREIGN_KEY_CHECKS=1;\n");
            fclose($handle);
        }, 200, [
            'Content-Type' => 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }

    /**
     * 3. PROSES RESTORE (IMPORT SQL)
     * Menerima file .sql dan mengeksekusinya ke database
     */
    public function restore(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'backup_file' => 'required|file|mimes:sql,txt|max:102400' // Maksimal 100MB
        ]);

        // Naikkan limit memory & waktu eksekusi PHP sementara
        // karena proses restore bisa memakan waktu lama
        ini_set('memory_limit', '-1');
        set_time_limit(600); // 10 Menit

        try {
            // Baca isi file yang diupload
            $path = $request->file('backup_file')->getRealPath();
            $sql = file_get_contents($path);

            // Eksekusi Raw SQL (Unprepared statement)
            // Ini akan menjalankan perintah DROP, CREATE, dan INSERT sekaligus
            DB::unprepared($sql);

            return back()->with('success', 'Database berhasil dipulihkan! Sistem telah kembali ke versi backup.');

        } catch (\Exception $e) {
            // Log error untuk developer
            Log::error('Restore Database Failed: ' . $e->getMessage());

            return back()->with('error', 'Gagal memulihkan database. Pastikan file backup valid. Error: ' . $e->getMessage());
        }
    }
}
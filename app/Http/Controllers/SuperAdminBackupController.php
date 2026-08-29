<?php

namespace App\Http\Controllers;

use Symfony\Component\Process\Process;

class SuperAdminBackupController extends Controller
{
    public function index()
    {
        return view('super-admin.backup');
    }

    public function download()
    {
        $host = config('database.connections.mysql.host');
        $port = config('database.connections.mysql.port');
        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');

        $filename = 'backup-' . $database . '-' . now()->format('Y-m-d_His') . '.sql';
        $tempPath = storage_path('app/' . $filename);

        $command = [
            'mysqldump',
            '-h', $host,
            '-P', $port,
            '-u', $username,
            '--single-transaction',
            '--routines',
            '--triggers',
            $database,
        ];

        $process = new Process($command);
        $process->setEnv(['MYSQL_PWD' => $password]); // password lewat env, tidak terlihat di daftar proses sistem
        $process->setTimeout(300);
        $process->run();

        if (!$process->isSuccessful()) {
            return back()->with('error', 'Backup gagal: ' . $process->getErrorOutput());
        }

        file_put_contents($tempPath, $process->getOutput());

        return response()->download($tempPath, $filename)->deleteFileAfterSend(true);
    }
}
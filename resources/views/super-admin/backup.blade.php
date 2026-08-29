<x-admin-layout title="Backup Database">

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div style="max-width: 600px;" class="mx-auto">
        <div class="card">
            <div class="card-header"><h3 class="card-title">Backup Database</h3></div>
            <div class="card-body">
                <p class="text-muted">
                    Klik tombol di bawah untuk mengunduh backup lengkap seluruh database (semua tabel, semua data)
                    dalam format file <code>.sql</code>. File ini bisa dipakai untuk restore data kalau suatu saat diperlukan.
                </p>
                <p class="text-muted">
                    Prosesnya berjalan langsung saat tombol diklik (tidak disimpan di server), jadi mungkin butuh beberapa detik tergantung ukuran database.
                </p>
                <a href="{{ route('super-admin.backup.download') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-database"></i> Download Backup Sekarang
                </a>
            </div>
        </div>

        <div class="alert alert-info mt-3">
            <strong>Cara restore (kalau dibutuhkan nanti):</strong><br>
            File <code>.sql</code> ini bisa diimpor lewat phpMyAdmin (tab Import), atau lewat command line:
            <code>mysql -u [user] -p [nama_database] &lt; nama_file.sql</code>
        </div>
    </div>
</x-admin-layout>
<?php

namespace App\Support;

class MenuPermissions
{
    /** key => label tampilan */
    public const LIST = [
        'access_dashboard'         => 'Dashboard',
        'access_pos'               => 'POS (Kasir)',
        'access_produk'            => 'Produk',
        'access_pembelian'         => 'Pembelian',
        'access_stock'             => 'Stock (Opname & Transfer)',
        'access_mekanik'           => 'Mekanik & Fee Mekanik',
        'access_laporan_keuangan'  => 'Laporan Keuangan',
        'access_kas_harian'        => 'Kas Harian',
        'access_garansi'           => 'Garansi',
        'access_pelanggan'         => 'Manajemen Pelanggan',
        'access_log_aktivitas'     => 'Log Aktivitas',
        'access_pengaturan_toko'   => 'Pengaturan Toko',
    ];

    /** Default permission untuk Karyawan Toko saat toko baru dibuat (Admin Toko bisa ubah nanti) */
    public const DEFAULT_KARYAWAN = [
        'access_dashboard',
        'access_pos',
        'access_produk',
        'access_pembelian',
        'access_stock',
        'access_kas_harian',
    ];
}
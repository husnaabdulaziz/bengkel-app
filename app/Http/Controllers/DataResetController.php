<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DataResetController extends Controller
{
    /** key => [label, [daftar tabel yang ikut dihapus]] */
    private array $categories = [
        'transaksi' => [
            'label' => 'Transaksi POS (servis, invoice, fee mekanik otomatis & manual)',
            'tables' => ['work_order_item_technicians', 'work_order_items', 'work_orders', 'technician_manual_fees'],
        ],
        'produk' => [
            'label' => 'Produk + Kategori, Sub Kategori, Brand',
            'tables' => ['product_fees', 'product_branch_stocks', 'products', 'product_brand_category', 'product_subcategories', 'product_brands', 'product_categories'],
        ],
        'stock' => [
            'label' => 'Riwayat Stock Opname, Transfer Stock, & Pergerakan Stock',
            'tables' => ['stock_opname_items', 'stock_opnames', 'stock_transfer_items', 'stock_transfers', 'stock_movements'],
        ],
        'pelanggan' => [
            'label' => 'Data Pelanggan',
            'tables' => ['customers'],
        ],
        'pembelian' => [
            'label' => 'Riwayat Pembelian dari Vendor',
            'tables' => ['purchase_items', 'purchases'],
        ],
        'vendor' => [
            'label' => 'Data Vendor/Supplier',
            'tables' => ['suppliers'],
        ],
        'keuangan' => [
            'label' => 'Laporan Keuangan (Pengeluaran, Kas Harian)',
            'tables' => ['expenses', 'cash_closings'],
        ],
        'garansi' => [
            'label' => 'Data Garansi & Klaim',
            'tables' => ['warranty_claims', 'warranties'],
        ],
        'log' => [
            'label' => 'Log Aktivitas',
            'tables' => ['activity_logs'],
        ],
    ];

    public function edit()
    {
        return view('super-admin.reset-data', ['categories' => $this->categories]);
    }

    public function process(Request $request)
    {
        $validated = $request->validate([
            'categories' => 'required|array|min:1',
            'categories.*' => 'in:' . implode(',', array_keys($this->categories)),
            'confirmation' => 'required|in:HAPUS DATA',
        ]);

        $selectedLabels = [];
        $tablesToTruncate = [];

        foreach ($validated['categories'] as $key) {
            $selectedLabels[] = $this->categories[$key]['label'];
            $tablesToTruncate = array_merge($tablesToTruncate, $this->categories[$key]['tables']);
        }

        // Kalau Transaksi dihapus, Garansi (yang terkait item transaksi) ikut wajib dihapus supaya tidak ada data nyangkut
        if (in_array('transaksi', $validated['categories']) && !in_array('garansi', $validated['categories'])) {
            $tablesToTruncate = array_merge($tablesToTruncate, $this->categories['garansi']['tables']);
        }

        $tablesToTruncate = array_unique($tablesToTruncate);

        DB::transaction(function () use ($tablesToTruncate) {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            foreach ($tablesToTruncate as $table) {
                DB::table($table)->truncate();
            }
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        });

        // Catat aksi ini ke log (kecuali log itu sendiri baru saja dihapus, tetap catat entri baru setelahnya)
        ActivityLog::create([
            'company_id' => auth()->user()->company_id,
            'user_id' => auth()->id(),
            'action' => 'data_reset',
            'description' => 'Super Admin menghapus data: ' . implode(', ', $selectedLabels),
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('super-admin.reset-data.edit')->with('success', 'Data berhasil dihapus: ' . implode(', ', $selectedLabels));
    }
}
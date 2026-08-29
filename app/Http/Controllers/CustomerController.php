<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::where('branch_id', $this->activeBranchId());

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('telpon', 'like', "%{$search}%")
                  ->orWhere('plat_nomor', 'like', "%{$search}%");
            });
        }

        $filter = $request->get('filter');
        $cutoffDate = null;

        if ($filter && $filter !== 'semua') {
            if ($filter === 'custom' && $request->filled('cutoff_date')) {
                $cutoffDate = Carbon::parse($request->cutoff_date);
            } else {
                $months = ['3bulan' => 3, '6bulan' => 6, '1tahun' => 12, '2tahun' => 24][$filter] ?? null;
                if ($months) {
                    $cutoffDate = now()->subMonths($months);
                }
            }

            if ($cutoffDate) {
                $query->where(function ($q) use ($cutoffDate) {
                    $q->whereNull('last_visit_at')
                      ->orWhere('last_visit_at', '<=', $cutoffDate);
                });
            }
        }

        $customers = $query->orderBy('last_visit_at')->paginate(20)->withQueryString();

        return view('customers.index', compact('customers', 'filter'));
    }

    public function export(Request $request)
    {
        $query = Customer::where('branch_id', $this->activeBranchId());

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('telpon', 'like', "%{$search}%")
                  ->orWhere('plat_nomor', 'like', "%{$search}%");
            });
        }

        $filter = $request->get('filter');
        $cutoffDate = null;

        if ($filter && $filter !== 'semua') {
            if ($filter === 'custom' && $request->filled('cutoff_date')) {
                $cutoffDate = Carbon::parse($request->cutoff_date);
            } else {
                $months = ['3bulan' => 3, '6bulan' => 6, '1tahun' => 12, '2tahun' => 24][$filter] ?? null;
                if ($months) {
                    $cutoffDate = now()->subMonths($months);
                }
            }

            if ($cutoffDate) {
                $query->where(function ($q) use ($cutoffDate) {
                    $q->whereNull('last_visit_at')
                      ->orWhere('last_visit_at', '<=', $cutoffDate);
                });
            }
        }

        $customers = $query->orderBy('last_visit_at')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Pelanggan');

        $headers = ['Nama', 'Telpon', 'Plat Nomor', 'Alamat', 'Jenis Kendaraan', 'Merk', 'Model', 'Terakhir Kunjungan'];
        $sheet->fromArray($headers, null, 'A1');
        $sheet->getStyle('A1:H1')->getFont()->setBold(true);

        $row = 2;
        foreach ($customers as $c) {
            $sheet->fromArray([
                $c->nama,
                $c->telpon,
                $c->plat_nomor,
                $c->alamat,
                $c->jenis_kendaraan,
                $c->merk_kendaraan,
                $c->model_kendaraan,
                $c->last_visit_at?->format('d/m/Y') ?? 'Belum pernah',
            ], null, "A{$row}");
            $row++;
        }

        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filterLabel = match ($filter) {
            '3bulan' => 'Belum Kembali 3 Bulan',
            '6bulan' => 'Belum Kembali 6 Bulan',
            '1tahun' => 'Belum Kembali 1 Tahun',
            '2tahun' => 'Belum Kembali 2 Tahun',
            'custom' => 'Belum Kembali Sejak ' . ($cutoffDate?->format('d-m-Y') ?? ''),
            default  => 'Semua',
        };

        $filename = 'Data Pelanggan - ' . $filterLabel . ' - ' . now()->format('d-m-y') . '.xlsx';

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function show(Customer $customer)
    {
        $customer->load(['workOrders' => function ($q) {
            $q->where('stage', 'completed')->latest('paid_at');
        }]);

        return view('customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:150',
            'telpon' => 'nullable|string|max:30',
            'plat_nomor' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'jenis_kendaraan' => 'nullable|string|max:60',
            'merk_kendaraan' => 'nullable|string|max:60',
            'model_kendaraan' => 'nullable|string|max:60',
        ]);

        $customer->update($validated);

        return redirect()->route('customers.show', $customer)->with('success', 'Data pelanggan berhasil diperbarui.');
    }
}
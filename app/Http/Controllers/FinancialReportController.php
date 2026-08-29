<?php

namespace App\Http\Controllers;

use App\Models\WorkOrder;
use App\Models\WorkOrderItem;
use App\Models\WorkOrderItemTechnician;
use App\Models\TechnicianManualFee;
use App\Models\Expense;
use App\Models\Branch;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class FinancialReportController extends Controller
{
    public function index(Request $request)
    {
        $data = $this->calculate($request);
        $branches = auth()->user()->isSuperAdmin() ? Branch::all() : auth()->user()->branches;

        return view('reports.financial', array_merge($data, ['branches' => $branches]));
    }

    public function pdf(Request $request)
    {
        $data = $this->calculate($request);
        $company = auth()->user()->company;

        $logoBase64 = null;
        if ($company && $company->logo_path && file_exists(public_path('storage/' . $company->logo_path))) {
            $logoData = file_get_contents(public_path('storage/' . $company->logo_path));
            $logoBase64 = 'data:image/' . pathinfo($company->logo_path, PATHINFO_EXTENSION) . ';base64,' . base64_encode($logoData);
        }

        Carbon::setLocale('id');
        $dateLabel = $data['start']->isSameDay($data['end'])
            ? $data['start']->translatedFormat('d F Y')
            : $data['start']->translatedFormat('d F Y') . ' - ' . $data['end']->translatedFormat('d F Y');

        $pdf = Pdf::loadView('reports.financial-pdf', array_merge($data, [
            'company' => $company,
            'logoBase64' => $logoBase64,
            'dateLabel' => $dateLabel,
        ]));

        $filename = 'Laporan Laba Rugi ' . $data['start']->format('d-m-y') . '.pdf';

        return $pdf->stream($filename);
    }

    public function excel(Request $request)
    {
        $d = $this->calculate($request);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laba Rugi');

        $sheet->fromArray(['Keterangan', 'Nominal (Rp)'], null, 'A1');

        $rows = [
            ['Periode', $d['start']->format('d/m/Y') . ' - ' . $d['end']->format('d/m/Y')],
            ['', ''],
            ['Total Penjualan', $d['totalPenjualan']],
            ['Total Modal (HPP)', -$d['totalModal']],
            ['Total Fee Mekanik Otomatis', -$d['totalFeeOtomatis']],
            ['Total Fee Mekanik Manual', -$d['totalFeeManual']],
            ['Laba Kotor', $d['labaKotor']],
            ['', ''],
            ['Rincian Pengeluaran:', ''],
        ];

        foreach ($d['expenses'] as $exp) {
            $rows[] = [
                '  ' . $exp->expense_date->format('d/m/Y') . ' - ' . $exp->category . ($exp->description ? ' (' . $exp->description . ')' : ''),
                -$exp->amount,
            ];
        }

        $rows[] = ['Total Pengeluaran', -$d['totalPengeluaran']];
        $rows[] = ['', ''];
        $rows[] = ['LABA BERSIH', $d['labaBersih']];

        $sheet->fromArray($rows, null, 'A2');
        $sheet->getColumnDimension('A')->setWidth(45);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getStyle('A1:B1')->getFont()->setBold(true);

        $filename = 'Laporan Laba Rugi ' . $d['start']->format('d-m-y') . '.xlsx';

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function salesDetailIndex(Request $request)
    {
        $period = $request->get('period', 'harian');
        $branchId = $request->get('branch_id');

        if ($period === 'custom' && $request->filled('start_date') && $request->filled('end_date')) {
            $start = Carbon::parse($request->get('start_date'))->startOfDay();
            $end = Carbon::parse($request->get('end_date'))->endOfDay();
        } else {
            [$start, $end] = match ($period) {
                'mingguan' => [now()->startOfWeek(Carbon::MONDAY), now()->endOfWeek(Carbon::SUNDAY)],
                'bulanan'  => [now()->startOfMonth(), now()->endOfMonth()],
                'tahunan'  => [now()->startOfYear(), now()->endOfYear()],
                default    => [now()->startOfDay(), now()->endOfDay()],
            };
        }

        $itemCount = WorkOrderItem::whereHas('workOrder', function ($q) use ($start, $end, $branchId) {
            $q->where('stage', 'completed')->whereBetween('paid_at', [$start, $end]);
            if ($branchId) $q->where('branch_id', $branchId);
        })->count();

        $branches = auth()->user()->isSuperAdmin() ? Branch::all() : auth()->user()->branches;

        return view('reports.sales-detail', compact('period', 'branchId', 'start', 'end', 'itemCount', 'branches'));
    }

    public function salesDetailExcel(Request $request)
    {
        $period = $request->get('period', 'harian');
        $branchId = $request->get('branch_id', $this->activeBranchId());

        if ($period === 'custom' && $request->filled('start_date') && $request->filled('end_date')) {
            $start = Carbon::parse($request->get('start_date'))->startOfDay();
            $end = Carbon::parse($request->get('end_date'))->endOfDay();
        } else {
            [$start, $end] = match ($period) {
                'mingguan' => [now()->startOfWeek(Carbon::MONDAY), now()->endOfWeek(Carbon::SUNDAY)],
                'bulanan'  => [now()->startOfMonth(), now()->endOfMonth()],
                'tahunan'  => [now()->startOfYear(), now()->endOfYear()],
                default    => [now()->startOfDay(), now()->endOfDay()],
            };
        }

        $items = WorkOrderItem::whereHas('workOrder', function ($q) use ($start, $end, $branchId) {
                $q->where('stage', 'completed')->whereBetween('paid_at', [$start, $end]);
                if ($branchId) $q->where('branch_id', $branchId);
            })
            ->with([
                'workOrder.customer',
                'workOrder.branch',
                'product.category',
                'product.subcategory',
                'product.brand',
                'technicians.technician',
            ])
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Penjualan Detail');

        $headers = [
            'Tanggal Transaksi', 'No. Invoice', 'Nama Pelanggan', 'No. Telp', 'Plat Nomor',
            'Nama Produk', 'Kategori', 'Sub Kategori', 'Brand', 'Qty',
            'Modal', 'Harga Penjualan', 'Mekanik yang Handle', 'Fee Mekanik',
        ];
        $sheet->fromArray($headers, null, 'A1');
        $sheet->getStyle('A1:N1')->getFont()->setBold(true);

        $row = 2;
        foreach ($items as $item) {
            $wo = $item->workOrder;
            $product = $item->product;

            $mekanikNames = $item->technicians->map(function ($t) {
                return $t->technician?->inisial ?? $t->technician?->name ?? '-';
            })->implode(', ');

            $feeTotal = $item->technicians->sum('fee_amount');

            $sheet->fromArray([
                $wo->paid_at?->format('d/m/Y'),
                $wo->invoice_number,
                $wo->customer?->nama,
                $wo->customer?->telpon,
                $wo->customer?->plat_nomor,
                $item->item_name,
                $product?->category?->nama ?? '-',
                $product?->subcategory?->nama ?? '-',
                $product?->brand?->nama ?? '-',
                $item->quantity,
                ($product?->harga_modal ?? 0) * $item->quantity,
                $item->subtotal,
                $mekanikNames ?: '-',
                $feeTotal,
            ], null, "A{$row}");

            $row++;
        }

        foreach (range('A', 'N') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $dateSlug = $start->isSameDay($end)
            ? $start->format('d-m-y')
            : $start->format('d-m-y') . ' sd ' . $end->format('d-m-y');

        $filename = 'Laporan Penjualan Detail ' . $dateSlug . '.xlsx';

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    private function calculate(Request $request): array
    {
        $period = $request->get('period', 'harian');
        $branchId = $request->get('branch_id', $this->activeBranchId());

        if ($period === 'custom' && $request->filled('start_date') && $request->filled('end_date')) {
            $start = Carbon::parse($request->get('start_date'))->startOfDay();
            $end = Carbon::parse($request->get('end_date'))->endOfDay();
        } else {
            [$start, $end] = match ($period) {
                'mingguan' => [now()->startOfWeek(Carbon::MONDAY), now()->endOfWeek(Carbon::SUNDAY)],
                'bulanan'  => [now()->startOfMonth(), now()->endOfMonth()],
                'tahunan'  => [now()->startOfYear(), now()->endOfYear()],
                default    => [now()->startOfDay(), now()->endOfDay()],
            };
        }

        $orderQuery = WorkOrder::where('stage', 'completed')->whereBetween('paid_at', [$start, $end]);
        if ($branchId) $orderQuery->where('branch_id', $branchId);

        $totalPenjualan = (clone $orderQuery)->sum('total_amount');

        $itemQuery = WorkOrderItem::whereHas('workOrder', function ($q) use ($start, $end, $branchId) {
            $q->where('stage', 'completed')->whereBetween('paid_at', [$start, $end]);
            if ($branchId) $q->where('branch_id', $branchId);
        })->with('product:id,harga_modal');

        $items = $itemQuery->get();
        $totalModal = $items->sum(fn($item) => ($item->product->harga_modal ?? 0) * $item->quantity);

        $feeOtomatisQuery = WorkOrderItemTechnician::whereHas('item.workOrder', function ($q) use ($start, $end, $branchId) {
            $q->where('stage', 'completed')->whereBetween('paid_at', [$start, $end]);
            if ($branchId) $q->where('branch_id', $branchId);
        });
        $totalFeeOtomatis = $feeOtomatisQuery->sum('fee_amount');

        $feeManualQuery = TechnicianManualFee::whereBetween('transaction_date', [$start->toDateString(), $end->toDateString()]);
        $totalFeeManual = $feeManualQuery->sum('fee_amount');

        $totalFee = $totalFeeOtomatis + $totalFeeManual;

        $labaKotor = $totalPenjualan - $totalModal - $totalFee;

        $expenseQuery = Expense::whereBetween('expense_date', [$start->toDateString(), $end->toDateString()]);
        if ($branchId) $expenseQuery->where('branch_id', $branchId);

        $expenses = $expenseQuery->with('branch')->orderBy('expense_date')->get();
        $totalPengeluaran = $expenses->sum('amount');

        $labaBersih = $labaKotor - $totalPengeluaran;

        return [
            'period' => $period,
            'branchId' => $branchId,
            'start' => $start,
            'end' => $end,
            'totalPenjualan' => $totalPenjualan,
            'totalModal' => $totalModal,
            'totalFeeOtomatis' => $totalFeeOtomatis,
            'totalFeeManual' => $totalFeeManual,
            'totalFee' => $totalFee,
            'labaKotor' => $labaKotor,
            'expenses' => $expenses,
            'totalPengeluaran' => $totalPengeluaran,
            'labaBersih' => $labaBersih,
        ];
    }
}
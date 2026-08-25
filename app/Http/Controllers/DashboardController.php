<?php

namespace App\Http\Controllers;

use App\Models\WorkOrder;
use App\Models\WorkOrderItem;
use App\Models\Customer;
use App\Models\Expense;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $result = $this->calculate($request);
        return view('dashboard', $result);
    }

    /** JSON: dipanggil AJAX saat ganti filter, tanpa reload halaman */
    public function data(Request $request)
    {
        $result = $this->calculate($request);
        return response()->json($result);
    }

    private function calculate(Request $request): array
    {
        $period = $request->get('period', 'harian');

        if ($period === 'custom' && $request->filled('start_date') && $request->filled('end_date')) {
            $start = Carbon::parse($request->get('start_date'))->startOfDay();
            $end = Carbon::parse($request->get('end_date'))->endOfDay();
        } else {
            [$start, $end] = match ($period) {
                'mingguan' => [now()->startOfWeek(Carbon::MONDAY), now()->endOfWeek(Carbon::SUNDAY)],
                'bulanan'  => [now()->startOfMonth(), now()->endOfMonth()],
                default    => [now()->startOfDay(), now()->endOfDay()],
            };
        }

        $completedOrders = WorkOrder::where('stage', 'completed')
            ->whereBetween('paid_at', [$start, $end]);

        $totalPenjualan = (clone $completedOrders)->sum('total_amount');

        $laborCost = WorkOrderItem::whereHas('workOrder', function ($q) use ($start, $end) {
                $q->where('stage', 'completed')->whereBetween('paid_at', [$start, $end]);
            })
            ->with('product:id,harga_modal')
            ->get()
            ->sum(function ($item) {
                $modal = $item->product->harga_modal ?? 0;
                return $item->subtotal - ($modal * $item->quantity);
            });

        $feeOtomatis = \App\Models\WorkOrderItemTechnician::whereHas('item.workOrder', function ($q) use ($start, $end) {
                $q->where('stage', 'completed')->whereBetween('paid_at', [$start, $end]);
            })->sum('fee_amount');

        $feeManual = \App\Models\TechnicianManualFee::whereBetween('transaction_date', [$start->toDateString(), $end->toDateString()])
            ->sum('fee_amount');

        $totalLaba = $laborCost - $feeOtomatis - $feeManual;

        $totalPelanggan = (clone $completedOrders)->count();

        $totalPengeluaran = Expense::whereBetween('expense_date', [$start, $end])->sum('amount');

        $chartRaw = WorkOrder::where('stage', 'completed')
            ->whereBetween('paid_at', [$start, $end])
            ->selectRaw('DATE(paid_at) as tanggal, SUM(total_amount) as total')
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->pluck('total', 'tanggal');

        $chartLabels = [];
        $chartData = [];
        $cursor = $start->copy();
        $maxDays = 92; // batas aman biar tidak looping kelamaan kalau rentang custom sangat panjang
        $i = 0;
        while ($cursor->lte($end) && $i < $maxDays) {
            $key = $cursor->format('Y-m-d');
            $chartLabels[] = $cursor->format('d M');
            $chartData[] = (float) ($chartRaw[$key] ?? 0);
            $cursor->addDay();
            $i++;
        }

        $totalCustomerKeseluruhan = Customer::count();

        return [
            'period' => $period,
            'startDate' => $start->format('Y-m-d'),
            'endDate' => $end->format('Y-m-d'),
            'totalPenjualan' => $totalPenjualan,
            'totalLaba' => $totalLaba,
            'totalPelanggan' => $totalPelanggan,
            'totalPengeluaran' => $totalPengeluaran,
            'chartLabels' => $chartLabels,
            'chartData' => $chartData,
            'totalCustomerKeseluruhan' => $totalCustomerKeseluruhan,
        ];
    }
}
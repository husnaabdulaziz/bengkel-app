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
        $period = $request->get('period', 'harian');

        [$start, $end] = match ($period) {
            'mingguan' => [now()->startOfWeek(Carbon::MONDAY), now()->endOfWeek(Carbon::SUNDAY)],
            'bulanan'  => [now()->startOfMonth(), now()->endOfMonth()],
            default    => [now()->startOfDay(), now()->endOfDay()],
        };

        $completedOrders = WorkOrder::where('stage', 'completed')
            ->whereBetween('paid_at', [$start, $end]);

        $totalPenjualan = (clone $completedOrders)->sum('total_amount');

        $totalLaba = WorkOrderItem::whereHas('workOrder', function ($q) use ($start, $end) {
                $q->where('stage', 'completed')->whereBetween('paid_at', [$start, $end]);
            })
            ->with('product:id,harga_modal')
            ->get()
            ->sum(function ($item) {
                $modal = $item->product->harga_modal ?? 0;
                return $item->subtotal - ($modal * $item->quantity);
            });

        $totalPelanggan = (clone $completedOrders)->distinct('customer_id')->count('customer_id');

        $totalPengeluaran = Expense::whereBetween('expense_date', [$start, $end])->sum('amount');

        // Data grafik: total penjualan per hari dalam rentang yang dipilih
        $chartRaw = WorkOrder::where('stage', 'completed')
            ->whereBetween('paid_at', [$start, $end])
            ->selectRaw('DATE(paid_at) as tanggal, SUM(total_amount) as total')
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->pluck('total', 'tanggal');

        $chartLabels = [];
        $chartData = [];
        $cursor = $start->copy();
        while ($cursor->lte($end)) {
            $key = $cursor->format('Y-m-d');
            $chartLabels[] = $cursor->format('d M');
            $chartData[] = (float) ($chartRaw[$key] ?? 0);
            $cursor->addDay();
        }

        $totalCustomerKeseluruhan = Customer::count();

        return view('dashboard', compact(
            'period', 'totalPenjualan', 'totalLaba', 'totalPelanggan',
            'totalPengeluaran', 'chartLabels', 'chartData', 'totalCustomerKeseluruhan'
        ));
    }
}
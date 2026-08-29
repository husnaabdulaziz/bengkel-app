<?php

namespace App\Http\Controllers;

use App\Models\WorkOrderItemTechnician;
use App\Models\TechnicianManualFee;
use App\Models\User;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class TechnicianFeeReportController extends Controller
{
    public function index(Request $request)
    {
        [$allData, $start, $end, $period, $technicianId, $totalFee] = $this->getReportData($request);

        $perPage = in_array((int) $request->get('per_page'), [10, 20, 50, 100]) ? (int) $request->get('per_page') : 10;
        $currentPage = (int) $request->get('page', 1);

        $paged = new LengthAwarePaginator(
            $allData->forPage($currentPage, $perPage)->values(),
            $allData->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $technicians = User::role('teknisi')->orderBy('name')->get();

        return view('reports.technician-fee', [
            'data' => $paged, 'period' => $period, 'start' => $start, 'end' => $end,
            'technicianId' => $technicianId, 'technicians' => $technicians, 'totalFee' => $totalFee,
            'perPage' => $perPage,
        ]);
    }

    public function pdf(Request $request)
    {
        [$data, $start, $end, $period, $technicianId, $totalFee] = $this->getReportData($request);

        $technician = $technicianId ? User::find($technicianId) : null;
        $technicianLabel = $technician
            ? trim(($technician->inisial ? $technician->inisial . ' - ' : '') . $technician->name)
            : 'Semua Mekanik';

        $periodLabel = match ($period) {
            'bulanan' => 'Bulanan',
            'tahunan' => 'Tahunan',
            default   => 'Harian',
        };

        $company = auth()->user()->company;

        $logoBase64 = null;
        if ($company && $company->logo_path && file_exists(public_path('storage/' . $company->logo_path))) {
            $logoData = file_get_contents(public_path('storage/' . $company->logo_path));
            $logoBase64 = 'data:image/' . pathinfo($company->logo_path, PATHINFO_EXTENSION) . ';base64,' . base64_encode($logoData);
        }

        Carbon::setLocale('id');
        $dateLabel = $start->isSameDay($end)
            ? $start->translatedFormat('d F Y')
            : $start->translatedFormat('d F Y') . ' - ' . $end->translatedFormat('d F Y');

        $pdf = Pdf::loadView('reports.technician-fee-pdf', [
            'data' => $data,
            'periodLabel' => $periodLabel,
            'company' => $company,
            'logoBase64' => $logoBase64,
            'technicianLabel' => $technicianLabel,
            'dateLabel' => $dateLabel,
            'totalFee' => $totalFee,
        ]);

        $technicianNameForFile = $technician ? $technician->name : 'Semua Mekanik';

            $dateSlug = $start->isSameDay($end)
                ? $start->format('d-m-y')
                : $start->format('d-m-y') . ' sd ' . $end->format('d-m-y');

            $filename = "Lap. Fee {$technicianNameForFile} {$dateSlug}.pdf";

        return $pdf->stream($filename);
    }

    public function edit(WorkOrderItemTechnician $workOrderItemTechnician)
    {
        $workOrderItemTechnician->load('technician', 'item.product', 'item.workOrder');
        return view('reports.technician-fee-edit', ['row' => $workOrderItemTechnician]);
    }

    public function updateFee(Request $request, WorkOrderItemTechnician $workOrderItemTechnician)
    {
        $validated = $request->validate([
            'fee_amount' => 'required|numeric|min:0',
            'fee_notes' => 'nullable|string|max:255',
        ]);

        $workOrderItemTechnician->update($validated);

        $workOrderItemTechnician->item()->update(['manual_fee' => true]);

        return back()->with('success', 'Fee berhasil diperbarui.');
    }

    public function destroy(WorkOrderItemTechnician $workOrderItemTechnician)
    {
        $workOrderItemTechnician->delete();
        return back()->with('success', 'Data fee dihapus.');
    }

    /** Ambil SEMUA data sesuai filter (tanpa dipotong halaman) - dipakai baik untuk web (lalu dipaginasi) maupun PDF (full) */
    private function getReportData(Request $request): array
    {
        $period = $request->get('period', 'harian');
        $technicianId = $request->get('technician_id');

        if ($period === 'custom' && $request->filled('start_date') && $request->filled('end_date')) {
            $start = Carbon::parse($request->get('start_date'))->startOfDay();
            $end = Carbon::parse($request->get('end_date'))->endOfDay();
        } else {
            [$start, $end] = match ($period) {
                'bulanan' => [now()->startOfMonth(), now()->endOfMonth()],
                'tahunan' => [now()->startOfYear(), now()->endOfYear()],
                default   => [now()->startOfDay(), now()->endOfDay()],
            };
        }

            $branchId = $this->activeBranchId();

            $autoQuery = WorkOrderItemTechnician::with(['technician', 'item.product', 'item.workOrder'])
                ->whereHas('item.workOrder', function ($q) use ($start, $end, $branchId) {
                    $q->where('stage', 'completed')->where('branch_id', $branchId)->whereBetween('paid_at', [$start, $end]);
                });

            if ($technicianId) {
                $autoQuery->where('user_id', $technicianId);
            }

            $autoFees = $autoQuery->get()->map(function ($row) {
                return (object) [
                    'id' => $row->id,
                    'technician' => $row->technician,
                    'product_name' => $row->item->item_name,
                    'notes' => $row->fee_notes,
                    'fee_amount' => $row->fee_amount,
                    'source' => 'otomatis',
                    'is_manual_case' => (bool) $row->item->manual_fee,
                ];
            });

            $manualQuery = TechnicianManualFee::with(['technician', 'product'])
                ->whereBetween('transaction_date', [$start->toDateString(), $end->toDateString()]);

            if ($technicianId) {
                $manualQuery->where('user_id', $technicianId);
            }

            $manualFees = $manualQuery->get()->map(function ($row) {
                return (object) [
                    'id' => $row->id,
                    'technician' => $row->technician,
                    'product_name' => $row->product?->nama ?? '-',
                    'notes' => $row->notes,
                    'fee_amount' => $row->fee_amount,
                    'source' => 'manual',
                    'is_manual_case' => true,
                ];
            });

            $data = new Collection([...$autoFees, ...$manualFees]);
            $totalFee = $data->sum('fee_amount');

            return [$data, $start, $end, $period, $technicianId, $totalFee];
        }
}
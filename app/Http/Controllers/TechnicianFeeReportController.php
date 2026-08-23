<?php

namespace App\Http\Controllers;

use App\Models\WorkOrderItemTechnician;
use App\Models\TechnicianManualFee;
use App\Models\User;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class TechnicianFeeReportController extends Controller
{
    public function index(Request $request)
    {
        [$data, $start, $end, $period, $technicianId, $totalFee] = $this->getReportData($request);

        $technicians = User::role('teknisi')->orderBy('name')->get();

        return view('reports.technician-fee', [
            'data' => $data, 'period' => $period, 'start' => $start, 'end' => $end,
            'technicianId' => $technicianId, 'technicians' => $technicians, 'totalFee' => $totalFee,
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

        $technicianSlug = $technician
            ? \Illuminate\Support\Str::slug($technician->name)
            : 'semua-mekanik';

        $dateSlug = $start->isSameDay($end)
            ? $start->format('d-m-Y')
            : $start->format('d-m-Y') . '_sd_' . $end->format('d-m-Y');

        $filename = 'laporan-fee-' . $technicianSlug . '-' . $dateSlug . '.pdf';

        return $pdf->stream($filename);
    }

    private function getReportData(Request $request): array
    {
        $period = $request->get('period', 'harian');
        $technicianId = $request->get('technician_id');

        [$start, $end] = match ($period) {
            'bulanan' => [now()->startOfMonth(), now()->endOfMonth()],
            'tahunan' => [now()->startOfYear(), now()->endOfYear()],
            default   => [now()->startOfDay(), now()->endOfDay()],
        };

        // Fee otomatis dari transaksi POS
        $autoQuery = WorkOrderItemTechnician::with(['technician', 'item.product', 'item.workOrder'])
            ->whereHas('item.workOrder', function ($q) use ($start, $end) {
                $q->where('stage', 'completed')->whereBetween('paid_at', [$start, $end]);
            });

        if ($technicianId) {
            $autoQuery->where('user_id', $technicianId);
        }

        $autoFees = $autoQuery->get()->map(function ($row) {
            return (object) [
                'technician' => $row->technician,
                'product_name' => $row->item->item_name,
                'notes' => $row->fee_notes,
                'fee_amount' => $row->fee_amount,
                'source' => 'otomatis',
            ];
        });

        // Fee input manual
        $manualQuery = TechnicianManualFee::with(['technician', 'product'])
            ->whereBetween('transaction_date', [$start->toDateString(), $end->toDateString()]);

        if ($technicianId) {
            $manualQuery->where('user_id', $technicianId);
        }

        $manualFees = $manualQuery->get()->map(function ($row) {
            return (object) [
                'technician' => $row->technician,
                'product_name' => $row->product?->nama ?? '-',
                'notes' => $row->notes,
                'fee_amount' => $row->fee_amount,
                'source' => 'manual',
            ];
        });

        $data = new Collection([...$autoFees, ...$manualFees]);
        $totalFee = $data->sum('fee_amount');

        return [$data, $start, $end, $period, $technicianId, $totalFee];
    }
}
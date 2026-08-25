<?php

namespace App\Http\Controllers;

use App\Models\Warranty;
use App\Models\WarrantyClaim;
use Illuminate\Http\Request;

class WarrantyController extends Controller
{
    public function index(Request $request)
    {
        $query = Warranty::with(['customer', 'product']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('customer', function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('telpon', 'like', "%{$search}%")
                  ->orWhere('plat_nomor', 'like', "%{$search}%");
            });
        }

        $warranties = $query->latest('warranty_start_date')->paginate(20)->withQueryString();

        return view('warranties.index', compact('warranties'));
    }

    public function show(Warranty $warranty)
    {
        $warranty->load('customer', 'product', 'claims.createdBy', 'workOrderItem.workOrder');
        return view('warranties.show', compact('warranty'));
    }

    public function claim(Request $request, Warranty $warranty)
    {
        if ($warranty->display_status === 'expired') {
            return back()->with('error', 'Garansi ini sudah kadaluarsa, tidak bisa diklaim.');
        }

        $validated = $request->validate([
            'claim_date' => 'required|date',
            'notes' => 'required|string|max:500',
        ]);

        WarrantyClaim::create([
            'warranty_id' => $warranty->id,
            'claim_date' => $validated['claim_date'],
            'notes' => $validated['notes'],
            'created_by' => auth()->id(),
        ]);

        $warranty->update(['status' => 'claimed']);

        return redirect()->route('warranties.show', $warranty)->with('success', 'Klaim garansi berhasil dicatat.');
    }
}
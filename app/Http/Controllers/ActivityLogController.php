<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $isSuperAdmin = auth()->user()->is_super_admin;
        $companyId = auth()->user()->company_id;

        $query = ActivityLog::with(['user', 'company'])->latest('created_at');

        if (!$isSuperAdmin) {
            $query->where('company_id', $companyId)
                  ->whereHas('user', fn($q) => $q->where('is_super_admin', false));
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('search')) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        $logs = $query->paginate(30)->withQueryString();

        $users = $isSuperAdmin
            ? User::orderBy('name')->get()
            : User::where('company_id', $companyId)->where('is_super_admin', false)->orderBy('name')->get();

        return view('activity-logs.index', compact('logs', 'users', 'isSuperAdmin'));
    }
}
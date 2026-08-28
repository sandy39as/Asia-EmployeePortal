<?php

namespace App\Http\Controllers\Hrd;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;

class HrdDashboardController extends Controller
{
    public function index()
    {
        $totalPending = LeaveRequest::query()
            ->where('status', 'pending')
            ->count();

        $totalApproved = LeaveRequest::query()
            ->where('status', 'approved')
            ->count();

        $totalRejected = LeaveRequest::query()
            ->where('status', 'rejected')
            ->count();

        $totalPengajuan = LeaveRequest::query()
            ->count();

        $recentRequests = LeaveRequest::query()
            ->with('employee')
            ->latest()
            ->take(8)
            ->get();

        return view('hrd.dashboard', compact(
            'totalPending',
            'totalApproved',
            'totalRejected',
            'totalPengajuan',
            'recentRequests'
        ));
    }
}

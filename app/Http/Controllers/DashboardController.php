<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $employee = $user->employee;

        /*
         * Kalau akun HRD/admin tidak terhubung employee,
         * tetap jangan error.
         */
        if (! $employee) {
            return view('dashboard', [
                'employee' => null,
                'totalPengajuan' => 0,
                'totalPending' => 0,
                'totalApproved' => 0,
                'totalRejected' => 0,
                'recentRequests' => collect(),
            ]);
        }

        $query = LeaveRequest::query()
            ->where(
                'employee_id',
                $employee->id
            );

        $totalPengajuan =
            (clone $query)->count();

        $totalPending =
            (clone $query)
                ->where('status', 'pending')
                ->count();

        $totalApproved =
            (clone $query)
                ->where('status', 'approved')
                ->count();

        $totalRejected =
            (clone $query)
                ->where('status', 'rejected')
                ->count();

        $recentRequests =
            (clone $query)
                ->latest()
                ->take(5)
                ->get();

        return view(
            'dashboard',
            compact(
                'employee',
                'totalPengajuan',
                'totalPending',
                'totalApproved',
                'totalRejected',
                'recentRequests'
            )
        );
    }
}

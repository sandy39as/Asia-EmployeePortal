<?php

namespace App\Http\Controllers\Kabag;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class KabagLeaveRequestController extends Controller
{
    public function index(Request $request)
    {
        $kabag = $request->user();

        $employeeIds = $kabag
            ->managedEmployees()
            ->pluck('employees.id');

        return view(
            'kabag.leave-requests.index',
            compact(
                'kabag',
                'employeeIds'
            )
        );
    }
}

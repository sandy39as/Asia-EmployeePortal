<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KabagIdentityMappingController extends Controller
{
    public function index(): View
    {
        $kabags =
            User::query()
                ->where('role', 'kabag')
                ->with('selfEmployee')
                ->orderBy('name')
                ->get();

        $employees =
            Employee::query()
                ->where('is_active', true)
                ->orderBy('nama')
                ->orderBy('source_device_id')
                ->orderBy('employee_code')
                ->get();

        return view(
            'master.kabag-identity-mapping.index',
            compact('kabags', 'employees')
        );
    }

    public function update(
        Request $request,
        User $kabag
    ): RedirectResponse {
        abort_unless(
            $kabag->role === 'kabag',
            404
        );

        $validated =
            $request->validate([
                'self_employee_id' => [
                    'nullable',
                    'integer',
                    'exists:employees,id',
                ],
            ]);

        $employeeId =
            $validated['self_employee_id']
            ?? null;

        if ($employeeId) {
            $employee =
                Employee::query()
                    ->whereKey($employeeId)
                    ->where('is_active', true)
                    ->first();

            if (! $employee) {
                return back()->with(
                    'error',
                    'Data karyawan yang dipilih tidak aktif atau tidak ditemukan.'
                );
            }
        }

        $kabag->update([
            'self_employee_id' => $employeeId,
        ]);

        return back()->with(
            'success',
            $employeeId
                ? 'Identitas karyawan untuk ' . $kabag->name . ' berhasil disimpan.'
                : 'Mapping identitas karyawan untuk ' . $kabag->name . ' berhasil dilepas.'
        );
    }
}

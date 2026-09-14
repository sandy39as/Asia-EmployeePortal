<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\SpecialLeaveType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SpecialLeaveTypeController extends Controller
{
    public function index(Request $request)
    {
        $search = trim(
            (string) $request->get('search', '')
        );

        $status = trim(
            (string) $request->get('status', '')
        );

        $items = SpecialLeaveType::query()
            ->when(
                $search !== '',
                function ($query) use ($search) {
                    $query->where(function ($sub) use ($search) {
                        $sub
                            ->where(
                                'name',
                                'like',
                                "%{$search}%"
                            )
                            ->orWhere(
                                'code',
                                'like',
                                "%{$search}%"
                            )
                            ->orWhere(
                                'description',
                                'like',
                                "%{$search}%"
                            );
                    });
                }
            )
            ->when(
                $status === 'active',
                fn ($query) =>
                    $query->where('is_active', true)
            )
            ->when(
                $status === 'inactive',
                fn ($query) =>
                    $query->where('is_active', false)
            )
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        $summary = [
            'total' =>
                SpecialLeaveType::count(),

            'active' =>
                SpecialLeaveType::where(
                    'is_active',
                    true
                )->count(),

            'inactive' =>
                SpecialLeaveType::where(
                    'is_active',
                    false
                )->count(),
        ];

        return view(
            'master.special-leave-types.index',
            compact(
                'items',
                'search',
                'status',
                'summary'
            )
        );
    }

    public function store(Request $request)
    {
        $validated =
            $this->validateForm($request);

        SpecialLeaveType::create(
            $validated
        );

        return back()->with(
            'success',
            'Jenis cuti khusus berhasil ditambahkan.'
        );
    }

    public function update(
        Request $request,
        SpecialLeaveType $specialLeaveType
    ) {
        $validated =
            $this->validateForm(
                $request,
                $specialLeaveType->id
            );

        $specialLeaveType->update(
            $validated
        );

        return back()->with(
            'success',
            'Jenis cuti khusus berhasil diperbarui.'
        );
    }

    public function destroy(
        SpecialLeaveType $specialLeaveType
    ) {
        /*
        |--------------------------------------------------------------------------
        | Nanti setelah leave_requests terhubung ke special_leave_type_id,
        | kita bisa tambahkan proteksi agar master yang sudah dipakai
        | tidak benar-benar dihapus.
        |--------------------------------------------------------------------------
        */

        $specialLeaveType->delete();

        return back()->with(
            'success',
            'Jenis cuti khusus berhasil dihapus.'
        );
    }

    private function validateForm(
        Request $request,
        ?int $ignoreId = null
    ): array {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:150',
            ],

            'code' => [
                'required',
                'string',
                'max:50',

                Rule::unique(
                    'special_leave_types',
                    'code'
                )->ignore($ignoreId),
            ],

            'default_days' => [
                'required',
                'integer',
                'min:1',
                'max:365',
            ],

            'description' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'is_active' => [
                'nullable',
                'boolean',
            ],
        ]);

        return [
            'name' =>
                trim($validated['name']),

            'code' =>
                strtoupper(
                    trim($validated['code'])
                ),

            'default_days' =>
                (int) $validated['default_days'],

            'description' =>
                isset($validated['description'])
                    ? trim(
                        $validated['description']
                    )
                    : null,

            'is_active' =>
                (bool) (
                    $validated['is_active']
                    ?? false
                ),
        ];
    }
}

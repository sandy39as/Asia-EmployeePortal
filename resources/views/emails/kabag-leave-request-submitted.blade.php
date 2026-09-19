@php
    $requestItem = $leaveRequest;

    $jenisLabel = match ($requestItem->jenis) {
        'cuti' => 'Cuti',
        'sakit' => 'Sakit',
        default => 'Izin',
    };

    if ($requestItem->jenis === 'izin' && $requestItem->permissionType) {
        $jenisLabel = 'Izin - ' . $requestItem->permissionType->name;
    }

    if ($requestItem->jenis === 'cuti' && $requestItem->leave_category === 'annual') {
        $jenisLabel = 'Cuti Tahunan';
    }

    if ($requestItem->jenis === 'cuti' && $requestItem->leave_category === 'special') {
        $jenisLabel = $requestItem->specialLeaveType?->name ?? 'Cuti Khusus';
    }

    $tanggalMulai = $requestItem->tanggal_mulai
        ? \Carbon\Carbon::parse($requestItem->tanggal_mulai)->format('d/m/Y')
        : '-';

    $tanggalSelesai = $requestItem->tanggal_selesai
        ? \Carbon\Carbon::parse($requestItem->tanggal_selesai)->format('d/m/Y')
        : '-';

    $periode = $tanggalMulai === $tanggalSelesai
        ? $tanggalMulai
        : $tanggalMulai . ' - ' . $tanggalSelesai;

    $durasiLabel = $requestItem->durasi_type === 'hourly'
        ? substr((string) $requestItem->jam_mulai, 0, 5)
            . ' - '
            . substr((string) $requestItem->jam_selesai, 0, 5)
        : (
            $requestItem->leave_days
                ? $requestItem->leave_days . ' hari'
                : 'Sehari penuh'
        );

    $portalUrl = route('kabag.leave-requests.index');
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengajuan Baru Menunggu Persetujuan</title>
</head>

<body style="margin:0;padding:0;background:#f8fafc;font-family:Arial,Helvetica,sans-serif;color:#0f172a;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f8fafc;padding:24px 12px;">
    <tr>
        <td align="center">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:620px;background:#ffffff;border:1px solid #e2e8f0;border-radius:18px;overflow:hidden;">
                <tr>
                    <td style="padding:24px 28px;background:#0f172a;color:#ffffff;">
                        <div style="font-size:12px;font-weight:700;letter-spacing:1.2px;text-transform:uppercase;color:#cbd5e1;">
                            Employee Portal
                        </div>

                        <div style="margin-top:6px;font-size:22px;font-weight:800;line-height:1.3;">
                            Pengajuan Baru Menunggu Persetujuan
                        </div>
                    </td>
                </tr>

                <tr>
                    <td style="padding:26px 28px;">
                        <p style="margin:0 0 16px;font-size:14px;line-height:1.7;color:#475569;">
                            Yth. <strong style="color:#0f172a;">{{ $kabag->name ?? 'Kabag' }}</strong>,
                        </p>

                        <p style="margin:0 0 22px;font-size:14px;line-height:1.7;color:#475569;">
                            Terdapat pengajuan baru yang menunggu persetujuan Anda.
                            Silakan buka Employee Portal untuk meninjau dan memberikan keputusan.
                        </p>

                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;background:#f8fafc;border:1px solid #e2e8f0;border-radius:14px;">
                            <tr>
                                <td style="width:34%;padding:11px 14px;border-bottom:1px solid #e2e8f0;font-size:12px;font-weight:700;color:#64748b;">
                                    Nama Karyawan
                                </td>
                                <td style="padding:11px 14px;border-bottom:1px solid #e2e8f0;font-size:13px;font-weight:700;color:#0f172a;">
                                    {{ $employee->nama ?? '-' }}
                                </td>
                            </tr>

                            <tr>
                                <td style="padding:11px 14px;border-bottom:1px solid #e2e8f0;font-size:12px;font-weight:700;color:#64748b;">
                                    ID Karyawan
                                </td>
                                <td style="padding:11px 14px;border-bottom:1px solid #e2e8f0;font-size:13px;color:#0f172a;">
                                    {{ $employee->employee_code ?? '-' }}
                                </td>
                            </tr>

                            <tr>
                                <td style="padding:11px 14px;border-bottom:1px solid #e2e8f0;font-size:12px;font-weight:700;color:#64748b;">
                                    Jenis
                                </td>
                                <td style="padding:11px 14px;border-bottom:1px solid #e2e8f0;font-size:13px;color:#0f172a;">
                                    {{ $jenisLabel }}
                                </td>
                            </tr>

                            <tr>
                                <td style="padding:11px 14px;border-bottom:1px solid #e2e8f0;font-size:12px;font-weight:700;color:#64748b;">
                                    Periode
                                </td>
                                <td style="padding:11px 14px;border-bottom:1px solid #e2e8f0;font-size:13px;color:#0f172a;">
                                    {{ $periode }}
                                </td>
                            </tr>

                            <tr>
                                <td style="padding:11px 14px;border-bottom:1px solid #e2e8f0;font-size:12px;font-weight:700;color:#64748b;">
                                    Durasi
                                </td>
                                <td style="padding:11px 14px;border-bottom:1px solid #e2e8f0;font-size:13px;color:#0f172a;">
                                    {{ $durasiLabel }}
                                </td>
                            </tr>

                            <tr>
                                <td style="padding:11px 14px;font-size:12px;font-weight:700;color:#64748b;vertical-align:top;">
                                    Alasan
                                </td>
                                <td style="padding:11px 14px;font-size:13px;line-height:1.6;color:#0f172a;">
                                    {{ $requestItem->alasan ?: '-' }}
                                </td>
                            </tr>
                        </table>

                        <div style="margin-top:24px;text-align:center;">
                            <a
                                href="{{ $portalUrl }}"
                                style="display:inline-block;background:#2563eb;color:#ffffff;text-decoration:none;font-size:14px;font-weight:700;padding:12px 22px;border-radius:10px;"
                            >
                                Buka Employee Portal
                            </a>
                        </div>

                        <p style="margin:22px 0 0;font-size:12px;line-height:1.6;color:#94a3b8;text-align:center;">
                            Persetujuan atau penolakan tetap dilakukan setelah login ke Employee Portal.
                        </p>
                    </td>
                </tr>

                <tr>
                    <td style="padding:16px 28px;border-top:1px solid #e2e8f0;background:#f8fafc;text-align:center;font-size:11px;color:#94a3b8;">
                        HRIS Asia Plastik · hrisasiaplastik.com
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>

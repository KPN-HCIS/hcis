<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Declaration</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        .header {
            width: 100%;
            height: auto;
        }

        .header img {
            width: 100%;
            height: auto;
            margin-bottom: 20px;
        }

        .content {
            padding: 20px;
        }

        h5 {
            font-size: 14px;
            margin: 0;
            padding: 0;
        }

        p {
            margin-top: 4px;
            padding: 2px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        td {
            padding: 5px;
            vertical-align: top;
        }

        .center {
            text-align: center;
        }

        .label {
            width: 30%;
        }

        .colon {
            width: 20px;
            text-align: center;
        }

        .value {
            width: 70%;
        }

        .section-title {
            margin-top: 20px;
        }

        .table-approve {
            border-collapse: collapse;
            width: 100%;
        }

        .table-approve th,
        .table-approve td {
            border: 1px solid black;
            padding: 8px;
            text-align: center;
        }

        .table-approve .head-row {
            font-weight: bold;
        }

        .table-approve th {
            background-color: #c6e0b4;
        }

        .table-approve .total-row {
            text-align: left;
        }
    </style>
</head>

<body>
    <div class="header">
        <img src="{{ public_path('images/kop.jpg') }}" alt="Kop Surat">
    </div>
    <h5 class="center">DECLARATION</h5>
    <h5 class="center">No. {{ $transactions->no_ca }}</h5>

    <table>
        <tr>
            <td colspan="3"><b>Data Karyawan:</b></td>
        </tr>
        <tr>
            <td class="label">Nama</td>
            <td class="colon">:</td>
            <td class="value">{{ $transactions->employee->fullname }}</td>
        </tr>
        <tr>
            <td class="label">NIK</td>
            <td class="colon">:</td>
            <td class="value">{{ $transactions->employee->employee_id }}</td>
        </tr>
        <tr>
            <td class="label">Email</td>
            <td class="colon">:</td>
            <td class="value">{{ $transactions->employee->email }}</td>
        </tr>
        <tr>
            <td class="label">Detail Rekening</td>
            <td class="colon">:</td>
            <td class="value">{{ $transactions->employee->bank_details }}</td>
        </tr>
        <tr>
            <td class="label">Divisi/Dept</td>
            <td class="colon">:</td>
            <td class="value">{{ $transactions->employee->unit }}</td>
        </tr>
        <tr>
            <td class="label">PT/Lokasi</td>
            <td class="colon">:</td>
            <td class="value">{{ $transactions->employee->company_name }}</td>
        </tr>
    </table>

    <table>
        <tr>
            <td colspan="3"><b>Detail Pengajuan CA:</b></td>
        </tr>
        <tr>
            <td class="label">Costing Company</td>
            <td class="colon">:</td>
            <td class="value">
                {{ $transactions->contribution_level_code }}
            </td>
        </tr>
        <tr>
            <td class="label">Lokasi</td>
            <td class="colon">:</td>
            <td class="value">
                {{ $transactions->destination == 'Others' ? $transactions->others_location : $transactions->destination }}
            </td>
        </tr>
        <tr>
            <td class="label">Mulai</td>
            <td class="colon">:</td>
            <td class="value">{{ \Carbon\Carbon::parse($transactions->start_date)->format('d-M-y') }}</td>
        </tr>
        <tr>
            <td class="label">Berakhir</td>
            <td class="colon">:</td>
            <td class="value">{{ \Carbon\Carbon::parse($transactions->end_date)->format('d-M-y') }}</td>
        </tr>
        <tr>
            <td class="label">Total Hari</td>
            <td class="colon">:</td>
            <td class="value">{{ $transactions->total_days }} Hari</td>
        </tr>
        <tr>
            <td class="label">Tanggal CA dibutuhkan</td>
            <td class="colon">:</td>
            <td class="value">{{ \Carbon\Carbon::parse($transactions->date_required)->format('d-M-y') }}</td>
        </tr>
        <tr>
            <td class="label">Estimasi Deklarasi</td>
            <td class="colon">:</td>
            <td class="value">{{ \Carbon\Carbon::parse($transactions->declare_estimate)->format('d-M-y') }}</td>
        </tr>
        <tr>
            <td class="label">Keperluan</td>
            <td class="colon">:</td>
            <td class="value">{{ $transactions->ca_needs }}</td>
        </tr>
    </table>

    @php
        $detailCA = json_decode($transactions->detail_ca, true);
        $declareCA = json_decode($transactions->declare_ca, true);
    @endphp

    @if ($transactions->type_ca == 'dns')

        @if (isset($detailCA['detail_perdiem']) && is_array($detailCA['detail_perdiem']))
            <table class="table-approve">
                <tr>
                    <th colspan="6"><b>Travel Plan:</b></th>
                </tr>
                <tr class="head-row">
                    <td>Mulai</td>
                    <td>Selesai</td>
                    <td>Lokasi dinas</td>
                    <td>Company Code</td>
                    <td>Jumlah Hari</td>
                    <td>Amount</td>
                </tr>

                @foreach ($detailCA['detail_perdiem'] as $perdiem)
                    @if (!empty($perdiem['total_days']) && !empty($perdiem['nominal']))
                        <tr style="text-align: center">
                            <td>{{ !empty($perdiem['start_date']) ? \Carbon\Carbon::parse($perdiem['start_date'])->format('d-M-y') : '-' }}
                            </td>
                            <td>{{ !empty($perdiem['end_date']) ? \Carbon\Carbon::parse($perdiem['end_date'])->format('d-M-y') : '-' }}
                            </td>
                            <td>{{ $perdiem['location'] == 'Others' ? $perdiem['other_location'] ?? '-' : $perdiem['location'] ?? '-' }}
                            </td>
                            <td>{{ $perdiem['company_code'] ?? '-' }}</td>
                            <td>{{ $perdiem['total_days'] }} Hari</td>
                            <td>Rp. {{ number_format($perdiem['nominal'], 0, ',', '.') }}</td>
                        </tr>
                    @endif
                @endforeach

                @php
                    $totalDays = array_sum(array_column($detailCA['detail_perdiem'], 'total_days') ?? []);
                    $totalNominal = array_sum(array_column($detailCA['detail_perdiem'], 'nominal') ?? []);
                @endphp

                @if ($totalDays > 0 && $totalNominal > 0)
                    <tr class="total-row">
                        <td colspan="4" class="head-row">Total</td>
                        <td>{{ $totalDays }} Hari</td>
                        <td>Rp. {{ number_format($totalNominal, 0, ',', '.') }}</td>
                    </tr>
                @endif
            </table>
        @endif


        @if (
            !empty($declareCA['detail_perdiem']) &&
                count($declareCA['detail_perdiem']) > 0 &&
                !empty($declareCA['detail_perdiem'][0]['company_code']))
            <table class="table-approve">
                <tr>
                    <th colspan="6"><b>Travel Plan (Declaration):</b></th>
                </tr>
                <tr class="head-row">
                    <td>Mulai</td>
                    <td>Selesai</td>
                    <td>Lokasi dinas</td>
                    <td>Company Code</td>
                    <td>Jumlah Hari</td>
                    <td>Amount</td>
                </tr>

                @foreach ($declareCA['detail_perdiem'] as $perdiem)
                    @if (!empty($perdiem['total_days']) && !empty($perdiem['nominal']))
                        <tr style="text-align: center">
                            <td>{{ !empty($perdiem['start_date']) ? \Carbon\Carbon::parse($perdiem['start_date'])->format('d-M-y') : '-' }}
                            </td>
                            <td>{{ !empty($perdiem['end_date']) ? \Carbon\Carbon::parse($perdiem['end_date'])->format('d-M-y') : '-' }}
                            </td>
                            <td>{{ !empty($perdiem['location']) && $perdiem['location'] == 'Others' ? $perdiem['other_location'] ?? '-' : $perdiem['location'] ?? '-' }}
                            </td>
                            <td>{{ $perdiem['company_code'] ?? '-' }}</td>
                            <td>{{ $perdiem['total_days'] ?? '-' }} Hari</td>
                            <td>Rp. {{ number_format($perdiem['nominal'] ?? 0, 0, ',', '.') }}</td>
                        </tr>
                    @endif
                @endforeach

                @php
                    $totalDays = array_sum(array_column($declareCA['detail_perdiem'], 'total_days') ?? []);
                    $totalNominal = array_sum(array_column($declareCA['detail_perdiem'], 'nominal') ?? []);
                @endphp

                @if ($totalDays > 0 && $totalNominal > 0)
                    <tr class="total-row">
                        <td colspan="4" class="head-row">Total</td>
                        <td>{{ $totalDays }} Hari</td>
                        <td>Rp. {{ number_format($totalNominal, 0, ',', '.') }}</td>
                    </tr>
                @endif
            </table>
        @endif


        @if (
            !empty($detailCA['detail_transport']) &&
                count($detailCA['detail_transport']) > 0 &&
                !empty($detailCA['detail_transport'][0]['company_code']))
            <table class="table-approve">
                <tr>
                    <th colspan="4"><b>Transport Plan :</b></th>
                </tr>
                <tr class="head-row">
                    <td>Tanggal</td>
                    <td>Keterangan</td>
                    <td>Company Code</td>
                    <td>Amount</td>
                </tr>

                @foreach ($detailCA['detail_transport'] as $transport)
                    @if (!empty($transport['company_code']) && !empty($transport['nominal']) && $transport['nominal'] != 0)
                        <tr style="text-align: center">
                            <td>{{ !empty($transport['tanggal']) ? \Carbon\Carbon::parse($transport['tanggal'])->format('d-M-y') : '-' }}
                            </td>
                            <td>{{ $transport['keterangan'] ?? '-' }}</td>
                            <td>{{ $transport['company_code'] ?? '-' }}</td>
                            <td>Rp. {{ number_format($transport['nominal'] ?? 0, 0, ',', '.') }}</td>
                        </tr>
                    @endif
                @endforeach

                @php
                    $totalNominal = array_sum(array_column($detailCA['detail_transport'], 'nominal') ?? []);
                @endphp

                @if ($totalNominal > 0)
                    <tr class="total-row">
                        <td colspan="3" class="head-row">Total</td>
                        <td>Rp. {{ number_format($totalNominal, 0, ',', '.') }}</td>
                    </tr>
                @endif
            </table>
        @endif
        @if (
            !empty($declareCA['detail_transport']) &&
                count($declareCA['detail_transport']) > 0 &&
                !empty($declareCA['detail_transport'][0]['company_code']))
            <table class="table-approve">
                <tr>
                    <th colspan="4"><b>Transport Plan (Declare CA):</b></th>
                </tr>
                <tr class="head-row">
                    <td>Date</td>
                    <td>Description</td>
                    <td>Company Code</td>
                    <td>Amount</td>
                </tr>

                @foreach ($declareCA['detail_transport'] as $transport)
                    @if (!empty($transport['company_code']) && !empty($transport['nominal']) && $transport['nominal'] != 0)
                        <tr style="text-align: center">
                            <td>{{ !empty($transport['tanggal']) ? \Carbon\Carbon::parse($transport['tanggal'])->format('d-M-y') : '-' }}
                            </td>
                            <td>{{ $transport['keterangan'] ?? '-' }}</td>
                            <td>{{ $transport['company_code'] ?? '-' }}</td>
                            <td>Rp. {{ number_format($transport['nominal'] ?? 0, 0, ',', '.') }}</td>
                        </tr>
                    @endif
                @endforeach

                @php
                    $totalNominalDeclare = array_sum(array_column($declareCA['detail_transport'], 'nominal') ?? []);
                @endphp

                @if ($totalNominalDeclare > 0)
                    <tr class="total-row">
                        <td colspan="3" class="head-row">Total</td>
                        <td>Rp. {{ number_format($totalNominalDeclare, 0, ',', '.') }}</td>
                    </tr>
                @endif
            </table>
        @endif
        @if (
            !empty($detailCA['detail_penginapan']) &&
                count($detailCA['detail_penginapan']) > 0 &&
                !empty($detailCA['detail_penginapan'][0]['company_code']))
            <table class="table-approve">
                <tr>
                    <th colspan="6"><b>Accommodation Plan:</b></th>
                </tr>
                <tr class="head-row">
                    <td>Start</td>
                    <td>End</td>
                    <td>Hotel Name</td>
                    <td>Company Code</td>
                    <td>Total Days</td>
                    <td>Amount</td>
                </tr>

                @foreach ($detailCA['detail_penginapan'] as $penginapan)
                    @if (!empty($penginapan['total_days']) && !empty($penginapan['nominal']) && $penginapan['nominal'] != 0)
                        <tr style="text-align: center">
                            <td>{{ !empty($penginapan['start_date']) ? \Carbon\Carbon::parse($penginapan['start_date'])->format('d-M-y') : '-' }}
                            </td>
                            <td>{{ !empty($penginapan['end_date']) ? \Carbon\Carbon::parse($penginapan['end_date'])->format('d-M-y') : '-' }}
                            </td>
                            <td>{{ $penginapan['hotel_name'] ?? '-' }}</td>
                            <td>{{ $penginapan['company_code'] ?? '-' }}</td>
                            <td>{{ $penginapan['total_days'] ?? '-' }} Days</td>
                            <td>Rp. {{ number_format($penginapan['nominal'] ?? 0, 0, ',', '.') }}</td>
                        </tr>
                    @endif
                @endforeach

                @php
                    $totalDays = array_sum(array_column($detailCA['detail_penginapan'], 'total_days') ?? []);
                    $totalNominal = array_sum(array_column($detailCA['detail_penginapan'], 'nominal') ?? []);
                @endphp

                @if ($totalDays > 0 && $totalNominal > 0)
                    <tr class="total-row">
                        <td colspan="4" class="head-row">Total</td>
                        <td>{{ $totalDays }} Days</td>
                        <td>Rp. {{ number_format($totalNominal, 0, ',', '.') }}</td>
                    </tr>
                @endif
            </table>
        @endif
        @if (
            !empty($declareCA['detail_penginapan']) &&
                count($declareCA['detail_penginapan']) > 0 &&
                !empty($declareCA['detail_penginapan'][0]['company_code']))
            <table class="table-approve">
                <tr>
                    <th colspan="6"><b>Accommodation Plan (Declare CA):</b></th>
                </tr>
                <tr class="head-row">
                    <td>Start</td>
                    <td>End</td>
                    <td>Hotel Name</td>
                    <td>Company Code</td>
                    <td>Total Days</td>
                    <td>Amount</td>
                </tr>

                @foreach ($declareCA['detail_penginapan'] as $penginapan)
                    @if (!empty($penginapan['total_days']) && !empty($penginapan['nominal']) && $penginapan['nominal'] != 0)
                        <tr style="text-align: center">
                            <td>{{ !empty($penginapan['start_date']) ? \Carbon\Carbon::parse($penginapan['start_date'])->format('d-M-y') : '-' }}
                            </td>
                            <td>{{ !empty($penginapan['end_date']) ? \Carbon\Carbon::parse($penginapan['end_date'])->format('d-M-y') : '-' }}
                            </td>
                            <td>{{ $penginapan['hotel_name'] ?? '-' }}</td>
                            <td>{{ $penginapan['company_code'] ?? '-' }}</td>
                            <td>{{ $penginapan['total_days'] ?? '-' }} Days</td>
                            <td>Rp. {{ number_format($penginapan['nominal'] ?? 0, 0, ',', '.') }}</td>
                        </tr>
                    @endif
                @endforeach

                @php
                    $totalDays = array_sum(array_column($declareCA['detail_penginapan'], 'total_days') ?? []);
                    $totalNominal = array_sum(array_column($declareCA['detail_penginapan'], 'nominal') ?? []);
                @endphp

                @if ($totalDays > 0 && $totalNominal > 0)
                    <tr class="total-row">
                        <td colspan="4" class="head-row">Total</td>
                        <td>{{ $totalDays }} Days</td>
                        <td>Rp. {{ number_format($totalNominal, 0, ',', '.') }}</td>
                    </tr>
                @endif
            </table>
        @endif



        @if (
            !empty($detailCA['detail_lainnya']) &&
                count($detailCA['detail_lainnya']) > 0 &&
                !empty($detailCA['detail_lainnya'][0]['keterangan']))
            <table class="table-approve">
                <tr>
                    <th colspan="3"><b>Other Plans :</b></th>
                </tr>
                <tr class="head-row">
                    <td>Tgl</td>
                    <td>Keterangan</td>
                    <td>Amount</td>
                </tr>

                @foreach ($detailCA['detail_lainnya'] as $lainnya)
                    @if (!empty($lainnya['nominal']) && $lainnya['nominal'] != 0)
                        <tr style="text-align: center">
                            <td>{{ !empty($lainnya['tanggal']) ? \Carbon\Carbon::parse($lainnya['tanggal'])->format('d-M-y') : '-' }}
                            </td>
                            <td>{{ $lainnya['keterangan'] ?? '-' }}</td>
                            <td>Rp. {{ number_format($lainnya['nominal'] ?? 0, 0, ',', '.') }}</td>
                        </tr>
                    @endif
                @endforeach

                @php
                    $totalNominal = array_sum(array_column($detailCA['detail_lainnya'], 'nominal') ?? []);
                @endphp

                @if ($totalNominal > 0)
                    <tr class="total-row">
                        <td colspan="2" class="head-row">Total</td>
                        <td>Rp. {{ number_format($totalNominal, 0, ',', '.') }}</td>
                    </tr>
                @endif
            </table>
        @endif
    @endif
    @if (
        !empty($declareCA['detail_lainnya']) &&
            count($declareCA['detail_lainnya']) > 0 &&
            !empty($declareCA['detail_lainnya'][0]['keterangan']))
        <table class="table-approve">
            <tr>
                <th colspan="3"><b>Other Plans (Declare CA):</b></th>
            </tr>
            <tr class="head-row">
                <td>Date</td>
                <td>Description</td>
                <td>Amount</td>
            </tr>

            @foreach ($declareCA['detail_lainnya'] as $lainnya)
                @if (!empty($lainnya['nominal']) && $lainnya['nominal'] != 0)
                    <tr style="text-align: center">
                        <td>{{ !empty($lainnya['tanggal']) ? \Carbon\Carbon::parse($lainnya['tanggal'])->format('d-M-y') : '-' }}
                        </td>
                        <td>{{ $lainnya['keterangan'] ?? '-' }}</td>
                        <td>Rp. {{ number_format($lainnya['nominal'] ?? 0, 0, ',', '.') }}</td>
                    </tr>
                @endif
            @endforeach

            @php
                $totalNominal = array_sum(array_column($declareCA['detail_lainnya'], 'nominal') ?? []);
            @endphp

            @if ($totalNominal > 0)
                <tr class="total-row">
                    <td colspan="2" class="head-row">Total</td>
                    <td>Rp. {{ number_format($totalNominal, 0, ',', '.') }}</td>
                </tr>
            @endif
        </table>
    @endif


    @if ($approval && count($approval) > 0)
        <table class="table-approve" style="width: 100%; text-align:center; margin-top: 20px;">
            <tr>
                <td colspan="{{ count($approval) }}">Verification</td>
            </tr>
            <tr>
                @foreach ($approval as $role)
                    <td>{{ $role->role_name }}</td>
                @endforeach
            </tr>
            <tr>
                @foreach ($approval as $role)
                    <td><br><br><br><br></td>
                @endforeach
            </tr>
            <tr>
                @foreach ($approval as $role)
                    <td>{{ $role->employee ? $role->employee->fullname : 'Data not available' }}</td>
                @endforeach
            </tr>
            <tr>
                @foreach ($approval as $role)
                    <td>
                        Date: <br>
                        {{ $role->approved_at ? \Carbon\Carbon::parse($role->approved_at)->format('d-M-y') : 'Data not available' }}
                    </td>
                @endforeach
            </tr>
        </table>
    @else
        <table>
            <tr>
                <td colspan="3">No approval data available.</td>
            </tr>
        </table>
    @endif


    @if ($approval && count($approval) > 0)
        <table class="table-approve" style="width: 100%; text-align:center; margin-top: 20px;">
            <tr>
                <td colspan="{{ count($approval) }}">Approval</td>
            </tr>
            <tr>
                @foreach ($approval as $role)
                    <td>{{ $role->role_name }}</td>
                @endforeach
            </tr>
            <tr>
                @foreach ($approval as $role)
                    <td><br><br><br><br></td>
                @endforeach
            </tr>
            <tr>
                @foreach ($approval as $role)
                    <td>{{ $role->employee ? $role->employee->fullname : 'Data not available' }}</td>
                @endforeach
            </tr>
            <tr>
                @foreach ($approval as $role)
                    <td>
                        Date: <br>
                        {{ $role->approved_at ? \Carbon\Carbon::parse($role->approved_at)->format('d-M-y') : 'Data not available' }}
                    </td>
                @endforeach
            </tr>
        </table>
    @else
        <table>
            <tr>
                <td colspan="3">No approval data available.</td>
            </tr>
        </table>
    @endif

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>CA Transaction</title>
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
            padding: 0px;
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

        .approved {
            color: green;
        }

        .pending {
            color: yellow;
        }

        footer {
            position: fixed;
            bottom: 0cm;
            left: 0cm;
            right: 0cm;
            height: 2cm;
            text-align: right;
            line-height: 1.5cm;
            font-size: 12px;
            color: #555;
        }
    </style>
</head>

<body>
    <div class="header">
        <img src="{{ public_path('images/kop.jpg') }}" alt="Kop Surat">
    </div>
    <h5 class="center">Form Cash Advanced</h5>
    <h5 class="center">No. {{ $transactions->no_ca }}</h5>

    <table>
        <tr>
            <td colspan="3"><b>Employee Data:</b></td>
        </tr>
        <tr>
            <td class="label">Name</td>
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
            <td class="label">Account Details</td>
            <td class="colon">:</td>
            <td class="value">{{ $transactions->employee->bank_name }} -
                {{ $transactions->employee->bank_account_number }} - {{ $transactions->employee->bank_account_name }}
            </td>
        </tr>
        <tr>
            <td class="label">Division/Dept</td>
            <td class="colon">:</td>
            <td class="value">{{ $transactions->employee->unit }}</td>
        </tr>
        <tr>
            <td class="label">PT/Location</td>
            <td class="colon">:</td>
            <td class="value">{{ $transactions->employee->contribution_level_code }} /
                {{ $transactions->employee->office_area }}</td>
        </tr>
    </table>

    <table>
        <tr>
            <td colspan="3"><b>CA Submission Details:</b></td>
        </tr>
        <tr>
            <td class="label">Costing Company</td>
            <td class="colon">:</td>
            <td class="value">
                {{ $transactions->companies->contribution_level }} ({{ $transactions->contribution_level_code }})
            </td>
        </tr>
        <tr>
            <td class="label">Location</td>
            <td class="colon">:</td>
            <td class="value">
                {{ $transactions->destination == 'Others' ? $transactions->others_location : $transactions->destination }}
            </td>
        </tr>
        <tr>
            <td class="label">Start Date</td>
            <td class="colon">:</td>
            <td class="value">{{ \Carbon\Carbon::parse($transactions->start_date)->format('d-M-y') }} to
                {{ \Carbon\Carbon::parse($transactions->end_date)->format('d-M-y') }} ({{ $transactions->total_days }}
                days)</td>
        </tr>
        <tr>
            <td class="label">Date CA Required</td>
            <td class="colon">:</td>
            <td class="value">{{ \Carbon\Carbon::parse($transactions->date_required)->format('d-M-y') }}</td>
        </tr>
        <tr>
            <td class="label">Purpose</td>
            <td class="colon">:</td>
            <td class="value">{{ $transactions->ca_needs }}</td>
        </tr>
        <tr>
            <td class="label">Status</td>
            <td class="colon">:</td>
            <td class="value">{{ $transactions->approval_sett }}</td>
        </tr>
    </table>

    @php
        $detailCA = json_decode($transactions->detail_ca, true) ?? [];
        $declareCA = json_decode($transactions->declare_ca, true) ?? [];
    @endphp
    {{-- @php
        dd($transactions->detail_ca);
    @endphp --}}


    @if (isset($declareCA['detail_perdiem']) &&
            is_array($declareCA['detail_perdiem']) &&
            count($declareCA['detail_perdiem']) > 0 &&
            !empty($declareCA['detail_perdiem'][0]['company_code']))
        <table class="table-approve">
            <tr>
                <th colspan="5"><b>Perdiem Plan :</b></th>
            </tr>
            <tr class="head-row">
                <td>Start Date</td>
                <td>End Date</td>
                <td>Office Location</td>
                <td>Company Code</td>
                <td>Total Days</td>
            </tr>

            @foreach ($declareCA['detail_perdiem'] as $perdiem)
                <tr style="text-align: center">
                    <td>{{ \Carbon\Carbon::parse($perdiem['start_date'])->format('d-M-y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($perdiem['end_date'])->format('d-M-y') }}</td>
                    <td>
                        @if ($perdiem['location'] == 'Others')
                            Other ({{ $perdiem['other_location'] }})
                        @else
                            {{ $perdiem['location'] }}
                        @endif
                    </td>
                    <td>{{ $perdiem['company_code'] }}</td>
                    <td>{{ $perdiem['total_days'] }} Days</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="4" class="head-row">Total</td>
                <td>
                    {{ array_sum(array_column($declareCA['detail_perdiem'], 'total_days')) }} Days
                </td>
            </tr>
        </table>
    @endif
    @php
        function safeArraySum($array, $key, $subkey)
        {
            if (isset($array[$key]) && is_array($array[$key])) {
                return array_sum(array_column($array[$key], $subkey));
            }
            return 0;
        }

        function formatCurrency($amount)
        {
            return 'Rp. ' . number_format($amount, 0, ',', '.');
        }

        function formatDays($days, $suffix = 'Days')
        {
            return $days > 0 ? "$days $suffix" : '-';
        }
    @endphp

    <table class="table-approve" style="width: 80%;">
        <tr>
            <th colspan="5"><b>Detail Cash Advanced :</b></th>
        </tr>
        <tr class="head-row">
            <td rowspan="2" style="text-align: center;">Types of Down Payments</td>
            <td colspan="2">Estimate Plan</td>
            <td colspan="2">Estimate Declaration</td>
        </tr>
        <tr class="head-row">
            <td>Total Days</td>
            <td>Amount</td>
            <td>Total Days</td>
            <td>Amount</td>
        </tr>
        <tr>
            <td class="label">Perdiem</td>
            <td>{{ formatDays(safeArraySum($detailCA, 'detail_perdiem', 'total_days')) }}</td>
            <td>{{ formatCurrency(safeArraySum($detailCA, 'detail_perdiem', 'nominal')) }}</td>
            <td>{{ formatDays(safeArraySum($declareCA, 'detail_perdiem', 'total_days')) }}</td>
            <td>{{ formatCurrency(safeArraySum($declareCA, 'detail_perdiem', 'nominal')) }}</td>
        </tr>
        <tr>
            <td>Meals</td>
            <td>-</td>
            <td>{{ formatCurrency(safeArraySum($detailCA, 'detail_meals', 'nominal')) }}</td>
            <td>-</td>
            <td>{{ formatCurrency(safeArraySum($declareCA, 'detail_meals', 'nominal')) }}</td>
        </tr>
        <tr>
            <td>Transport</td>
            <td>-</td>
            <td>{{ formatCurrency(safeArraySum($detailCA, 'detail_transport', 'nominal')) }}</td>
            <td>-</td>
            <td>{{ formatCurrency(safeArraySum($declareCA, 'detail_transport', 'nominal')) }}</td>
        </tr>
        <tr>
            <td>Accomodation</td>
            <td>{{ formatDays(safeArraySum($detailCA, 'detail_penginapan', 'total_days'), 'Night') }}</td>
            <td>{{ formatCurrency(safeArraySum($detailCA, 'detail_penginapan', 'nominal')) }}</td>
            <td>{{ formatDays(safeArraySum($declareCA, 'detail_penginapan', 'total_days'), 'Night') }}</td>
            <td>{{ formatCurrency(safeArraySum($declareCA, 'detail_penginapan', 'nominal')) }}</td>
        </tr>
        <tr>
            <td>Others</td>
            <td>-</td>
            <td>{{ formatCurrency(safeArraySum($detailCA, 'detail_lainnya', 'nominal')) }}</td>
            <td>-</td>
            <td>{{ formatCurrency(safeArraySum($declareCA, 'detail_lainnya', 'nominal')) }}</td>
        </tr>
        <tr>
            <td colspan="2">Total</td>
            <td> {{ safeArraySum($detailCA, 'detail_perdiem', 'nominal') > 0 ||
            safeArraySum($detailCA, 'detail_transport', 'nominal') > 0 ||
            safeArraySum($detailCA, 'detail_penginapan', 'nominal') > 0 ||
            safeArraySum($detailCA, 'detail_lainnya', 'nominal') > 0
                ? formatCurrency($transactions->total_ca ?? 0)
                : 'Rp.'.' 0' }}
            </td>
            <td></td>
            <td>{{ formatCurrency($transactions->total_real ?? 0) }}</td>
        </tr>
    </table>
    <br>
    <div style="page-break-after:always;">
        <table border=0 style="width: 100%; font-size: 11px;">
            <tr>
                <td style="width: 20%; vertical-align: top;">
                    <table class="table-approve" style="width: 100%; text-align: center; display: inline-table;">
                        <tr>
                            <th>Submitted By</th>
                        </tr>
                        <tr>
                            <td>User</td>
                        </tr>
                        <tr>
                            <td><br><br><br><br><br></td>
                        </tr>
                        <tr>
                            <td>{{ $transactions->employee->fullname }}</td>
                        </tr>
                        <tr>
                            <td>Date : </td>
                        </tr>
                    </table>
                </td>
                <td style="width: 60%; vertical-align: top;">
                    <table class="table-approve" style="width: 100%; text-align: center; display: inline-table;">
                        <tr>
                            <th colspan="3">Verifikasi</th>
                        </tr>
                        <tr>
                            <td style="width: 33%"><br></td>
                            <td style="width: 33%"><br></td>
                            <td style="width: 33%"><br></td>
                        </tr>
                        <tr>
                            <td><br><br><br><br><br></td>
                            <td><br><br><br><br><br></td>
                            <td><br><br><br><br><br></td>
                        </tr>
                        <tr>
                            <td><br></td>
                            <td><br></td>
                            <td><br></td>
                        </tr>
                        <tr>
                            <td><br></td>
                            <td><br></td>
                            <td><br></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <table border="0" style="width: 100%; font-size: 11px;">
            <tr>
                <td style="width: 100%;">
                    <table class="table-approve" style="text-align:center;">
                        @if (!empty($approval) && count($approval) > 0)
                            <tr>
                                <th colspan="{{ count($approval) }}">Approval</th>
                            </tr>
                            <tr>
                                @foreach ($approval as $role)
                                    <td style="width: 20%;">{{ $role->role_name }}</td>
                                @endforeach
                            </tr>
                            <tr>
                                @foreach ($approval as $role)
                                    <td>
                                        @if ($role->approval_status === 'Approved')
                                            <img src="{{ public_path('images/approved_64.png') }}" alt="logo">
                                        @else
                                            <br><br><br><br><br>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                            <tr>
                                @foreach ($approval as $role)
                                    <td>{{ $role->employee ? $role->employee->fullname : 'N/A' }}</td>
                                @endforeach
                            </tr>
                            <tr>
                                @foreach ($approval as $role)
                                    <td style="text-align:center;">
                                        {{ $role->approved_at ? \Carbon\Carbon::parse($role->approved_at)->format('d-M-Y') : 'Date: N/A' }}
                                    </td>
                                @endforeach
                            </tr>
                        @else
                            <tr>
                                <td colspan="4" style="text-align:center;">No approvals available.</td>
                            </tr>
                        @endif
                    </table>
                </td>
            </tr>
        </table>
    </div>
    <div>
        <div>
            <h2 style="text-align: center">Lampiran Cash Advanced</h2>

            @php
                function hasValidData($data, $key, $subKey = null)
                {
                    return isset($data[$key]) &&
                        is_array($data[$key]) &&
                        count($data[$key]) > 0 &&
                        (!$subKey || !empty($data[$key][0][$subKey]));
                }

                function safeArraySumLampiran($array, $key)
                {
                    return isset($array) && is_array($array) ? array_sum(array_column($array, $key)) : 0;
                }

                // function formatCurrency($amount)
                // {
                //     return 'Rp. ' . number_format($amount, 0, ',', '.');
                // }

            @endphp

            @if (hasValidData($detailCA, 'detail_perdiem', 'company_code'))
                <table class="table-approve">
                    <tr>
                        <th colspan="6"><b>Perdiem Plan :</b></th>
                    </tr>
                    <tr class="head-row">
                        <td>Start Date</td>
                        <td>End Date</td>
                        <td>Office Location</td>
                        <td>Company Code</td>
                        <td>Total Days</td>
                        <td>Amount</td>
                    </tr>

                    @foreach ($detailCA['detail_perdiem'] as $perdiem)
                        <tr style="text-align: center">
                            <td>{{ \Carbon\Carbon::parse($perdiem['start_date'] ?? now())->format('d-M-y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($perdiem['end_date'] ?? now())->format('d-M-y') }}</td>
                            <td>
                                @if (($perdiem['location'] ?? '') == 'Others')
                                    Other ({{ $perdiem['other_location'] ?? '' }})
                                @else
                                    {{ $perdiem['location'] ?? '' }}
                                @endif
                            </td>
                            <td>{{ $perdiem['company_code'] ?? '' }}</td>
                            <td>{{ $perdiem['total_days'] ?? 0 }} Days</td>
                            <td>{{ formatCurrency($perdiem['nominal'] ?? 0) }}</td>
                        </tr>
                    @endforeach
                    <tr class="total-row">
                        <td colspan="4" class="head-row">Total</td>
                        <td>{{ safeArraySumLampiran($detailCA['detail_perdiem'], 'total_days') }} Days</td>
                        <td>{{ formatCurrency(safeArraySumLampiran($detailCA['detail_perdiem'], 'nominal')) }}</td>
                    </tr>
                </table>
            @endif

            @if (hasValidData($declareCA, 'detail_perdiem', 'company_code'))
                <table class="table-approve">
                    <tr>
                        <th colspan="6"><b>Perdiem Plan Declaration :</b></th>
                    </tr>
                    <tr class="head-row">
                        <td>Start Date</td>
                        <td>End Date</td>
                        <td>Office Location</td>
                        <td>Company Code</td>
                        <td>Total Days</td>
                        <td>Amount</td>
                    </tr>

                    @foreach ($declareCA['detail_perdiem'] as $perdiem)
                        <tr style="text-align: center">
                            <td>{{ \Carbon\Carbon::parse($perdiem['start_date'] ?? now())->format('d-M-y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($perdiem['end_date'] ?? now())->format('d-M-y') }}</td>
                            <td>
                                @if (($perdiem['location'] ?? '') == 'Others')
                                    Other ({{ $perdiem['other_location'] ?? '' }})
                                @else
                                    {{ $perdiem['location'] ?? '' }}
                                @endif
                            </td>
                            <td>{{ $perdiem['company_code'] ?? '' }}</td>
                            <td>{{ $perdiem['total_days'] ?? 0 }} Days</td>
                            <td>{{ formatCurrency($perdiem['nominal'] ?? 0) }}</td>
                        </tr>
                    @endforeach
                    <tr class="total-row">
                        <td colspan="4" class="head-row">Total</td>
                        <td>{{ safeArraySumLampiran($declareCA['detail_perdiem'], 'total_days') }} Days</td>
                        <td>{{ formatCurrency(safeArraySumLampiran($declareCA['detail_perdiem'], 'nominal')) }}</td>
                    </tr>
                </table>
            @endif

            @if (hasValidData($detailCA, 'detail_transport', 'company_code'))
                <table class="table-approve">
                    <tr>
                        <th colspan="4"><b>Transport Plan :</b></th>
                    </tr>
                    <tr class="head-row">
                        <td>Date</td>
                        <td>Information</td>
                        <td>Company Code</td>
                        <td>Amount</td>
                    </tr>

                    @foreach ($detailCA['detail_transport'] as $transport)
                        @if (!empty($transport['company_code']))
                            <tr style="text-align: center">
                                <td>{{ \Carbon\Carbon::parse($transport['tanggal'] ?? now())->format('d-M-y') }}</td>
                                <td>{{ $transport['keterangan'] ?? '' }}</td>
                                <td>{{ $transport['company_code'] }}</td>
                                <td>{{ formatCurrency($transport['nominal'] ?? 0) }}</td>
                            </tr>
                        @endif
                    @endforeach
                    <tr class="total-row">
                        <td colspan="3" class="head-row">Total</td>
                        <td>{{ formatCurrency(safeArraySumLampiran($detailCA['detail_transport'], 'nominal')) }}</td>
                    </tr>
                </table>
            @endif

            @if (hasValidData($declareCA, 'detail_transport', 'company_code'))
                <table class="table-approve">
                    <tr>
                        <th colspan="4"><b>Transport Plan Declaration :</b></th>
                    </tr>
                    <tr class="head-row">
                        <td>Date</td>
                        <td>Information</td>
                        <td>Company Code</td>
                        <td>Amount</td>
                    </tr>

                    @foreach ($declareCA['detail_transport'] as $transport)
                        @if (!empty($transport['company_code']))
                            <tr style="text-align: center">
                                <td>{{ \Carbon\Carbon::parse($transport['tanggal'] ?? now())->format('d-M-y') }}</td>
                                <td>{{ $transport['keterangan'] ?? '' }}</td>
                                <td>{{ $transport['company_code'] }}</td>
                                <td>{{ formatCurrency($transport['nominal'] ?? 0) }}</td>
                            </tr>
                        @endif
                    @endforeach
                    <tr class="total-row">
                        <td colspan="3" class="head-row">Total</td>
                        <td>{{ formatCurrency(safeArraySumLampiran($declareCA['detail_transport'], 'nominal')) }}</td>
                    </tr>
                </table>
            @endif

            @if (hasValidData($detailCA, 'detail_penginapan', 'company_code'))
                <table class="table-approve">
                    <tr>
                        <th colspan="6"><b>Accomodation Plan :</b></th>
                    </tr>
                    <tr class="head-row">
                        <td>Start Date</td>
                        <td>End Date</td>
                        <td>Hotel Name</td>
                        <td>Company Code</td>
                        <td>Total Nights</td>
                        <td>Amount</td>
                    </tr>

                    @foreach ($detailCA['detail_penginapan'] as $penginapan)
                        <tr style="text-align: center">
                            <td>{{ \Carbon\Carbon::parse($penginapan['start_date'] ?? now())->format('d-M-y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($penginapan['end_date'] ?? now())->format('d-M-y') }}</td>
                            <td>{{ $penginapan['hotel_name'] ?? '' }}</td>
                            <td>{{ $penginapan['company_code'] ?? '' }}</td>
                            <td>{{ $penginapan['total_days'] ?? 0 }} Nights</td>
                            <td>{{ formatCurrency($penginapan['nominal'] ?? 0) }}</td>
                        </tr>
                    @endforeach
                    <tr class="total-row">
                        <td colspan="4" class="head-row">Total</td>
                        <td>{{ safeArraySumLampiran($detailCA['detail_penginapan'], 'total_days') }} Nights</td>
                        <td>{{ formatCurrency(safeArraySumLampiran($detailCA['detail_penginapan'], 'nominal')) }}</td>
                    </tr>
                </table>
            @endif

            @if (hasValidData($declareCA, 'detail_penginapan', 'company_code'))
                <table class="table-approve">
                    <tr>
                        <th colspan="6"><b>Accomodation Plan Declaration :</b></th>
                    </tr>
                    <tr class="head-row">
                        <td>Start Date</td>
                        <td>End Date</td>
                        <td>Hotel Name</td>
                        <td>Company Code</td>
                        <td>Total Nights</td>
                        <td>Amount</td>
                    </tr>

                    @foreach ($declareCA['detail_penginapan'] as $penginapan)
                        <tr style="text-align: center">
                            <td>{{ \Carbon\Carbon::parse($penginapan['start_date'] ?? now())->format('d-M-y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($penginapan['end_date'] ?? now())->format('d-M-y') }}</td>
                            <td>{{ $penginapan['hotel_name'] ?? '' }}</td>
                            <td>{{ $penginapan['company_code'] ?? '' }}</td>
                            <td>{{ $penginapan['total_days'] ?? 0 }} Nights</td>
                            <td>{{ formatCurrency($penginapan['nominal'] ?? 0) }}</td>
                        </tr>
                    @endforeach
                    <tr class="total-row">
                        <td colspan="4" class="head-row">Total</td>
                        <td>{{ safeArraySumLampiran($declareCA['detail_penginapan'], 'total_days') }} Nights</td>
                        <td>{{ formatCurrency(safeArraySumLampiran($declareCA['detail_penginapan'], 'nominal')) }}</td>
                    </tr>
                </table>
            @endif

            @if (hasValidData($detailCA, 'detail_lainnya', 'keterangan'))
                <table class="table-approve">
                    <tr>
                        <th colspan="3"><b>Others Plan :</b></th>
                    </tr>
                    <tr class="head-row">
                        <td>Date</td>
                        <td>Information</td>
                        <td>Amount</td>
                    </tr>

                    @foreach ($detailCA['detail_lainnya'] as $lainnya)
                        <tr style="text-align: center">
                            <td>{{ \Carbon\Carbon::parse($lainnya['tanggal'] ?? now())->format('d-M-y') }}</td>
                            <td>{{ $lainnya['keterangan'] ?? '' }}</td>
                            <td>{{ formatCurrency($lainnya['nominal'] ?? 0) }}</td>
                        </tr>
                    @endforeach
                    <tr class="total-row">
                        <td colspan="2" class="head-row">Total</td>
                        <td>{{ formatCurrency(safeArraySumLampiran($detailCA['detail_lainnya'], 'nominal')) }}</td>
                    </tr>
                </table>
            @endif

            @if (hasValidData($declareCA, 'detail_lainnya', 'keterangan'))
                <table class="table-approve">
                    <tr>
                        <th colspan="3"><b>Others Plan Declaration :</b></th>
                    </tr>
                    <tr class="head-row">
                        <td>Date</td>
                        <td>Information</td>
                        <td>Amount</td>
                    </tr>

                    @foreach ($declareCA['detail_lainnya'] as $lainnya)
                        <tr style="text-align: center">
                            <td>{{ \Carbon\Carbon::parse($lainnya['tanggal'] ?? now())->format('d-M-y') }}</td>
                            <td>{{ $lainnya['keterangan'] ?? '' }}</td>
                            <td>{{ formatCurrency($lainnya['nominal'] ?? 0) }}</td>
                        </tr>
                    @endforeach
                    <tr class="total-row">
                        <td colspan="2" class="head-row">Total</td>
                        <td>{{ formatCurrency(safeArraySumLampiran($declareCA['detail_lainnya'], 'nominal')) }}</td>
                    </tr>
                </table>
            @endif
        </div>

        <table>
            <tr>
                <td class="label"><b>Total Plan Cash Advanced</b></td>
                <td class="colon">:</td>
                <td class="value">Rp. {{ number_format($transactions->total_ca, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="label"><b>Total Real Cash Advanced</b></td>
                <td class="colon">:</td>
                <td class="value">Rp. {{ number_format($transactions->total_real, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="label"><b>Balance</b></td>
                <td class="colon">:</td>
                <td class="value">Rp. {{ number_format($transactions->total_cost, 0, ',', '.') }}</td>
            </tr>
            @if ($transactions->total_cost > 0)
                <tr>
                    <td class="label"><b>Transfer To</b></td>
                    <td class="colon">:</td>
                    <td class="value">{{ $transactions->companies->contribution_level }} /
                        {{ $transactions->companies->account_number }}</td>
                </tr>
            @endif
        </table>
    </div>
    <footer>
    </footer>
</body>

</html>

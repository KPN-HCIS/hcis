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
    <h5 class="center">CASH ADVANCE (CA) TRANSACTION</h5>
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
            <td class="value">{{ $transactions->employee->bank_name }} - {{ $transactions->employee->bank_account_number }} - {{ $transactions->employee->bank_account_name }}</td>
        </tr>
        <tr>
            <td class="label">Division/Dept</td>
            <td class="colon">:</td>
            <td class="value">{{ $transactions->employee->unit }}</td>
        </tr>
        <tr>
            <td class="label">PT/Location</td>
            <td class="colon">:</td>
            <td class="value">{{ $transactions->employee->contribution_level_code }} / {{ $transactions->employee->office_area }}</td>
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
            <td class="value">{{ \Carbon\Carbon::parse($transactions->start_date)->format('d-M-y') }} to {{ \Carbon\Carbon::parse($transactions->end_date)->format('d-M-y') }} ({{ $transactions->total_days }} days)</td>
        </tr>
        {{-- <tr>
            <td class="label">End Date</td>
            <td class="colon">:</td>
            <td class="value">{{ \Carbon\Carbon::parse($transactions->end_date)->format('d-M-y') }}</td>
        </tr>
        <tr>
            <td class="label">Total Days</td>
            <td class="colon">:</td>
            <td class="value">{{ $transactions->total_days }} Hari</td>
        </tr> --}}
        <tr>
            <td class="label">Date CA Required</td>
            <td class="colon">:</td>
            <td class="value">{{ \Carbon\Carbon::parse($transactions->date_required)->format('d-M-y') }}</td>
        </tr>
        {{-- <tr>
            <td class="label">Estimated Declaration</td>
            <td class="colon">:</td>
            <td class="value">{{ \Carbon\Carbon::parse($transactions->declare_estimate)->format('d-M-y') }}</td>
        </tr> --}}
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
        $detailCA = json_decode($transactions->detail_ca, true);
        $declareCA = json_decode($transactions->declare_ca, true);
    @endphp

    @if ( $transactions->type_ca == 'dns' )
        @if (count($declareCA['detail_perdiem']) > 0 && !empty($declareCA['detail_perdiem'][0]['company_code']))
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

                @foreach($declareCA['detail_perdiem'] as $perdiem)
                <tr style="text-align: center">
                    <td>{{ \Carbon\Carbon::parse($perdiem['start_date'])->format('d-M-y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($perdiem['end_date'])->format('d-M-y') }}</td>
                    <td>
                        @if ($perdiem['location'] == 'Others')
                            Other ({{$perdiem['other_location']}})
                        @else
                            {{$perdiem['location']}}
                        @endif
                    </td>
                    <td>{{ $perdiem['company_code'] }}</td>
                    <td>{{ $perdiem['total_days'] }} Hari</td>
                </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="4" class="head-row">Total</td>
                    <td>
                        {{ array_sum(array_column($declareCA['detail_perdiem'], 'total_days')) }} Hari
                    </td>
                </tr>
            </table>
        @endif

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
                <td>
                    @if (array_sum(array_column($detailCA['detail_perdiem'], 'total_days')) <= 0)
                        -
                    @else
                        {{ array_sum(array_column($detailCA['detail_perdiem'], 'total_days')) }} Days
                    @endif
                </td>
                <td>
                    Rp. {{ number_format(array_sum(array_column($detailCA['detail_perdiem'], 'nominal')), 0, ',', '.') }}
                </td>
                <td>
                    @if (array_sum(array_column($declareCA['detail_perdiem'], 'total_days')) <= 0)
                        -
                    @else
                        {{ array_sum(array_column($declareCA['detail_perdiem'], 'total_days')) }} Days
                    @endif
                </td>
                <td>
                    Rp. {{ number_format(array_sum(array_column($declareCA['detail_perdiem'], 'nominal')), 0, ',', '.') }}
                </td>
            </tr>
            <tr>
                <td>Transport</td>
                <td>
                    -
                </td>
                <td>
                    Rp. {{ number_format(array_sum(array_column($detailCA['detail_transport'], 'nominal')), 0, ',', '.') }}
                </td>
                <td>
                    -
                </td>
                <td>
                    Rp. {{ number_format(array_sum(array_column($declareCA['detail_transport'], 'nominal')), 0, ',', '.') }}
                </td>
            </tr>
            <tr>
                <td>Accomodation</td>
                <td>
                    @if (array_sum(array_column($detailCA['detail_penginapan'], 'total_days')) <= 0)
                        -
                    @else
                        {{ array_sum(array_column($detailCA['detail_penginapan'], 'total_days')) }} Night
                    @endif
                </td>
                <td>
                    Rp. {{ number_format(array_sum(array_column($detailCA['detail_penginapan'], 'nominal')), 0, ',', '.') }}
                </td>
                <td>
                    @if (array_sum(array_column($declareCA['detail_penginapan'], 'total_days')) <= 0)
                        -
                    @else
                        {{ array_sum(array_column($declareCA['detail_penginapan'], 'total_days')) }} Night
                    @endif
                </td>
                <td>
                    Rp. {{ number_format(array_sum(array_column($declareCA['detail_penginapan'], 'nominal')), 0, ',', '.') }}
                </td>
            </tr>
            <tr>
                <td>Others</td>
                <td>
                    -
                </td>
                <td>
                    Rp. {{ number_format(array_sum(array_column($detailCA['detail_lainnya'], 'nominal')), 0, ',', '.') }}
                </td>
                <td>
                    -
                </td>
                <td>
                    Rp. {{ number_format(array_sum(array_column($declareCA['detail_lainnya'], 'nominal')), 0, ',', '.') }}
                </td>
            </tr>
            <tr>
                <td colspan="2">Total</td>
                <td>Rp. {{ number_format($transactions->total_ca), 0, ',', '.' }}</td>
                <td></td>
                <td>Rp. {{ number_format($transactions->total_real), 0, ',', '.' }}</td>
            </tr>
        </table>
    @elseif ( $transactions->type_ca == 'ndns' )
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
                <td class="label">Non Bussiness Trip</td>
                <td>
                    {{ $transactions->total_days }} Days
                </td>
                <td>
                    Rp. {{ number_format(array_sum(array_column($detailCA, 'nominal_nbt')), 0, ',', '.') }}
                </td>
                <td>
                    {{ $transactions->total_days }} Days
                </td>
                <td>
                    Rp. {{ number_format(array_sum(array_column($declareCA, 'nominal_nbt')), 0, ',', '.') }}
                </td>
            </tr>
            <tr>
                <td colspan="2">Total</td>
                <td>Rp. {{ number_format($transactions->total_ca), 0, ',', '.' }}</td>
                <td></td>
                <td>Rp. {{ number_format($transactions->total_real), 0, ',', '.' }}</td>
            </tr>
        </table>
    @elseif ( $transactions->type_ca == 'entr' )
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
                <td class="label">Detail Entertain</td>
                <td>
                    {{ $transactions->total_days }} Days
                </td>
                <td>
                    Rp. {{ number_format(array_sum(array_column($detailCA['detail_e'], 'nominal')), 0, ',', '.') }}
                </td>
                <td>
                    {{ $transactions->total_days }} Days
                </td>
                <td>
                    Rp. {{ number_format(array_sum(array_column($declareCA['detail_e'], 'nominal')), 0, ',', '.') }}
                </td>
            </tr>
            <tr>
                <td colspan="2">Total</td>
                <td>Rp. {{ number_format($transactions->total_ca), 0, ',', '.' }}</td>
                <td></td>
                <td>Rp. {{ number_format($transactions->total_real), 0, ',', '.' }}</td>
            </tr>
        </table>
    @endif

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

        <table border=0 style="width: 100%; font-size: 11px;">
            <tr>
                <td style="width: 100%;">
                    <table class="table-approve" style="text-align:center;">
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
                                    @if($role->approval_status =='Approved')
                                        <img src="{{ asset('images/approved_64.png')}}" alt="logo">
                                    @else
                                        <br><br><br><br><br>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                        <tr>
                            @foreach ($approval as $role)
                                <td>{{ $role->employee ? $role->employee->fullname : '' }}</td>
                            @endforeach
                        </tr>
                        <tr>
                            @foreach ($approval as $role)
                                <td>
                                    Date: <br> {{ $role->approved_at ? \Carbon\Carbon::parse($role->approved_at) : '' }}
                                </td>
                            @endforeach
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    <div>
        <h2 style="text-align: center">Lampiran Cash Advanced</h2>
        @if ( $transactions->type_ca == 'dns' )
            @if (count($detailCA['detail_perdiem']) > 0 && !empty($detailCA['detail_perdiem'][0]['company_code']))
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

                    @foreach($detailCA['detail_perdiem'] as $perdiem)
                    <tr style="text-align: center">
                        <td>{{ \Carbon\Carbon::parse($perdiem['start_date'])->format('d-M-y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($perdiem['end_date'])->format('d-M-y') }}</td>
                        <td>
                            @if ($perdiem['location'] == 'Others')
                                Other ({{$perdiem['other_location']}})
                            @else
                                {{$perdiem['location']}}
                            @endif
                        </td>
                        <td>{{ $perdiem['company_code'] }}</td>
                        <td>{{ $perdiem['total_days'] }} Hari</td>
                        <td>Rp. {{ number_format($perdiem['nominal'], 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                    <tr class="total-row">
                        <td colspan="4" class="head-row">Total</td>
                        <td>
                            {{ array_sum(array_column($detailCA['detail_perdiem'], 'total_days')) }} Hari
                        </td>
                        <td>
                            Rp. {{ number_format(array_sum(array_column($detailCA['detail_perdiem'], 'nominal')), 0, ',', '.') }}
                        </td>
                    </tr>
                </table>
            @endif

            @if (count($declareCA['detail_perdiem']) > 0 && !empty($declareCA['detail_perdiem'][0]['company_code']))
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

                    @foreach($declareCA['detail_perdiem'] as $perdiem)
                    <tr style="text-align: center">
                        <td>{{ \Carbon\Carbon::parse($perdiem['start_date'])->format('d-M-y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($perdiem['end_date'])->format('d-M-y') }}</td>
                        <td>
                            @if ($perdiem['location'] == 'Others')
                                Other ({{$perdiem['other_location']}})
                            @else
                                {{$perdiem['location']}}
                            @endif
                        </td>
                        <td>{{ $perdiem['company_code'] }}</td>
                        <td>{{ $perdiem['total_days'] }} Hari</td>
                        <td>Rp. {{ number_format($perdiem['nominal'], 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                    <tr class="total-row">
                        <td colspan="4" class="head-row">Total</td>
                        <td>
                            {{ array_sum(array_column($declareCA['detail_perdiem'], 'total_days')) }} Hari
                        </td>
                        <td>
                            Rp. {{ number_format(array_sum(array_column($declareCA['detail_perdiem'], 'nominal')), 0, ',', '.') }}
                        </td>
                    </tr>
                </table>
            @endif

            @if (count($detailCA['detail_transport']) > 0 && !empty($detailCA['detail_transport'][0]['company_code']))
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

                    @foreach($detailCA['detail_transport'] as $transport)
                    @if (!empty($transport['company_code']))
                    <tr style="text-align: center">
                        <td>{{ \Carbon\Carbon::parse($transport['tanggal'])->format('d-M-y') }}</td>
                        <td>{{ $transport['keterangan'] }}</td>
                        <td>{{ $transport['company_code'] }}</td>
                        <td>Rp. {{ number_format($transport['nominal'], 0, ',', '.') }}</td>
                    </tr>
                    @endif
                    @endforeach
                    <tr class="total-row">
                        <td colspan="3" class="head-row">Total</td>
                        <td>
                            Rp. {{ number_format(array_sum(array_column($detailCA['detail_transport'], 'nominal')), 0, ',', '.') }}
                        </td>
                    </tr>
                </table>
            @endif

            @if (count($declareCA['detail_transport']) > 0 && !empty($declareCA['detail_transport'][0]['company_code']))
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

                    @foreach($declareCA['detail_transport'] as $transport_dec)
                    @if (!empty($transport_dec['company_code']))
                    <tr style="text-align: center">
                        <td>{{ \Carbon\Carbon::parse($transport_dec['tanggal'])->format('d-M-y') }}</td>
                        <td>{{ $transport_dec['keterangan'] }}</td>
                        <td>{{ $transport_dec['company_code'] }}</td>
                        <td>Rp. {{ number_format($transport_dec['nominal'], 0, ',', '.') }}</td>
                    </tr>
                    @endif
                    @endforeach
                    <tr class="total-row">
                        <td colspan="3" class="head-row">Total</td>
                        <td>
                            Rp. {{ number_format(array_sum(array_column($declareCA['detail_transport'], 'nominal')), 0, ',', '.') }}
                        </td>
                    </tr>
                </table>
            @endif

            @if (count($detailCA['detail_penginapan']) > 0 && !empty($detailCA['detail_penginapan'][0]['company_code']))
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

                    @foreach($detailCA['detail_penginapan'] as $penginapan)
                        <tr style="text-align: center">
                            <td>{{ \Carbon\Carbon::parse($penginapan['start_date'])->format('d-M-y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($penginapan['end_date'])->format('d-M-y') }}</td>
                            <td>{{ $penginapan['hotel_name'] }}</td>
                            <td>{{ $penginapan['company_code'] }}</td>
                            <td>{{ $penginapan['total_days'] }} Hari</td>
                            <td>Rp. {{ number_format($penginapan['nominal'], 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                    <tr class="total-row">
                        <td colspan="4" class="head-row">Total</td>
                        <td>
                            {{ array_sum(array_column($detailCA['detail_penginapan'], 'total_days')) }} Hari
                        </td>
                        <td>
                            Rp. {{ number_format(array_sum(array_column($detailCA['detail_penginapan'], 'nominal')), 0, ',', '.') }}
                        </td>
                    </tr>
                </table>
            @endif

            @if (count($declareCA['detail_penginapan']) > 0 && !empty($declareCA['detail_penginapan'][0]['company_code']))
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

                    @foreach($declareCA['detail_penginapan'] as $penginapan_dec)
                        <tr style="text-align: center">
                            <td>{{ \Carbon\Carbon::parse($penginapan_dec['start_date'])->format('d-M-y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($penginapan_dec['end_date'])->format('d-M-y') }}</td>
                            <td>{{ $penginapan_dec['hotel_name'] }}</td>
                            <td>{{ $penginapan_dec['company_code'] }}</td>
                            <td>{{ $penginapan_dec['total_days'] }} Hari</td>
                            <td>Rp. {{ number_format($penginapan_dec['nominal'], 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                    <tr class="total-row">
                        <td colspan="4" class="head-row">Total</td>
                        <td>
                            {{ array_sum(array_column($declareCA['detail_penginapan'], 'total_days')) }} Hari
                        </td>
                        <td>
                            Rp. {{ number_format(array_sum(array_column($declareCA['detail_penginapan'], 'nominal')), 0, ',', '.') }}
                        </td>
                    </tr>
                </table>
            @endif

            @if (count($detailCA['detail_lainnya']) > 0 && !empty($detailCA['detail_lainnya'][0]['keterangan']))
                <table class="table-approve">
                    <tr>
                        <th colspan="3"><b>Others Plan :</b></th>
                    </tr>
                    <tr class="head-row">
                        <td>Date</td>
                        <td>Information</td>
                        <td>Amount</td>
                    </tr>

                    @foreach($detailCA['detail_lainnya'] as $lainnya)
                    <tr style="text-align: center">
                        <td>{{ \Carbon\Carbon::parse($lainnya['tanggal'])->format('d-M-y') }}</td>
                        <td>{{ $lainnya['keterangan'] }}</td>
                        <td>Rp. {{ number_format($lainnya['nominal'], 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                    <tr class="total-row">
                        <td colspan="2" class="head-row">Total</td>
                        <td>
                            Rp. {{ number_format(array_sum(array_column($detailCA['detail_lainnya'], 'nominal')), 0, ',', '.') }}
                        </td>
                    </tr>
                </table>
            @endif

            @if (count($declareCA['detail_lainnya']) > 0 && !empty($declareCA['detail_lainnya'][0]['keterangan']))
                <table class="table-approve">
                    <tr>
                        <th colspan="3"><b>Others Plan Declaration :</b></th>
                    </tr>
                    <tr class="head-row">
                        <td>Date</td>
                        <td>Information</td>
                        <td>Amount</td>
                    </tr>

                    @foreach($declareCA['detail_lainnya'] as $lainnya_dec)
                    <tr style="text-align: center">
                        <td>{{ \Carbon\Carbon::parse($lainnya_dec['tanggal'])->format('d-M-y') }}</td>
                        <td>{{ $lainnya_dec['keterangan'] }}</td>
                        <td>Rp. {{ number_format($lainnya_dec['nominal'], 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                    <tr class="total-row">
                        <td colspan="2" class="head-row">Total</td>
                        <td>
                            Rp. {{ number_format(array_sum(array_column($declareCA['detail_lainnya'], 'nominal')), 0, ',', '.') }}
                        </td>
                    </tr>
                </table>
            @endif

        @elseif ( $transactions->type_ca == 'ndns' )
            <table class="table-approve">
                <tr>
                    <th colspan="3"><b>Detail Non Bussiness Trip :</b></th>
                </tr>
                <tr class="head-row">
                    <td>Date</td>
                    <td>Information</td>
                    <td>Amount</td>
                </tr>

                @foreach($detailCA as $item)
                <tr style="text-align: center">
                    <td>{{ \Carbon\Carbon::parse($item['tanggal_nbt'])->format('d-M-y') }}</td>
                    <td>{{ $item['keterangan_nbt'] }}</td>
                    <td>Rp. {{ number_format($item['nominal_nbt'], 0, ',', '.') }}</td>
                </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="2" class="head-row">Total</td>
                    <td>
                        Rp. {{ number_format(array_sum(array_column($detailCA, 'nominal_nbt')), 0, ',', '.') }}
                    </td>
                </tr>
            </table>
            <table class="table-approve">
                <tr>
                    <th colspan="3"><b>Detail Non Bussiness Trip Declaration :</b></th>
                </tr>
                <tr class="head-row">
                    <td>Date</td>
                    <td>Information</td>
                    <td>Amount</td>
                </tr>

                @foreach($declareCA as $nbt)
                <tr style="text-align: center">
                    <td>{{ \Carbon\Carbon::parse($nbt['tanggal_nbt'])->format('d-M-y') }}</td>
                    <td>{{ $nbt['keterangan_nbt'] }}</td>
                    <td>Rp. {{ number_format($nbt['nominal_nbt'], 0, ',', '.') }}</td>
                </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="2" class="head-row">Total</td>
                    <td>
                        Rp. {{ number_format(array_sum(array_column($declareCA, 'nominal_nbt')), 0, ',', '.') }}
                    </td>
                </tr>
            </table>
        @elseif ( $transactions->type_ca == 'entr' )
            @if (count($detailCA['detail_e']) > 0 && !empty($detailCA['detail_e'][0]['type']))
                <table class="table-approve">
                    <tr>
                        <td colspan="3"><b>Detail Entertain :</b></td>
                    </tr>
                    <tr class="head-row">
                        <th>Type</th>
                        <th>Information</th>
                        <th>Amount</th>
                    </tr>

                    @foreach($detailCA['detail_e'] as $detail)
                    <tr style="text-align: center">
                        @php
                            $typeMap = [
                                'food' => 'Food/Beverages/Souvenir',
                                'transport' => 'Transport',
                                'accommodation' => 'Accommodation',
                                'gift' => 'Gift',
                                'fund' => 'Fund',
                            ];
                        @endphp
                        <td>{{ $typeMap[$detail['type']] ?? $detail['type'] }}</td>
                        <td>{{ $detail['fee_detail'] }}</td>
                        <td>Rp. {{ number_format($detail['nominal'], 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                    <tr class="total-row">
                        <td colspan="2" class="head-row">Total</td>
                        <td>
                            Rp. {{ number_format(array_sum(array_column($detailCA['detail_e'], 'nominal')), 0, ',', '.') }}
                        </td>
                    </tr>
                </table>
            @endif

            @if (count($declareCA['detail_e']) > 0 && !empty($declareCA['detail_e'][0]['type']))
                <table class="table-approve">
                    <tr>
                        <td colspan="3"><b>Detail Entertain Deklarasi :</b></td>
                    </tr>
                    <tr class="head-row">
                        <th>Type</th>
                        <th>Information</th>
                        <th>Amount</th>
                    </tr>

                    @foreach($declareCA['detail_e'] as $detail_dec)
                    <tr style="text-align: center">
                        @php
                            $typeMap = [
                                'food' => 'Food/Beverages/Souvenir',
                                'transport' => 'Transport',
                                'accommodation' => 'Accommodation',
                                'gift' => 'Gift',
                                'fund' => 'Fund',
                            ];
                        @endphp
                        <td>{{ $typeMap[$detail_dec['type']] ?? $detail_dec['type'] }}</td>
                        <td>{{ $detail_dec['fee_detail'] }}</td>
                        <td>Rp. {{ number_format($detail_dec['nominal'], 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                    <tr class="total-row">
                        <td colspan="2" class="head-row">Total</td>
                        <td>
                            Rp. {{ number_format(array_sum(array_column($detailCA['detail_e'], 'nominal')), 0, ',', '.') }}
                        </td>
                    </tr>
                </table>
            @endif

            @if (count($detailCA['relation_e']) > 0 && !empty($detailCA['relation_e'][0]['name']))
                <table class="table-approve">
                    <tr>
                        <td colspan="5"><b>Relation Entertain:</b></td>
                    </tr>
                    <tr class="head-row">
                        <th>Name</th>
                        <th>Position</th>
                        <th>Company</th>
                        <th>Purpose</th>
                        <th>Relation Type</th>
                    </tr>

                    @foreach($detailCA['relation_e'] as $relation)
                    <tr style="text-align: center">
                        <td>{{ $relation['name'] }}</td>
                        <td>{{ $relation['position'] }}</td>
                        <td>{{ $relation['company'] }}</td>
                        <td>{{ $relation['purpose'] }}</td>
                        <td>
                            @php
                                $relationTypes = [];
                                $typeMap = [
                                    'Food' => 'Food/Beverages/Souvenir',
                                    'Gift' => 'Gift',
                                    'Transport' => 'Transport',
                                    'Accommodation' => 'Accommodation',
                                    'Fund' => 'Fund',
                                ];

                                // Mengumpulkan semua tipe relasi yang berstatus true
                                foreach($relation['relation_type'] as $type => $status) {
                                    if ($status && isset($typeMap[$type])) {
                                        $relationTypes[] = $typeMap[$type]; // Menggunakan pemetaan untuk mendapatkan deskripsi
                                    }
                                }
                            @endphp

                            {{ implode(', ', $relationTypes) }} {{-- Menggabungkan tipe relasi yang relevan menjadi string --}}
                        </td>
                    </tr>
                    @endforeach
                </table>
            @endif

            @if (count($declareCA['relation_e']) > 0 && !empty($declareCA['relation_e'][0]['name']))
                <table class="table-approve">
                    <tr>
                        <td colspan="5"><b>Relation Entertain Declaration :</b></td>
                    </tr>
                    <tr class="head-row">
                        <th>Name</th>
                        <th>Position</th>
                        <th>Company</th>
                        <th>Purpose</th>
                        <th>Relation Type</th>
                    </tr>

                    @foreach($declareCA['relation_e'] as $relation_dec)
                    <tr style="text-align: center">
                        <td>{{ $relation_dec['name'] }}</td>
                        <td>{{ $relation_dec['position'] }}</td>
                        <td>{{ $relation_dec['company'] }}</td>
                        <td>{{ $relation_dec['purpose'] }}</td>
                        <td>
                            @php
                                $relationTypes = [];
                                $typeMap = [
                                    'Food' => 'Food/Beverages/Souvenir',
                                    'Gift' => 'Gift',
                                    'Transport' => 'Transport',
                                    'Accommodation' => 'Accommodation',
                                    'Fund' => 'Fund',
                                ];

                                // Mengumpulkan semua tipe relasi yang berstatus true
                                foreach($relation_dec['relation_type'] as $type => $status) {
                                    if ($status && isset($typeMap[$type])) {
                                        $relationTypes[] = $typeMap[$type]; // Menggunakan pemetaan untuk mendapatkan deskripsi
                                    }
                                }
                            @endphp

                            {{ implode(', ', $relationTypes) }} {{-- Menggabungkan tipe relasi yang relevan menjadi string --}}
                        </td>
                    </tr>
                    @endforeach
                </table>
            @endif
        @endif
    </div>

    <table>
        <tr>
            <td class="label"><b>Total Plan Cash Advanced</b></td>
            <td class="colon">:</td>
            <td class="value">Rp. {{ number_format($transactions->total_ca), 0, ',', '.' }}</td>
        </tr>
        <tr>
            <td class="label"><b>Total Real Cash Advanced</b></td>
            <td class="colon">:</td>
            <td class="value">Rp. {{ number_format($transactions->total_real), 0, ',', '.' }}</td>
        </tr>
        <tr>
            <td class="label"><b>Balance</b></td>
            <td class="colon">:</td>
            <td class="value">Rp. {{ number_format($transactions->total_cost), 0, ',', '.' }}</td>
        </tr>
        @if($transactions->total_cost>0)
        <tr>
            <td class="label"><b>Transfer To</b></td>
            <td class="colon">:</td>
            <td class="value">{{ $transactions->companies->contribution_level }} / {{ $transactions->companies->account_number }}</td>
        </tr>
        @endif
    </table>

    <footer>
        <script type="text/php">
            if (isset($pdf)) {
                $x = 400;
                $y = 810;
                $text = "Page {PAGE_NUM} of {PAGE_COUNT} Cash Advanced No. {{ $transactions->no_ca }}";
                $font = null;
                $size = 8;
                $color = array(0, 0, 0);
                $word_space = 0.0;
                $char_space = 0.0;
                $angle = 0.0;

                // Tambahkan pengecekan PAGE_COUNT
                if ($pdf->get_page_count() > 2) {
                    $pdf->page_text($x, $y, $text, $font, $size, $color, $word_space, $char_space, $angle);
                }
            }
        </script>
    </footer>
</body>

</html>

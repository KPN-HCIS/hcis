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
            <td class="value">{{ $transactions->employee->bank_details }}</td>
        </tr>
        <tr>
            <td class="label">Division/Dept</td>
            <td class="colon">:</td>
            <td class="value">{{ $transactions->employee->unit }}</td>
        </tr>
        <tr>
            <td class="label">PT/Location</td>
            <td class="colon">:</td>
            <td class="value">{{ $transactions->employee->company_name }}</td>
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
                {{ $transactions->contribution_level_code }}
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
            <td class="value">{{ \Carbon\Carbon::parse($transactions->start_date)->format('d-M-y') }}</td>
        </tr>
        <tr>
            <td class="label">End Date</td>
            <td class="colon">:</td>
            <td class="value">{{ \Carbon\Carbon::parse($transactions->end_date)->format('d-M-y') }}</td>
        </tr>
        <tr>
            <td class="label">Total Days</td>
            <td class="colon">:</td>
            <td class="value">{{ $transactions->total_days }} Hari</td>
        </tr>
        <tr>
            <td class="label">Date CA Required</td>
            <td class="colon">:</td>
            <td class="value">{{ \Carbon\Carbon::parse($transactions->date_required)->format('d-M-y') }}</td>
        </tr>
        <tr>
            <td class="label">Estimated Declaration</td>
            <td class="colon">:</td>
            <td class="value">{{ \Carbon\Carbon::parse($transactions->declare_estimate)->format('d-M-y') }}</td>
        </tr>
        <tr>
            <td class="label">Purpose</td>
            <td class="colon">:</td>
            <td class="value">{{ $transactions->ca_needs }}</td>
        </tr>
    </table>

    @if ($approval && count($approval) > 0)
        <table>
            <tr>
                <td colspan="3"><b>Approved By:</b></td>
            </tr>
            @foreach ($approval as $role)
                <tr>
                    <td class="label">{{ $role->role_name }}</td>
                    <td class="colon">:</td>
                    <td class="value">
                        @if ($role->approval_status == 'Approved')
                        {{ $role->employee ? $role->employee->fullname : 'Data tidak tersedia' }}
                        ({{ \Carbon\Carbon::parse($role->approved_at)->format('d-M-y') }})
                        @else
                        Not Approved
                        @endif
                    </td>
                </tr>
            @endforeach
        </table>
    @else
        <table>
            <tr>
                <td colspan="3"><b>Not submitted:</b></td>
            </tr>
        </table>
    @endif

    @php
        $detailCA = json_decode($transactions->detail_ca, true);
        $declareCA = json_decode($transactions->declare_ca, true);
    @endphp

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
                <td>{{ $perdiem['location'] == 'Others' ? $perdiem['other_location'] : $perdiem['location'] }}</td>
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
                <td>{{ $perdiem['location'] == 'Others' ? $perdiem['other_location'] : $perdiem['location'] }}</td>
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
                <td>Rp. {{ number_format($perdiem['nominal'], 0, ',', '.') }}</td>
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
                <td>Total Days</td>
                <td>Amount</td>
            </tr>

            @foreach($detailCA['detail_penginapan'] as $perdiem)
                <tr style="text-align: center">
                    <td>{{ \Carbon\Carbon::parse($perdiem['start_date'])->format('d-M-y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($perdiem['end_date'])->format('d-M-y') }}</td>
                    <td>{{ $perdiem['hotel_name'] }}</td>
                    <td>{{ $perdiem['company_code'] }}</td>
                    <td>{{ $perdiem['total_days'] }} Hari</td>
                    <td>Rp. {{ number_format($perdiem['nominal'], 0, ',', '.') }}</td>
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

            @foreach($detailCA['detail_lainnya'] as $perdiem)
            <tr style="text-align: center">
                <td>{{ \Carbon\Carbon::parse($perdiem['tanggal'])->format('d-M-y') }}</td>
                <td>{{ $perdiem['keterangan'] }}</td>
                <td>Rp. {{ number_format($perdiem['nominal'], 0, ',', '.') }}</td>
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

            @foreach($declareCA['detail_e'] as $detail)
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
            <tr class="total-row">
                <td colspan="4" class="head-row">Total Relation</td>
                <td>{{ count($detailCA['relation_e']) }}</td>
            </tr>
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

            @foreach($declareCA['relation_e'] as $relation)
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
            <tr class="total-row">
                <td colspan="4" class="head-row">Total Relation</td>
                <td>{{ count($detailCA['relation_e']) }}</td>
            </tr>
        </table>
        @endif
    @endif

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
            <td class="label"><b>Difference</b></td>
            <td class="colon">:</td>
            <td class="value">Rp. {{ number_format($transactions->total_cost), 0, ',', '.' }}</td>
        </tr>
    </table>

    @if ( $transactions->approval_sett == 'Approved' )
        <div class="flex-container">
            <table class="table-approve" style="width: 20%; margin-top:100px; text-align:center; margin-bottom:-220px">
                <tr>
                    <td>Submitted by</td>
                </tr>
                <tr>
                    <td>User</td>
                </tr>
                <tr>
                    <td><br><br><br><br></td>
                </tr>
                <tr>
                    <td>{{ $transactions->employee->fullname }}</td>
                </tr>
                <tr>
                    <td>Date...</td>
                </tr>
            </table>
            @if ($approval && count($approval) > 0)
                <table class="table-approve" style="margin-left:279px; margin-top:-160px; width: 60%; text-align:center;">
                    <tr>
                        <td colspan="{{ count($approval) }}">Verifikasi</td>
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
                            <td>{{ $role->employee ? $role->employee->fullname : 'Data tidak tersedia' }}</td>
                        @endforeach
                    </tr>
                    <tr>
                        @foreach ($approval as $role)
                            <td>
                                Date: <br> {{ $role->approved_at ? \Carbon\Carbon::parse($role->approved_at)->format('d-M-y') : 'Data tidak tersedia' }}
                            </td>
                        @endforeach
                    </tr>
                </table>
            @else
            <table>
                <tr>
                    <td colspan="3">Tidak ada data approval.</td>
                </tr>
            </table>
            @endif

            @if ($approval && count($approval) > 0)
            <table class="table-approve" style="width: 100%; text-align:center;">
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
                        <td>{{ $role->employee ? $role->employee->fullname : 'Data tidak tersedia' }}</td>
                    @endforeach
                </tr>
                <tr>
                    @foreach ($approval as $role)
                        <td style="text-align: left">
                            Date: <br>
                        </td>
                    @endforeach
                </tr>
            </table>
            @else
            <table>
                <tr>
                    <td colspan="3">Tidak ada data approval.</td>
                </tr>
            </table>
            @endif
        </div>
    @endif
</body>

</html>

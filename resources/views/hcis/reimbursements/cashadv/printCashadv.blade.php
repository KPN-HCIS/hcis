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
            <td class="label">PT</td>
            <td class="colon">:</td>
            <td class="value">
                {{ $transactions->destination == 'Others' ? $transactions->others_location : $transactions->destination }}
            </td>
        </tr>
        <tr>
            <td class="label">Lokasi</td>
            <td class="colon">:</td>
            <td class="value"></td>
        </tr>
        <tr>
            <td class="label">Mulai</td>
            <td class="colon">:</td>
            <td class="value">{{ $transactions->start_date }}</td>
        </tr>
        <tr>
            <td class="label">Berakhir</td>
            <td class="colon">:</td>
            <td class="value">{{ $transactions->end_date }}</td>
        </tr>
        <tr>
            <td class="label">Total</td>
            <td class="colon">:</td>
            <td class="value">{{ $transactions->total_days }}</td>
        </tr>
        <tr>
            <td class="label">Tanggal CA dibutuhkan</td>
            <td class="colon">:</td>
            <td class="value">{{ $transactions->date_required }}</td>
        </tr>
        <tr>
            <td class="label">Estimasi Deklarasi</td>
            <td class="colon">:</td>
            <td class="value">{{ $transactions->declare_estimate }}</td>
        </tr>
        <tr>
            <td class="label">Keperluan</td>
            <td class="colon">:</td>
            <td class="value">{{ $transactions->ca_needs }}</td>
        </tr>
    </table>

    @php
        $detailCA = json_decode($transactions->detail_ca, true);
    @endphp

    @if ( $transactions->type_ca == 'dns' )
        {{-- <table class="approve">
            <tr>
                <td colspan="6" style="background-color: #c6e0b4; text-align: center"><b>Rencana Perjalanan :</b></td>
            </tr>
            <tr>
                <th>Mulai</th>
                <th>Selesai</th>
                <th>Lokasi dinas</th>
                <th>Company Code</th>
                <th>Jl Hr</th>
                <th>Amount</th>
            </tr>

            @foreach($detailCA['detail_perdiem'] as $perdiem)
            <tr style="text-align: center">
                <td>{{ \Carbon\Carbon::parse($perdiem['start_date'])->format('d-M-y') }}</td>
                <td>{{ \Carbon\Carbon::parse($perdiem['end_date'])->format('d-M-y') }}</td>
                <td>{{ $perdiem['location'] == 'Others' ? $perdiem['company_code'] : $perdiem['location'] }}</td>
                <td>{{ $perdiem['company_code'] }}</td>
                <td>{{ $perdiem['total_days'] }}</td>
                <td> {{ number_format($perdiem['nominal'], 0, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr style="text-align: center">
                <td style="text-align: left" colspan="4">Total</td>
                <td>
                    {{ array_sum(array_column($detailCA['detail_perdiem'], 'total_days')) }}
                </td>
                <td>
                    {{ number_format(array_sum(array_column($detailCA['detail_perdiem'], 'nominal')), 0, ',', '.') }}
                </td>
            </tr>
        </table> --}}
        <table class="table-approve">
            <tr>
                <th colspan="6"><b>Rencana Perjalanan :</b></th>
            </tr>
            <tr class="head-row">
                <td>Mulai</td>
                <td>Selesai</td>
                <td>Lokasi dinas</td>
                <td>Company Code</td>
                <td>Jl Hr</td>
                <td>Amount</td>
            </tr>

            @foreach($detailCA['detail_perdiem'] as $perdiem)
            <tr style="text-align: center">
                <td>{{ \Carbon\Carbon::parse($perdiem['start_date'])->format('d-M-y') }}</td>
                <td>{{ \Carbon\Carbon::parse($perdiem['end_date'])->format('d-M-y') }}</td>
                <td>{{ $perdiem['location'] == 'Others' ? $perdiem['company_code'] : $perdiem['location'] }}</td>
                <td>{{ $perdiem['company_code'] }}</td>
                <td>{{ $perdiem['total_days'] }}</td>
                <td> {{ number_format($perdiem['nominal'], 0, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="4">Total</td>
                <td>
                    {{ array_sum(array_column($detailCA['detail_perdiem'], 'total_days')) }}
                </td>
                <td>
                    {{ number_format(array_sum(array_column($detailCA['detail_perdiem'], 'nominal')), 0, ',', '.') }}
                </td>
            </tr>
        </table>

        <table class="table-approve">
            <tr>
                <th colspan="4"><b>Rencana Transport :</b></th>
            </tr>
            <tr class="head-row">
                <td>Tanggal</td>
                <td>Keterangan</td>
                <td>Company Code</td>
                <td>Amount</td>
            </tr>

            @foreach($detailCA['detail_transport'] as $transport)
            <tr style="text-align: center">
                <td>{{ \Carbon\Carbon::parse($transport['tanggal'])->format('d-M-y') }}</td>
                <td>{{ $transport['keterangan'] }}</td>
                <td>{{ $transport['company_code'] }}</td>
                <td> {{ number_format($perdiem['nominal'], 0, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="3">Total</td>
                <td>
                    {{ number_format(array_sum(array_column($detailCA['detail_transport'], 'nominal')), 0, ',', '.') }}
                </td>
            </tr>
        </table>

        <table class="table-approve">
            <tr>
                <th colspan="6"><b>Rencana Penginapan :</b></th>
            </tr>
            <tr class="head-row">
                <td>Mulai</td>
                <td>Selesai</td>
                <td>Nama Hotel</td>
                <td>Company Code</td>
                <td>Jl Hr</td>
                <td>Amount</td>
            </tr>

            @foreach($detailCA['detail_penginapan'] as $perdiem)
            <tr style="text-align: center">
                <td>{{ \Carbon\Carbon::parse($perdiem['start_date'])->format('d-M-y') }}</td>
                <td>{{ \Carbon\Carbon::parse($perdiem['end_date'])->format('d-M-y') }}</td>
                <td>{{ $perdiem['hotel_name'] }}</td>
                <td>{{ $perdiem['company_code'] }}</td>
                <td>{{ $perdiem['total_days'] }}</td>
                <td> {{ number_format($perdiem['nominal'], 0, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="4">Total</td>
                <td>
                    {{ array_sum(array_column($detailCA['detail_penginapan'], 'total_days')) }}
                </td>
                <td>
                    {{ number_format(array_sum(array_column($detailCA['detail_penginapan'], 'nominal')), 0, ',', '.') }}
                </td>
            </tr>
        </table>

        <table class="table-approve">
            <tr>
                <th colspan="3"><b>Rencana Lainnya :</b></th>
            </tr>
            <tr class="head-row">
                <td>Tgl</td>
                <td>Keterangan</td>
                <td>Amount</td>
            </tr>

            @foreach($detailCA['detail_lainnya'] as $perdiem)
            <tr style="text-align: center">
                <td>{{ \Carbon\Carbon::parse($perdiem['tanggal'])->format('d-M-y') }}</td>
                <td>{{ $perdiem['keterangan'] }}</td>
                <td> {{ number_format($perdiem['nominal'], 0, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="2">Total</td>
                <td>
                    {{ number_format(array_sum(array_column($detailCA['detail_lainnya'], 'nominal')), 0, ',', '.') }}
                </td>
            </tr>
        </table>
    @elseif ( $transactions->type_ca == 'ndns' )
        <table class="table-approve">
            <tr>
                <th colspan="3"><b>Detail Entertain :</b></th>
            </tr>
            <tr class="head-row">
                <td>Mulai</td>
                <td>Selesai</td>
                <td>Lokasi dinas</td>
            </tr>

            @foreach($detailCA as $item)
            <tr style="text-align: center">
                <td>{{ \Carbon\Carbon::parse($item['tanggal_nbt'])->format('d-M-y') }}</td>
                <td>{{ $item['keterangan_nbt'] }}</td>
                <td> {{ number_format($item['nominal_nbt'], 0, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="2" class="head-row">Total</td>
                <td>
                    {{ number_format(array_sum(array_column($detailCA, 'nominal_nbt')), 0, ',', '.') }}
                </td>
            </tr>
        </table>
    @elseif ( $transactions->type_ca == 'entr' )
        <table class="table-approve">
            <tr>
                <td colspan="3"><b>Detail Entertain :</b></td>
            </tr>
            <tr class="head-row">
                <th>Type</th>
                <th>Keterangan</th>
                <th>Nominal</th>
            </tr>

            @foreach($detailCA['detail_e'] as $detail)
            <tr style="text-align: center">
                @php
                    $typeMap = [
                        'food_cost' => 'Food/Beverages/Souvenir',
                        'transport' => 'Transport',
                        'accommodation' => 'Accommodation',
                        'gift' => 'Gift',
                        'fund' => 'Fund',
                    ];
                @endphp
                <td>{{ $typeMap[$detail['type']] ?? $detail['type'] }}</td>
                <td>{{ $detail['fee_detail'] }}</td>
                <td>{{ number_format($detail['nominal'], 0, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="2" class="head-row">Total</td>
                <td>
                    {{ number_format(array_sum(array_column($detailCA['detail_e'], 'nominal')), 0, ',', '.') }}
                </td>
            </tr>
        </table>

        <table class="table-approve">
            <tr>
                <td colspan="5"><b>Relation Entertain :</b></td>
            </tr>
            <tr class="head-row">
                <th>Nama</th>
                <th>Posisi</th>
                <th>Perusahaan</th>
                <th>Tujuan</th>
                <th>Tipe Relasi</th>
            </tr>

            @foreach($detailCA['relation_e'] as $relation)
            <tr style="text-align: center">
                @php
                    $typeMap = [
                        'food_cost' => 'Food/Beverages/Souvenir',
                        'transport' => 'Transport',
                        'accommodation' => 'Accommodation',
                        'gift' => 'Gift',
                        'fund' => 'Fund',
                    ];
                @endphp
                <td>{{ $relation['name'] }}</td>
                <td>{{ $relation['position'] }}</td>
                <td>{{ $relation['company'] }}</td>
                <td>{{ $relation['purpose'] }}</td>
                <td>
                    @foreach($relation['relation_type'] as $type)
                        {{ $typeMap[$type] ?? $type }} @if (!$loop->last), @endif
                    @endforeach
                </td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="4" class="head-row">Total Relation</td>
                <td>{{ count($detailCA['relation_e']) }}</td>
            </tr>
        </table>
    @endif
</body>

</html>

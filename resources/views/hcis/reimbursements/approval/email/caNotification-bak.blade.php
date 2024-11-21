<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cash Advanced Notification</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
        }

        .header {
            width: 100%;
            height: auto;
            text-align: center;
        }

        .header img {
            height: auto;
            margin-bottom: 20px;
            width: 20%; /* Default untuk desktop */
        }

        /* Media query untuk mobile devices */
        @media screen and (max-width: 768px) {
            .header img {
                width: 50%; /* Ukuran untuk mobile */
            }
        }

        h5 {
            font-size: 13px;
            margin: 0;
            padding: 0;
            margin-bottom: 10px;
        }

        p {
            margin: 4px 0;
            padding: 2px;
        }

        .table-approve {
            border-collapse: collapse;
            width: 100%;
            margin-top: 8px;
            font-size: 10px;
        }

        .table-approve th,
        .table-approve td {
            border: 1px solid #ddd;
            padding: 4px;
            text-align: left;
            vertical-align: top;
        }

        .table-approve .head-row {
            font-weight: bold;
            background-color: #f5f5f5;
        }

        .table-approve .head-row td {
            text-align: center;
        }

        .table-approve th {
            background-color: #ab2f2b;
            color: #ffffff;
            font-size: 10px;
            font-weight: bold;
            white-space: nowrap;
            text-align: center;
        }

        .table-wrapper {
            overflow-x: auto;
            max-width: 100%;
        }

        .col-small {
            width: 40px;
            white-space: nowrap;
        }

        .col-medium {
            width: 80px;
            white-space: nowrap;
        }

        .col-amount {
            width: 70px;
            text-align: right;
        }

        .col-date {
            width: 70px;
            white-space: nowrap;
        }
    </style>
</head>
    <body>
        <div class="header">
            <img src="https://stag-corp.kpndownstream.com/images/logo/logo-kpn-red.png" alt="Kop Surat">
        </div>
        <h5>Reimburse Cash Advanced Notification</h5>
        @if ($nextApproval)
        {{-- {{dd($nextApproval)}} --}}
            <p>Kepada Yth : Bapak/Ibu <strong>{{ $nextApproval->employee->fullname }}</strong></p>
            <br>
        @endif

        <p>{{ $textNotification }}</p>

        @php
            $detailCA = json_decode($caTransaction->detail_ca, true);
            $declareCA = json_decode($caTransaction->declare_ca, true);
        @endphp

        @if ($caTransaction)
            <table>
                <tr>
                    <td class="label">No Dokumen</td>
                    <td class="colon">:</td>
                    <td class="value">{{ $caTransaction->no_ca }}</td>
                </tr>
                <tr>
                    <td class="label">Name</td>
                    <td class="colon">:</td>
                    <td class="value">{{ $caTransaction->employee->fullname }}</td>
                </tr>
                <tr>
                    <td class="label">Start Date</td>
                    <td class="colon">:</td>
                    <td class="value">{{ $caTransaction->start_date }}</td>
                </tr>
                <tr>
                    <td class="label">End Date</td>
                    <td class="colon">:</td>
                    <td class="value">{{ $caTransaction->end_date }}</td>
                </tr>
                <tr>
                    <td class="label">Destination</td>
                    <td class="colon">:</td>
                    <td class="value">{{ $caTransaction->destination == 'Others' ? $caTransaction->others_location : $caTransaction->destination }}</td>
                </tr>
                <tr>
                    <td class="label">Purpose</td>
                    <td class="colon">:</td>
                    <td class="value">{{ $caTransaction->ca_needs }}</td>
                </tr>
                <tr>
                    <td class="label">PT</td>
                    <td class="colon">:</td>
                    <td class="value">{{ $caTransaction->companies->contribution_level }} ({{ $caTransaction->contribution_level_code }})</td>
                </tr>
                <tr>
                    <td class="label">CA Type</td>
                    <td class="colon">:</td>
                    <td class="value">
                        @if($caTransaction->type_ca === 'entr')
                            Entertaiment
                        @elseif($caTransaction->type_ca === 'dns')
                            Business Trip
                        @elseif($caTransaction->type_ca === 'ndns')
                            Non Business Trip
                        @else
                            Unknown Type
                        @endif
                    </td>
                </tr>
            </table>

            @if($caTransaction->type_ca === 'entr')
                @if ($declaration == "Declaration")
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
                                {{ $caTransaction->total_days }} Days
                            </td>
                            <td>
                                Rp. {{ number_format(array_sum(array_column($detailCA['detail_e'], 'nominal')), 0, ',', '.') }}
                            </td>
                            <td>
                                {{ $caTransaction->total_days }} Days
                            </td>
                            <td>
                                Rp. {{ number_format(array_sum(array_column($declareCA['detail_e'], 'nominal')), 0, ',', '.') }}
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">Total</td>
                            <td>Rp. {{ number_format($caTransaction->total_ca, 0, ',', '.') }}</td>
                            <td></td>
                            <td>Rp. {{ number_format($caTransaction->total_real, 0, ',', '.' )}}</td>
                        </tr>
                    </table>
                @else
                    <table class="table-approve" style="width: 70%;">
                        <tr>
                            <th colspan="3"><b>Detail Cash Advanced :</b></th>
                        </tr>
                        <tr class="head-row">
                            <td rowspan="2" style="text-align: center;">Types of Down Payments</td>
                            <td colspan="2">Estimate</td>
                        </tr>
                        <tr class="head-row">
                            <td>Total Days</td>
                            <td>Amount</td>
                        </tr>
                        <tr>
                            <td class="label">Detail Entertain</td>
                            <td>
                                {{ $caTransaction->total_days }} Days
                            </td>
                            <td>
                                Rp. {{ number_format(array_sum(array_column($detailCA['detail_e'], 'nominal')), 0, ',', '.') }}
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">Total</td>
                            <td>Rp. {{ number_format($caTransaction->total_ca, 0, ',', '.') }}</td>
                        </tr>
                    </table>
                @endif
            @elseif($caTransaction->type_ca === 'dns')
                @if ($declaration == "Declaration")
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
                            <td>Rp. {{ number_format($caTransaction->total_ca, 0, ',', '.') }}</td>
                            <td></td>
                            <td>Rp. {{ number_format($caTransaction->total_real, 0, ',', '.') }}</td>
                        </tr>
                    </table>
                @else
                    @if (count($detailCA['detail_perdiem']) > 0 && !empty($detailCA['detail_perdiem'][0]['company_code']))
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
                            </tr>
                            @endforeach
                            <tr class="total-row">
                                <td colspan="4" class="head-row">Total</td>
                                <td>
                                    {{ array_sum(array_column($detailCA['detail_perdiem'], 'total_days')) }} Hari
                                </td>
                            </tr>
                        </table>
                    @endif
                    <table class="table-approve" style="width: 70%;">
                        <tr>
                            <th colspan="3"><b>Detail Cash Advanced :</b></th>
                        </tr>
                        <tr class="head-row">
                            <td rowspan="2" style="text-align: center;">Types of Down Payments</td>
                            <td colspan="2">Estimate</td>
                        </tr>
                        <tr class="head-row">
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
                        </tr>
                        <tr>
                            <td>Transport</td>
                            <td>
                                -
                            </td>
                            <td>
                                Rp. {{ number_format(array_sum(array_column($detailCA['detail_transport'], 'nominal')), 0, ',', '.') }}
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
                        </tr>
                        <tr>
                            <td>Others</td>
                            <td>
                                -
                            </td>
                            <td>
                                Rp. {{ number_format(array_sum(array_column($detailCA['detail_lainnya'], 'nominal')), 0, ',', '.') }}
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">Total</td>
                            <td>Rp. {{ number_format($caTransaction->total_ca, 0, ',', '.') }}</td>
                        </tr>
                    </table>
                @endif
            @elseif($caTransaction->type_ca === 'ndns')
                @if ($declaration == "Declaration")
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
                                {{ $caTransaction->total_days }} Days
                            </td>
                            <td>
                                Rp. {{ number_format(array_sum(array_column($detailCA, 'nominal_nbt')), 0, ',', '.') }}
                            </td>
                            <td>
                                {{ $caTransaction->total_days }} Days
                            </td>
                            <td>
                                Rp. {{ number_format(array_sum(array_column($declareCA, 'nominal_nbt')), 0, ',', '.') }}
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">Total</td>
                            <td>Rp. {{ number_format($caTransaction->total_ca, 0, ',', '.') }}</td>
                            <td></td>
                            <td>Rp. {{ number_format($caTransaction->total_real, 0, ',', '.') }}</td>
                        </tr>
                    </table>
                @else
                    <table class="table-approve" style="width: 70%;">
                        <tr>
                            <th colspan="3"><b>Detail Cash Advanced :</b></th>
                        </tr>
                        <tr class="head-row">
                            <td rowspan="2" style="text-align: center;">Types of Down Payments</td>
                            <td colspan="2">Estimate</td>
                        </tr>
                        <tr class="head-row">
                            <td>Total Days</td>
                            <td>Amount</td>
                        </tr>
                        <tr>
                            <td class="label">Non Bussiness Trip</td>
                            <td>
                                {{ $caTransaction->total_days }} Days
                            </td>
                            <td>
                                Rp. {{ number_format(array_sum(array_column($detailCA, 'nominal_nbt')), 0, ',', '.') }}
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">Total</td>
                            <td>Rp. {{ number_format($caTransaction->total_ca, 0, ',', '.') }}</td>
                        </tr>
                    </table>
                @endif
            @endif
            <br>

            @if ($linkApprove)
                <p>Untuk Menyetujui atau Menolak Perjalanan Dinas tersebut dapat memilih link berikut : <a href="{{ $linkApprove }}"> Approve </a>/<a href="{{ $linkReject }}"> Reject </a></p>
            @else
                <p>Cash Advanced yang telah di Approve bisa di lihat pada lampiran</p>
            @endif
            <br>

            <p>Apabila ada pertanyaan, dapat menghubungi masing-masing bisnis unit </p>
            <br>
            <p><strong>----------------</strong></p>
            <p>Human Capital - KPN Corp</p>
        @endif
    </body>
</html>

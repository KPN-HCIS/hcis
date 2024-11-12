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
            {{-- {{dd(substr($base64Image, 0, 100));}} --}}
            {{-- <img src="{{ $base64Image }}" alt="Kop Surat" style="max-width: 100%; height: auto;">  
            <img src="{{ asset('images/kop.jpg') }}" alt="Kop Surat" style="max-width: 100%; height: auto;">   --}}
        </div>

        @if ($nextApproval)
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
                        @if($caTransaction->type_ca === 'ent')  
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
            
            @if($caTransaction->type_ca === 'ent')  
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

            <p>Untuk Menyetujui atau Menolak Perjalanan Dinas tersebut dapat memilih link berikut : <a href="">Approve</a>/<a href="">Reject</a></p>
            <br>

            <p>Apabila ada pertanyaan, dapat menghubungi : </p>
            <p>Pak Hifni : muhammad.hifni@kpnplantation.com</p>
            <p>Pak Hendro : hendro.fiktor@kpnplantation.com</p>
            <br>
            <p><strong>----------------</strong></p>
            <p>Human Capital - KPN Corp</p>
        @endif
    </body>
</html>

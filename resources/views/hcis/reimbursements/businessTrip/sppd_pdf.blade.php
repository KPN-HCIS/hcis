<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>SPPD</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('/public/images/favicon.ico') }}">
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

        h4 {
            padding-left: 2px;
            padding-top: 4px;
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
            padding: 2px;
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

        .bottom-table {
            margin-top: 10px;
        }

        .bottom-table th,
        .bottom-table td {
            padding: 10px;
            text-align: left !important;
            width: 70px;
        }

        .bottom-table th {
            background-color: #dddddd;
        }

        .bottom-table tr:nth-child(even) {
            background-color: #d3d3d3;
        }

        .bottom-table tr:nth-child(odd) {
            background-color: #ededed;
        }

        table .ttd {
            height: 64px;
        }
    </style>
</head>

<body>
    <div class="header">
        <img src="{{ public_path('images/kop.jpg') }}" alt="Kop Surat">
    </div>
    <h5 class="center">SURAT PERINTAH PERJALANAN DINAS</h5>
    <h5 class="center">No. {{ $sppd->no_sppd }}</h5>

    <table>
        <tr>
            <td colspan="3"><b>Ditugaskan Kepada :</b></td>
        </tr>
        <tr>
            <td class="label">Nama</td>
            <td class="colon">:</td>
            <td class="value">{{ $sppd->employee->fullname }}</td>
        </tr>
        <tr>
            <td class="label">NIK</td>
            <td class="colon">:</td>
            <td class="value">{{ $sppd->employee->employee_id }}</td>
        </tr>
        <tr>
            <td class="label">Email</td>
            <td class="colon">:</td>
            <td class="value">{{ $sppd->employee->email }}</td>
        </tr>
        <tr>
            <td class="label">Divisi</td>
            <td class="colon">:</td>
            <td class="value">{{ $sppd->divisi }}</td>
        </tr>
        <tr>
            <td class="label">PT</td>
            <td class="colon">:</td>
            <td class="value">{{ $sppd->employee->company_name }}</td>
        </tr>
        <tr>
            <td class="label">Cost Center</td>
            <td class="colon">:</td>
            <td class="value">{{ $sppd->cost_center ?? '0' }}</td>
        </tr>
    </table>

    <table>
        <tr>
            <td colspan="3"><b>Untuk Melakukan Perjalanan Dinas ke :</b></td>
        </tr>
        <tr>
            <td class="label">Tujuan</td>
            <td class="colon">:</td>
            <td class="value">{{ $sppd->tujuan }}</td>
        </tr>
        <tr>
            <td class="label">Keperluan</td>
            <td class="colon">:</td>
            <td class="value">{{ $sppd->keperluan }}</td>
        </tr>

        @php
            use Carbon\Carbon;
            Carbon::setLocale('id');
        @endphp
        <tr>
            <td class="label">Dari Tanggal</td>
            <td class="colon">:</td>
            <td class="value">{{ Carbon::parse($sppd->mulai)->format('d F Y') }}</td>
        </tr>
        <tr>
            <td class="label">Sampai dengan tanggal</td>
            <td class="colon">:</td>
            <td class="value">{{ Carbon::parse($sppd->kembali)->format('d F Y') }}</td>
        </tr>
    </table>

    <table>
        <tr>
            <td colspan="3"><b>Disetujui Oleh :</b></td>
        </tr>
        <tr>
            <td class="label">Nama Atasan 1</td>
            <td class="colon">:</td>
            <td class="value">{{ $sppd->atasan_1 }}</td>
        </tr>
        <tr>
            <td class="label">Tanggal</td>
            <td class="colon">:</td>
            <td class="value">{{ $sppd->tanggal_atasan_1 }}</td>
        </tr>
        <tr>
            <td class="label">Nama Atasan 2</td>
            <td class="colon">:</td>
            <td class="value">{{ $sppd->atasan_2 }}</td>
        </tr>
        <tr>
            <td class="label">Tanggal</td>
            <td class="colon">:</td>
            <td class="value">{{ $sppd->tanggal_atasan_2 }}</td>
        </tr>
    </table>
    <p><b><i>Catatan : Persetujuan ini sudah disetujui secara Elektronik dan tidak memerlukan tanda tangan</b></i></p>

    <h4>Diisi oleh Pejabat di Tempat tujuan</h4>
    <table class="bottom-table">
        <tr>
            <td>Tanggal Tiba</td>
            <td>1.</td>
            <td>2.</td>
            <td>3.</td>
            <td>4.</td>
        </tr>
        <tr>
            <td>Tanggal Kembali</td>
            <td>1.</td>
            <td>2.</td>
            <td>3.</td>
            <td>4.</td>
        </tr>
        <tr>
            <td class="ttd">Tanda tangan & Cap</td>
            <td>1.</td>
            <td>2.</td>
            <td>3.</td>
            <td>4.</td>
        </tr>
    </table>
</body>

</html>

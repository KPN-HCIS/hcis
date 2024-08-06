<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Business Trip</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
        }
        h5, p {
            margin: 0;
            padding: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
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
    </style>
</head>
<body>
    <h5 class="center">SURAT PERINTAH PERJALANAN DINAS</h5>
    <h5 class="center">No. {{ $ca ? $ca->no_sppd : 'N/A' }}</h5>

    <table>
        <tr>
            <td colspan="3"><b>Ditugaskan Kepada :</b></td>
        </tr>
        <tr>
            <td class="label">Nama</td>
            <td class="colon">:</td>
            <td class="value">{{ $data->nama }}</td>
        </tr>
        <tr>
            <td class="label">No. CA</td>
            <td class="colon">:</td>
            <td class="value">{{ $ca->no_ca }}</td>
        </tr>
        <tr>
            <td class="label">Email</td>
            <td class="colon">:</td>
            <td class="value">{{ $ca->email }}</td>
        </tr>
        <tr>
            <td class="label">Divisi</td>
            <td class="colon">:</td>
            <td class="value">{{ $ca->divisi }}</td>
        </tr>
        <tr>
            <td class="label">Cost Center</td>
            <td class="colon">:</td>
            <td class="value">{{ $ca->cost_center }}</td>
        </tr>
    </table>

    <table>
        <tr>
            <td colspan="3"><b>Untuk Melakukan Perjalanan Dinas ke :</b></td>
        </tr>
        <tr>
            <td class="label">Tujuan</td>
            <td class="colon">:</td>
            <td class="value">{{ $ca->tujuan }}</td>
        </tr>
        <tr>
            <td class="label">Keperluan</td>
            <td class="colon">:</td>
            <td class="value">{{ $ca->keperluan }}</td>
        </tr>
        <tr>
            <td class="label">Dari Tanggal</td>
            <td class="colon">:</td>
            <td class="value">{{ $ca->start_date }}</td>
        </tr>
        <tr>
            <td class="label">Sampai dengan tanggal</td>
            <td class="colon">:</td>
            <td class="value">{{ $ca->end_date }}</td>
        </tr>
    </table>

    <table>
        <tr>
            <td colspan="3"><b>Disetujui Oleh :</b></td>
        </tr>
        <tr>
            <td class="label">Nama Atasan 1</td>
            <td class="colon">:</td>
            <td class="value">{{ $ca->atasan_1 }}</td>
        </tr>
        <tr>
            <td class="label">Tanggal</td>
            <td class="colon">:</td>
            <td class="value">{{ $ca->tanggal_atasan_1 }}</td>
        </tr>
        <tr>
            <td class="label">Nama Atasan 2</td>
            <td class="colon">:</td>
            <td class="value">{{ $ca->atasan_2 }}</td>
        </tr>
        <tr>
            <td class="label">Tanggal</td>
            <td class="colon">:</td>
            <td class="value">{{ $ca->tanggal_atasan_2 }}</td>
        </tr>
    </table>
</body>
</html>

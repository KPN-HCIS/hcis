<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Taxi Voucher Details</title>
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
            margin-bottom: 10px;
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
    </style>
</head>

<body>
    <div class="header">
        <img src="{{ public_path('images/kop.jpg') }}" alt="Kop Surat">
    </div>
    <h5 class="center">TAXI VOUCHER DETAILS</h5>
    <h5 class="center">No. {{ $hotel->no_htl }}</h5>

    <table>
        <tr>
            <td colspan="3"><b>Voucher Information:</b></td>
        </tr>
        <tr>
            <td class="label">SPPD Number</td>
            <td class="colon">:</td>
            <td class="value">{{ $hotel->no_sppd }}</td>
        </tr>
        <tr>
            <td class="label">Unit</td>
            <td class="colon">:</td>
            <td class="value">{{ $hotel->unit }}</td>
        </tr>
    </table>

    <table>
        <tr>
            <td colspan="3"><b>Hotel Details:</b></td>
        </tr>
        <tr>
            <td class="label">Hotel Name</td>
            <td class="colon">:</td>
            <td class="value">{{ $hotel->nama_htl }}</td>
        </tr>
        <tr>
            <td class="label">Hotel Location</td>
            <td class="colon">:</td>
            <td class="value">{{ $hotel->lokasi_htl }}</td>
        </tr>
        <tr>
            <td class="label">Total Room</td>
            <td class="colon">:</td>
            <td class="value">{{ $hotel->jmlkmr_htl }} Kamar</td>
        </tr>
        <tr>
            <td class="label">Bed Type</td>
            <td class="colon">:</td>
            <td class="value">{{ $hotel->bed_htl }}</td>
        </tr>
    </table>

    <table>
        <tr>
            <td colspan="3"><b>Others:</b></td>
        </tr>
        <tr>
            <td class="label">Check In Date:</td>
            <td class="colon">:</td>
            <td class="value">{{ $hotel->tgl_masuk_htl }}</td>
        </tr>
        <tr>
            <td class="label">Check Out Date:</td>
            <td class="colon">:</td>
            <td class="value">{{ $hotel->tgl_keluar_htl }}</td>
        </tr>
        <tr>
            <td class="label">Total Days:</td>
            <td class="colon">:</td>
            <td class="value">{{ $hotel->total_hari }}</td>
        </tr>
    </table>
</body>

</html>

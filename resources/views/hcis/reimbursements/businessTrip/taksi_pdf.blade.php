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
    </style>
</head>

<body>
    <div class="header">
        <img src="{{ public_path('images/kop.jpg') }}" alt="Kop Surat">
    </div>
    <h5 class="center">TAXI VOUCHER DETAILS</h5>
    <h5 class="center">No. {{ $taksi->no_vt }}</h5>

    <table>
        <tr>
            <td colspan="3"><b>Voucher Information:</b></td>
        </tr>
        <tr>
            <td class="label">Voucher Number</td>
            <td class="colon">:</td>
            <td class="value">{{ $taksi->no_vt }}</td>
        </tr>
        <tr>
            <td class="label">SPPD Number</td>
            <td class="colon">:</td>
            <td class="value">{{ $taksi->no_sppd }}</td>
        </tr>
        <tr>
            <td class="label">Unit</td>
            <td class="colon">:</td>
            <td class="value">{{ $taksi->unit }}</td>
        </tr>
    </table>

    <table>
        <tr>
            <td colspan="3"><b>Financial Details:</b></td>
        </tr>
        <tr>
            <td class="label">Nominal Value</td>
            <td class="colon">:</td>
            <td class="value">{{ $taksi->nominal_vt }}</td>
        </tr>
    </table>

    <table>
        <tr>
            <td colspan="3"><b>Keeper Information:</b></td>
        </tr>
        <tr>
            <td class="label">Keeper</td>
            <td class="colon">:</td>
            <td class="value">{{ $taksi->keeper_vt }}</td>
        </tr>
    </table>
</body>

</html>

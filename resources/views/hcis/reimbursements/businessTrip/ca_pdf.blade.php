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
    </style>
</head>

<body>
    <div class="header">
        <img src="{{ public_path('images/kop.jpg') }}" alt="Kop Surat">
    </div>
    <h5 class="center">CASH ADVANCE (CA) TRANSACTION</h5>
    <h5 class="center">No. {{ $ca->no_ca }}</h5>

    <table>
        <tr>
            <td colspan="3"><b>Transaction Details:</b></td>
        </tr>
        <tr>
            <td class="label">Type CA</td>
            <td class="colon">:</td>
            <td class="value">{{ $ca->type_ca }}</td>
        </tr>
        <tr>
            <td class="label">No. CA</td>
            <td class="colon">:</td>
            <td class="value">{{ $ca->no_ca }}</td>
        </tr>
        <tr>
            <td class="label">No. SPPD</td>
            <td class="colon">:</td>
            <td class="value">{{ $ca->no_sppd }}</td>
        </tr>
        <tr>
            <td class="label">Unit</td>
            <td class="colon">:</td>
            <td class="value">{{ $ca->unit }}</td>
        </tr>
        <tr>
            <td class="label">Contribution Level Code</td>
            <td class="colon">:</td>
            <td class="value">{{ $ca->contribution_level_code }}</td>
        </tr>
    </table>

    <table>
        <tr>
            <td colspan="3"><b>Travel Details:</b></td>
        </tr>
        <tr>
            <td class="label">Destination</td>
            <td class="colon">:</td>
            <td class="value">{{ $ca->destination }}</td>
        </tr>
        <tr>
            <td class="label">CA Needs</td>
            <td class="colon">:</td>
            <td class="value">{{ $ca->ca_needs }}</td>
        </tr>
        <tr>
            <td class="label">Start Date</td>
            <td class="colon">:</td>
            <td class="value">{{ $ca->start_date }}</td>
        </tr>
        <tr>
            <td class="label">End Date</td>
            <td class="colon">:</td>
            <td class="value">{{ $ca->end_date }}</td>
        </tr>
        {{-- <tr>
            <td class="label">Date Required</td>
            <td class="colon">:</td>
            <td class="value">{{ $ca->date_required }}</td>
        </tr> --}}
    </table>

    <table>
        <tr>
            <td colspan="3"><b>Financial Details:</b></td>
        </tr>
        <tr>
            <td class="label">Total CA</td>
            <td class="colon">:</td>
            <td class="value">{{ $ca->total_ca }}</td>
        </tr>
        <tr>
            <td class="label">Total Real</td>
            <td class="colon">:</td>
            <td class="value">{{ $ca->total_real }}</td>
        </tr>
        <tr>
            <td class="label">Total Cost</td>
            <td class="colon">:</td>
            <td class="value">{{ $ca->total_cost }}</td>
        </tr>
    </table>

    <table>
        <tr>
            <td colspan="3"><b>Approval Status:</b></td>
        </tr>
        <tr>
            <td class="label">Approval Status</td>
            <td class="colon">:</td>
            <td class="value">{{ $ca->approval_status }}</td>
        </tr>
        <tr>
            <td class="label">Approval Sett</td>
            <td class="colon">:</td>
            <td class="value">{{ $ca->approval_sett }}</td>
        </tr>
        <tr>
            <td class="label">Approval Extend</td>
            <td class="colon">:</td>
            <td class="value">{{ $ca->approval_extend }}</td>
        </tr>
    </table>
</body>

</html>

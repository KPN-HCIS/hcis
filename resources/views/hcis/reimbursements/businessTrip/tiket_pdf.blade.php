<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Ticket Details</title>
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
    <h5 class="center">TICKET DETAILS</h5>
    <h5 class="center">No. {{ $ticket->no_tkt }}</h5>

    <table>
        <tr>
            <td colspan="3"><b>Ticket Information:</b></td>
        </tr>
        <tr>
            <td class="label">Ticket Number</td>
            <td class="colon">:</td>
            <td class="value">{{ $ticket->no_tkt }}</td>
        </tr>
        <tr>
            <td class="label">SPPD Number</td>
            <td class="colon">:</td>
            <td class="value">{{ $ticket->no_sppd }}</td>
        </tr>
        <tr>
            <td class="label">Unit</td>
            <td class="colon">:</td>
            <td class="value">{{ $ticket->unit }}</td>
        </tr>
    </table>

    <table>
        <tr>
            <td colspan="3"><b>Passenger Information:</b></td>
        </tr>
        <tr>
            <td class="label">Passenger Name</td>
            <td class="colon">:</td>
            <td class="value">{{ $ticket->np_tkt }}</td>
        </tr>
        <tr>
            <td class="label">ID Number</td>
            <td class="colon">:</td>
            <td class="value">{{ $ticket->noktp_tkt }}</td>
        </tr>
        <tr>
            <td class="label">Phone Number</td>
            <td class="colon">:</td>
            <td class="value">{{ $ticket->tlp_tkt }}</td>
        </tr>
        <tr>
            <td class="label">Gender</td>
            <td class="colon">:</td>
            <td class="value">{{ $ticket->jk_tkt }}</td>
        </tr>
    </table>

    <table>
        <tr>
            <td colspan="3"><b>Travel Details:</b></td>
        </tr>
        <tr>
            <td class="label">From</td>
            <td class="colon">:</td>
            <td class="value">{{ $ticket->dari_tkt }}</td>
        </tr>
        <tr>
            <td class="label">To</td>
            <td class="colon">:</td>
            <td class="value">{{ $ticket->ke_tkt }}</td>
        </tr>
        <tr>
            <td class="label">Departure Date</td>
            <td class="colon">:</td>
            <td class="value">{{ $ticket->tgl_brkt_tkt }}</td>
        </tr>
        <tr>
            <td class="label">Departure Time</td>
            <td class="colon">:</td>
            <td class="value">{{ $ticket->jam_brkt_tkt }}</td>
        </tr>
        <tr>
            <td class="label">Return Date</td>
            <td class="colon">:</td>
            <td class="value">{{ $ticket->tgl_plg_tkt }}</td>
        </tr>
        <tr>
            <td class="label">Return Time</td>
            <td class="colon">:</td>
            <td class="value">{{ $ticket->jam_plg_tkt }}</td>
        </tr>
    </table>

    <table>
        <tr>
            <td colspan="3"><b>Ticket Details:</b></td>
        </tr>
        <tr>
            <td class="label">Ticket Type</td>
            <td class="colon">:</td>
            <td class="value">{{ $ticket->jenis_tkt }}</td>
        </tr>
        <tr>
            <td class="label">Travel Type</td>
            <td class="colon">:</td>
            <td class="value">{{ $ticket->type_tkt }}</td>
        </tr>
    </table>
</body>

</html>

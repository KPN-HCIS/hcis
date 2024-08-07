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
            margin-bottom: 2px;
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
    </style>
</head>

<body>
    <div class="header">
        <img src="{{ public_path('images/kop.jpg') }}" alt="Kop Surat">
    </div>
    <h5 class="center">TICKET FORM</h5>
    <h5 class="center">No. {{ $ticket->no_sppd }}</h5>

    <table>
        <p>Harap dipesankan tiket sebagai berikut :</p>
        <p><b>Detail tiket yang dipesan :</b></p>
    </table>

    <table>
        <tr>
            <td colspan="3"><b>Passenger Information:</b></td>
        </tr>
        <tr>
            <td class="label">Passenger Name</td>
            <td class="colon">:</td>
            <td class="value">{{ $ticket->employee->fullname }}</td>
        </tr>
        <tr>
            <td class="label">Phone Number</td>
            <td class="colon">:</td>
            <td class="value">{{ $ticket->noktp_tkt }}</td>
        </tr>
        <tr>
            <td class="label">Gender</td>
            <td class="colon">:</td>
            <td class="value">{{ $ticket->jk_tkt }}</td>
        </tr>
    </table>

    <table>
        <tr>
            <td class="label">Destination</td>
            <td class="colon">:</td>
            <td class="value">{{ $ticket->dari_tkt }} - {{ $ticket->ke_tkt }} </td>
        </tr>
        {{-- <tr>
            <td class="label">To</td>
            <td class="colon">:</td>
            <td class="value">{{ $ticket->ke_tkt }}</td>
        </tr> --}}
        @php
            use Carbon\Carbon;
        @endphp

        <tr>
            <td class="label">Departure Date</td>
            <td class="colon">:</td>
            <td class="value">
                @php
                    $formattedDate = Carbon::parse($ticket->tgl_brkt_tkt)->format('d-m-Y');
                    $formattedTime = Carbon::parse($ticket->jam_brkt_tkt)->format('H:i');
                @endphp
                {{ $formattedDate }}, {{ $formattedTime }} WIB
            </td>
        </tr>

        @if ($ticket->type_tkt != 'One Way')
            <tr>
                <td class="label">Return Date</td>
                <td class="colon">:</td>
                <td class="value">
                    @php
                        $formattedReturnDate = Carbon::parse($ticket->tgl_plg_tkt)->format('d-m-Y');
                        $formattedReturnTime = Carbon::parse($ticket->jam_plg_tkt)->format('H:i');
                    @endphp
                    {{ $formattedReturnDate }}, {{ $formattedReturnTime }} WIB
                </td>
            </tr>
        @endif
    </table>

    <table>
        <tr>
            <td class="label">Ticket Type</td>
            <td class="colon">:</td>
            <td class="value">{{ $ticket->jenis_tkt }}</td>
        </tr>
        <tr>
            <td class="label">Travel Type</td>
            <td class="colon">:</td>
            <td class="value"><b>{{ $ticket->type_tkt }}</td></b>
        </tr>
    </table>
    <table>
        <tr>
            <td class="label">Under the Burden of PT</td>
            <td class="colon">:</td>
            <td class="value">{{ $ticket->employee->company_name }}</td>
        </tr>
        <tr>
            <td class="label">Cost Center</td>
            <td class="colon">:</td>
            <td class="value"><b>{{ $ticket->cost_center ?? '0' }}</td></b>
        </tr>
    </table>
    <table>
        <tr>
            <td colspan="3"><b>Disetujui Oleh :</b></td>
        </tr>
        <tr>
            <td class="label">Nama Atasan 1</td>
            <td class="colon">:</td>
            <td class="value">{{ $ticket->businessTrip->atasan_1 }}</td>
        </tr>
        <tr>
            <td class="label">Tanggal</td>
            <td class="colon">:</td>
            <td class="value">{{ $ticket->businessTrip->atasan_2 }}</td>
        </tr>
    </table>

    <table>
        <tr>
            <td class="label">Nama Atasan 2</td>
            <td class="colon">:</td>
            <td class="value">{{ $ticket->businessTrip->atasan_2 }}</td>
        </tr>
        <tr>
            <td class="label">Tanggal</td>
            <td class="colon">:</td>
            <td class="value">{{ $ticket->businessTrip->tanggal_atasan_2 }}</td>
        </tr>
    </table>

    <table>
        <tr>
            <td class="label">HRD</td>
            <td class="colon">:</td>
            <td class="value">{{ $ticket->businessTrip->atasan_2 }}</td>
        </tr>
        <tr>
            <td class="label">Tanggal</td>
            <td class="colon">:</td>
            <td class="value">{{ $ticket->businessTrip->tanggal_atasan_2 }}</td>
        </tr>
    </table>
</body>

</html>

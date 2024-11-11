<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Business Trip Notification</title>
</head>

<body>
    <h1>New Business Trip Request</h1>
    <p>Dear Sir/Madam: <b>{{ $managerName }}</b></p><br>
    <p><strong>No. SPPD:</strong> {{ $businessTrip->no_sppd }}</p>
    <p><strong>Employee Name:</strong> {{ $businessTrip->nama }}</p>
    <p><strong>Start Date:</strong> {{ $businessTrip->mulai }}</p>
    <p><strong>End Date:</strong> {{ $businessTrip->kembali }}</p>
    <p><strong>Type of Service:</strong> {{ ucwords(strtolower($businessTrip->jns_dinas)) }}</p>
    <p><strong>Location:</strong> {{ $businessTrip->tujuan }}</p>
    <p><strong>Trip Purpose:</strong> {{ $businessTrip->keperluan }}</p>
    <p><strong>PT:</strong> {{ $businessTrip->bb_perusahaan }}</p>
    <hr>
    <p><strong>Cash Advance:</strong>
        {{ $businessTrip->ca === 'Ya' ? 'Yes' : ($businessTrip->ca === 'Tidak' ? 'No' : $businessTrip->ca) }}
    </p>
    @if ($businessTrip->ca === 'Ya')
        <p><strong><u>CA Details:</u></strong></p>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Category</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Total Days</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: right;">Amount (Rp)</th>
                </tr>
            </thead>
            <tbody>
                <!-- Perdiem Row -->
                <tr>
                    <td style="border: 1px solid #ddd; padding: 8px;">Perdiem</td>
                    <td style="border: 1px solid #ddd; padding: 8px;">{{ $caDetails['total_days_perdiem'] ?? 0 }} Days</td>
                    <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">
                        {{ number_format($caDetails['total_amount_perdiem'] ?? 0, 0, ',', '.') }}</td>
                </tr>

                <!-- Transport Row -->
                <tr>
                    <td style="border: 1px solid #ddd; padding: 8px;">Transport</td>
                    <td style="border: 1px solid #ddd; padding: 8px;">{{ $caDetails['total_days_transport'] ?? 0 }} Days
                    </td>
                    <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">
                        {{ number_format($caDetails['total_amount_transport'] ?? 0, 0, ',', '.') }}</td>
                </tr>

                <!-- Accommodation Row -->
                <tr>
                    <td style="border: 1px solid #ddd; padding: 8px;">Accommodation</td>
                    <td style="border: 1px solid #ddd; padding: 8px;">{{ $caDetails['total_days_accommodation'] ?? 0 }} Days
                    </td>
                    <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">
                        {{ number_format($caDetails['total_amount_accommodation'] ?? 0, 0, ',', '.') }}</td>
                </tr>

                <!-- Others Row -->
                <tr>
                    <td style="border: 1px solid #ddd; padding: 8px;">Others</td>
                    <td style="border: 1px solid #ddd; padding: 8px;">{{ $caDetails['total_days_others'] ?? 0 }} Days</td>
                    <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">
                        {{ number_format($caDetails['total_amount_others'] ?? 0, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    @endif

    <p><strong>Ticket:</strong>
        {{ $businessTrip->tiket === 'Ya' ? 'Yes' : ($businessTrip->tiket === 'Tidak' ? 'No' : $businessTrip->tiket) }}
    </p>
    @if ($businessTrip->tiket === 'Ya')
        <p><strong><u>Ticket Details:</u></strong></p>
        <ul>
            @foreach ($ticketDetails as $index => $ticket)
                <li>
                    <p><strong>Ticket #{{ $index + 1 }}</strong></p>
                    <p><strong>Passenger Name:</strong> {{ $ticket->np_tkt }}</p>
                    <p><strong>Transport Type:</strong> {{ $ticket->jenis_tkt }}</p>
                    <p><strong>From:</strong> {{ $ticket->dari_tkt }}</p>
                    <p><strong>To:</strong> {{ $ticket->ke_tkt }}</p>
                    <p><strong>Ticket Type:</strong> {{ $ticket->type_tkt }}</p>
                    <p><strong>Departure:</strong> {{ $ticket->tgl_brkt_tkt }} at {{ $ticket->jam_brkt_tkt }}</p>
                    @if ($ticket->type_tkt == 'Round Trip')
                        <p><strong>Return:</strong> {{ $ticket->tgl_plg_tkt }} at {{ $ticket->jam_plg_tkt }}</p>
                    @endif
                </li>
            @endforeach
        </ul>
    @endif

    <p><strong>Hotel:</strong>
        {{ $businessTrip->hotel === 'Ya' ? 'Yes' : ($businessTrip->hotel === 'Tidak' ? 'No' : $businessTrip->hotel) }}
    </p>

    @if ($businessTrip->hotel === 'Ya')
        <p><strong><u>Hotel Details:</u></strong></p>
        <ul>
            @foreach ($hotelDetails as $index => $hotel)
                <li>
                    <p><strong>Hotel #{{ $index + 1 }}</strong></p>
                    <p><strong>Hotel Name:</strong> {{ $hotel->nama_htl }}</p>
                    <p><strong>No. Hotel:</strong> {{ $hotel->no_htl }}</p>
                    <p><strong>Location:</strong> {{ $hotel->lokasi_htl }}</p>
                    <p><strong>Check-in:</strong> {{ $hotel->tgl_masuk_htl }}</p>
                    <p><strong>Check-out:</strong> {{ $hotel->tgl_keluar_htl }}</p>
                    <p><strong>Total Days:</strong> {{ $hotel->total_hari }}</p>
                </li>
            @endforeach
        </ul>
    @endif

    <p><strong>Voucher Taxi:</strong>
        {{ $businessTrip->taksi === 'Ya' ? 'Yes' : ($businessTrip->taksi === 'Tidak' ? 'No' : $businessTrip->taksi) }}
    </p>
    @if ($businessTrip->taksi === 'Ya' && $taksiDetails)
        <p><strong><u>Taxi Details:</u></strong></p>
        <p><strong>Total Voucher:</strong> {{ $taksiDetails->no_vt }}</p>
        <p><strong>Keeper Voucher:</strong> Rp. {{ number_format($taksiDetails->keeper_vt, 0, ',', '.') }}</p>
        <p><strong>Nominal Voucher:</strong> Rp. {{ number_format($taksiDetails->nominal_vt, 0, ',', '.') }}</p>
    @endif

    <hr>
    <p>For approval or rejection of the Business Trip, you can choose the following links: <a href="#">Approve</a>
        / <a href="#">Reject</a></p>

    <p>Thank you,</p>
    <p>HC System</p>

</body>

</html>

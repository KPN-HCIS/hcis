<!DOCTYPE html>
<html>

<head>
    <title>Hotel Request Notification</title>
</head>

<body>
    <h1>New Hotel Request Notification</h1>
    <p>Dear Sir/Madam: <b>{{ $managerName }}</b></p><br>
    <p><strong>No SPPD:</strong> {{ $noSppd }}</p>
    <p><strong>Approval Status:</strong> {{ $approvalStatus }}</p>

    <h2>Hotel Details</h2>

    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr>
                <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">#</th>
                <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">No HTL</th>
                <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Hotel Name</th>
                <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Location</th>
                <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Check-in Date</th>
                <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Check-out Date</th>
                <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Total Days</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($noHtlList as $index => $noHtl)
                <tr>
                    <td style="border: 1px solid #ddd; padding: 8px;">{{ $index + 1 }}</td>
                    <td style="border: 1px solid #ddd; padding: 8px;">{{ $noHtl }}</td>
                    <td style="border: 1px solid #ddd; padding: 8px;">{{ $namaHtl[$index] }}</td>
                    <td style="border: 1px solid #ddd; padding: 8px;">{{ $lokasiHtl[$index] }}</td>
                    <td style="border: 1px solid #ddd; padding: 8px;">{{ $tglMasukHtl[$index] }}</td>
                    <td style="border: 1px solid #ddd; padding: 8px;">{{ $tglKeluarHtl[$index] }}</td>
                    <td style="border: 1px solid #ddd; padding: 8px;">{{ $totalHari[$index] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>


    <hr>
    <p>For approval or rejection of the Business Trip, you can choose the following links: <a href="#">Approve</a>
        / <a href="#">Reject</a></p>

    <p>Thank you,</p>
    <p>HC System</p>
</body>

</html>

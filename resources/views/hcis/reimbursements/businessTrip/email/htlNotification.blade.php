<!DOCTYPE html>
<html>

<head>
    <title>Hotel Request Notification</title>
</head>

<body>
    <h1>New Hotel Request Notification</h1>

    <p><strong>No SPPD:</strong> {{ $noSppd }}</p>
    <p><strong>Approval Status:</strong> {{ $approvalStatus }}</p>

    <h2>Hotel Details</h2>

    @foreach ($noHtlList as $index => $noHtl)
        <p><strong>Hotel #{{ $index + 1 }}</strong></p>
        <p><strong>No HTL:</strong> {{ $noHtl }}</p>
        <p><strong>Hotel Name:</strong> {{ $namaHtl[$index] }}</p>
        <p><strong>Location:</strong> {{ $lokasiHtl[$index] }}</p>
        <p><strong>Check-in Date:</strong> {{ $tglMasukHtl[$index] }}</p>
        <p><strong>Check-out Date:</strong> {{ $tglKeluarHtl[$index] }}</p>
        <p><strong>Total Days:</strong> {{ $totalHari[$index] }}</p>
        <hr>
    @endforeach

    <p>Thank you,</p>
    <p>HC System</p>
</body>

</html>

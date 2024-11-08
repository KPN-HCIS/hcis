<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Request Notification</title>
</head>

<body>
    <h2>Ticket Request Notification</h2>
    <p>A new ticket request has been submitted.</p>

    <p><strong>No SPPD:</strong> {{ $noSppd }}</p>
    <p><strong>Approval Status:</strong> {{ $approvalStatus }}</p>

    <h3>Ticket Details</h3>
    @foreach ($noTktList as $index => $noTkt)
        <p><strong>Ticket #{{ $index + 1 }}</strong></p>
        <p><strong>Ticket Number:</strong> {{ $noTkt }}</p>
        <p><strong>Passenger Name:</strong> {{ $namaPenumpang[$index] }}</p>
        <p><strong>Departure:</strong> {{ $dariTkt[$index] }} - {{ $keTkt[$index] }}</p>
        <p><strong>Departure Date & Time:</strong> {{ $tglBrktTkt[$index] }} at {{ $jamBrktTkt[$index] }}</p>
        <hr>
    @endforeach

    <p>Thank you,</p>
    <p>HC System</p>
</body>

</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Business Trip Notification</title>
</head>
<body>
    <h1>New Business Trip Request</h1>
    <p>A new business trip request has been submitted.</p>
    <p><strong>Employee Name:</strong> {{ $businessTrip->no_sppd }}</p>
    <p><strong>Employee Name:</strong> {{ $businessTrip->nama }}</p>
    <p><strong>Trip Purpose:</strong> {{ $businessTrip->keperluan }}</p>
    <p><strong>Start Date:</strong> {{ $businessTrip->mulai }}</p>
    <p><strong>End Date:</strong> {{ $businessTrip->kembali }}</p>
    <p><strong>Location:</strong> {{ $businessTrip->tujuan }}</p>
</body>
</html>

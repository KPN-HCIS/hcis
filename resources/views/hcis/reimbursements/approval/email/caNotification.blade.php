<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Business Trip Notification</title>
</head>
    <body>
        <h1>Requesting for Your Approva</h1>
        <p>A new business trip request has been submitted.</p>

        @if ($nextApproval)
            <p><strong>Employee ID :</strong> {{ $nextApproval->employee_id }}</p>
            <p><strong>Layer:</strong> {{ $nextApproval->layer }}</p>
        @endif

        @if ($caTransaction)
            <p><strong>Transaction Number:</strong> {{ $caTransaction->no_ca }}</p>
        @endif

        @if ($model)
            <p><strong>Transaction Number:</strong> {{ $model->no_ca }}</p>
        @endif
    </body>
</html>

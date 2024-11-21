<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Medical Notification</title>

    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
        }

        .header {
            width: 100%;
            height: auto;
            text-align: center;
        }

        .header img {
            height: auto;
            margin-bottom: 20px;
            width: 20%; /* Default untuk desktop */
        }

        /* Media query untuk mobile devices */
        @media screen and (max-width: 768px) {
            .header img {
                width: 50%; /* Ukuran untuk mobile */
            }
        }

        h5 {
            font-size: 13px;
            margin: 0;
            padding: 0;
            margin-bottom: 10px;
        }

        p {
            margin: 4px 0;
            padding: 2px;
        }

        .table-approve {
            border-collapse: collapse;
            width: 100%;
            margin-top: 8px;
            font-size: 10px;
        }

        .table-approve th,
        .table-approve td {
            border: 1px solid #ddd;
            padding: 4px;
            text-align: left;
            vertical-align: top;
        }

        .table-approve .head-row {
            font-weight: bold;
            background-color: #f5f5f5;
        }

        .table-approve th {
            background-color: #ab2f2b;
            color: #ffffff;
            font-size: 10px;
            font-weight: bold;
            white-space: nowrap;
            text-align: center;
        }

        .table-wrapper {
            overflow-x: auto;
            max-width: 100%;
        }

        .col-small {
            width: 40px;
            white-space: nowrap;
        }

        .col-medium {
            width: 80px;
            white-space: nowrap;
        }

        .col-amount {
            width: 70px;
            text-align: right;
        }

        .col-date {
            width: 70px;
            white-space: nowrap;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="https://stag-corp.kpndownstream.com/images/logo/logo-kpn-red.png" alt="Kop Surat">
    </div>
    <h5>Reimburse Medical Notification</h5>
    <p>Kepada Yth : Bapak/Ibu <strong>{{ $healthCoverages->first()->employee->fullname }}</strong></p>

    <p>Pengajuan Plafon Medical anda telah di Import oleh Admin <strong>{{ $healthCoverages->first()->employee_approve->fullname }}</strong> sebanyak {{ $healthCoverages->count() }} data.</p>
    @if ($healthCoverages->contains(fn($item) => $item->balance_uncoverage != 0))
        <p>Beberapa pengajuan Anda melebihi plafon yang tersedia, sehingga terdapat sebagian pengeluaran yang tidak ditanggung oleh plafon medical Anda.</p>
    @endif
    <p>Dengan Detail Sebagai Berikut : </p>

    <div class="table-wrapper">
        <table class="table-approve">
            <tr>
                <th colspan="10"><b>Detail Reimburse Medical:</b></th>
            </tr>
            <tr class="head-row">
                <td class="col-small">No</td>
                <td class="col-medium">No Invoice</td>
                <td>Hospital</td>
                <td>Patient</td>
                <td class="col-medium">Disease</td>
                <td class="col-date">Date</td>
                <td class="col-medium">Type</td>
                <td class="col-amount">Amount</td>
                <td class="col-amount">Uncovered</td>
                <td class="col-amount">Verified</td>
            </tr>
            @foreach($healthCoverages as $index => $healthCoverage)
            <tr>
                <td class="col-small">{{ $index + 1 }}</td>
                <td class="col-medium">{{ $healthCoverage->no_invoice }}</td>
                <td>{{ $healthCoverage->hospital_name }}</td>
                <td>{{ $healthCoverage->patient_name }}</td>
                <td class="col-medium">{{ $healthCoverage->disease }}</td>
                <td class="col-date">{{ \Carbon\Carbon::parse($healthCoverage->date)->format('d/m/Y') }}</td>
                <td class="col-medium">{{ $healthCoverage->medical_type }}</td>
                <td class="col-amount">{{ number_format($healthCoverage->balance, 0, ',', '.') }}</td>
                <td class="col-amount">{{ number_format($healthCoverage->balance_uncoverage, 0, ',', '.') }}</td>
                <td class="col-amount">{{ number_format($healthCoverage->balance_verif, 0, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr>
                <td colspan="7" style="text-align: right;"><strong>Total:</strong></td>
                <td class="col-amount"><strong>{{ number_format($healthCoverages->sum('balance'), 0, ',', '.') }}</strong></td>
                <td class="col-amount"><strong>{{ number_format($healthCoverages->sum('balance_uncoverage'), 0, ',', '.') }}</strong></td>
                <td class="col-amount"><strong>{{ number_format($healthCoverages->sum('balance_verif'), 0, ',', '.') }}</strong></td>
            </tr>
        </table>
    </div>
</body>
</html>

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
            font-size: 12px;
        }

        .header {
            width: 100%;
            height: auto;
        }

        .header img {
            height: auto;
            margin-bottom: 20px;
        }

        .content {
            padding: 0px;
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

        .table-approve {
            border-collapse: collapse;
            width: 100%;
        }

        .table-approve th,
        .table-approve td {
            border: 1px solid black;
            padding: 8px;
            text-align: center;
        }

        .table-approve .head-row {
            font-weight: bold;
        }

        .table-approve th {
            background-color: #c6e0b4;
        }

        .table-approve .total-row {
            text-align: left;
        }
        .approved {
            color: green;
        }
        .pending {
            color: yellow;
        }

        footer {
            position: fixed;
            bottom: 0cm;
            left: 0cm;
            right: 0cm;
            height: 2cm;
            text-align: right;
            line-height: 1.5cm;
            font-size: 12px;
            color: #555;
        }
    </style>
</head>
    <body>
        <h5>Reimburse Medical Notification</h5>
        <p>Kepada Yth : Bapak/Ibu <strong>{{ $healthCoverage->employee->fullname }}</strong></p>

        <p>Pengajuan Plafon Medical anda telah di Import oleh Admin {{ $healthCoverage->employee_approve->fullname }} sebanyak </p>
        <p>Dengan Detail Sebagai Berikut : </p>
        <table class="table-approve" style="width: 80%;">
            <tr>
                <th colspan="13"><b>Detail Reimburse Medical  :</b></th>
            </tr>
            <tr class="head-row">
                <td style="width: 5%">No</td>
                <td>Employee ID</td>
                <td>No Invoice</td>
                <td>Hospital Name</td>
                <td>Patient Name</td>
                <td>Desease</td>
                <td>Date</td>
                <td>Coverage Detail</td>
                <td>Period</td>
                <td>Medical Type</td>
                <td>Amount</td>
                <td>Amount Uncoverage</td>
                <td>Amount Verify</td>
            </tr>
            <tr>
                <td class="label">1</td>
                <td>
                    {{ $healthCoverage->employee_id }}
                </td>
                <td>
                    {{ $healthCoverage->no_invoice }}
                </td>
                <td>
                    {{ $healthCoverage->hospital_name }}
                </td>
                <td>
                    {{ $healthCoverage->patient_name }}
                </td>
                <td>
                    {{ $healthCoverage->disease }}
                </td>
                <td>
                    {{ $healthCoverage->date }}
                </td>
                <td>
                    {{ $healthCoverage->coverage_detail }}
                </td>
                <td>
                    {{ $healthCoverage->period }}
                </td>
                <td>
                    {{ $healthCoverage->medical_type }}
                </td>
                <td>
                    {{ $healthCoverage->balance }}
                </td>
                <td>
                    {{ $healthCoverage->balance_uncoverage }}
                </td>
                <td>
                    {{ $healthCoverage->balance_verif }}
                </td>
            </tr>
        </table>
        {{-- {{ var_dump($healthCoverage) }} --}}
    </body>
</html>

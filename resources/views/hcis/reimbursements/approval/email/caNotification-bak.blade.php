<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cash Advanced Notification</title>
</head>
    <body>
        <h1>Requesting for Your Approva</h1>
        @if ($caTransaction->approval_status == 'Approved')
            <p>Declaration  Cash Advanced from {{ $caTransaction->employee->fullname }} request has been submitted.</p>    
        @else
            <p>A new Cash Advanced request has been submitted  by {{ $caTransaction->employee->fullname }}.</p>
        @endif

        @if ($nextApproval)
            <p><strong>Employee ID :</strong> {{ $nextApproval->employee_id }}</p>
            <p><strong>Layer:</strong> {{ $nextApproval->layer }}</p>
        @endif

        @if ($caTransaction)
            <p><strong>Transaction Number:</strong> {{ $caTransaction->no_ca }}</p>
            <p><strong>Status:</strong> {{ $caTransaction->approval_status }}</p>
            <p><strong>Status:</strong> {{ $caTransaction->approval_sett }}</p>

            @php
                $detailCA = json_decode($caTransaction->detail_ca, true);
                $declareCA = json_decode($caTransaction->declare_ca, true);
            @endphp

            @if ($caTransaction->type_ca == "dns")
                <p>
                    Rp. {{ number_format(array_sum(array_column($detailCA['detail_perdiem'], 'nominal')), 0, ',', '.') }}
                </p>
                <p>
                    Rp. {{ number_format(array_sum(array_column($detailCA['detail_transport'], 'nominal')), 0, ',', '.') }}
                </p>
                <p>
                    Rp. {{ number_format(array_sum(array_column($detailCA['detail_penginapan'], 'nominal')), 0, ',', '.') }}
                </p>
                <p>
                    Rp. {{ number_format(array_sum(array_column($detailCA['detail_lainnya'], 'nominal')), 0, ',', '.') }}
                </p>
                @if ($caTransaction->approval_status == 'Approved')
                    <p>
                        Rp. {{ number_format(array_sum(array_column($declareCA['detail_perdiem'], 'nominal')), 0, ',', '.') }}
                    </p>
                    <p>
                        Rp. {{ number_format(array_sum(array_column($declareCA['detail_transport'], 'nominal')), 0, ',', '.') }}
                    </p>
                    <p>
                        Rp. {{ number_format(array_sum(array_column($declareCA['detail_penginapan'], 'nominal')), 0, ',', '.') }}
                    </p>
                    <p>
                        Rp. {{ number_format(array_sum(array_column($declareCA['detail_lainnya'], 'nominal')), 0, ',', '.') }}
                    </p>
                @endif
            @elseif ($caTransaction->type_ca == "ent")
                <p>
                    Rp. {{ number_format(array_sum(array_column($detailCA['detail_e'], 'nominal')), 0, ',', '.') }}
                </p>
                @if ($caTransaction->approval_status == 'Approved')
                    <p>
                        Rp. {{ number_format(array_sum(array_column($declareCA['detail_e'], 'nominal')), 0, ',', '.') }}
                    </p>
                @endif
            @elseif ($caTransaction->type_ca == "ndns")
                <p>
                    Rp. {{ number_format(array_sum(array_column($detailCA, 'nominal_nbt')), 0, ',', '.') }}
                </p>
                @if ($caTransaction->approval_status == 'Approved')
                    <p>
                        Rp. {{ number_format(array_sum(array_column($declareCA, 'nominal_nbt')), 0, ',', '.') }}
                    </p>
                @endif
            @endif
        @endif
    </body>
</html>

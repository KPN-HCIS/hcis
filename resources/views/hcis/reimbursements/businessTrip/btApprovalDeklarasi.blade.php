@extends('layouts_.vertical', ['page_title' => 'Business Trip'])

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-datepicker@1.9.0/dist/css/bootstrap-datepicker.min.css"
        rel="stylesheet">

    <style>
        table {
            white-space: nowrap;
        }

        .table-responsive.table-container {
            padding-top: 0 !important;
            margin-top: 0 !important;
        }

        .table-responsive.table-container table {
            margin-top: 0 !important;
        }

        .table-responsive.table-container thead tr:first-child th {
            border-top: none;
        }
    </style>
@endsection

@section('content')
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Declaration Data</h4>
                        <a href="{{ route('businessTrip.approval') }}" class="btn-close btn-close-white"></a>
                    </div>
                    <div class="card-body">
                        <form action="/businessTrip/deklarasi/update/{{ $n->id }}" method="POST" id="btEditForm"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <!-- Employee Data Table -->
                            <div class="row">
                                <div class="col-6 col-sm-6">
                                    <div class="form-group row">
                                        <label for="staticEmail" class="py-0 col-sm-4 col-form-label">Employee Name</label>
                                        <div class="col-sm-8">
                                            <input type="text" readonly="" class="py-1 form-control-plaintext"
                                                id="staticEmail" value=": {{ $employee_data->fullname }}">
                                        </div>
                                    </div>

                                </div>
                                <div class="col-6 col-sm-6">
                                    <div class="form-group row">
                                        <label for="staticEmail" class="py-0 col-sm-4 col-form-label">NIK</label>
                                        <div class="col-sm-8">
                                            <input type="text" readonly="" class="py-1 form-control-plaintext"
                                                id="staticEmail" value=": {{ $employee_data->ktp }}">
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6 col-sm-6">
                                    <div class="form-group row">
                                        <label for="staticEmail" class="py-0 col-sm-4 col-form-label">Email</label>
                                        <div class="col-sm-8">
                                            <input type="text" readonly="" class="py-1 form-control-plaintext"
                                                id="staticEmail" value=": {{ $employee_data->email }}">
                                        </div>
                                    </div>

                                </div>
                                <div class="col-6 col-sm-6">
                                    <div class="form-group row">
                                        <label for="staticEmail" class="py-0 col-sm-4 col-form-label">Bank Account</label>
                                        <div class="col-sm-8">
                                            <input type="text" readonly="" class="py-1 form-control-plaintext"
                                                id="staticEmail"
                                                value=": {{ $employee_data->bank_name }} - {{ $employee_data->bank_account_number }} - {{ $employee_data->bank_account_name }}">
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6 col-sm-6">
                                    <div class="form-group row">
                                        <label for="staticEmail" class="py-0 col-sm-4 col-form-label">Division</label>
                                        <div class="col-sm-8">
                                            <input type="text" readonly="" class="py-1 form-control-plaintext"
                                                id="staticEmail"
                                                value=": {{ $employee_data->unit }} / {{ $employee_data->designation_name }}">
                                        </div>
                                    </div>

                                </div>
                                <div class="col-6 col-sm-6">
                                    <div class="form-group row">
                                        <label for="staticEmail" class="py-0 col-sm-4 col-form-label">PT/Location</label>
                                        <div class="col-sm-8">
                                            <input type="text" readonly="" class="py-1 form-control-plaintext"
                                                id="staticEmail"
                                                value=": {{ $employee_data->company_name }} / {{ $employee_data->office_area }}">
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6 col-sm-6">
                                    <div class="form-group row">
                                        <label for="staticEmail" class="py-0 col-sm-4 col-form-label">NO SPPD</label>
                                        <div class="col-sm-8">
                                            <input type="text" readonly="" class="py-1 form-control-plaintext"
                                                id="staticEmail" value=": {{ $n->no_sppd }}">
                                        </div>
                                    </div>

                                </div>
                                <div class="col-6 col-sm-6">
                                    <div class="form-group row">
                                        <label for="staticEmail" class="py-0 col-sm-4 col-form-label">Requestor</label>
                                        <div class="col-sm-8">
                                            <input type="text" readonly="" class="py-1 form-control-plaintext"
                                                id="staticEmail" value=": {{ $n->nama }}">
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6 col-sm-6">
                                    <div class="form-group row">
                                        <label for="staticEmail" class="py-0 col-sm-4 col-form-label">Start Date</label>
                                        <div class="col-sm-8">
                                            <input type="text" readonly="" class="py-1 form-control-plaintext"
                                                id="staticEmail" value=": {{ $n->mulai }}">
                                        </div>
                                    </div>

                                </div>
                                <div class="col-6 col-sm-6">
                                    <div class="form-group row">
                                        <label for="staticEmail" class="py-0 col-sm-4 col-form-label">End Date</label>
                                        <div class="col-sm-8">
                                            <input type="text" readonly="" class="py-1 form-control-plaintext"
                                                id="staticEmail" value=": {{ $n->kembali }}">
                                        </div>
                                    </div>

                                </div>
                            </div>

                            @php
                                // Provide default empty arrays if caDetail or sections are not set
                                $detailPerdiem = $caDetail['detail_perdiem'] ?? [];
                                $detailTransport = $caDetail['detail_transport'] ?? [];
                                $detailPenginapan = $caDetail['detail_penginapan'] ?? [];
                                $detailLainnya = $caDetail['detail_lainnya'] ?? [];
                            @endphp
                            <!-- 1st Form -->
                            <div class="row mt-2" id="ca_div">
                                <div class="col-md-6">
                                    <div class="table-responsive table-container"
                                        style="height: 200px; overflow-y: auto;">
                                        <table class="table table-hover table-sm nowrap"
                                            id="{{ isset($caDetail['detail_perdiem']) && is_array($caDetail['detail_perdiem']) ? (array_sum(array_column($caDetail['detail_perdiem'], 'nominal')) > 0 ? 'perdiemTable' : '') : '' }}"
                                            width="100%" cellspacing="0">
                                            <thead class="thead-light">
                                                <tr class="bg-primary">
                                                    <th colspan="8" class="text-center text-white"><b>Perdiem Plan:</b>
                                                    </th>
                                                </tr>
                                                <tr style="text-align-last: center;">
                                                    <th></th>
                                                    <th>No</th>
                                                    <th>Start Date</th>
                                                    <th>End Date</th>
                                                    <th>Location</th>
                                                    <th>Company Code</th>
                                                    <th>Total Days</th>
                                                    <th>Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $totalPerdiem = 0;
                                                $totalDays = 0;
                                                $hasData = isset($caDetail['detail_perdiem']) && is_array($caDetail['detail_perdiem']);
                                                $allNominalZero = true; // Flag to check if all nominal values are zero
                                                ?>

                                                @if ($hasData)
                                                    @foreach ($caDetail['detail_perdiem'] as $perdiem)
                                                        <?php
                                                        $nominal = floatval($perdiem['nominal'] ?? '0');
                                                        $totalPerdiem += $nominal;
                                                        $totalDays += intval($perdiem['total_days'] ?? '0');

                                                        // Check if any nominal value is not zero
                                                        if ($nominal > 0) {
                                                            $allNominalZero = false;
                                                        }
                                                        ?>
                                                    @endforeach

                                                    @if ($allNominalZero)
                                                        <tr>
                                                            <td colspan="8" class="text-center">No data available</td>
                                                        </tr>
                                                    @else
                                                        @foreach ($caDetail['detail_perdiem'] as $perdiem)
                                                            <tr class="text-center">
                                                                <td class="text-center"></td>
                                                                <td class="text-center">{{ $loop->index + 1 }}</td>
                                                                <td>{{ isset($perdiem['start_date']) ? \Carbon\Carbon::parse($perdiem['start_date'])->format('d-M-y') : '-' }}
                                                                </td>
                                                                <td>{{ isset($perdiem['end_date']) ? \Carbon\Carbon::parse($perdiem['end_date'])->format('d-M-y') : '-' }}
                                                                </td>
                                                                <td>
                                                                    @if (isset($perdiem['location']) && $perdiem['location'] == 'Others')
                                                                        {{ $perdiem['other_location'] ?? '-' }}
                                                                    @else
                                                                        {{ $perdiem['location'] ?? '-' }}
                                                                    @endif
                                                                </td>
                                                                <td>{{ $perdiem['company_code'] ?? '-' }}</td>
                                                                <td>{{ $perdiem['total_days'] ?? '-' }} Days</td>
                                                                <td style="text-align: right">Rp.
                                                                    {{ number_format($nominal, 0, ',', '.') }}</td>
                                                            </tr>
                                                        @endforeach
                                                    @endif
                                                @else
                                                    <tr>
                                                        <td colspan="8" class="text-center">No data available</td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                            <tbody>
                                                <tr>
                                                    <td colspan="{{ $hasData && !$allNominalZero ? 4 : 6 }}"
                                                        class="text-right">Total</td>
                                                    <td class="text-center">{{ $totalDays }} Days</td>
                                                    <td style="text-align: right">Rp.
                                                        {{ number_format($totalPerdiem, 0, ',', '.') }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="table-responsive table-container"
                                        style="height: 200px; overflow-y: auto;">
                                        <table class="table table-hover table-sm nowrap"
                                            id="{{ isset($caDetail['detail_transport']) && is_array($caDetail['detail_transport']) ? (array_sum(array_column($caDetail['detail_transport'], 'nominal')) > 0 ? 'transportTable' : '') : '' }}"
                                            width="100%" cellspacing="0">
                                            <thead class="thead-light">
                                                <tr class="bg-primary">
                                                    <th colspan="5" class="text-center text-white">Transport Plan</th>
                                                </tr>
                                                <tr style="text-align-last: center;">
                                                    {{-- <th></th> --}}
                                                    <th>No</th>
                                                    <th>Date</th>
                                                    <th>Information</th>
                                                    <th>Company Code</th>
                                                    <th>Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                                <?php $totalTransport = 0; ?>
                                                @if (isset($caDetail['detail_transport']) &&
                                                        is_array($caDetail['detail_transport']) &&
                                                        count($caDetail['detail_transport']) > 0)
                                                    @foreach ($caDetail['detail_transport'] as $transport)
                                                        <?php
                                                        $totalTransport += floatval($transport['nominal'] ?? 0);
                                                        ?>
                                                    @endforeach

                                                    @if ($totalTransport > 0)
                                                        @foreach ($caDetail['detail_transport'] as $transport)
                                                            <tr class="text-center">
                                                                {{-- <td></td> --}}
                                                                <td class="text-center">{{ $loop->index + 1 }}</td>
                                                                <td>
                                                                    @if (isset($transport['tanggal']) && $transport['tanggal'])
                                                                        {{ \Carbon\Carbon::parse($transport['tanggal'])->format('d-M-y') }}
                                                                    @else
                                                                        -
                                                                    @endif
                                                                </td>
                                                                <td>{{ $transport['keterangan'] ?? '-' }}</td>
                                                                <td>{{ $transport['company_code'] ?? '-' }}</td>
                                                                <td style="text-align: right">
                                                                    Rp.
                                                                    {{ number_format(floatval($transport['nominal'] ?? 0), 0, ',', '.') }}
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    @else
                                                        <tr>
                                                            <td colspan="5" class="text-center">No data available</td>
                                                        </tr>
                                                    @endif
                                                @else
                                                    <tr>
                                                        <td colspan="5" class="text-center">No data available</td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="4" class="text-right"><strong>Total</strong></td>
                                                    <td style="text-align: right">
                                                        <strong>Rp.
                                                            {{ number_format($totalTransport, 0, ',', '.') }}</strong>
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                    <div class="table-responsive table-container"
                                        style="height: 200px; overflow-y: auto;">
                                        <table class="table table-hover table-sm nowrap"
                                            id="{{ isset($caDetail['detail_penginapan']) && is_array($caDetail['detail_penginapan']) ? (array_sum(array_column($caDetail['detail_penginapan'], 'nominal')) > 0 ? 'penginapanTable' : '') : '' }}"
                                            width="100%" cellspacing="0">
                                            <thead class="thead-light">
                                                <tr class="bg-primary">
                                                    <th colspan="7" class="text-center text-white">Accommodation Plan
                                                    </th>
                                                </tr>
                                                <tr style="text-align-last: center;">
                                                    <th>No</th>
                                                    <th>Start Date</th>
                                                    <th>End Date</th>
                                                    <th>Hotel Name</th>
                                                    <th>Company Code</th>
                                                    <th>Total Days</th>
                                                    <th>Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $totalPenginapan = 0;
                                                $totalDays = 0; ?>
                                                @if (isset($caDetail['detail_penginapan']) &&
                                                        is_array($caDetail['detail_penginapan']) &&
                                                        count($caDetail['detail_penginapan']) > 0)
                                                    @foreach ($caDetail['detail_penginapan'] as $penginapan)
                                                        <?php
                                                        $totalPenginapan += floatval($penginapan['nominal'] ?? 0);
                                                        $totalDays += intval($penginapan['total_days'] ?? 0);
                                                        ?>
                                                    @endforeach

                                                    @if ($totalPenginapan > 0)
                                                        @foreach ($caDetail['detail_penginapan'] as $penginapan)
                                                            <tr style="text-align-last: center;">
                                                                <td>{{ $loop->index + 1 }}</td>
                                                                <td>{{ isset($penginapan['start_date']) ? \Carbon\Carbon::parse($penginapan['start_date'])->format('d-M-y') : '-' }}
                                                                </td>
                                                                <td>{{ isset($penginapan['end_date']) ? \Carbon\Carbon::parse($penginapan['end_date'])->format('d-M-y') : '-' }}
                                                                </td>
                                                                <td>{{ $penginapan['hotel_name'] ?? '-' }}</td>
                                                                <td>{{ $penginapan['company_code'] ?? '-' }}</td>
                                                                <td>{{ $penginapan['total_days'] ?? '-' }} Days</td>
                                                                <td>Rp.
                                                                    {{ number_format(floatval($penginapan['nominal'] ?? 0), 0, ',', '.') }}
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    @else
                                                        <tr>
                                                            <td colspan="7" class="text-center">No data available</td>
                                                        </tr>
                                                    @endif
                                                @else
                                                    <tr>
                                                        <td colspan="7" class="text-center">No data available</td>
                                                    </tr>
                                                @endif
                                            <tfoot>
                                                <td colspan="5" class="text-right">Total</td>
                                                <td class="text-center">{{ $totalDays }} Days</td>
                                                <td class="text-center">Rp.
                                                    {{ number_format($totalPenginapan, 0, ',', '.') }}</td>
                                            </tfoot>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="table-responsive table-container"
                                        style="height: 200px; overflow-y: auto;">
                                        <table class="table table-hover table-sm nowrap"
                                            id="{{ isset($caDetail['detail_lainnya']) && is_array($caDetail['detail_lainnya']) ? (array_sum(array_column($caDetail['detail_lainnya'], 'nominal')) > 0 ? 'otherTable' : '') : '' }}"
                                            width="100%" cellspacing="0">
                                            <thead class="thead-light">
                                                <tr class="bg-primary">
                                                    <th colspan="4" class="text-center text-white">Others Plan</th>
                                                </tr>
                                                <tr style="text-align-last: center;">
                                                    <th>No</th>
                                                    <th>Date</th>
                                                    <th>Information</th>
                                                    <th>Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $totalLainnya = 0; ?>
                                                @if (isset($caDetail['detail_lainnya']) &&
                                                        is_array($caDetail['detail_lainnya']) &&
                                                        count($caDetail['detail_lainnya']) > 0)
                                                    @foreach ($caDetail['detail_lainnya'] as $lainnya)
                                                        <?php
                                                        $totalLainnya += floatval($lainnya['nominal'] ?? 0);
                                                        ?>
                                                    @endforeach

                                                    @if ($totalLainnya > 0)
                                                        @foreach ($caDetail['detail_lainnya'] as $lainnya)
                                                            <tr style="text-align-last: center;">
                                                                <td>{{ $loop->index + 1 }}</td>
                                                                <td>{{ isset($lainnya['tanggal']) ? \Carbon\Carbon::parse($lainnya['tanggal'])->format('d-M-y') : '-' }}
                                                                </td>
                                                                <td>{{ $lainnya['keterangan'] ?? '-' }}</td>
                                                                <td style="text-align-last: right;">Rp.
                                                                    {{ number_format(floatval($lainnya['nominal'] ?? 0), 0, ',', '.') }}
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    @else
                                                        <tr>
                                                            <td colspan="4" class="text-center">No data available</td>
                                                        </tr>
                                                    @endif
                                                @else
                                                    <tr>
                                                        <td colspan="4" class="text-center">No data available</td>
                                                    </tr>
                                                @endif
                                            <tfoot>
                                                <td colspan="3" class="text-right">Total</td>
                                                <td style="text-align: right">Rp.
                                                    {{ number_format($totalLainnya, 0, ',', '.') }}</td>
                                            </tfoot>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                {{-- DECLARE TABLE --}}
                                <div class="col-md-6">
                                    <div class="table-responsive table-container"
                                        style="height: 200px; overflow-y: auto;">
                                        <table class="table table-hover table-sm nowrap"
                                            id="{{ isset($declareCa['detail_perdiem']) && is_array($declareCa['detail_perdiem']) ? (array_sum(array_column($declareCa['detail_perdiem'], 'nominal')) > 0 ? 'perdiemTableDec' : '') : '' }}"
                                            width="100%" cellspacing="0">
                                            <thead class="thead-light">
                                                <tr class="bg-primary">
                                                    <th colspan="8" class="text-center text-white"><b>Perdiem Plan
                                                            (Declaration):</b></th>
                                                </tr>
                                                <tr style="text-align-last: center;">
                                                    <th></th>
                                                    <th>No</th>
                                                    <th>Start Date</th>
                                                    <th>End Date</th>
                                                    <th>Location</th>
                                                    <th>Company Code</th>
                                                    <th>Total Days</th>
                                                    <th>Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $totalPerdiem = 0;
                                                $totalDays = 0;
                                                $hasData = isset($declareCa['detail_perdiem']) && is_array($declareCa['detail_perdiem']);
                                                $allNominalZero = true; // Flag to check if all nominal values are zero
                                                ?>

                                                @if ($hasData)
                                                    @foreach ($declareCa['detail_perdiem'] as $perdiem)
                                                        <?php
                                                        $nominal = floatval($perdiem['nominal'] ?? '0');
                                                        $totalPerdiem += $nominal;
                                                        $totalDays += intval($perdiem['total_days'] ?? '0');

                                                        // Check if any nominal value is not zero
                                                        if ($nominal > 0) {
                                                            $allNominalZero = false;
                                                        }
                                                        ?>
                                                    @endforeach

                                                    @if ($allNominalZero)
                                                        <tr>
                                                            <td colspan="8" class="text-center">No data available</td>
                                                        </tr>
                                                    @else
                                                        @foreach ($declareCa['detail_perdiem'] as $perdiem)
                                                            <tr class="text-center">
                                                                <td class="text-center"></td>
                                                                <td class="text-center">{{ $loop->index + 1 }}</td>
                                                                <td>{{ isset($perdiem['start_date']) ? \Carbon\Carbon::parse($perdiem['start_date'])->format('d-M-y') : '-' }}
                                                                </td>
                                                                <td>{{ isset($perdiem['end_date']) ? \Carbon\Carbon::parse($perdiem['end_date'])->format('d-M-y') : '-' }}
                                                                </td>
                                                                <td>
                                                                    @if (isset($perdiem['location']) && $perdiem['location'] == 'Others')
                                                                        {{ $perdiem['other_location'] ?? '-' }}
                                                                    @else
                                                                        {{ $perdiem['location'] ?? '-' }}
                                                                    @endif
                                                                </td>
                                                                <td>{{ $perdiem['company_code'] ?? '-' }}</td>
                                                                <td>{{ $perdiem['total_days'] ?? '-' }} Days</td>
                                                                <td style="text-align: right">Rp.
                                                                    {{ number_format($nominal, 0, ',', '.') }}</td>
                                                            </tr>
                                                        @endforeach
                                                    @endif
                                                @else
                                                    <tr>
                                                        <td colspan="8" class="text-center">No data available</td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                            <tbody>
                                                <tr>
                                                    <td colspan="{{ $hasData && !$allNominalZero ? 4 : 6 }}"
                                                        class="text-right">Total</td>
                                                    <td class="text-center">{{ $totalDays }} Days</td>
                                                    <td style="text-align: right">Rp.
                                                        {{ number_format($totalPerdiem, 0, ',', '.') }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="table-responsive table-container"
                                        style="height: 200px; overflow-y: auto;">
                                        <table class="table table-hover table-sm nowrap"
                                            id="{{ isset($declareCa['detail_transport']) && is_array($declareCa['detail_transport']) ? (array_sum(array_column($declareCa['detail_transport'], 'nominal')) > 0 ? 'transportTableDec' : '') : '' }}"
                                            width="100%" cellspacing="0">
                                            <thead class="thead-light">
                                                <tr class="bg-primary">
                                                    <th colspan="5" class="text-center text-white">Transport Plan
                                                        (Declaration):</th>
                                                </tr>
                                                <tr style="text-align-last: center;">
                                                    <th>No</th>
                                                    <th>Date</th>
                                                    <th>Information</th>
                                                    <th>Company Code</th>
                                                    <th>Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $totalTransport = 0; ?>
                                                @if (isset($declareCa['detail_transport']) &&
                                                        is_array($declareCa['detail_transport']) &&
                                                        count($declareCa['detail_transport']) > 0)
                                                    @foreach ($declareCa['detail_transport'] as $transport)
                                                        <?php
                                                        $totalTransport += floatval($transport['nominal'] ?? 0);
                                                        ?>
                                                    @endforeach

                                                    @if ($totalTransport > 0)
                                                        @foreach ($declareCa['detail_transport'] as $transport)
                                                            <tr class="text-center">
                                                                <td class="text-center">{{ $loop->index + 1 }}</td>
                                                                <td>
                                                                    @if (isset($transport['tanggal']) && $transport['tanggal'])
                                                                        {{ \Carbon\Carbon::parse($transport['tanggal'])->format('d-M-y') }}
                                                                    @else
                                                                        -
                                                                    @endif
                                                                </td>
                                                                <td>{{ $transport['keterangan'] ?? '-' }}</td>
                                                                <td>{{ $transport['company_code'] ?? '-' }}</td>
                                                                <td style="text-align: right">
                                                                    Rp.
                                                                    {{ number_format(floatval($transport['nominal'] ?? 0), 0, ',', '.') }}
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    @else
                                                        <tr>
                                                            <td colspan="5" class="text-center">No data available</td>
                                                        </tr>
                                                    @endif
                                                @else
                                                    <tr>
                                                        <td colspan="5" class="text-center">No data available</td>
                                                    </tr>
                                                @endif
                                            </tbody>

                                            <tfoot>
                                                <tr>
                                                    <td colspan="4" class="text-right"><strong>Total</strong></td>
                                                    <td style="text-align: right">
                                                        <strong>Rp.
                                                            {{ number_format($totalTransport, 0, ',', '.') }}</strong>
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                    <div class="table-responsive table-container"
                                        style="height: 200px; overflow-y: auto;">
                                        <table class="table table-hover table-sm nowrap"
                                            id="{{ isset($declareCa['detail_penginapan']) && is_array($declareCa['detail_penginapan']) ? (array_sum(array_column($declareCa['detail_penginapan'], 'nominal')) > 0 ? 'penginapanTableDec' : '') : '' }}"
                                            width="100%" cellspacing="0">

                                            <thead class="thead-light">
                                                <tr class="bg-primary">
                                                    <th colspan="7" class="text-center text-white">Accommodation Plan
                                                        (Declaration):</th>
                                                </tr>
                                                <tr style="text-align-last: center;">
                                                    <th>No</th>
                                                    <th>Start Date</th>
                                                    <th>End Date</th>
                                                    <th>Hotel Name</th>
                                                    <th>Company Code</th>
                                                    <th>Total Days</th>
                                                    <th>Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $totalPenginapan = 0;
                                                $totalDays = 0; ?>
                                                @if (isset($declareCa['detail_penginapan']) &&
                                                        is_array($declareCa['detail_penginapan']) &&
                                                        count($declareCa['detail_penginapan']) > 0)
                                                    @foreach ($declareCa['detail_penginapan'] as $penginapan)
                                                        <?php
                                                        $totalPenginapan += floatval($penginapan['nominal'] ?? 0);
                                                        $totalDays += intval($penginapan['total_days'] ?? 0);
                                                        ?>
                                                    @endforeach

                                                    @if ($totalPenginapan > 0)
                                                        @foreach ($declareCa['detail_penginapan'] as $penginapan)
                                                            <tr style="text-align-last: center;">
                                                                <td>{{ $loop->index + 1 }}</td>
                                                                <td>{{ isset($penginapan['start_date']) ? \Carbon\Carbon::parse($penginapan['start_date'])->format('d-M-y') : '-' }}
                                                                </td>
                                                                <td>{{ isset($penginapan['end_date']) ? \Carbon\Carbon::parse($penginapan['end_date'])->format('d-M-y') : '-' }}
                                                                </td>
                                                                <td>{{ $penginapan['hotel_name'] ?? '-' }}</td>
                                                                <td>{{ $penginapan['company_code'] ?? '-' }}</td>
                                                                <td>{{ $penginapan['total_days'] ?? '-' }} Days</td>
                                                                <td>Rp.
                                                                    {{ number_format(floatval($penginapan['nominal'] ?? 0), 0, ',', '.') }}
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    @else
                                                        <tr>
                                                            <td colspan="7" class="text-center">No data available</td>
                                                        </tr>
                                                    @endif
                                                @else
                                                    <tr>
                                                        <td colspan="7" class="text-center">No data available</td>
                                                    </tr>
                                                @endif
                                            <tfoot>
                                                <td colspan="5" class="text-right">Total</td>
                                                <td class="text-center">{{ $totalDays }} Days</td>
                                                <td class="text-center">Rp.
                                                    {{ number_format($totalPenginapan, 0, ',', '.') }}</td>
                                            </tfoot>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="table-responsive table-container p-0"
                                        style="height: 200px; overflow-y: auto;">
                                        <table class="table table-hover table-sm nowrap m-0"
                                            id="{{ isset($declareCa['detail_lainnya']) && is_array($declareCa['detail_lainnya']) ? (array_sum(array_column($declareCa['detail_lainnya'], 'nominal')) > 0 ? 'otherTableDec' : '') : '' }}"
                                            width="100%" cellspacing="0">
                                            <thead class="thead-light">
                                                <tr class="bg-primary">
                                                    <th colspan="4" class="text-center text-white">Others Plan
                                                        (Declaration):</th>
                                                </tr>
                                                <tr style="text-align-last: center;">
                                                    <th>No</th>
                                                    <th>Date</th>
                                                    <th>Information</th>
                                                    <th>Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $totalLainnya = 0; ?>
                                                @if (isset($declareCa['detail_lainnya']) &&
                                                        is_array($declareCa['detail_lainnya']) &&
                                                        count($declareCa['detail_lainnya']) > 0)
                                                    @foreach ($declareCa['detail_lainnya'] as $lainnya)
                                                        <?php
                                                        $totalLainnya += floatval($lainnya['nominal'] ?? 0);
                                                        ?>
                                                    @endforeach

                                                    @if ($totalLainnya > 0)
                                                        @foreach ($declareCa['detail_lainnya'] as $lainnya)
                                                            <tr style="text-align-last: center;">
                                                                <td>{{ $loop->index + 1 }}</td>
                                                                <td>{{ isset($lainnya['tanggal']) ? \Carbon\Carbon::parse($lainnya['tanggal'])->format('d-M-y') : '-' }}
                                                                </td>
                                                                <td>{{ $lainnya['keterangan'] ?? '-' }}</td>
                                                                <td style="text-align-last: right;">Rp.
                                                                    {{ number_format(floatval($lainnya['nominal'] ?? 0), 0, ',', '.') }}
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    @else
                                                        <tr>
                                                            <td colspan="4" class="text-center">No data available</td>
                                                        </tr>
                                                    @endif
                                                @else
                                                    <tr>
                                                        <td colspan="4" class="text-center">No data available</td>
                                                    </tr>
                                                @endif
                                            <tfoot>
                                                <td colspan="3" class="text-right">Total</td>
                                                <td style="text-align: right">Rp.
                                                    {{ number_format($totalLainnya, 0, ',', '.') }}</td>
                                            </tfoot>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            @php
                                // Provide default empty arrays if any section is not set
                                $detailPerdiem = $caDetail['detail_perdiem'] ?? [];
                                $detailTransport = $caDetail['detail_transport'] ?? [];
                                $detailPenginapan = $caDetail['detail_penginapan'] ?? [];
                                $detailLainnya = $caDetail['detail_lainnya'] ?? [];

                                $formattedTotalCashAdvanced = number_format($ca->total_ca, 0, ',', '.');
                                $formattedTotalReal = number_format($ca->total_real, 0, ',', '.');
                                $formattedTotalCost = number_format($ca->total_cost, 0, ',', '.');
                            @endphp
                            <div class="row">
                                <div class="col-md-4 mb-2">
                                    <label class="form-label">Total Cash Advanced</label>
                                    <div class="input-group">
                                        <div class="input-group-append">
                                            <span class="input-group-text">Rp</span>
                                        </div>
                                        <input class="form-control bg-light" name="totalca_deklarasi"
                                            id="totalca_deklarasi" type="text" min="0"
                                            value="{{ $formattedTotalCashAdvanced }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="form-label">Total Declaration</label>
                                    <div class="input-group">
                                        <div class="input-group-append">
                                            <span class="input-group-text">Rp</span>
                                        </div>
                                        <input class="form-control bg-light" name="totalca_deklarasi"
                                            id="totalca_deklarasi" type="text" min="0"
                                            value="{{ $formattedTotalReal }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="form-label">Total Cost</label>
                                    <div class="input-group">
                                        <div class="input-group-append">
                                            <span class="input-group-text">Rp</span>
                                        </div>
                                        <input class="form-control bg-light" name="totalca_deklarasi"
                                            id="totalca_deklarasi" type="text" min="0"
                                            value="{{ $formattedTotalCost }}" readonly>
                                    </div>
                                </div>
                            </div>
                            @php
                                $detailPerdiem2 = $declareCa['detail_perdiem'] ?? [];
                                $detailTransport2 = $declareCa['detail_transport'] ?? [];
                                $detailPenginapan2 = $declareCa['detail_penginapan'] ?? [];
                                $detailLainnya2 = $declareCa['detail_lainnya'] ?? [];
                            @endphp
                        </form>
                        @php
                            use Illuminate\Support\Facades\Storage;
                        @endphp
                        <div class="d-flex justify-content-end mt-3">
                            @if (isset($ca->prove_declare) && $ca->prove_declare)
                                <a href="{{ Storage::url($ca->prove_declare) }}" target="_blank"
                                    class="btn btn-outline-primary rounded-pill" style="margin-right: 20px;">View</a>
                            @endif

                            <!-- Decline Form -->
                            <button type="button" class="btn btn-outline-primary rounded-pill" data-bs-toggle="modal"
                                data-bs-target="#rejectReasonModal" style="padding: 0.5rem 1rem; margin-right: 5px">
                                Decline
                            </button>

                            <form method="POST" action="{{ route('confirm.deklarasi', ['id' => $n->id]) }}"
                                style="display: inline-block; margin-right: 5px;" class="status-form">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status_approval"
                                    value="{{ Auth::user()->id == $n->manager_l1_id ? 'Pending L2' : 'Declaration Approved' }}">
                                <button type="submit" class="btn btn-success rounded-pill" style="padding: 0.5rem 1rem;"
                                    onclick="confirmSubmission(event)">
                                    Approve
                                </button>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- </div> --}}

    <!-- Rejection Reason Modal -->
    <div class="modal fade" id="rejectReasonModal" tabindex="-1" aria-labelledby="rejectReasonModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light border-bottom-0">
                    <h5 class="modal-title" id="rejectReasonModalLabel" style="color: #333; font-weight: 600;">
                        Rejection
                        Reason</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="rejectReasonForm" method="POST"
                        action="{{ route('confirm.deklarasi', ['id' => $n->id]) }}">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="status_approval" value="Declaration Rejected">

                        <div class="mb-3">
                            <label for="reject_info" class="form-label" style="color: #555; font-weight: 500;">Please
                                provide a reason for rejection:</label>
                            <textarea class="form-control border-2" name="reject_info" id="reject_info" rows="4" required
                                style="resize: vertical; min-height: 100px;"></textarea>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="button" class="btn btn-outline-primary rounded-pill me-2"
                                data-bs-dismiss="modal" style="min-width: 100px;">Cancel</button>
                            <button type="submit" class="btn btn-primary rounded-pill"
                                style="min-width: 100px;">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-light rounded-4 border-0 shadow" style="border-radius: 1rem;">
                <div class="modal-body text-center p-5" style="padding: 2rem;">
                    <div class="mb-4">
                        <i class="bi bi-check-circle-fill" style="font-size: 100px; color: #AB2F2B !important;"></i>
                    </div>
                    <h4 class="mb-3 fw-bold" style="font-size: 32px; color: #AB2F2B !important;">Success!</h4>
                    <p class="mb-4" id="successModalBody" style="font-size: 20px;">
                        <!-- The success message will be inserted here -->
                    </p>
                    <button type="button" class="btn btn-outline-primary rounded-pill px-4"
                        data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        //JS TABLE

        $(document).ready(function() {
            var tableIds = [
                '#transportTable',
                '#transportTableDec',
                '#otherTable',
                '#otherTableDec',
                '#penginapanTable',
                '#penginapanTableDec'
            ];

            // Loop through each table ID
            $.each(tableIds, function(index, tableId) {
                // Check if DataTable is already initialized and destroy it
                if ($.fn.dataTable.isDataTable(tableId)) {
                    $(tableId).DataTable().destroy();
                }

                // Initialize DataTable
                $(tableId).DataTable({
                    paging: false,
                    info: false,
                    searching: false,
                    autoWidth: false,
                });
            });
        });


        var tableIds = [
            '#perdiemTable',
            '#perdiemTableDec',
        ];
        tableIds.forEach(function(id) {

            $(id).DataTable({
                responsive: {
                    details: {
                        type: 'column',
                        target: 'tr',
                    },
                },
                columnDefs: [{
                        className: 'control',
                        orderable: false,
                        targets: 0
                    },
                    {
                        responsivePriority: 1,
                        targets: 0,
                        visible: true
                    }, // Ensure the No column is visible
                    {
                        responsivePriority: 2,
                        targets: -1
                    }
                ],
                order: [1, 'asc'],
                info: false,
                paging: false,
                searching: false,
            });
        });
        // $('#otherTableDec').DataTable({
        //     paging: false,
        //     info: false,
        //     searching: false,
        //     ordering: true, // Enable sorting if needed
        //     autoWidth: false, // Prevent automatic column width adjustment
        // });


        function confirmSubmission(event) {
            event.preventDefault(); // Stop the form from submitting immediately

            // Display a confirmation alert
            const userConfirmed = confirm("Are you sure you want to approve this request?");

            if (userConfirmed) {
                // If the user confirms, submit the form
                event.target.closest('form').submit();
            } else {
                // If the user cancels, do nothing
                alert("Approval cancelled.");
            }
        }
        document.getElementById('rejectReasonForm').addEventListener('submit', function(event) {
            const reason = document.getElementById('reject_info').value.trim();
            if (!reason) {
                alert('Please provide a reason for rejection.');
                event.preventDefault(); // Stop form submission if no reason is provided
            }
        });

        // Add event listener to the decline button to open the modal
        document.getElementById('declineButton').addEventListener('click', function() {
            $('#rejectReasonModal').modal('show');
        });

        document.addEventListener('DOMContentLoaded', function() {
            const forms = document.querySelectorAll('.status-form');
            forms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const action = this.querySelector('input[name="status_approval"]').value;
                    const confirmMessage = action === 'Declaration Rejected' ?
                        'Are you sure you want to reject this?' :
                        'Are you sure you want to confirm this?';

                    if (confirm(confirmMessage)) {
                        const formData = new FormData(this);
                        fetch(this.action, {
                                method: 'POST',
                                body: formData,
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    // Update the success modal content
                                    document.getElementById('successModalBody').textContent =
                                        data.message;

                                    // Show the success modal
                                    var successModal = new bootstrap.Modal(document
                                        .getElementById('successModal'));
                                    successModal.show();

                                    // Reload the page after modal is closed
                                    document.getElementById('successModal').addEventListener(
                                        'hidden.bs.modal',
                                        function() {
                                            window.location.href = '/businessTrip/approval';
                                        });
                                } else {
                                    alert('An error occurred. Please try again.');
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                alert('An error occurred. Please try again.');
                            });
                    }
                });
            });
        });
    </script>
@endsection

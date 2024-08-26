@extends('layouts_.vertical', ['page_title' => 'Business Trip'])

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-datepicker@1.9.0/dist/css/bootstrap-datepicker.min.css"
        rel="stylesheet">
@endsection

@section('content')
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Declaration Data</h4>
                        <a href="{{ route('businessTrip') }}" class="btn-close btn-close-white"></a>
                    </div>
                    <div class="card-body">
                        <form action="/businessTrip/update/{{ $n->id }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="row mb-4">
                                <div class="col-md-4">
                                <label for="no_sppd" class="form-label">No SPPD</label>
                                <input type="text" class="form-control bg-light" id="no_sppd" name="no_sppd"
                                    value="{{ $n->no_sppd }}" readonly>
                            </div>

                            {{-- <div class="row mb-4"> --}}
                                <div class="col-md-4">
                                    <label for="mulai" class="form-label">Start Date</label>
                                    <input type="date" class="form-control bg-light" id="mulai" name="mulai"
                                        value="{{ $n->mulai }}" readonly>
                                </div>
                                <div class="col-md-4">
                                    <label for="kembali" class="form-label">End Date</label>
                                    <input type="date" class="form-control bg-light" id="kembali" name="kembali"
                                        value="{{ $n->kembali }}" readonly>
                                {{-- </div> --}}
                            </div>
                        </div>
                            <!-- 1st Form -->
                            <div class="row mt-2" id="ca_div">
                                <div class="col-md-6">
                                    <div class="table-responsive-sm">
                                        <div class="d-flex flex-column gap-2">
                                            <div class="text-bg-primary p-2" style="text-align:center; border-radius:4px;">
                                                Cash Advanced Data</div>
                                            <div class="row" id="ca_bt">
                                                <div class="col-md-12">
                                                    <div class="table-responsive-sm">
                                                        <div class="d-flex flex-column gap-2">
                                                            <div class="card">
                                                                <div class="card-body text-center">
                                                                    <button type="button" style="width: 60%" disabled
                                                                        id="toggle-bt-perdiem-deklarasi" class="btn btn-primary mt-3"
                                                                        data-state="false">
                                                                        Perdiem</button>
                                                                </div>
                                                                <div id="perdiem-card" class="card-body"
                                                                    style="display: none;">
                                                                    <div class="accordion" id="accordionPerdiem">
                                                                        <div class="accordion-item">
                                                                            <h2 class="accordion-header"
                                                                                id="enter-headingOne">
                                                                                <button class="accordion-button fw-medium"
                                                                                    type="button" data-bs-toggle="collapse"
                                                                                    data-bs-target="#enter-collapseOne"
                                                                                    aria-expanded="true"
                                                                                    aria-controls="enter-collapseOne">
                                                                                    Perdiem Plan
                                                                                </button>
                                                                            </h2>
                                                                            <div id="enter-collapseOne"
                                                                                class="accordion-collapse show"
                                                                                aria-labelledby="enter-headingOne">
                                                                                <div class="accordion-body">
                                                                                    <div id="form-container-bt-perdiem-deklarasi">
                                                                                        @php
                                                                                            // Provide default empty arrays if caDetail or sections are not set
                                                                                            $detailPerdiem =
                                                                                                $caDetail[
                                                                                                    'detail_perdiem'
                                                                                                ] ?? [];
                                                                                            $detailTransport =
                                                                                                $caDetail[
                                                                                                    'detail_transport'
                                                                                                ] ?? [];
                                                                                            $detailPenginapan =
                                                                                                $caDetail[
                                                                                                    'detail_penginapan'
                                                                                                ] ?? [];
                                                                                            $detailLainnya =
                                                                                                $caDetail[
                                                                                                    'detail_lainnya'
                                                                                                ] ?? [];

                                                                                            // Calculate totals with default values
                                                                                            $totalPerdiem = array_reduce(
                                                                                                $detailPerdiem,
                                                                                                function (
                                                                                                    $carry,
                                                                                                    $item,
                                                                                                ) {
                                                                                                    return $carry +
                                                                                                        (int) ($item[
                                                                                                            'nominal'
                                                                                                        ] ?? 0);
                                                                                                },
                                                                                                0,
                                                                                            );

                                                                                            $totalTransport = array_reduce(
                                                                                                $detailTransport,
                                                                                                function (
                                                                                                    $carry,
                                                                                                    $item,
                                                                                                ) {
                                                                                                    return $carry +
                                                                                                        (int) ($item[
                                                                                                            'nominal'
                                                                                                        ] ?? 0);
                                                                                                },
                                                                                                0,
                                                                                            );

                                                                                            $totalPenginapan = array_reduce(
                                                                                                $detailPenginapan,
                                                                                                function (
                                                                                                    $carry,
                                                                                                    $item,
                                                                                                ) {
                                                                                                    return $carry +
                                                                                                        (int) ($item[
                                                                                                            'nominal'
                                                                                                        ] ?? 0);
                                                                                                },
                                                                                                0,
                                                                                            );

                                                                                            $totalLainnya = array_reduce(
                                                                                                $detailLainnya,
                                                                                                function (
                                                                                                    $carry,
                                                                                                    $item,
                                                                                                ) {
                                                                                                    return $carry +
                                                                                                        (int) ($item[
                                                                                                            'nominal'
                                                                                                        ] ?? 0);
                                                                                                },
                                                                                                0,
                                                                                            );

                                                                                            // Total Cash Advanced
                                                                                            $totalCashAdvanced =
                                                                                                $totalPerdiem +
                                                                                                $totalTransport +
                                                                                                $totalPenginapan +
                                                                                                $totalLainnya;
                                                                                        @endphp
                                                                                        @if (!empty($detailPerdiem))
                                                                                            @foreach ($detailPerdiem as $index => $perdiem)
                                                                                                <div class="mb-2">
                                                                                                    <label
                                                                                                        class="form-label">Start
                                                                                                        Perdiem</label>
                                                                                                    <input type="date"
                                                                                                        name="start_bt_perdiem[]"
                                                                                                        disabled
                                                                                                        class="form-control start-perdiem"
                                                                                                        value="{{ old('start_bt_perdiem.' . $index, $perdiem['start_date'] ?? '') }}">
                                                                                                </div>
                                                                                                <div class="mb-2">
                                                                                                    <label
                                                                                                        class="form-label">End
                                                                                                        Perdiem</label>
                                                                                                    <input type="date"
                                                                                                        name="end_bt_perdiem[]"
                                                                                                        class="form-control end-perdiem"
                                                                                                        value="{{ old('end_bt_perdiem.' . $index, $perdiem['end_date'] ?? '') }}"
                                                                                                        disabled>
                                                                                                </div>
                                                                                                <div class="mb-2">
                                                                                                    <label
                                                                                                        class="form-label"
                                                                                                        for="start">Total
                                                                                                        Days</label>
                                                                                                    <div
                                                                                                        class="input-group">
                                                                                                        <input
                                                                                                            class="form-control bg-light total-days-perdiem"
                                                                                                            id="total_days_bt_perdiem_{{ $index }}"
                                                                                                            name="total_days_bt_perdiem[]"
                                                                                                            type="text"
                                                                                                            min="0"
                                                                                                            value="{{ old('total_days_bt_perdiem.' . $index, $perdiem['total_days'] ?? '') }}"
                                                                                                            readonly>
                                                                                                        <div
                                                                                                            class="input-group-append">
                                                                                                            <span
                                                                                                                class="input-group-text">days</span>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="mb-2">
                                                                                                    <label
                                                                                                        class="form-label"
                                                                                                        for="name">Location
                                                                                                        Agency</label>
                                                                                                    <select
                                                                                                        class="form-control select2 location-select"
                                                                                                        name="location_bt_perdiem[]"
                                                                                                        disabled>
                                                                                                        <option
                                                                                                            value="">
                                                                                                            Select
                                                                                                            location...
                                                                                                        </option>
                                                                                                        @foreach ($locations as $location)
                                                                                                            <option
                                                                                                                value="{{ $location->area }}"
                                                                                                                {{ ($perdiem['location'] ?? '') == $location->area ? 'selected' : '' }}>
                                                                                                                {{ $location->area . ' (' . $location->company_name . ')' }}
                                                                                                            </option>
                                                                                                        @endforeach
                                                                                                        <option
                                                                                                            value="Others"
                                                                                                            {{ ($perdiem['location'] ?? '') == 'Others' ? 'selected' : '' }}>
                                                                                                            Others
                                                                                                        </option>
                                                                                                    </select>
                                                                                                    <br>
                                                                                                    <input type="text"
                                                                                                        name="other_location_bt_perdiem[]"
                                                                                                        class="form-control other-location"
                                                                                                        placeholder="Other Location"
                                                                                                        value="{{ old('other_location_bt_perdiem.' . $index, $perdiem['other_location'] ?? '') }}"
                                                                                                        style="{{ ($perdiem['location'] ?? '') == 'Others' ? 'display:block;' : 'display:none;' }}">
                                                                                                </div>
                                                                                                <div class="mb-2">
                                                                                                    <label
                                                                                                        class="form-label"
                                                                                                        for="name">Company
                                                                                                        Code</label>
                                                                                                    <select
                                                                                                        class="form-control select2"
                                                                                                        id="companyFilter_{{ $index }}"
                                                                                                        name="company_bt_perdiem[]"
                                                                                                        disabled>
                                                                                                        <option
                                                                                                            value="">
                                                                                                            ---
                                                                                                            Select
                                                                                                            Company
                                                                                                            ---
                                                                                                        </option>
                                                                                                        @foreach ($companies as $company)
                                                                                                            <option
                                                                                                                value="{{ $company->contribution_level_code }}"
                                                                                                                {{ ($perdiem['company_code'] ?? '') == $company->contribution_level_code ? 'selected' : '' }}>
                                                                                                                {{ $company->contribution_level . ' (' . $company->contribution_level_code . ')' }}
                                                                                                            </option>
                                                                                                        @endforeach
                                                                                                    </select>
                                                                                                </div>
                                                                                                <div class="mb-2">
                                                                                                    <label
                                                                                                        class="form-label">Amount</label>
                                                                                                </div>
                                                                                                <div
                                                                                                    class="input-group mb-3">
                                                                                                    <div
                                                                                                        class="input-group-append">
                                                                                                        <span
                                                                                                            class="input-group-text">Rp</span>
                                                                                                    </div>
                                                                                                    @php
                                                                                                        // Format nominal value if available
                                                                                                        $formattedNominal = isset(
                                                                                                            $perdiem[
                                                                                                                'nominal'
                                                                                                            ],
                                                                                                        )
                                                                                                            ? number_format(
                                                                                                                $perdiem[
                                                                                                                    'nominal'
                                                                                                                ],
                                                                                                                0,
                                                                                                                ',',
                                                                                                                '.',
                                                                                                            )
                                                                                                            : '';
                                                                                                    @endphp
                                                                                                    <input
                                                                                                        class="form-control"
                                                                                                        name="nominal_bt_perdiem_1[]"
                                                                                                        id="nominal_bt_perdiem_{{ $index }}"
                                                                                                        type="text"
                                                                                                        min="0"
                                                                                                        value="{{ old('nominal_bt_perdiem.' . $index, $formattedNominal) }}"
                                                                                                        disabled>
                                                                                                </div>
                                                                                                <hr
                                                                                                    class="border border-primary border-1 opacity-50">
                                                                                            @endforeach
                                                                                        @else
                                                                                            <!-- Default empty fields if no data is available -->
                                                                                            <div class="mb-2">
                                                                                                <label
                                                                                                    class="form-label">Start
                                                                                                    Perdiem</label>
                                                                                                <input type="date"
                                                                                                    name="start_bt_perdiem[]"
                                                                                                    class="form-control start-perdiem"
                                                                                                    disabled>
                                                                                            </div>
                                                                                            <div class="mb-2">
                                                                                                <label
                                                                                                    class="form-label">End
                                                                                                    Perdiem</label>
                                                                                                <input type="date"
                                                                                                    name="end_bt_perdiem[]"
                                                                                                    class="form-control end-perdiem"
                                                                                                    disabled>
                                                                                            </div>
                                                                                            <div class="mb-2">
                                                                                                <label class="form-label"
                                                                                                    for="start">Total
                                                                                                    Days</label>
                                                                                                <div class="input-group">
                                                                                                    <input
                                                                                                        class="form-control bg-light total-days-perdiem"
                                                                                                        id="total_days_bt_perdiem_0"
                                                                                                        name="total_days_bt_perdiem[]"
                                                                                                        type="text"
                                                                                                        min="0"
                                                                                                        readonly>
                                                                                                    <div
                                                                                                        class="input-group-append">
                                                                                                        <span
                                                                                                            class="input-group-text">days</span>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="mb-2">
                                                                                                <label class="form-label"
                                                                                                    for="name">Location
                                                                                                    Agency</label>
                                                                                                <select
                                                                                                    class="form-control select2 location-select"
                                                                                                    name="location_bt_perdiem[]"
                                                                                                    disabled>
                                                                                                    <option value="">
                                                                                                        Select
                                                                                                        location...
                                                                                                    </option>
                                                                                                    @foreach ($locations as $location)
                                                                                                        <option
                                                                                                            value="{{ $location->area }}">
                                                                                                            {{ $location->area . ' (' . $location->company_name . ')' }}
                                                                                                        </option>
                                                                                                    @endforeach
                                                                                                    <option value="Others">
                                                                                                        Others
                                                                                                    </option>
                                                                                                </select>
                                                                                                <br>

                                                                                                <input type="text"
                                                                                                    name="other_location_bt_perdiem[]"
                                                                                                    class="form-control other-location"
                                                                                                    placeholder="Other Location"
                                                                                                    style="display:none;"
                                                                                                    disabled>
                                                                                            </div>
                                                                                            <div class="mb-2">
                                                                                                <label class="form-label"
                                                                                                    for="name">Company
                                                                                                    Code</label>
                                                                                                <select
                                                                                                    class="form-control select2"
                                                                                                    id="companyFilter_0"
                                                                                                    name="company_bt_perdiem[]"
                                                                                                    disabled>
                                                                                                    <option value="">
                                                                                                        --- Select
                                                                                                        Company ---
                                                                                                    </option>
                                                                                                    @foreach ($companies as $company)
                                                                                                        <option
                                                                                                            value="{{ $company->contribution_level_code }}">
                                                                                                            {{ $company->contribution_level . ' (' . $company->contribution_level_code . ')' }}
                                                                                                        </option>
                                                                                                    @endforeach
                                                                                                </select>
                                                                                            </div>
                                                                                            <div class="mb-2">
                                                                                                <label
                                                                                                    class="form-label">Amount</label>
                                                                                            </div>
                                                                                            <div class="input-group mb-3">
                                                                                                <div
                                                                                                    class="input-group-append">
                                                                                                    <span
                                                                                                        class="input-group-text">Rp</span>
                                                                                                </div>
                                                                                                <input class="form-control"
                                                                                                    name="nominal_bt_perdiem[]"
                                                                                                    id="nominal_bt_perdiem_0"
                                                                                                    type="text"
                                                                                                    min="0"
                                                                                                    disabled>
                                                                                            </div>
                                                                                            <hr
                                                                                                class="border border-primary border-1 opacity-50">
                                                                                        @endif

                                                                                        <div class="mb-2">
                                                                                            <label class="form-label">Total
                                                                                                Perdiem</label>
                                                                                            <div class="input-group">
                                                                                                <div
                                                                                                    class="input-group-append">
                                                                                                    <span
                                                                                                        class="input-group-text">Rp</span>
                                                                                                </div>
                                                                                                @php
                                                                                                    // Format totalPerdiem value if available
                                                                                                    $formattedTotalPerdiem = number_format(
                                                                                                        $totalPerdiem ??
                                                                                                            0,
                                                                                                        0,
                                                                                                        ',',
                                                                                                        '.',
                                                                                                    );
                                                                                                @endphp
                                                                                                <input
                                                                                                    class="form-control bg-light"
                                                                                                    name="total_bt_perdiem_1[]"
                                                                                                    id="total_bt_perdiem[]"
                                                                                                    type="text"
                                                                                                    min="0"
                                                                                                    value="{{ $formattedTotalPerdiem ?? 0 }}"
                                                                                                    readonly>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <!-- Button and Card for Transport -->
                                                                <div class="card-body text-center">
                                                                    <button type="button" style="width: 60%" disabled
                                                                        id="toggle-bt-transport-deklarasi"
                                                                        class="btn btn-primary mt-3" data-state="false">
                                                                        Transport</button>
                                                                </div>
                                                                <div id="transport-card" class="card-body"
                                                                    style="display: none;">
                                                                    <div class="accordion" id="accordionTransport">
                                                                        <div class="accordion-item">
                                                                            <h2 class="accordion-header"
                                                                                id="headingTransport">
                                                                                <button class="accordion-button fw-medium"
                                                                                    type="button"
                                                                                    data-bs-toggle="collapse"
                                                                                    data-bs-target="#collapseTransport"
                                                                                    aria-expanded="true"
                                                                                    aria-controls="collapseTransport">
                                                                                    Rencana Transport
                                                                                </button>
                                                                            </h2>
                                                                            <div id="collapseTransport"
                                                                                class="accordion-collapse collapse show"
                                                                                aria-labelledby="headingTransport">
                                                                                <div class="accordion-body">
                                                                                    <div id="form-container-bt-transport-deklarasi">
                                                                                        @php
                                                                                            // Provide default empty array if detail_transport is not set
                                                                                            $detailTransport =
                                                                                                $caDetail[
                                                                                                    'detail_transport'
                                                                                                ] ?? [];

                                                                                            // Calculate total transport cost with default values
                                                                                            $totalTransport = array_reduce(
                                                                                                $detailTransport,
                                                                                                function (
                                                                                                    $carry,
                                                                                                    $item,
                                                                                                ) {
                                                                                                    return $carry +
                                                                                                        (int) ($item[
                                                                                                            'nominal'
                                                                                                        ] ?? 0);
                                                                                                },
                                                                                                0,
                                                                                            );
                                                                                        @endphp
                                                                                        @if (!empty($detailTransport))
                                                                                            @foreach ($detailTransport as $index => $transport)
                                                                                                <div class="mb-2">
                                                                                                    <label
                                                                                                        class="form-label">Tanggal
                                                                                                        Transport</label>
                                                                                                    <input type="date"
                                                                                                        name="tanggal_bt_transport[]"
                                                                                                        class="form-control"
                                                                                                        placeholder="mm/dd/yyyy"
                                                                                                        value="{{ old('tanggal_bt_transport.' . $index, $transport['tanggal'] ?? '') }}"
                                                                                                        disabled>
                                                                                                </div>
                                                                                                <div class="mb-2">
                                                                                                    <label
                                                                                                        class="form-label"
                                                                                                        for="name">Company
                                                                                                        Code</label>
                                                                                                    <select
                                                                                                        class="form-control select2"
                                                                                                        id="companyFilter_{{ $index }}"
                                                                                                        name="company_bt_transport[]"
                                                                                                        disabled>
                                                                                                        <option
                                                                                                            value="">
                                                                                                            Select
                                                                                                            Company...
                                                                                                        </option>
                                                                                                        @foreach ($companies as $company)
                                                                                                            <option
                                                                                                                value="{{ $company->contribution_level_code }}"
                                                                                                                {{ ($transport['company_code'] ?? '') == $company->contribution_level_code ? 'selected' : '' }}>
                                                                                                                {{ $company->contribution_level . ' (' . $company->contribution_level_code . ')' }}
                                                                                                            </option>
                                                                                                        @endforeach
                                                                                                    </select>
                                                                                                </div>
                                                                                                <div class="mb-2">
                                                                                                    <label
                                                                                                        class="form-label">Keterangan</label>
                                                                                                    <textarea name="keterangan_bt_transport[]" class="form-control" placeholder="Write your information here..."
                                                                                                        disabled>{{ old('keterangan_bt_transport.' . $index, $transport['keterangan'] ?? '') }}</textarea>
                                                                                                </div>
                                                                                                <div class="mb-2">
                                                                                                    <label
                                                                                                        class="form-label">Amount</label>
                                                                                                </div>
                                                                                                <div
                                                                                                    class="input-group mb-3">
                                                                                                    <div
                                                                                                        class="input-group-append">
                                                                                                        <span
                                                                                                            class="input-group-text">Rp</span>
                                                                                                    </div>
                                                                                                    @php
                                                                                                        // Format the nominal value if available
                                                                                                        $formattedNominalTransport = number_format(
                                                                                                            old(
                                                                                                                'nominal_bt_transport.' .
                                                                                                                    $index,
                                                                                                                $transport[
                                                                                                                    'nominal'
                                                                                                                ] ??
                                                                                                                    '0',
                                                                                                            ),
                                                                                                            0,
                                                                                                            ',',
                                                                                                            '.',
                                                                                                        );
                                                                                                    @endphp
                                                                                                    <input
                                                                                                        class="form-control"
                                                                                                        name="nominal_bt_transport_1[]"
                                                                                                        id="nominal_bt_transport_{{ $index }}"
                                                                                                        type="text"
                                                                                                        min="0"
                                                                                                        value="{{ $formattedNominalTransport }}"
                                                                                                        disabled>
                                                                                                </div>

                                                                                                <hr
                                                                                                    class="border border-primary border-1 opacity-50">
                                                                                            @endforeach
                                                                                        @else
                                                                                            <!-- Default empty fields if no data is available -->
                                                                                            <div class="mb-2">
                                                                                                <label
                                                                                                    class="form-label">Tanggal
                                                                                                    Transport</label>
                                                                                                <input type="date"
                                                                                                    name="tanggal_bt_transport[]"
                                                                                                    class="form-control"
                                                                                                    placeholder="mm/dd/yyyy"
                                                                                                    disabled>
                                                                                            </div>
                                                                                            <div class="mb-2">
                                                                                                <label class="form-label"
                                                                                                    for="name">Company
                                                                                                    Code</label>
                                                                                                <select
                                                                                                    class="form-control select2"
                                                                                                    id="companyFilter_0"
                                                                                                    name="company_bt_transport[]"
                                                                                                    disabled>
                                                                                                    <option value="">
                                                                                                        Select
                                                                                                        Company...
                                                                                                    </option>
                                                                                                    @foreach ($companies as $company)
                                                                                                        <option
                                                                                                            value="{{ $company->contribution_level_code }}">
                                                                                                            {{ $company->contribution_level . ' (' . $company->contribution_level_code . ')' }}
                                                                                                        </option>
                                                                                                    @endforeach
                                                                                                </select>
                                                                                            </div>
                                                                                            <div class="mb-2">
                                                                                                <label
                                                                                                    class="form-label">Keterangan</label>
                                                                                                <textarea name="keterangan_bt_transport[]" class="form-control" placeholder="Write your information here..."
                                                                                                    disabled></textarea>
                                                                                            </div>
                                                                                            <div class="mb-2">
                                                                                                <label
                                                                                                    class="form-label">Amount</label>
                                                                                            </div>
                                                                                            <div class="input-group mb-3">
                                                                                                <div
                                                                                                    class="input-group-append">
                                                                                                    <span
                                                                                                        class="input-group-text">Rp</span>
                                                                                                </div>
                                                                                                <input class="form-control"
                                                                                                    name="nominal_bt_transport_1[]"
                                                                                                    id="nominal_bt_transport_0"
                                                                                                    type="text"
                                                                                                    min="0"
                                                                                                    disabled>
                                                                                            </div>

                                                                                            <hr
                                                                                                class="border border-primary border-1 opacity-50">
                                                                                        @endif

                                                                                        <div class="mb-2">
                                                                                            <label class="form-label">Total
                                                                                                Transport</label>
                                                                                            <div class="input-group">
                                                                                                <div
                                                                                                    class="input-group-append">
                                                                                                    <span
                                                                                                        class="input-group-text">Rp</span>
                                                                                                </div>
                                                                                                @php
                                                                                                    // Format the total transport value if available
                                                                                                    $formattedTotalTransport = number_format(
                                                                                                        $totalTransport ??
                                                                                                            0,
                                                                                                        0,
                                                                                                        ',',
                                                                                                        '.',
                                                                                                    );
                                                                                                @endphp
                                                                                                <input
                                                                                                    class="form-control bg-light"
                                                                                                    name="total_bt_transport_1[]"
                                                                                                    id="total_bt_transport[]"
                                                                                                    type="text"
                                                                                                    min="0"
                                                                                                    value="{{ $formattedTotalTransport ?? 0 }}"
                                                                                                    readonly>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <!-- Button and Card for Penginapan -->
                                                                <div class="card-body text-center">
                                                                    <button type="button" style="width: 60%" disabled
                                                                        id="toggle-bt-penginapan-deklarasi"
                                                                        class="btn btn-primary mt-3" data-state="false">
                                                                        Accommodation</button>
                                                                </div>
                                                                <div id="penginapan-card" class="card-body"
                                                                    style="display: none;">
                                                                    <div class="accordion" id="accordionPenginapan">
                                                                        <div class="accordion-item">
                                                                            <h2 class="accordion-header"
                                                                                id="headingPenginapan">
                                                                                <button class="accordion-button fw-medium"
                                                                                    type="button"
                                                                                    data-bs-toggle="collapse"
                                                                                    data-bs-target="#collapsePenginapan"
                                                                                    aria-expanded="true"
                                                                                    aria-controls="collapsePenginapan">
                                                                                    Rencana Penginapan
                                                                                </button>
                                                                            </h2>
                                                                            <div id="collapsePenginapan"
                                                                                class="accordion-collapse collapse show"
                                                                                aria-labelledby="headingPenginapan">
                                                                                <div class="accordion-body">
                                                                                    <div id="form-container-bt-penginapan-deklarasi">
                                                                                        @php
                                                                                            // Default empty array if 'detail_penginapan' is not set
                                                                                            $penginapan =
                                                                                                $caDetail[
                                                                                                    'detail_penginapan'
                                                                                                ] ?? [];

                                                                                            // Calculate total penginapan cost
                                                                                            $totalPenginapanCost = array_reduce(
                                                                                                $penginapan,
                                                                                                function (
                                                                                                    $carry,
                                                                                                    $item,
                                                                                                ) {
                                                                                                    return $carry +
                                                                                                        (int) ($item[
                                                                                                            'nominal'
                                                                                                        ] ?? 0);
                                                                                                },
                                                                                                0,
                                                                                            );
                                                                                        @endphp

                                                                                        <!-- Form for Penginapan Details -->
                                                                                        <div
                                                                                            id="form-container-bt-penginapan-deklarasi">
                                                                                            @if (!empty($penginapan))
                                                                                                @foreach ($penginapan as $index => $item)
                                                                                                    <div class="mb-2">
                                                                                                        <label
                                                                                                            class="form-label">Start
                                                                                                            Penginapan</label>
                                                                                                        <input
                                                                                                            type="date"
                                                                                                            name="start_bt_penginapan[]"
                                                                                                            class="form-control start-penginapan"
                                                                                                            placeholder="mm/dd/yyyy"
                                                                                                            value="{{ old('start_bt_penginapan.' . $index, $item['start_date'] ?? '') }}"
                                                                                                            disabled>
                                                                                                    </div>
                                                                                                    <div class="mb-2">
                                                                                                        <label
                                                                                                            class="form-label">End
                                                                                                            Penginapan</label>
                                                                                                        <input
                                                                                                            type="date"
                                                                                                            name="end_bt_penginapan[]"
                                                                                                            class="form-control end-penginapan"
                                                                                                            placeholder="mm/dd/yyyy"
                                                                                                            value="{{ old('end_bt_penginapan.' . $index, $item['end_date'] ?? '') }}"
                                                                                                            disabled>
                                                                                                    </div>
                                                                                                    <div class="mb-2">
                                                                                                        <label
                                                                                                            class="form-label"
                                                                                                            for="start">Total
                                                                                                            Days</label>
                                                                                                        <div
                                                                                                            class="input-group">
                                                                                                            <input
                                                                                                                class="form-control bg-light total-days-penginapan"
                                                                                                                id="total_days_bt_penginapan_{{ $index }}"
                                                                                                                name="total_days_bt_penginapan[]"
                                                                                                                type="text"
                                                                                                                min="0"
                                                                                                                value="{{ old('total_days_bt_penginapan.' . $index, $item['total_days'] ?? '0') }}"
                                                                                                                readonly>
                                                                                                            <div
                                                                                                                class="input-group-append">
                                                                                                                <span
                                                                                                                    class="input-group-text">days</span>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <div class="mb-2">
                                                                                                        <label
                                                                                                            class="form-label"
                                                                                                            for="name">Hotel
                                                                                                            Name</label>
                                                                                                        <input
                                                                                                            type="text"
                                                                                                            name="hotel_name_bt_penginapan[]"
                                                                                                            class="form-control"
                                                                                                            placeholder="ex: Westin"
                                                                                                            value="{{ old('hotel_name_bt_penginapan.' . $index, $item['hotel_name'] ?? '') }}"
                                                                                                            disabled>
                                                                                                    </div>
                                                                                                    <div class="mb-2">
                                                                                                        <label
                                                                                                            class="form-label"
                                                                                                            for="name">Company
                                                                                                            Code</label>
                                                                                                        <select
                                                                                                            class="form-control select2"
                                                                                                            id="companyFilter_{{ $index }}"
                                                                                                            name="company_bt_penginapan[]"
                                                                                                            disabled>
                                                                                                            <option
                                                                                                                value="">
                                                                                                                Select
                                                                                                                Company...
                                                                                                            </option>
                                                                                                            @foreach ($companies as $company)
                                                                                                                <option
                                                                                                                    value="{{ $company->contribution_level_code }}"
                                                                                                                    {{ ($item['company_code'] ?? '') == $company->contribution_level_code ? 'selected' : '' }}>
                                                                                                                    {{ $company->contribution_level . ' (' . $company->contribution_level_code . ')' }}
                                                                                                                </option>
                                                                                                            @endforeach
                                                                                                        </select>
                                                                                                    </div>
                                                                                                    <div class="mb-2">
                                                                                                        <label
                                                                                                            class="form-label">Amount</label>
                                                                                                    </div>
                                                                                                    <div
                                                                                                        class="input-group mb-3">
                                                                                                        <div
                                                                                                            class="input-group-append">
                                                                                                            <span
                                                                                                                class="input-group-text">Rp</span>
                                                                                                        </div>
                                                                                                        @php
                                                                                                            // Format the nominal value for each item in the penginapan section
                                                                                                            $formattedNominalPenginapan = number_format(
                                                                                                                old(
                                                                                                                    'nominal_bt_penginapan.' .
                                                                                                                        $index,
                                                                                                                    $item[
                                                                                                                        'nominal'
                                                                                                                    ] ??
                                                                                                                        '0',
                                                                                                                ),
                                                                                                                0,
                                                                                                                ',',
                                                                                                                '.',
                                                                                                            );
                                                                                                        @endphp
                                                                                                        <input
                                                                                                            class="form-control"
                                                                                                            name="nominal_bt_penginapan_1[]"
                                                                                                            id="nominal_bt_penginapan_{{ $index }}"
                                                                                                            type="text"
                                                                                                            min="0"
                                                                                                            value="{{ $formattedNominalPenginapan }}"
                                                                                                            disabled>
                                                                                                    </div>

                                                                                                    <hr
                                                                                                        class="border border-primary border-1 opacity-50">
                                                                                                @endforeach
                                                                                            @else
                                                                                                <!-- Default empty fields if no data is available -->
                                                                                                <div class="mb-2">
                                                                                                    <label
                                                                                                        class="form-label">Start
                                                                                                        Penginapan</label>
                                                                                                    <input type="date"
                                                                                                        name="start_bt_penginapan[]"
                                                                                                        class="form-control start-penginapan"
                                                                                                        placeholder="mm/dd/yyyy"
                                                                                                        disabled>
                                                                                                </div>
                                                                                                <div class="mb-2">
                                                                                                    <label
                                                                                                        class="form-label">End
                                                                                                        Penginapan</label>
                                                                                                    <input type="date"
                                                                                                        name="end_bt_penginapan[]"
                                                                                                        class="form-control end-penginapan"
                                                                                                        placeholder="mm/dd/yyyy"
                                                                                                        disabled>
                                                                                                </div>
                                                                                                <div class="mb-2">
                                                                                                    <label
                                                                                                        class="form-label"
                                                                                                        for="start">Total
                                                                                                        Days</label>
                                                                                                    <div
                                                                                                        class="input-group">
                                                                                                        <input
                                                                                                            class="form-control bg-light total-days-penginapan"
                                                                                                            id="total_days_bt_penginapan_0"
                                                                                                            name="total_days_bt_penginapan[]"
                                                                                                            type="text"
                                                                                                            min="0"
                                                                                                            readonly>
                                                                                                        <div
                                                                                                            class="input-group-append">
                                                                                                            <span
                                                                                                                class="input-group-text">days</span>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="mb-2">
                                                                                                    <label
                                                                                                        class="form-label"
                                                                                                        for="name">Hotel
                                                                                                        Name</label>
                                                                                                    <input type="text"
                                                                                                        name="hotel_name_bt_penginapan[]"
                                                                                                        class="form-control"
                                                                                                        placeholder="ex: Westin"
                                                                                                        disabled>
                                                                                                </div>
                                                                                                <div class="mb-2">
                                                                                                    <label
                                                                                                        class="form-label"
                                                                                                        for="name">Company
                                                                                                        Code</label>
                                                                                                    <select
                                                                                                        class="form-control select2"
                                                                                                        id="companyFilter_0"
                                                                                                        name="company_bt_penginapan[]"
                                                                                                        disabled>
                                                                                                        <option
                                                                                                            value="">
                                                                                                            Select
                                                                                                            Company...
                                                                                                        </option>
                                                                                                        @foreach ($companies as $company)
                                                                                                            <option
                                                                                                                value="{{ $company->contribution_level_code }}">
                                                                                                                {{ $company->contribution_level . ' (' . $company->contribution_level_code . ')' }}
                                                                                                            </option>
                                                                                                        @endforeach
                                                                                                    </select>
                                                                                                </div>
                                                                                                <div class="mb-2">
                                                                                                    <label
                                                                                                        class="form-label">Amount</label>
                                                                                                </div>
                                                                                                <div
                                                                                                    class="input-group mb-3">
                                                                                                    <div
                                                                                                        class="input-group-append">
                                                                                                        <span
                                                                                                            class="input-group-text">Rp</span>
                                                                                                    </div>
                                                                                                    <input
                                                                                                        class="form-control"
                                                                                                        name="nominal_bt_penginapan[]"
                                                                                                        id="nominal_bt_penginapan_0"
                                                                                                        type="text"
                                                                                                        min="0"
                                                                                                        disabled>
                                                                                                </div>

                                                                                                <hr
                                                                                                    class="border border-primary border-1 opacity-50">
                                                                                            @endif

                                                                                            <div class="mb-2">
                                                                                                <label
                                                                                                    class="form-label">Total
                                                                                                    Penginapan</label>
                                                                                                <div class="input-group">
                                                                                                    <div
                                                                                                        class="input-group-append">
                                                                                                        <span
                                                                                                            class="input-group-text">Rp</span>
                                                                                                    </div>
                                                                                                    @php
                                                                                                        // Format the total penginapan cost
                                                                                                        $formattedTotalPenginapanCost = number_format(
                                                                                                            $totalPenginapanCost,
                                                                                                            0,
                                                                                                            ',',
                                                                                                            '.',
                                                                                                        );
                                                                                                    @endphp
                                                                                                    <input
                                                                                                        class="form-control bg-light"
                                                                                                        name="total_bt_penginapan_1[]"
                                                                                                        id="total_bt_penginapan"
                                                                                                        type="text"
                                                                                                        min="0"
                                                                                                        value="{{ $formattedTotalPenginapanCost }}"
                                                                                                        readonly>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>


                                                                <!-- Button and Card for Lainnya -->
                                                                <div class="card-body text-center">
                                                                    <button type="button" style="width: 60%" disabled
                                                                        id="toggle-bt-lainnya-deklarasi"
                                                                        class="btn btn-primary mt-3" data-state="false">
                                                                        Others</button>
                                                                </div>
                                                                <div id="lainnya-card" class="card-body"
                                                                    style="display: none;">
                                                                    <div class="accordion" id="accordionLainnya">
                                                                        <div class="accordion-item">
                                                                            <h2 class="accordion-header"
                                                                                id="headingLainnya">
                                                                                <button class="accordion-button fw-medium"
                                                                                    type="button"
                                                                                    data-bs-toggle="collapse"
                                                                                    data-bs-target="#collapseLainnya"
                                                                                    aria-expanded="true"
                                                                                    aria-controls="collapseLainnya">
                                                                                    Rencana Lainnya
                                                                                </button>
                                                                            </h2>
                                                                            <div id="collapseLainnya"
                                                                                class="accordion-collapse collapse show"
                                                                                aria-labelledby="headingLainnya">
                                                                                <div class="accordion-body">
                                                                                    <div id="form-container-bt-lainnya-deklarasi">
                                                                                        @php
                                                                                            // Default empty array if 'detail_lainnya' is not set
                                                                                            $lainnya =
                                                                                                $caDetail[
                                                                                                    'detail_lainnya'
                                                                                                ] ?? [];

                                                                                            // Calculate total lainnya cost
                                                                                            $totalLainnyaCost = array_reduce(
                                                                                                $lainnya,
                                                                                                function (
                                                                                                    $carry,
                                                                                                    $item,
                                                                                                ) {
                                                                                                    return $carry +
                                                                                                        (int) ($item[
                                                                                                            'nominal'
                                                                                                        ] ?? 0);
                                                                                                },
                                                                                                0,
                                                                                            );
                                                                                        @endphp

                                                                                        <div
                                                                                            id="form-container-bt-lainnya-deklarasi">
                                                                                            @if (!empty($lainnya))
                                                                                                @foreach ($lainnya as $index => $lainnyaItem)
                                                                                                    <div
                                                                                                        class="lainnya-item">
                                                                                                        <div
                                                                                                            class="mb-2">
                                                                                                            <label
                                                                                                                class="form-label">Tanggal</label>
                                                                                                            <input
                                                                                                                type="date"
                                                                                                                name="tanggal_bt_lainnya[]"
                                                                                                                class="form-control"
                                                                                                                value="{{ old('tanggal_bt_lainnya.' . $index, $lainnyaItem['tanggal'] ?? '') }}"
                                                                                                                placeholder="mm/dd/yyyy"
                                                                                                                disabled>
                                                                                                        </div>
                                                                                                        <div
                                                                                                            class="mb-2">
                                                                                                            <label
                                                                                                                class="form-label">Keterangan</label>
                                                                                                            <textarea name="keterangan_bt_lainnya[]" class="form-control" placeholder="Write your other purposes ..." disabled>{{ old('keterangan_bt_lainnya.' . $index, $lainnyaItem['keterangan'] ?? '') }}</textarea>
                                                                                                        </div>
                                                                                                        <div
                                                                                                            class="mb-2">
                                                                                                            <label
                                                                                                                class="form-label">Amount</label>
                                                                                                            <div
                                                                                                                class="input-group mb-3">
                                                                                                                <div
                                                                                                                    class="input-group-append">
                                                                                                                    <span
                                                                                                                        class="input-group-text">Rp</span>
                                                                                                                </div>
                                                                                                                @php
                                                                                                                    // Format the nominal value for 'lainnya' items
                                                                                                                    $formattedNominalLainnya = number_format(
                                                                                                                        old(
                                                                                                                            'nominal_bt_lainnya.' .
                                                                                                                                $index,
                                                                                                                            $lainnyaItem[
                                                                                                                                'nominal'
                                                                                                                            ] ??
                                                                                                                                '0',
                                                                                                                        ),
                                                                                                                        0,
                                                                                                                        ',',
                                                                                                                        '.',
                                                                                                                    );
                                                                                                                @endphp
                                                                                                                <input
                                                                                                                    class="form-control nominal-lainnya"
                                                                                                                    name="nominal_bt_lainnya_1[]"
                                                                                                                    type="text"
                                                                                                                    min="0"
                                                                                                                    value="{{ $formattedNominalLainnya }}"
                                                                                                                    disabled>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                        <hr
                                                                                                            class="border border-primary border-1 opacity-50">
                                                                                                    </div>
                                                                                                @endforeach
                                                                                            @else
                                                                                                <div class="lainnya-item">
                                                                                                    <div class="mb-2">
                                                                                                        <label
                                                                                                            class="form-label">Tanggal</label>
                                                                                                        <input
                                                                                                            type="date"
                                                                                                            name="tanggal_bt_lainnya[]"
                                                                                                            class="form-control"
                                                                                                            placeholder="mm/dd/yyyy"
                                                                                                            disabled>
                                                                                                    </div>
                                                                                                    <div class="mb-2">
                                                                                                        <label
                                                                                                            class="form-label">Keterangan</label>
                                                                                                        <textarea name="keterangan_bt_lainnya[]" class="form-control" placeholder="Write your other purposes ..." disabled></textarea>
                                                                                                    </div>
                                                                                                    <div class="mb-2">
                                                                                                        <label
                                                                                                            class="form-label">Amount</label>
                                                                                                        <div
                                                                                                            class="input-group mb-3">
                                                                                                            <div
                                                                                                                class="input-group-append">
                                                                                                                <span
                                                                                                                    class="input-group-text">Rp</span>
                                                                                                            </div>
                                                                                                            <input
                                                                                                                class="form-control nominal-lainnya"
                                                                                                                name="nominal_bt_lainnya[]"
                                                                                                                type="text"
                                                                                                                min="0"
                                                                                                                value="0"
                                                                                                                disabled>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <hr
                                                                                                        class="border border-primary border-1 opacity-50">
                                                                                                </div>
                                                                                            @endif

                                                                                            <div class="mb-2">
                                                                                                <label
                                                                                                    class="form-label">Total
                                                                                                    Lainnya</label>
                                                                                                <div class="input-group">
                                                                                                    <div
                                                                                                        class="input-group-append">
                                                                                                        <span
                                                                                                            class="input-group-text">Rp</span>
                                                                                                    </div>
                                                                                                    @php
                                                                                                        $formattedTotalLainnya = number_format(
                                                                                                            old(
                                                                                                                'total_bt_lainnya.' .
                                                                                                                    $index,
                                                                                                                $lainnyaItem[
                                                                                                                    'nominal'
                                                                                                                ] ??
                                                                                                                    '0',
                                                                                                            ),
                                                                                                            0,
                                                                                                            ',',
                                                                                                            '.',
                                                                                                        );
                                                                                                    @endphp
                                                                                                    <input
                                                                                                        class="form-control bg-light"
                                                                                                        name="total_bt_lainnya_1[]"
                                                                                                        id="total_bt_lainnya"
                                                                                                        type="text"
                                                                                                        min="0"
                                                                                                        value="{{ $formattedTotalLainnya }}"
                                                                                                        readonly>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>

                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                {{-- </div> --}}
                                                                {{-- </div> --}}
                                                            </div>
                                                        </div>
                                                        <br>
                                                        @php
                                                            // Provide default empty arrays if any section is not set
                                                            $detailPerdiem = $caDetail['detail_perdiem'] ?? [];
                                                            $detailTransport = $caDetail['detail_transport'] ?? [];
                                                            $detailPenginapan = $caDetail['detail_penginapan'] ?? [];
                                                            $detailLainnya = $caDetail['detail_lainnya'] ?? [];

                                                            // Calculate total costs for each section
                                                            $totalPerdiem = array_reduce(
                                                                $detailPerdiem,
                                                                function ($carry, $item) {
                                                                    return $carry + (int) ($item['nominal'] ?? 0);
                                                                },
                                                                0,
                                                            );

                                                            $totalTransport = array_reduce(
                                                                $detailTransport,
                                                                function ($carry, $item) {
                                                                    return $carry + (int) ($item['nominal'] ?? 0);
                                                                },
                                                                0,
                                                            );

                                                            $totalPenginapan = array_reduce(
                                                                $detailPenginapan,
                                                                function ($carry, $item) {
                                                                    return $carry + (int) ($item['nominal'] ?? 0);
                                                                },
                                                                0,
                                                            );

                                                            $totalLainnya = array_reduce(
                                                                $detailLainnya,
                                                                function ($carry, $item) {
                                                                    return $carry + (int) ($item['nominal'] ?? 0);
                                                                },
                                                                0,
                                                            );

                                                            // Total Cash Advanced
                                                            $totalCashAdvanced =
                                                                $totalPerdiem +
                                                                $totalTransport +
                                                                $totalPenginapan +
                                                                $totalLainnya;
                                                                $formattedTotalCashAdvanced = number_format(
                                                                $totalCashAdvanced,
                                                                0,
                                                                ',',
                                                                '.',
                                                            );
                                                        @endphp
                                                        <div class="col-md-12 mb-2">
                                                            <label class="form-label">Total Cash Advanced</label>
                                                            <div class="input-group">
                                                                <div class="input-group-append">
                                                                    <span class="input-group-text">Rp</span>
                                                                </div>
                                                                <input class="form-control bg-light" name="totalca_1"
                                                                    id="totalca" type="text" min="0"
                                                                    value="{{ $formattedTotalCashAdvanced }}" readonly>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                {{-- 2ND FORM --}}
                                <div class="col-md-6">
                                    <div class="table-responsive-sm">
                                        <div class="d-flex flex-column gap-2">
                                            <div class="text-bg-primary p-2"
                                                style="text-align:center; border-radius:4px;">Cash Advanced Declaration
                                            </div>
                                            <div class="row" id="ca_bt" style="">
                                                <div class="col-md-12">
                                                    <div class="table-responsive-sm">
                                                        <div class="d-flex flex-row gap-2">
                                                            <div class="card">
                                                                <div class="card-body text-center">
                                                                    <button type="button" style="width: 60%" disabled
                                                                        id="toggle-bt-perdiem-2"
                                                                        class="btn btn-primary mt-3" data-state="false">
                                                                        Perdiem</button>
                                                                </div>
                                                                <div id="perdiem-card-2" class="card-body"
                                                                    style="display: none;">
                                                                    <div class="accordion" id="accordionPerdiem">
                                                                        <div class="accordion-item">
                                                                            <h2 class="accordion-header"
                                                                                id="enter-headingOne">
                                                                                <button class="accordion-button fw-medium"
                                                                                    type="button"
                                                                                    data-bs-toggle="collapse"
                                                                                    data-bs-target="#enter-collapseOne"
                                                                                    aria-expanded="true"
                                                                                    aria-controls="enter-collapseOne">
                                                                                    Perdiem Plan
                                                                                </button>
                                                                            </h2>
                                                                            <div id="enter-collapseOne"
                                                                                class="accordion-collapse show"
                                                                                aria-labelledby="enter-headingOne">
                                                                                <div class="accordion-body">
                                                                                    <div id="form-container-bt-perdiem">
                                                                                        @php
                                                                                            $detailPerdiem2 =
                                                                                                $declareCa[
                                                                                                    'detail_perdiem'
                                                                                                ] ?? [];
                                                                                            $detailTransport2 =
                                                                                                $declareCa[
                                                                                                    'detail_transport'
                                                                                                ] ?? [];
                                                                                            $detailPenginapan2 =
                                                                                                $declareCa[
                                                                                                    'detail_penginapan'
                                                                                                ] ?? [];
                                                                                            $detailLainnya2 =
                                                                                                $declareCa[
                                                                                                    'detail_lainnya'
                                                                                                ] ?? [];
                                                                                            $totalPerdiem2 = array_reduce(
                                                                                                $detailPerdiem2,
                                                                                                function (
                                                                                                    $carry,
                                                                                                    $item,
                                                                                                ) {
                                                                                                    return $carry +
                                                                                                        (int) ($item[
                                                                                                            'nominal'
                                                                                                        ] ?? 0);
                                                                                                },
                                                                                                0,
                                                                                            );

                                                                                            $totalTransport2 = array_reduce(
                                                                                                $detailTransport2,
                                                                                                function (
                                                                                                    $carry,
                                                                                                    $item,
                                                                                                ) {
                                                                                                    return $carry +
                                                                                                        (int) ($item[
                                                                                                            'nominal'
                                                                                                        ] ?? 0);
                                                                                                },
                                                                                                0,
                                                                                            );

                                                                                            $totalPenginapan2 = array_reduce(
                                                                                                $detailPenginapan2,
                                                                                                function (
                                                                                                    $carry,
                                                                                                    $item,
                                                                                                ) {
                                                                                                    return $carry +
                                                                                                        (int) ($item[
                                                                                                            'nominal'
                                                                                                        ] ?? 0);
                                                                                                },
                                                                                                0,
                                                                                            );

                                                                                            $totalLainnya2 = array_reduce(
                                                                                                $detailLainnya2,
                                                                                                function (
                                                                                                    $carry,
                                                                                                    $item,
                                                                                                ) {
                                                                                                    return $carry +
                                                                                                        (int) ($item[
                                                                                                            'nominal'
                                                                                                        ] ?? 0);
                                                                                                },
                                                                                                0,
                                                                                            );
                                                                                        @endphp
                                                                                        @if (!empty($detailPerdiem2))
                                                                                            @foreach ($detailPerdiem2 as $index => $perdiem2)
                                                                                                <div class="mb-2">
                                                                                                    <label
                                                                                                        class="form-label">Start
                                                                                                        Perdiem</label>
                                                                                                    <input type="date"
                                                                                                        name="start_bt_perdiem[]"
                                                                                                        class="form-control start-perdiem"
                                                                                                        value="{{ old('start_bt_perdiem.' . $index, $perdiem2['start_date'] ?? '') }}">
                                                                                                </div>
                                                                                                <div class="mb-2">
                                                                                                    <label
                                                                                                        class="form-label">End
                                                                                                        Perdiem</label>
                                                                                                    <input type="date"
                                                                                                        name="end_bt_perdiem[]"
                                                                                                        class="form-control end-perdiem"
                                                                                                        value="{{ old('end_bt_perdiem.' . $index, $perdiem2['end_date'] ?? '') }}">
                                                                                                </div>
                                                                                                <div class="mb-2">
                                                                                                    <label
                                                                                                        class="form-label"
                                                                                                        for="start">Total
                                                                                                        Days</label>
                                                                                                    <div
                                                                                                        class="input-group">
                                                                                                        <input
                                                                                                            class="form-control bg-light total-days-perdiem"
                                                                                                            id="total_days_bt_perdiem_{{ $index }}"
                                                                                                            name="total_days_bt_perdiem[]"
                                                                                                            type="text"
                                                                                                            min="0"
                                                                                                            value="{{ old('total_days_bt_perdiem.' . $index, $perdiem2['total_days'] ?? '') }}"
                                                                                                            readonly>
                                                                                                        <div
                                                                                                            class="input-group-append">
                                                                                                            <span
                                                                                                                class="input-group-text">days</span>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="mb-2">
                                                                                                    <label
                                                                                                        class="form-label"
                                                                                                        for="name">Location
                                                                                                        Agency</label>
                                                                                                    <select
                                                                                                        class="form-control select2 location-select"
                                                                                                        name="location_bt_perdiem[]">
                                                                                                        <option
                                                                                                            value="">
                                                                                                            Select
                                                                                                            location...
                                                                                                        </option>
                                                                                                        @foreach ($locations as $location)
                                                                                                            <option
                                                                                                                value="{{ $location->area }}"
                                                                                                                {{ ($perdiem['location'] ?? '') == $location->area ? 'selected' : '' }}>
                                                                                                                {{ $location->area . ' (' . $location->company_name . ')' }}
                                                                                                            </option>
                                                                                                        @endforeach
                                                                                                        <option
                                                                                                            value="Others"
                                                                                                            {{ ($perdiem2['location'] ?? '') == 'Others' ? 'selected' : '' }}>
                                                                                                            Others
                                                                                                        </option>
                                                                                                    </select>
                                                                                                    <br>
                                                                                                    <input type="text"
                                                                                                        name="other_location_bt_perdiem[]"
                                                                                                        class="form-control other-location"
                                                                                                        placeholder="Other Location"
                                                                                                        value="{{ old('other_location_bt_perdiem.' . $index, $perdiem2['other_location'] ?? '') }}"
                                                                                                        style="{{ ($perdiem2['location'] ?? '') == 'Others' ? 'display:block;' : 'display:none;' }}">
                                                                                                </div>
                                                                                                <div class="mb-2">
                                                                                                    <label
                                                                                                        class="form-label"
                                                                                                        for="name">Company
                                                                                                        Code</label>
                                                                                                    <select
                                                                                                        class="form-control select2"
                                                                                                        id="companyFilter_{{ $index }}"
                                                                                                        name="company_bt_perdiem[]">
                                                                                                        <option
                                                                                                            value="">
                                                                                                            ---
                                                                                                            Select
                                                                                                            Company
                                                                                                            ---
                                                                                                        </option>
                                                                                                        @foreach ($companies as $company)
                                                                                                            <option
                                                                                                                value="{{ $company->contribution_level_code }}"
                                                                                                                {{ ($perdiem2['company_code'] ?? '') == $company->contribution_level_code ? 'selected' : '' }}>
                                                                                                                {{ $company->contribution_level . ' (' . $company->contribution_level_code . ')' }}
                                                                                                            </option>
                                                                                                        @endforeach
                                                                                                    </select>
                                                                                                </div>
                                                                                                <div class="mb-2">
                                                                                                    <label
                                                                                                        class="form-label">Amount</label>
                                                                                                </div>
                                                                                                <div
                                                                                                    class="input-group mb-3">
                                                                                                    <div
                                                                                                        class="input-group-append">
                                                                                                        <span
                                                                                                            class="input-group-text">Rp</span>
                                                                                                    </div>
                                                                                                    @php
                                                                                                        // Format nominal value if available
                                                                                                        $formattedNominal2 = isset(
                                                                                                            $perdiem2[
                                                                                                                'nominal'
                                                                                                            ],
                                                                                                        )
                                                                                                            ? number_format(
                                                                                                                $perdiem2[
                                                                                                                    'nominal'
                                                                                                                ],
                                                                                                                0,
                                                                                                                ',',
                                                                                                                '.',
                                                                                                            )
                                                                                                            : '';
                                                                                                    @endphp
                                                                                                    <input
                                                                                                        class="form-control"
                                                                                                        name="nominal_bt_perdiem[]"
                                                                                                        id="nominal_bt_perdiem_{{ $index }}"
                                                                                                        type="text"
                                                                                                        min="0"
                                                                                                        value="{{ old('nominal_bt_perdiem.' . $index, $formattedNominal2) }}">
                                                                                                </div>
                                                                                                <hr
                                                                                                    class="border border-primary border-1 opacity-50">
                                                                                            @endforeach
                                                                                        @else
                                                                                            <!-- Default empty fields if no data is available -->
                                                                                            <div class="mb-2">
                                                                                                <label
                                                                                                    class="form-label">Start
                                                                                                    Perdiem</label>
                                                                                                <input type="date"
                                                                                                    name="start_bt_perdiem[]"
                                                                                                    class="form-control start-perdiem">
                                                                                            </div>
                                                                                            <div class="mb-2">
                                                                                                <label
                                                                                                    class="form-label">End
                                                                                                    Perdiem</label>
                                                                                                <input type="date"
                                                                                                    name="end_bt_perdiem[]"
                                                                                                    class="form-control end-perdiem">
                                                                                            </div>
                                                                                            <div class="mb-2">
                                                                                                <label class="form-label"
                                                                                                    for="start">Total
                                                                                                    Days</label>
                                                                                                <div class="input-group">
                                                                                                    <input
                                                                                                        class="form-control bg-light total-days-perdiem"
                                                                                                        id="total_days_bt_perdiem_0"
                                                                                                        name="total_days_bt_perdiem[]"
                                                                                                        type="text"
                                                                                                        min="0"
                                                                                                        readonly>
                                                                                                    <div
                                                                                                        class="input-group-append">
                                                                                                        <span
                                                                                                            class="input-group-text">days</span>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="mb-2">
                                                                                                <label class="form-label"
                                                                                                    for="name">Location
                                                                                                    Agency</label>
                                                                                                <select
                                                                                                    class="form-control select2 location-select"
                                                                                                    name="location_bt_perdiem[]">
                                                                                                    <option value="">
                                                                                                        Select
                                                                                                        location...
                                                                                                    </option>
                                                                                                    @foreach ($locations as $location)
                                                                                                        <option
                                                                                                            value="{{ $location->area }}">
                                                                                                            {{ $location->area . ' (' . $location->company_name . ')' }}
                                                                                                        </option>
                                                                                                    @endforeach
                                                                                                    <option value="Others">
                                                                                                        Others
                                                                                                    </option>
                                                                                                </select>
                                                                                                <br>
                                                                                                <input type="text"
                                                                                                    name="other_location_bt_perdiem[]"
                                                                                                    class="form-control other-location"
                                                                                                    placeholder="Other Location"
                                                                                                    style="display:none;">
                                                                                            </div>
                                                                                            <div class="mb-2">
                                                                                                <label class="form-label"
                                                                                                    for="name">Company
                                                                                                    Code</label>
                                                                                                <select
                                                                                                    class="form-control select2"
                                                                                                    id="companyFilter_0"
                                                                                                    name="company_bt_perdiem[]">
                                                                                                    <option value="">
                                                                                                        --- Select
                                                                                                        Company ---
                                                                                                    </option>
                                                                                                    @foreach ($companies as $company)
                                                                                                        <option
                                                                                                            value="{{ $company->contribution_level_code }}">
                                                                                                            {{ $company->contribution_level . ' (' . $company->contribution_level_code . ')' }}
                                                                                                        </option>
                                                                                                    @endforeach
                                                                                                </select>
                                                                                            </div>
                                                                                            <div class="mb-2">
                                                                                                <label
                                                                                                    class="form-label">Amount</label>
                                                                                            </div>
                                                                                            <div class="input-group mb-3">
                                                                                                <div
                                                                                                    class="input-group-append">
                                                                                                    <span
                                                                                                        class="input-group-text">Rp</span>
                                                                                                </div>
                                                                                                <input class="form-control"
                                                                                                    name="nominal_bt_perdiem[]"
                                                                                                    id="nominal_bt_perdiem_0"
                                                                                                    type="text"
                                                                                                    min="0">
                                                                                            </div>
                                                                                            <hr
                                                                                                class="border border-primary border-1 opacity-50">
                                                                                        @endif

                                                                                        <div class="mb-2">
                                                                                            <label class="form-label">Total
                                                                                                Perdiem</label>
                                                                                            <div class="input-group">
                                                                                                <div
                                                                                                    class="input-group-append">
                                                                                                    <span
                                                                                                        class="input-group-text">Rp</span>
                                                                                                </div>
                                                                                                @php
                                                                                                    // Format totalPerdiem value if available
                                                                                                    $formattedTotalPerdiem2 = number_format(
                                                                                                        $totalPerdiem2 ??
                                                                                                            0,
                                                                                                        0,
                                                                                                        ',',
                                                                                                        '.',
                                                                                                    );
                                                                                                @endphp
                                                                                                <input
                                                                                                    class="form-control bg-light"
                                                                                                    name="total_bt_perdiem[]"
                                                                                                    id="total_bt_perdiem[]"
                                                                                                    type="text"
                                                                                                    min="0"
                                                                                                    value="{{ $formattedTotalPerdiem2 ?? 0 }}"
                                                                                                    readonly>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>

                                                                                    <button type="button"
                                                                                        id="add-more-bt-perdiem"
                                                                                        class="btn btn-primary mt-3">Add
                                                                                        More</button>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <!-- Button and Card for Transport -->
                                                                <div class="card-body text-center">
                                                                    <button type="button" style="width: 60%" disabled
                                                                        id="toggle-bt-transport-2"
                                                                        class="btn btn-primary mt-3" data-state="false">
                                                                        Transport</button>
                                                                </div>
                                                                <div id="transport-card-2" class="card-body"
                                                                    style="display: none;">
                                                                    <div class="accordion" id="accordionTransport">
                                                                        <div class="accordion-item">
                                                                            <h2 class="accordion-header"
                                                                                id="headingTransport">
                                                                                <button class="accordion-button fw-medium"
                                                                                    type="button"
                                                                                    data-bs-toggle="collapse"
                                                                                    data-bs-target="#collapseTransport"
                                                                                    aria-expanded="true"
                                                                                    aria-controls="collapseTransport">
                                                                                    Rencana Transport
                                                                                </button>
                                                                            </h2>
                                                                            <div id="collapseTransport"
                                                                                class="accordion-collapse collapse show"
                                                                                aria-labelledby="headingTransport">
                                                                                <div class="accordion-body">
                                                                                    <div id="form-container-bt-transport">
                                                                                        @php
                                                                                            // Provide default empty array if detail_transport is not set
                                                                                            $detailTransport2 =
                                                                                                $declareCa[
                                                                                                    'detail_transport'
                                                                                                ] ?? [];

                                                                                            // Calculate total transport cost with default values
                                                                                            $totalTransport2 = array_reduce(
                                                                                                $detailTransport2,
                                                                                                function (
                                                                                                    $carry,
                                                                                                    $item,
                                                                                                ) {
                                                                                                    return $carry +
                                                                                                        (int) ($item[
                                                                                                            'nominal'
                                                                                                        ] ?? 0);
                                                                                                },
                                                                                                0,
                                                                                            );
                                                                                        @endphp
                                                                                        @if (!empty($detailTransport2))
                                                                                            @foreach ($detailTransport2 as $index => $transport2)
                                                                                                <div class="mb-2">
                                                                                                    <label
                                                                                                        class="form-label">Tanggal
                                                                                                        Transport</label>
                                                                                                    <input type="date"
                                                                                                        name="tanggal_bt_transport[]"
                                                                                                        class="form-control"
                                                                                                        placeholder="mm/dd/yyyy"
                                                                                                        value="{{ old('tanggal_bt_transport.' . $index, $transport2['tanggal'] ?? '') }}">
                                                                                                </div>
                                                                                                <div class="mb-2">
                                                                                                    <label
                                                                                                        class="form-label"
                                                                                                        for="name">Company
                                                                                                        Code</label>
                                                                                                    <select
                                                                                                        class="form-control select2"
                                                                                                        id="companyFilter_{{ $index }}"
                                                                                                        name="company_bt_transport[]">
                                                                                                        <option
                                                                                                            value="">
                                                                                                            Select
                                                                                                            Company...
                                                                                                        </option>
                                                                                                        @foreach ($companies as $company)
                                                                                                            <option
                                                                                                                value="{{ $company->contribution_level_code }}"
                                                                                                                {{ ($transport['company_code'] ?? '') == $company->contribution_level_code ? 'selected' : '' }}>
                                                                                                                {{ $company->contribution_level . ' (' . $company->contribution_level_code . ')' }}
                                                                                                            </option>
                                                                                                        @endforeach
                                                                                                    </select>
                                                                                                </div>
                                                                                                <div class="mb-2">
                                                                                                    <label
                                                                                                        class="form-label">Keterangan</label>
                                                                                                    <textarea name="keterangan_bt_transport[]" class="form-control" placeholder="Write your information here...">{{ old('keterangan_bt_transport.' . $index, $transport2['keterangan'] ?? '') }}</textarea>
                                                                                                </div>
                                                                                                <div class="mb-2">
                                                                                                    <label
                                                                                                        class="form-label">Amount</label>
                                                                                                </div>
                                                                                                <div
                                                                                                    class="input-group mb-3">
                                                                                                    <div
                                                                                                        class="input-group-append">
                                                                                                        <span
                                                                                                            class="input-group-text">Rp</span>
                                                                                                    </div>
                                                                                                    <input
                                                                                                        class="form-control"
                                                                                                        name="nominal_bt_transport[]"
                                                                                                        id="nominal_bt_transport_{{ $index }}"
                                                                                                        type="text"
                                                                                                        min="0"
                                                                                                        value="{{ old('nominal_bt_transport.' . $index, $transport2['nominal'] ?? '0') }}">
                                                                                                </div>

                                                                                                <hr
                                                                                                    class="border border-primary border-1 opacity-50">
                                                                                            @endforeach
                                                                                        @else
                                                                                            <!-- Default empty fields if no data is available -->
                                                                                            <div class="mb-2">
                                                                                                <label
                                                                                                    class="form-label">Tanggal
                                                                                                    Transport</label>
                                                                                                <input type="date"
                                                                                                    name="tanggal_bt_transport[]"
                                                                                                    class="form-control"
                                                                                                    placeholder="mm/dd/yyyy">
                                                                                            </div>
                                                                                            <div class="mb-2">
                                                                                                <label class="form-label"
                                                                                                    for="name">Company
                                                                                                    Code</label>
                                                                                                <select
                                                                                                    class="form-control select2"
                                                                                                    id="companyFilter_0"
                                                                                                    name="company_bt_transport[]">
                                                                                                    <option value="">
                                                                                                        Select
                                                                                                        Company...
                                                                                                    </option>
                                                                                                    @foreach ($companies as $company)
                                                                                                        <option
                                                                                                            value="{{ $company->contribution_level_code }}">
                                                                                                            {{ $company->contribution_level . ' (' . $company->contribution_level_code . ')' }}
                                                                                                        </option>
                                                                                                    @endforeach
                                                                                                </select>
                                                                                            </div>
                                                                                            <div class="mb-2">
                                                                                                <label
                                                                                                    class="form-label">Keterangan</label>
                                                                                                <textarea name="keterangan_bt_transport[]" class="form-control" placeholder="Write your information here..."></textarea>
                                                                                            </div>
                                                                                            <div class="mb-2">
                                                                                                <label
                                                                                                    class="form-label">Amount</label>
                                                                                            </div>
                                                                                            <div class="input-group mb-3">
                                                                                                <div
                                                                                                    class="input-group-append">
                                                                                                    <span
                                                                                                        class="input-group-text">Rp</span>
                                                                                                </div>
                                                                                                <input class="form-control"
                                                                                                    name="nominal_bt_transport[]"
                                                                                                    id="nominal_bt_transport_0"
                                                                                                    type="text"
                                                                                                    min="0">
                                                                                            </div>

                                                                                            <hr
                                                                                                class="border border-primary border-1 opacity-50">
                                                                                        @endif

                                                                                        <div class="mb-2">
                                                                                            <label class="form-label">Total
                                                                                                Transport</label>
                                                                                            <div class="input-group">
                                                                                                <div
                                                                                                    class="input-group-append">
                                                                                                    <span
                                                                                                        class="input-group-text">Rp</span>
                                                                                                </div>
                                                                                                @php
                                                                                                    // Format totalPerdiem value if available
                                                                                                    $formattedtotalTransport2 = number_format(
                                                                                                        $totalTransport2 ??
                                                                                                            0,
                                                                                                        0,
                                                                                                        ',',
                                                                                                        '.',
                                                                                                    );
                                                                                                @endphp
                                                                                                <input
                                                                                                    class="form-control bg-light"
                                                                                                    name="total_bt_transport[]"
                                                                                                    id="total_bt_transport[]"
                                                                                                    type="text"
                                                                                                    min="0"
                                                                                                    value="{{ $formattedtotalTransport2 ?? 0 }}"
                                                                                                    readonly>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <button type="button"
                                                                                        id="add-more-bt-transport"
                                                                                        class="btn btn-primary mt-3">Add
                                                                                        More</button>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <!-- Button and Card for Penginapan -->
                                                                <div class="card-body text-center">
                                                                    <button type="button" style="width: 60%" disabled
                                                                        id="toggle-bt-penginapan-2"
                                                                        class="btn btn-primary mt-3" data-state="false">
                                                                        Accommodation</button>
                                                                </div>
                                                                <div id="penginapan-card-2" class="card-body"
                                                                    style="display: none;">
                                                                    <div class="accordion" id="accordionPenginapan">
                                                                        <div class="accordion-item">
                                                                            <h2 class="accordion-header"
                                                                                id="headingPenginapan">
                                                                                <button class="accordion-button fw-medium"
                                                                                    type="button"
                                                                                    data-bs-toggle="collapse"
                                                                                    data-bs-target="#collapsePenginapan"
                                                                                    aria-expanded="true"
                                                                                    aria-controls="collapsePenginapan">
                                                                                    Rencana Penginapan
                                                                                </button>
                                                                            </h2>
                                                                            <div id="collapsePenginapan"
                                                                                class="accordion-collapse collapse show"
                                                                                aria-labelledby="headingPenginapan">
                                                                                <div class="accordion-body">
                                                                                    <div id="form-container-bt-penginapan">
                                                                                        @php
                                                                                            // Default empty array if 'detail_penginapan' is not set
                                                                                            $penginapan2 =
                                                                                                $declareCa[
                                                                                                    'detail_penginapan'
                                                                                                ] ?? [];

                                                                                            // Calculate total penginapan cost
                                                                                            $totalPenginapanCost2 = array_reduce(
                                                                                                $penginapan2,
                                                                                                function (
                                                                                                    $carry,
                                                                                                    $item,
                                                                                                ) {
                                                                                                    return $carry +
                                                                                                        (int) ($item[
                                                                                                            'nominal'
                                                                                                        ] ?? 0);
                                                                                                },
                                                                                                0,
                                                                                            );
                                                                                        @endphp

                                                                                        <!-- Form for Penginapan Details -->
                                                                                        <div
                                                                                            id="form-container-bt-penginapan">
                                                                                            @if (!empty($penginapan2))
                                                                                                @foreach ($penginapan2 as $index => $item2)
                                                                                                    <div class="mb-2">
                                                                                                        <label
                                                                                                            class="form-label">Start
                                                                                                            Penginapan</label>
                                                                                                        <input
                                                                                                            type="date"
                                                                                                            name="start_bt_penginapan[]"
                                                                                                            class="form-control start-penginapan"
                                                                                                            placeholder="mm/dd/yyyy"
                                                                                                            value="{{ old('start_bt_penginapan.' . $index, $item2['start_date'] ?? '') }}">
                                                                                                    </div>
                                                                                                    <div class="mb-2">
                                                                                                        <label
                                                                                                            class="form-label">End
                                                                                                            Penginapan</label>
                                                                                                        <input
                                                                                                            type="date"
                                                                                                            name="end_bt_penginapan[]"
                                                                                                            class="form-control end-penginapan"
                                                                                                            placeholder="mm/dd/yyyy"
                                                                                                            value="{{ old('end_bt_penginapan.' . $index, $item2['end_date'] ?? '') }}">
                                                                                                    </div>
                                                                                                    <div class="mb-2">
                                                                                                        <label
                                                                                                            class="form-label"
                                                                                                            for="start">Total
                                                                                                            Days</label>
                                                                                                        <div
                                                                                                            class="input-group">
                                                                                                            <input
                                                                                                                class="form-control bg-light total-days-penginapan"
                                                                                                                id="total_days_bt_penginapan_{{ $index }}"
                                                                                                                name="total_days_bt_penginapan[]"
                                                                                                                type="text"
                                                                                                                min="0"
                                                                                                                value="{{ old('total_days_bt_penginapan.' . $index, $item2['total_days'] ?? '0') }}"
                                                                                                                readonly>
                                                                                                            <div
                                                                                                                class="input-group-append">
                                                                                                                <span
                                                                                                                    class="input-group-text">days</span>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <div class="mb-2">
                                                                                                        <label
                                                                                                            class="form-label"
                                                                                                            for="name">Hotel
                                                                                                            Name</label>
                                                                                                        <input
                                                                                                            type="text"
                                                                                                            name="hotel_name_bt_penginapan[]"
                                                                                                            class="form-control"
                                                                                                            placeholder="ex: Westin"
                                                                                                            value="{{ old('hotel_name_bt_penginapan.' . $index, $item2['hotel_name'] ?? '') }}">
                                                                                                    </div>
                                                                                                    <div class="mb-2">
                                                                                                        <label
                                                                                                            class="form-label"
                                                                                                            for="name">Company
                                                                                                            Code</label>
                                                                                                        <select
                                                                                                            class="form-control select2"
                                                                                                            id="companyFilter_{{ $index }}"
                                                                                                            name="company_bt_penginapan[]">
                                                                                                            <option
                                                                                                                value="">
                                                                                                                Select
                                                                                                                Company...
                                                                                                            </option>
                                                                                                            @foreach ($companies as $company)
                                                                                                                <option
                                                                                                                    value="{{ $company->contribution_level_code }}"
                                                                                                                    {{ ($item['company_code'] ?? '') == $company->contribution_level_code ? 'selected' : '' }}>
                                                                                                                    {{ $company->contribution_level . ' (' . $company->contribution_level_code . ')' }}
                                                                                                                </option>
                                                                                                            @endforeach
                                                                                                        </select>
                                                                                                    </div>
                                                                                                    <div class="mb-2">
                                                                                                        <label
                                                                                                            class="form-label">Amount</label>
                                                                                                    </div>
                                                                                                    <div
                                                                                                        class="input-group mb-3">
                                                                                                        <div
                                                                                                            class="input-group-append">
                                                                                                            <span
                                                                                                                class="input-group-text">Rp</span>
                                                                                                        </div>
                                                                                                        <input
                                                                                                            class="form-control"
                                                                                                            name="nominal_bt_penginapan[]"
                                                                                                            id="nominal_bt_penginapan_{{ $index }}"
                                                                                                            type="text"
                                                                                                            min="0"
                                                                                                            value="{{ old('nominal_bt_penginapan.' . $index, $item2['nominal'] ?? '0') }}">
                                                                                                    </div>

                                                                                                    <hr
                                                                                                        class="border border-primary border-1 opacity-50">
                                                                                                @endforeach
                                                                                            @else
                                                                                                <!-- Default empty fields if no data is available -->
                                                                                                <div class="mb-2">
                                                                                                    <label
                                                                                                        class="form-label">Start
                                                                                                        Penginapan</label>
                                                                                                    <input type="date"
                                                                                                        name="start_bt_penginapan[]"
                                                                                                        class="form-control start-penginapan"
                                                                                                        placeholder="mm/dd/yyyy">
                                                                                                </div>
                                                                                                <div class="mb-2">
                                                                                                    <label
                                                                                                        class="form-label">End
                                                                                                        Penginapan</label>
                                                                                                    <input type="date"
                                                                                                        name="end_bt_penginapan[]"
                                                                                                        class="form-control end-penginapan"
                                                                                                        placeholder="mm/dd/yyyy">
                                                                                                </div>
                                                                                                <div class="mb-2">
                                                                                                    <label
                                                                                                        class="form-label"
                                                                                                        for="start">Total
                                                                                                        Days</label>
                                                                                                    <div
                                                                                                        class="input-group">
                                                                                                        <input
                                                                                                            class="form-control bg-light total-days-penginapan"
                                                                                                            id="total_days_bt_penginapan_0"
                                                                                                            name="total_days_bt_penginapan[]"
                                                                                                            type="text"
                                                                                                            min="0"
                                                                                                            readonly>
                                                                                                        <div
                                                                                                            class="input-group-append">
                                                                                                            <span
                                                                                                                class="input-group-text">days</span>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="mb-2">
                                                                                                    <label
                                                                                                        class="form-label"
                                                                                                        for="name">Hotel
                                                                                                        Name</label>
                                                                                                    <input type="text"
                                                                                                        name="hotel_name_bt_penginapan[]"
                                                                                                        class="form-control"
                                                                                                        placeholder="ex: Westin">
                                                                                                </div>
                                                                                                <div class="mb-2">
                                                                                                    <label
                                                                                                        class="form-label"
                                                                                                        for="name">Company
                                                                                                        Code</label>
                                                                                                    <select
                                                                                                        class="form-control select2"
                                                                                                        id="companyFilter_0"
                                                                                                        name="company_bt_penginapan[]">
                                                                                                        <option
                                                                                                            value="">
                                                                                                            Select
                                                                                                            Company...
                                                                                                        </option>
                                                                                                        @foreach ($companies as $company)
                                                                                                            <option
                                                                                                                value="{{ $company->contribution_level_code }}">
                                                                                                                {{ $company->contribution_level . ' (' . $company->contribution_level_code . ')' }}
                                                                                                            </option>
                                                                                                        @endforeach
                                                                                                    </select>
                                                                                                </div>
                                                                                                <div class="mb-2">
                                                                                                    <label
                                                                                                        class="form-label">Amount</label>
                                                                                                </div>
                                                                                                <div
                                                                                                    class="input-group mb-3">
                                                                                                    <div
                                                                                                        class="input-group-append">
                                                                                                        <span
                                                                                                            class="input-group-text">Rp</span>
                                                                                                    </div>
                                                                                                    <input
                                                                                                        class="form-control"
                                                                                                        name="nominal_bt_penginapan[]"
                                                                                                        id="nominal_bt_penginapan_0"
                                                                                                        type="text"
                                                                                                        min="0">
                                                                                                </div>

                                                                                                <hr
                                                                                                    class="border border-primary border-1 opacity-50">
                                                                                            @endif

                                                                                            <div class="mb-2">
                                                                                                <label
                                                                                                    class="form-label">Total
                                                                                                    Penginapan</label>
                                                                                                <div class="input-group">
                                                                                                    <div
                                                                                                        class="input-group-append">
                                                                                                        <span
                                                                                                            class="input-group-text">Rp</span>
                                                                                                    </div>
                                                                                                    @php
                                                                                                        // Format totalPerdiem value if available
                                                                                                        $formattedtotalPenginapanCost2 = number_format(
                                                                                                            $totalPenginapanCost2 ??
                                                                                                                0,
                                                                                                            0,
                                                                                                            ',',
                                                                                                            '.',
                                                                                                        );
                                                                                                    @endphp
                                                                                                    <input
                                                                                                        class="form-control bg-light"
                                                                                                        name="total_bt_penginapan[]"
                                                                                                        id="total_bt_penginapan"
                                                                                                        type="text"
                                                                                                        min="0"
                                                                                                        value="{{ $formattedtotalPenginapanCost2 }}"
                                                                                                        readonly>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>


                                                                                        <button type="button"
                                                                                            id="add-more-bt-penginapan"
                                                                                            class="btn btn-primary mt-3">Add
                                                                                            More</button>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>


                                                                <!-- Button and Card for Lainnya -->
                                                                <div class="card-body text-center">
                                                                    <button type="button" style="width: 60%" disabled
                                                                        id="toggle-bt-lainnya-2"
                                                                        class="btn btn-primary mt-3" data-state="false">
                                                                        Others</button>
                                                                </div>
                                                                <div id="lainnya-card-2" class="card-body"
                                                                    style="display: none;">
                                                                    <div class="accordion" id="accordionLainnya">
                                                                        <div class="accordion-item">
                                                                            <h2 class="accordion-header"
                                                                                id="headingLainnya">
                                                                                <button class="accordion-button fw-medium"
                                                                                    type="button"
                                                                                    data-bs-toggle="collapse"
                                                                                    data-bs-target="#collapseLainnya"
                                                                                    aria-expanded="true"
                                                                                    aria-controls="collapseLainnya">
                                                                                    Rencana Lainnya
                                                                                </button>
                                                                            </h2>
                                                                            <div id="collapseLainnya"
                                                                                class="accordion-collapse collapse show"
                                                                                aria-labelledby="headingLainnya">
                                                                                <div class="accordion-body">
                                                                                    <div id="form-container-bt-lainnya">
                                                                                        @php
                                                                                            // Default empty array if 'detail_lainnya' is not set
                                                                                            $lainnya2 =
                                                                                                $declareCa[
                                                                                                    'detail_lainnya'
                                                                                                ] ?? [];

                                                                                            // Calculate total lainnya cost
                                                                                            $totalLainnyaCost2 = array_reduce(
                                                                                                $lainnya2,
                                                                                                function (
                                                                                                    $carry,
                                                                                                    $item,
                                                                                                ) {
                                                                                                    return $carry +
                                                                                                        (int) ($item[
                                                                                                            'nominal'
                                                                                                        ] ?? 0);
                                                                                                },
                                                                                                0,
                                                                                            );
                                                                                        @endphp

                                                                                        <div
                                                                                            id="form-container-bt-lainnya">
                                                                                            @if (!empty($lainnya2))
                                                                                                @foreach ($lainnya2 as $index => $lainnyaItem2)
                                                                                                    <div
                                                                                                        class="lainnya-item">
                                                                                                        <div
                                                                                                            class="mb-2">
                                                                                                            <label
                                                                                                                class="form-label">Tanggal</label>
                                                                                                            <input
                                                                                                                type="date"
                                                                                                                name="tanggal_bt_lainnya[]"
                                                                                                                class="form-control"
                                                                                                                value="{{ old('tanggal_bt_lainnya.' . $index, $lainnyaItem2['tanggal'] ?? '') }}"
                                                                                                                placeholder="mm/dd/yyyy">
                                                                                                        </div>
                                                                                                        <div
                                                                                                            class="mb-2">
                                                                                                            <label
                                                                                                                class="form-label">Keterangan</label>
                                                                                                            <textarea name="keterangan_bt_lainnya[]" class="form-control" placeholder="Write your other purposes ...">{{ old('keterangan_bt_lainnya.' . $index, $lainnyaItem2['keterangan'] ?? '') }}</textarea>
                                                                                                        </div>
                                                                                                        <div
                                                                                                            class="mb-2">
                                                                                                            <label
                                                                                                                class="form-label">Amount</label>
                                                                                                            <div
                                                                                                                class="input-group mb-3">
                                                                                                                <div
                                                                                                                    class="input-group-append">
                                                                                                                    <span
                                                                                                                        class="input-group-text">Rp</span>
                                                                                                                </div>
                                                                                                                <input
                                                                                                                    class="form-control nominal-lainnya"
                                                                                                                    name="nominal_bt_lainnya[]"
                                                                                                                    type="text"
                                                                                                                    min="0"
                                                                                                                    value="{{ old('nominal_bt_lainnya.' . $index, $lainnyaItem2['nominal'] ?? '0') }}">
                                                                                                            </div>
                                                                                                        </div>
                                                                                                        <hr
                                                                                                            class="border border-primary border-1 opacity-50">
                                                                                                    </div>
                                                                                                @endforeach
                                                                                            @else
                                                                                                <div class="lainnya-item">
                                                                                                    <div class="mb-2">
                                                                                                        <label
                                                                                                            class="form-label">Tanggal</label>
                                                                                                        <input
                                                                                                            type="date"
                                                                                                            name="tanggal_bt_lainnya[]"
                                                                                                            class="form-control"
                                                                                                            placeholder="mm/dd/yyyy">
                                                                                                    </div>
                                                                                                    <div class="mb-2">
                                                                                                        <label
                                                                                                            class="form-label">Keterangan</label>
                                                                                                        <textarea name="keterangan_bt_lainnya[]" class="form-control" placeholder="Write your other purposes ..."></textarea>
                                                                                                    </div>
                                                                                                    <div class="mb-2">
                                                                                                        <label
                                                                                                            class="form-label">Amount</label>
                                                                                                        <div
                                                                                                            class="input-group mb-3">
                                                                                                            <div
                                                                                                                class="input-group-append">
                                                                                                                <span
                                                                                                                    class="input-group-text">Rp</span>
                                                                                                            </div>
                                                                                                            <input
                                                                                                                class="form-control nominal-lainnya"
                                                                                                                name="nominal_bt_lainnya[]"
                                                                                                                type="text"
                                                                                                                min="0"
                                                                                                                value="0">
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <hr
                                                                                                        class="border border-primary border-1 opacity-50">
                                                                                                </div>
                                                                                            @endif
                                                                                            @php
                                                                                                // Assuming $totalLainnya is available and holds the total value for 'lainnya'
                                                                                                $totalLainnya2 =
                                                                                                    $totalLainnya2 ?? 0; // Default to 0 if $totalLainnya is not set
                                                                                                $formattedTotalLainnya2 = number_format(
                                                                                                    $totalLainnya2,
                                                                                                    0,
                                                                                                    ',',
                                                                                                    '.',
                                                                                                );
                                                                                            @endphp
                                                                                            <div class="mb-2">
                                                                                                <label
                                                                                                    class="form-label">Total
                                                                                                    Lainnya</label>
                                                                                                <div class="input-group">
                                                                                                    <div
                                                                                                        class="input-group-append">
                                                                                                        <span
                                                                                                            class="input-group-text">Rp</span>
                                                                                                    </div>
                                                                                                    <input
                                                                                                        class="form-control bg-light"
                                                                                                        name="total_bt_lainnya[]"
                                                                                                        id="total_bt_lainnya"
                                                                                                        type="text"
                                                                                                        min="0"
                                                                                                        value="{{ old('total_bt_lainnya.' . $index, $formattedTotalLainnya2) }}"
                                                                                                        readonly>
                                                                                                </div>
                                                                                            </div>

                                                                                            <button type="button"
                                                                                                id="add-more-bt-lainnya"
                                                                                                class="btn btn-primary mt-3">Add
                                                                                                More</button>
                                                                                        </div>

                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                {{-- </div> --}}
                                                                {{-- </div> --}}
                                                            </div>
                                                        </div>
                                                        <br>
                                                        @php
                                                            // Provide default empty arrays if any section is not set
                                                            $detailPerdiem = $caDetail['detail_perdiem'] ?? [];
                                                            $detailTransport = $caDetail['detail_transport'] ?? [];
                                                            $detailPenginapan = $caDetail['detail_penginapan'] ?? [];
                                                            $detailLainnya = $caDetail['detail_lainnya'] ?? [];

                                                            // Calculate total costs for each section
                                                            $totalPerdiem = array_reduce(
                                                                $detailPerdiem,
                                                                function ($carry, $item) {
                                                                    return $carry + (int) ($item['nominal'] ?? 0);
                                                                },
                                                                0,
                                                            );

                                                            $totalTransport = array_reduce(
                                                                $detailTransport,
                                                                function ($carry, $item) {
                                                                    return $carry + (int) ($item['nominal'] ?? 0);
                                                                },
                                                                0,
                                                            );

                                                            $totalPenginapan = array_reduce(
                                                                $detailPenginapan,
                                                                function ($carry, $item) {
                                                                    return $carry + (int) ($item['nominal'] ?? 0);
                                                                },
                                                                0,
                                                            );

                                                            $totalLainnya = array_reduce(
                                                                $detailLainnya,
                                                                function ($carry, $item) {
                                                                    return $carry + (int) ($item['nominal'] ?? 0);
                                                                },
                                                                0,
                                                            );

                                                            // Total Cash Advanced
                                                            $totalCashAdvanced2 =
                                                                $totalPerdiem2 +
                                                                $totalTransport2 +
                                                                $totalPenginapan2 +
                                                                $totalLainnya2;
                                                            $formattedTotalCashAdvanced2 = number_format(
                                                                $totalCashAdvanced2,
                                                                0,
                                                                ',',
                                                                '.',
                                                            );
                                                        @endphp
                                                        <div class="col-md-12 mb-2">
                                                            <label class="form-label">Total Cash Advanced</label>
                                                            <div class="input-group">
                                                                <div class="input-group-append">
                                                                    <span class="input-group-text">Rp</span>
                                                                </div>
                                                                <input class="form-control bg-light" name="totalca"
                                                                    id="totalca" type="text" min="0"
                                                                    value="{{ $formattedTotalCashAdvanced2 }}" readonly>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="struk" class="form-label">Upload Proof</label>
                                <input type="file" id="struk" name="struk" accept="image/*,application/pdf"
                                    class="form-control">
                            </div>
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary rounded-pill">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-datepicker@1.9.0/dist/js/bootstrap-datepicker.min.js"></script>
    <script>
        //CA JS
        $(document).ready(function() {
            function toggleCard2(buttonId, cardId, shouldOpen) {
                var $button = $(buttonId);
                var $card = $(cardId);

                if (shouldOpen === undefined) {
                    // Determine if the card is currently visible
                    shouldOpen = !$card.is(':visible');
                }

                if (shouldOpen) {
                    $card.slideDown('fast', function() {
                        // Set button text and icon for open state
                        var buttonText = $button.data('text') || $button.text();
                        $button.html('<i class="bi bi-dash-circle"></i> ' + buttonText);
                        $button.data('state', 'true');
                    });
                } else {
                    $card.slideUp('fast', function() {
                        // Clear form inputs after hiding
                        // $card.find('input[type="text"], input[type="date"], textarea').val('');
                        // $card.find('select').prop('selectedIndex', 0);
                        // $card.find('input[readonly]').val(0);
                        // $card.find('input[type="number"]').val(0);

                        // // Set button text and icon for closed state
                        // var buttonText = $button.data('text') || $button.text();
                        // $button.html('<i class="bi bi-plus-circle"></i> ' + buttonText);
                        // $button.data('state', 'false');
                    });
                }
            }

            // Store the original button text for the second form
            $('#toggle-bt-perdiem-2, #toggle-bt-transport-2, #toggle-bt-penginapan-2, #toggle-bt-lainnya-2')
                .each(function() {
                    $(this).data('text', $(this).text().trim());
                });

            // Attach click event handlers for the second form
            $('#toggle-bt-perdiem-2').click(function() {
                toggleCard2('#toggle-bt-perdiem-2', '#perdiem-card-2');
            });

            $('#toggle-bt-transport-2').click(function() {
                toggleCard2('#toggle-bt-transport-2', '#transport-card-2');
            });

            $('#toggle-bt-penginapan-2').click(function() {
                toggleCard2('#toggle-bt-penginapan-2', '#penginapan-card-2');
            });

            $('#toggle-bt-lainnya-2').click(function() {
                toggleCard2('#toggle-bt-lainnya-2', '#lainnya-card-2');
            });

            // Open all cards by default when the page loads
            toggleCard2('#toggle-bt-perdiem-2', '#perdiem-card-2', true);
            toggleCard2('#toggle-bt-transport-2', '#transport-card-2', true);
            toggleCard2('#toggle-bt-penginapan-2', '#penginapan-card-2', true);
            toggleCard2('#toggle-bt-lainnya-2', '#lainnya-card-2', true);
        });


        function toggleDivs() {
            // ca_type ca_nbt ca_e
            var ca_type = document.getElementById("ca_type");
            var ca_nbt = document.getElementById("ca_nbt");
            var ca_e = document.getElementById("ca_e");
            var div_bisnis_numb = document.getElementById("div_bisnis_numb");
            var bisnis_numb = document.getElementById("bisnis_numb");
            var div_allowance = document.getElementById("div_allowance");

            if (ca_type.value === "dns") {
                ca_bt.style.display = "block";
                ca_nbt.style.display = "none";
                ca_e.style.display = "none";
                div_bisnis_numb.style.display = "block";
                div_allowance.style.display = "block";
            } else if (ca_type.value === "ndns") {
                ca_bt.style.display = "none";
                ca_nbt.style.display = "block";
                ca_e.style.display = "none";
                div_bisnis_numb.style.display = "none";
                bisnis_numb.style.value = "";
                div_allowance.style.display = "none";
            } else if (ca_type.value === "entr") {
                ca_bt.style.display = "none";
                ca_nbt.style.display = "none";
                ca_e.style.display = "block";
                div_bisnis_numb.style.display = "block";
            } else {
                ca_bt.style.display = "none";
                ca_nbt.style.display = "none";
                ca_e.style.display = "none";
                div_bisnis_numb.style.display = "none";
                bisnis_numb.style.value = "";
            }
        }

        function toggleOthers() {
            // ca_type ca_nbt ca_e
            var locationFilter = document.getElementById("locationFilter");
            var others_location = document.getElementById("others_location");

            if (locationFilter.value === "Others") {
                others_location.style.display = "block";
            } else {
                others_location.style.display = "none";
                others_location.value = "";
            }
        }

        function validateInput(input) {
            //input.value = input.value.replace(/[^0-9,]/g, '');
            input.value = input.value.replace(/[^0-9]/g, '');
        }

        document.addEventListener('DOMContentLoaded', function() {
            const startDateInput = document.getElementById('start_date');
            const endDateInput = document.getElementById('end_date');
            const totalDaysInput = document.getElementById('totaldays');
            const perdiemInput = document.getElementById('perdiem');
            const allowanceInput = document.getElementById('allowance');
            const othersLocationInput = document.getElementById('others_location');
            const transportInput = document.getElementById('transport');
            const accommodationInput = document.getElementById('accommodation');
            const otherInput = document.getElementById('other');
            const totalcaInput = document.getElementById('totalca');
            const nominal_1Input = document.getElementById('nominal_1');
            const nominal_2Input = document.getElementById('nominal_2');
            const nominal_3Input = document.getElementById('nominal_3');
            const nominal_4Input = document.getElementById('nominal_4');
            const nominal_5Input = document.getElementById('nominal_5');
            const caTypeInput = document.getElementById('ca_type');

            function formatNumber(num) {
                return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }

            function parseNumber(value) {
                return parseFloat(value.replace(/\./g, '')) || 0;
            }

            function calculateTotalDays() {
                const startDate = new Date(startDateInput.value);
                const endDate = new Date(endDateInput.value);
                if (startDate && endDate && !isNaN(startDate) && !isNaN(endDate)) {
                    const timeDiff = endDate - startDate;
                    const daysDiff = Math.ceil(timeDiff / (1000 * 60 * 60 * 24));
                    const totalDays = daysDiff > 0 ? daysDiff + 1 : 0 + 1;
                    totalDaysInput.value = totalDays;

                    const perdiem = parseFloat(perdiemInput.value) || 0;
                    let allowance = totalDays * perdiem;

                    if (othersLocationInput.value.trim() !== '') {
                        allowance *= 1; // allowance * 50%
                    } else {
                        allowance *= 0.5;
                    }

                    allowanceInput.value = formatNumber(Math.floor(allowance));
                } else {
                    totalDaysInput.value = 0;
                    allowanceInput.value = 0;
                }
                calculateTotalCA();
            }

            function formatInput(input) {
                let value = input.value.replace(/\./g, '');
                value = parseFloat(value);
                if (!isNaN(value)) {
                    // input.value = formatNumber(value);
                    input.value = formatNumber(Math.floor(value));
                } else {
                    input.value = formatNumber(0);
                }

                calculateTotalCA();
            }

            function calculateTotalCA() {
                const allowance = parseNumber(allowanceInput.value);
                const transport = parseNumber(transportInput.value);
                const accommodation = parseNumber(accommodationInput.value);
                const other = parseNumber(otherInput.value);
                const nominal_1 = parseNumber(nominal_1Input.value);
                const nominal_2 = parseNumber(nominal_2Input.value);
                const nominal_3 = parseNumber(nominal_3Input.value);
                const nominal_4 = parseNumber(nominal_4Input.value);
                const nominal_5 = parseNumber(nominal_5Input.value);

                // Perbaiki penulisan caTypeInput.value
                const ca_type = caTypeInput.value;

                let totalca = 0;
                if (ca_type === 'dns') {
                    totalca = allowance + transport + accommodation + other;
                } else if (ca_type === 'ndns') {
                    totalca = transport + accommodation + other;
                    allowanceInput.value = 0;
                } else if (ca_type === 'entr') {
                    totalca = nominal_1 + nominal_2 + nominal_3 + nominal_4 + nominal_5;
                    allowanceInput.value = 0;
                }

                // totalcaInput.value = formatNumber(totalca.toFixed(2));
                totalcaInput.value = formatNumber(Math.floor(totalca));
            }

            startDateInput.addEventListener('change', calculateTotalDays);
            endDateInput.addEventListener('change', calculateTotalDays);
            othersLocationInput.addEventListener('input', calculateTotalDays);
            caTypeInput.addEventListener('change', calculateTotalDays);
            [transportInput, accommodationInput, otherInput, allowanceInput, nominal_1, nominal_2, nominal_3,
                nominal_4, nominal_5
            ].forEach(input => {
                input.addEventListener('input', () => formatInput(input));
            });
        });

        document.getElementById('end_date').addEventListener('change', function() {
            const endDate = new Date(this.value);
            const declarationEstimateDate = new Date(endDate);
            declarationEstimateDate.setDate(declarationEstimateDate.getDate() + 3);

            const year = declarationEstimateDate.getFullYear();
            const month = String(declarationEstimateDate.getMonth() + 1).padStart(2, '0');
            const day = String(declarationEstimateDate.getDate()).padStart(2, '0');

            document.getElementById('ca_decla').value = `${year}-${month}-${day}`;
        });
    </script>

    <script>
        $(document).ready(function() {
            $('.select2').select2({
                theme: "bootstrap-5",

            });
        });

        $(document).ready(function() {
            function toggleCard(buttonId, cardId, shouldOpen) {
                var $button = $(buttonId);
                var $card = $(cardId);

                if (shouldOpen === undefined) {
                    // Determine if the card is currently visible
                    shouldOpen = !$card.is(':visible');
                }

                if (shouldOpen) {
                    $card.slideDown('fast', function() {
                        // Set button text and icon for open state
                        var buttonText = $button.data('text') || $button.text();
                        $button.html('<i class="bi bi-dash-circle"></i> ' + buttonText);
                        $button.data('state', 'true');
                    });
                } else {
                    $card.slideUp('fast', function() {
                        // Clear form inputs after hiding
                        // $card.find('input[type="text"], input[type="date"], textarea').val('');
                        // $card.find('select').prop('selectedIndex', 0);
                        // $card.find('input[readonly]').val(0);
                        // $card.find('input[type="number"]').val(0);

                        // // Set button text and icon for closed state
                        // var buttonText = $button.data('text') || $button.text();
                        // $button.html('<i class="bi bi-plus-circle"></i> ' + buttonText);
                        // $button.data('state', 'false');
                    });
                }
            }

            // Store the original button text for the second form
            $('#toggle-bt-perdiem, #toggle-bt-transport, #toggle-bt-penginapan, #toggle-bt-lainnya')
                .each(function() {
                    $(this).data('text', $(this).text().trim());
                });

            // Attach click event handlers for the second form
            $('#toggle-bt-perdiem-2').click(function() {
                toggleCard('#toggle-bt-perdiem', '#perdiem-card');
            });

            $('#toggle-bt-transport-2').click(function() {
                toggleCard('#toggle-bt-transport', '#transport-card');
            });

            $('#toggle-bt-penginapan-2').click(function() {
                toggleCard('#toggle-bt-penginapan', '#penginapan-card');
            });

            $('#toggle-bt-lainnya-2').click(function() {
                toggleCard('#toggle-bt-lainnya', '#lainnya-card');
            });

            // Open all cards by default when the page loads
            toggleCard('#toggle-bt-perdiem', '#perdiem-card', true);
            toggleCard('#toggle-bt-transport', '#transport-card', true);
            toggleCard('#toggle-bt-penginapan', '#penginapan-card', true);
            toggleCard('#toggle-bt-lainnya', '#lainnya-card', true);
        });


        document.addEventListener('DOMContentLoaded', function() {
            const formContainerBTPerdiem = document.getElementById('form-container-bt-perdiem');
            const formContainerBTTransport = document.getElementById('form-container-bt-transport');
            const formContainerBTPenginapan = document.getElementById('form-container-bt-penginapan');
            const formContainerBTLainnya = document.getElementById('form-container-bt-lainnya');

            function toggleOthersBT(selectElement) {
                const formGroup = selectElement.closest('.mb-2').parentElement;
                const othersInput = formGroup.querySelector('input[name="other_location_bt_perdiem[]"]');

                if (selectElement.value === "Others") {
                    othersInput.style.display = 'block';
                    othersInput.required = true;
                } else {
                    othersInput.style.display = 'none';
                    othersInput.required = false;
                    othersInput.value = "";
                }
            }

            document.querySelectorAll('.location-select').forEach(function(selectElement) {
                selectElement.addEventListener('change', function() {
                    toggleOthersBT(this);
                });
            });

            function formatNumber(num) {
                return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }

            function parseNumber(value) {
                return parseFloat(value.replace(/\./g, '')) || 0;
            }

            function formatInput(input) {
                let value = input.value.replace(/\./g, '');
                value = parseFloat(value);
                if (!isNaN(value)) {
                    input.value = formatNumber(Math.floor(value));
                } else {
                    input.value = formatNumber(0);
                }
                calculateTotalNominalBTPerdiem();
                calculateTotalNominalBTTransport();
                calculateTotalNominalBTPenginapan();
                calculateTotalNominalBTLainnya();
                calculateTotalNominalBTTotal();
            }

            function calculateTotalNominalBTPerdiem() {
                let total = 0;
                document.querySelectorAll('input[name="nominal_bt_perdiem[]"]').forEach(input => {
                    total += parseNumber(input.value);
                });
                document.querySelector('input[name="total_bt_perdiem[]"]').value = formatNumber(total);
            }

            function calculateTotalNominalBTTransport() {
                let total = 0;
                document.querySelectorAll('input[name="nominal_bt_transport[]"]').forEach(input => {
                    total += parseNumber(input.value);
                });
                document.querySelector('input[name="total_bt_transport[]"]').value = formatNumber(total);
            }

            function calculateTotalNominalBTPenginapan() {
                let total = 0;
                document.querySelectorAll('input[name="nominal_bt_penginapan[]"]').forEach(input => {
                    total += parseNumber(input.value);
                });
                document.querySelector('input[name="total_bt_penginapan[]"]').value = formatNumber(total);
            }

            function calculateTotalNominalBTLainnya() {
                let total = 0;
                document.querySelectorAll('input[name="nominal_bt_lainnya[]"]').forEach(input => {
                    total += parseNumber(input.value);
                });
                document.querySelector('input[name="total_bt_lainnya[]"]').value = formatNumber(total);
            }

            function calculateTotalNominalBTTotal() {
                let total = 0;
                document.querySelectorAll('input[name="total_bt_perdiem[]"]').forEach(input => {
                    total += parseNumber(input.value);
                });
                document.querySelectorAll('input[name="total_bt_transport[]"]').forEach(input => {
                    total += parseNumber(input.value);
                });
                document.querySelectorAll('input[name="total_bt_penginapan[]"]').forEach(input => {
                    total += parseNumber(input.value);
                });
                document.querySelectorAll('input[name="total_bt_lainnya[]"]').forEach(input => {
                    total += parseNumber(input.value);
                });
                document.querySelector('input[name="totalca"]').value = formatNumber(total);
            }



            function calculateTotalDaysPerdiem(input) {
                const formGroup = input.closest('.mb-2').parentElement;
                const startDateInput = formGroup.querySelector('input[name="start_bt_perdiem[]"]');
                const endDateInput = formGroup.querySelector('input[name="end_bt_perdiem[]"]');

                const startDate = new Date(startDateInput.value);
                const endDate = new Date(endDateInput.value);

                if (!isNaN(startDate) && !isNaN(endDate)) {
                    if (startDate > endDate) {
                        alert('End date cannot be earlier than start date.');
                        endDateInput.value = ''; // Clear the end date field
                        formGroup.querySelector('input[name="total_days_bt_perdiem[]"]').value = 0;
                        return; // Exit the function to prevent further calculation
                    }

                    const diffTime = Math.abs(endDate - startDate);
                    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
                    formGroup.querySelector('input[name="total_days_bt_perdiem[]"]').value = diffDays;
                } else {
                    formGroup.querySelector('input[name="total_days_bt_perdiem[]"]').value = 0;
                }
            }


            function calculateTotalDaysPenginapan(input) {
                const formGroup = input.closest('.mb-2').parentElement;
                const startDateInput = formGroup.querySelector('input[name="start_bt_penginapan[]"]');
                const endDateInput = formGroup.querySelector('input[name="end_bt_penginapan[]"]');

                const startDate = new Date(startDateInput.value);
                const endDate = new Date(endDateInput.value);

                if (!isNaN(startDate) && !isNaN(endDate)) {
                    if (startDate > endDate) {
                        alert('End date cannot be earlier than start date.');
                        endDateInput.value = ''; // Clear the end date field
                        formGroup.querySelector('input[name="total_days_bt_penginapan[]"]').value = 0;
                        return; // Exit the function to prevent further calculation
                    }

                    const diffTime = Math.abs(endDate - startDate);
                    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
                    formGroup.querySelector('input[name="total_days_bt_penginapan[]"]').value = diffDays;
                } else {
                    formGroup.querySelector('input[name="total_days_bt_penginapan[]"]').value = 0;
                }
            }

            function addNewPerdiemForm() {
                const newFormBTPerdiem = document.createElement('div');
                newFormBTPerdiem.classList.add('mb-2');

                newFormBTPerdiem.innerHTML = `
                    <div class="mb-2">
                        <label class="form-label">Start Perdiem</label>
                        <input type="date" name="start_bt_perdiem[]" class="form-control start-perdiem" placeholder="mm/dd/yyyy" >
                    </div>
                    <div class="mb-2">
                        <label class="form-label">End Perdiem</label>
                        <input type="date" name="end_bt_perdiem[]" class="form-control end-perdiem" placeholder="mm/dd/yyyy" >
                    </div>
                    <div class="mb-2">
                        <label class="form-label" for="start">Total Days</label>
                        <div class="input-group">
                            <input class="form-control bg-light total-days-perdiem" id="total_days_bt_perdiem[]" name="total_days_bt_perdiem[]" type="text" min="0" value="0" readonly>
                            <div class="input-group-append">
                                <span class="input-group-text">days</span>
                            </div>
                        </div>
                    </div>
                     <div class="mb-2">
                        <label class="form-label" for="name">Location Agency</label>
                        <select class="form-control select2 location-select" name="location_bt_perdiem[]">
                            <option value="">Select location...</option>
                            @foreach ($locations as $location)
                                <option value="{{ $location->area }}">{{ $location->area . ' (' . $location->company_name . ')' }}</option>
                            @endforeach
                            <option value="Others">Others</option>
                        </select>
                        <br>
                        <input type="text" name="other_location_bt_perdiem[]" class="form-control other-location" placeholder="Other Location" value="" style="display: none;">
                    </div>
                    <div class="mb-2">
                        <label class="form-label" for="name">Company Code</label>
                        <select class="form-control select2" name="company_bt_perdiem[]" >
                            <option value="">Select Company...</option>
                            @foreach ($companies as $company)
                                <option value="{{ $company->contribution_level_code }}">{{ $company->contribution_level . ' (' . $company->contribution_level_code . ')' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Amount</label>
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-append">
                            <span class="input-group-text">Rp</span>
                        </div>
                        <input class="form-control" name="nominal_bt_perdiem[]" type="text" min="0" value="0">
                    </div>
                    <button type="button" class="btn btn-danger remove-form">Remove</button>
                    <hr class="border border-primary border-1 opacity-50">
                `;

                document.getElementById('form-container-bt-perdiem').appendChild(newFormBTPerdiem);

                formContainerBTPerdiem.appendChild(newFormBTPerdiem);

                newFormBTPerdiem.querySelector('.location-select').addEventListener('change', function() {
                    toggleOthersBT(this);
                });


                // Attach input event to the newly added nominal field
                newFormBTPerdiem.querySelector('input[name="nominal_bt_perdiem[]"]').addEventListener('input',
                    function() {
                        formatInput(this);
                    });

                // Attach change event to the date fields to calculate total days
                newFormBTPerdiem.querySelector('input[name="start_bt_perdiem[]"]').addEventListener('change',
                    function() {
                        calculateTotalDaysPerdiem(this);
                    });

                newFormBTPerdiem.querySelector('input[name="end_bt_perdiem[]"]').addEventListener('change',
                    function() {
                        calculateTotalDaysPerdiem(this);
                    });

                // Attach click event to the remove button
                newFormBTPerdiem.querySelector('.remove-form').addEventListener('click', function() {
                    newFormBTPerdiem.remove();
                    calculateTotalNominalBTPerdiem();
                    calculateTotalNominalBTTotal();
                });

                // Update the date constraints for the new 'start_bt_perdiem[]' and 'end_bt_perdiem[]' input fields
                const startDateInput = document.getElementById('start_date').value;
                const endDateInput = document.getElementById('end_date').value;

                newFormBTPerdiem.querySelectorAll('input[name="start_bt_perdiem[]"]').forEach(function(input) {
                    input.min = startDateInput;
                    input.max = endDateInput;
                });

                newFormBTPerdiem.querySelectorAll('input[name="end_bt_perdiem[]"]').forEach(function(input) {
                    input.min = startDateInput;
                    input.max = endDateInput;
                });
            }

            function addNewTransportForm() {
                const newFormBTTransport = document.createElement('div');
                newFormBTTransport.classList.add('mb-2');

                newFormBTTransport.innerHTML = `
                    <div class="mb-2">
                        <label class="form-label">Tanggal Transport</label>
                        <input type="date" name="tanggal_bt_transport[]" class="form-control" placeholder="mm/dd/yyyy" >
                    </div>
                    <div class="mb-2">
                        <label class="form-label" for="name">Company Code</label>
                        <select class="form-control select2" name="company_bt_transport[]" >
                            <option value="">Select Company...</option>
                            @foreach ($companies as $company)
                                <option value="{{ $company->contribution_level_code }}">{{ $company->contribution_level . ' (' . $company->contribution_level_code . ')' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan_bt_transport[]" class="form-control"></textarea>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Amount</label>
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-append">
                            <span class="input-group-text">Rp</span>
                        </div>
                        <input class="form-control" name="nominal_bt_transport[]" type="text" min="0" value="0">
                    </div>
                    <button type="button" class="btn btn-danger remove-form">Remove</button>
                    <hr class="border border-primary border-1 opacity-50">
                `;

                formContainerBTTransport.appendChild(newFormBTTransport);

                // Attach input event to the newly added nominal field
                newFormBTTransport.querySelector('input[name="nominal_bt_transport[]"]').addEventListener('input',
                    function() {
                        formatInput(this);
                    });

                // Attach click event to the remove button
                newFormBTTransport.querySelector('.remove-form').addEventListener('click', function() {
                    newFormBTTransport.remove();
                    calculateTotalNominalBTTransport();
                    calculateTotalNominalBTTotal();
                });
            }

            function addNewPenginapanForm() {
                const newFormBTPenginapan = document.createElement('div');
                newFormBTPenginapan.classList.add('mb-2');

                newFormBTPenginapan.innerHTML = `
                    <div class="mb-2">
                        <label class="form-label">Start Penginapan</label>
                        <input type="date" name="start_bt_penginapan[]" class="form-control start-penginapan" placeholder="mm/dd/yyyy">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">End Penginapan</label>
                        <input type="date" name="end_bt_penginapan[]" class="form-control end-penginapan" placeholder="mm/dd/yyyy">
                    </div>
                    <div class="mb-2">
                        <label class="form-label" for="start">Total Days</label>
                        <div class="input-group">
                            <input class="form-control bg-light total-days-penginapan" id="total_days_bt_penginapan[]" name="total_days_bt_penginapan[]" type="text" min="0" value="0" readonly>
                            <div class="input-group-append">
                                <span class="input-group-text">days</span>
                            </div>
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label" for="name">Hotel Name</label>
                        <input type="text" name="hotel_name_bt_penginapan[]" class="form-control" placeholder="Hotel">
                    </div>
                    <div class="mb-2">
                        <label class="form-label" for="name">Company Code</label>
                        <select class="form-control select2" id="companyFilter" name="company_bt_penginapan[]">
                            <option value="">Select Company...</option>
                            @foreach ($companies as $company)
                                <option value="{{ $company->contribution_level_code }}">{{ $company->contribution_level . ' (' . $company->contribution_level_code . ')' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Amount</label>
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-append">
                            <span class="input-group-text">Rp</span>
                        </div>
                        <input class="form-control" name="nominal_bt_penginapan[]" id="nominal_bt_penginapan[]" type="text" min="0" value="0">
                    </div>
                    <button type="button" class="btn btn-danger remove-form">Remove</button>
                    <hr class="border border-primary border-1 opacity-50">
                `;

                formContainerBTPenginapan.appendChild(newFormBTPenginapan);

                // Attach input event to the newly added nominal field
                newFormBTPenginapan.querySelector('input[name="nominal_bt_penginapan[]"]').addEventListener('input',
                    function() {
                        formatInput(this);
                    });

                // Attach change event to the date fields to calculate total days
                newFormBTPenginapan.querySelector('input[name="start_bt_penginapan[]"]').addEventListener('change',
                    function() {
                        calculateTotalDaysPenginapan(this);
                    });

                newFormBTPenginapan.querySelector('input[name="end_bt_penginapan[]"]').addEventListener('change',
                    function() {
                        calculateTotalDaysPenginapan(this);
                    });

                // Attach click event to the remove button
                newFormBTPenginapan.querySelector('.remove-form').addEventListener('click', function() {
                    newFormBTPenginapan.remove();
                    calculateTotalNominalBTPenginapan();
                    calculateTotalNominalBTTotal();
                });
            }

            function addNewLainnyaForm() {
                const newFormBTLainnya = document.createElement('div');
                newFormBTLainnya.classList.add('mb-2');

                newFormBTLainnya.innerHTML = `
                    <div class="mb-2">
                        <label class="form-label">Tanggal</label>
                        <input type="date" name="tanggal_bt_lainnya[]" class="form-control" placeholder="mm/dd/yyyy">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan_bt_lainnya[]" class="form-control"></textarea>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Accommodation</label>
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-append">
                            <span class="input-group-text">Rp</span>
                        </div>
                        <input class="form-control" name="nominal_bt_lainnya[]" type="text" min="0" value="0">
                    </div>
                    <button type="button" class="btn btn-danger remove-form">Remove</button>
                    <hr class="border border-primary border-1 opacity-50">
                `;

                formContainerBTLainnya.appendChild(newFormBTLainnya);

                // Attach input event to the newly added nominal field
                newFormBTLainnya.querySelector('input[name="nominal_bt_lainnya[]"]').addEventListener('input',
                    function() {
                        formatInput(this);
                    });

                // Attach click event to the remove button
                newFormBTLainnya.querySelector('.remove-form').addEventListener('click', function() {
                    newFormBTLainnya.remove();
                    calculateTotalNominalBTLainnya();
                    calculateTotalNominalBTTotal();
                });
            }

            document.getElementById('add-more-bt-perdiem').addEventListener('click', addNewPerdiemForm);
            document.getElementById('add-more-bt-transport').addEventListener('click', addNewTransportForm);
            document.getElementById('add-more-bt-penginapan').addEventListener('click', addNewPenginapanForm);
            document.getElementById('add-more-bt-lainnya').addEventListener('click', addNewLainnyaForm);

            // Attach input event to the existing nominal fields
            document.querySelectorAll('input[name="nominal_bt_perdiem[]"]').forEach(input => {
                input.addEventListener('input', function() {
                    formatInput(this);
                });
            });

            document.querySelectorAll('input[name="nominal_bt_transport[]"]').forEach(input => {
                input.addEventListener('input', function() {
                    formatInput(this);
                });
            });

            document.querySelectorAll('input[name="nominal_bt_penginapan[]"]').forEach(input => {
                input.addEventListener('input', function() {
                    formatInput(this);
                });
            });

            // Attach change event to the existing start and end date fields to calculate total days
            document.querySelectorAll('input[name="start_bt_perdiem[]"], input[name="end_bt_perdiem[]"]').forEach(
                input => {
                    input.addEventListener('change', function() {
                        calculateTotalDaysPerdiem(this);
                    });
                });

            document.querySelectorAll('input[name="start_bt_penginapan[]"], input[name="end_bt_penginapan[]"]')
                .forEach(input => {
                    input.addEventListener('change', function() {
                        calculateTotalDaysPenginapan(this);
                    });
                });

            document.querySelectorAll('input[name="nominal_bt_lainnya[]"]').forEach(input => {
                input.addEventListener('input', function() {
                    formatInput(this);
                });
            });

            // Initial calculation for the total nominal
            calculateTotalNominalBTPerdiem();
            calculateTotalNominalBTTransport();
            calculateTotalNominalBTPenginapan();
            calculateTotalNominalBTLainnya();
            calculateTotalNominalBTTotal();

            document.getElementById('start_date').addEventListener('change', handleDateChange);
            document.getElementById('end_date').addEventListener('change', handleDateChange);

            function handleDateChange() {
                const startDateInput = document.getElementById('start_date');
                const endDateInput = document.getElementById('end_date');

                const startDate = new Date(startDateInput.value);
                const endDate = new Date(endDateInput.value);

                // Set the min attribute of the end_date input to the selected start_date
                endDateInput.min = startDateInput.value;

                // Validate dates
                if (endDate < startDate) {
                    alert("End Date cannot be earlier than Start Date");
                    endDateInput.value = "";
                }

                // Update min and max values for all dynamic perdiem date fields
                document.querySelectorAll('input[name="start_bt_perdiem[]"]').forEach(function(input) {
                    input.min = startDateInput.value;
                    input.max = endDateInput.value;
                });

                document.querySelectorAll('input[name="end_bt_perdiem[]"]').forEach(function(input) {
                    input.min = startDateInput.value;
                    input.max = endDateInput.value;
                });

                document.querySelectorAll('input[name="total_days_bt_perdiem[]"]').forEach(function(input) {
                    calculateTotalDaysPerdiem(input);
                });
            }



            // Attach click event to the remove button for existing forms
            document.querySelectorAll('.remove-form').forEach(button => {
                button.addEventListener('click', function() {
                    this.closest('.mb-2').remove();
                    calculateTotalNominalBTPerdiem();
                    calculateTotalNominalBTTransport();
                    calculateTotalNominalBTPenginapan();
                    calculateTotalNominalBTLainnya();
                    calculateTotalNominalBTTotal();
                });
            });
        });



        //

        document.addEventListener('DOMContentLoaded', function() {
            const formContainer = document.getElementById('form-container');

            function formatNumber(num) {
                return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }

            function parseNumber(value) {
                return parseFloat(value.replace(/\./g, '')) || 0;
            }

            function formatInput(input) {
                let value = input.value.replace(/\./g, '');
                value = parseFloat(value);
                if (!isNaN(value)) {
                    input.value = formatNumber(Math.floor(value));
                } else {
                    input.value = formatNumber(0);
                }
                calculateTotalNominal();
            }

            function calculateTotalNominal() {
                let total = 0;
                document.querySelectorAll('input[name="nominal_nbt[]"]').forEach(input => {
                    total += parseNumber(input.value);
                });
                document.getElementById('totalca').value = formatNumber(total);
            }

            document.getElementById('add-more').addEventListener('click', function() {
                const newForm = document.createElement('div');
                newForm.classList.add('mb-2', 'form-group');

                newForm.innerHTML = `
                    <div class="mb-2">
                        <label class="form-label">Tanggal</label>
                        <input type="date" name="tanggal_nbt[]" class="form-control" placeholder="mm/dd/yyyy">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan_nbt[]" class="form-control"></textarea>
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-append">
                            <span class="input-group-text">Rp</span>
                        </div>
                        <input class="form-control" name="nominal_nbt[]" type="text" min="0" value="0">
                    </div>
                    <button type="button" class="btn btn-danger remove-form">Remove</button>
                    <hr class="border border-primary border-1 opacity-50">
                `;

                formContainer.appendChild(newForm);

                // Attach input event to the newly added nominal field
                newForm.querySelector('input[name="nominal_nbt[]"]').addEventListener('input', function() {
                    formatInput(this);
                });

                // Attach click event to the remove button
                newForm.querySelector('.remove-form').addEventListener('click', function() {
                    newForm.remove();
                    calculateTotalNominal();
                });

                // Update the date constraints for the new 'tanggal_nbt[]' input fields
                const startDateInput = document.getElementById('start_date').value;
                const endDateInput = document.getElementById('end_date').value;

                newForm.querySelectorAll('input[name="tanggal_nbt[]"]').forEach(function(input) {
                    input.min = startDateInput;
                    input.max = endDateInput;
                });
            });

            // Attach input event to the existing nominal fields
            document.querySelectorAll('input[name="nominal_nbt[]"]').forEach(input => {
                input.addEventListener('input', function() {
                    formatInput(this);
                });
            });

            // Initial calculation for the total nominal
            calculateTotalNominal();

        });

        document.addEventListener('DOMContentLoaded', function() {
            const formContainerEDetail = document.getElementById('form-container-e-detail');
            const formContainerERelation = document.getElementById('form-container-e-relation');

            // Function to update checkboxes visibility based on selected options
            function updateCheckboxVisibility() {
                // Gather all selected options from enter_type_e_detail
                const selectedOptions = Array.from(document.querySelectorAll(
                        'select[name="enter_type_e_detail[]"]'))
                    .map(select => select.value)
                    .filter(value => value !== "");

                // Update visibility for each checkbox in enter_type_e_relation
                formContainerERelation.querySelectorAll('.form-check').forEach(checkDiv => {
                    const checkbox = checkDiv.querySelector('input.form-check-input');
                    const checkboxValue = checkbox.value.toLowerCase().replace(/\s/g, "_");
                    if (selectedOptions.includes(checkboxValue)) {
                        checkDiv.style.display = 'block';
                    } else {
                        checkDiv.style.display = 'none';
                    }
                });
            }

            // Function to format number with thousands separator
            function formatNumber(num) {
                return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }

            // Function to parse number from formatted string
            function parseNumber(value) {
                return parseFloat(value.replace(/\./g, '')) || 0;
            }

            // Function to format input fields
            function formatInput(input) {
                let value = input.value.replace(/\./g, '');
                value = parseFloat(value);
                if (!isNaN(value)) {
                    input.value = formatNumber(Math.floor(value));
                } else {
                    input.value = formatNumber(0);
                }
                calculateTotalNominalEDetail();
            }

            // Function to calculate the total nominal value for EDetail
            function calculateTotalNominalEDetail() {
                let total = 0;
                document.querySelectorAll('input[name="nominal_e_detail[]"]').forEach(input => {
                    total += parseNumber(input.value);
                });
                document.querySelector('input[name="total_e_detail[]"]').value = formatNumber(total);
                document.getElementById('totalca').value = formatNumber(total);
            }

            // Function to add new EDetail form
            function addNewEDetailForm() {
                const newFormEDetail = document.createElement('div');
                newFormEDetail.classList.add('mb-2');

                newFormEDetail.innerHTML = `
                    <div class="mb-2">
                        <label class="form-label">Entertainment Type</label>
                        <select name="enter_type_e_detail[]" class="form-select">
                            <option value="">-</option>
                            <option value="food_cost">Food/Beverages/Souvenir</option>
                            <option value="transport">Transport</option>
                            <option value="accommodation">Accommodation</option>
                            <option value="gift">Gift</option>
                            <option value="fund">Fund</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Entertainment Fee Detail</label>
                        <textarea name="enter_fee_e_detail[]" class="form-control"></textarea>
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-append">
                            <span class="input-group-text">Rp</span>
                        </div>
                        <input class="form-control" name="nominal_e_detail[]" type="text" min="0" value="0">
                    </div>
                    <button type="button" class="btn btn-danger remove-form-e-detail">Remove</button>
                    <hr class="border border-primary border-1 opacity-50">
                `;

                formContainerEDetail.appendChild(newFormEDetail);

                // Attach input event to the newly added nominal field
                newFormEDetail.querySelector('input[name="nominal_e_detail[]"]').addEventListener('input',
                    function() {
                        formatInput(this);
                    });

                // Attach change event to update checkbox visibility
                newFormEDetail.querySelector('select[name="enter_type_e_detail[]"]').addEventListener('change',
                    updateCheckboxVisibility);

                // Attach click event to the remove button
                newFormEDetail.querySelector('.remove-form-e-detail').addEventListener('click', function() {
                    newFormEDetail.remove();
                    updateCheckboxVisibility();
                    calculateTotalNominalEDetail();
                });
            }

            // Function to add new ERelation form
            function addNewERelationForm() {
                const newFormERelation = document.createElement('div');
                newFormERelation.classList.add('mb-2');

                newFormERelation.innerHTML = `
                    <div class="mb-2">
                        <label class="form-label">Relation Type</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="food_cost_e_relation[]" value="food_cost">
                            <label class="form-check-label" for="food_cost_e_relation[]">Food/Beverages/Souvenir</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="transport_e_relation[]" value="transport">
                            <label class="form-check-label" for="transport_e_relation[]">Transport</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="accommodation_e_relation[]" value="accommodation">
                            <label class="form-check-label" for="accommodation_e_relation[]">Accommodation</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="gift_e_relation[]" value="gift">
                            <label class="form-check-label" for="gift_e_relation[]">Gift</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="fund_e_relation[]" value="fund">
                            <label class="form-check-label" for="fund_e_relation[]">Fund</label>
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Name</label>
                        <input type="text" name="rname_e_relation[]" class="form-control">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Position</label>
                        <input type="text" name="rposition_e_relation[]" class="form-control">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Company</label>
                        <input type="text" name="rcompany_e_relation[]" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Purpose</label>
                        <input type="text" name="rpurpose_e_relation[]" class="form-control">
                    </div>
                    <button type="button" class="btn btn-danger remove-form-e-relation">Remove</button>
                    <hr class="border border-primary border-1 opacity-50">
                `;

                formContainerERelation.appendChild(newFormERelation);

                // Initial update of checkbox visibility
                updateCheckboxVisibility();

                // Attach click event to the remove button
                newFormERelation.querySelector('.remove-form-e-relation').addEventListener('click', function() {
                    newFormERelation.remove();
                    updateCheckboxVisibility();
                });
            }

            document.getElementById('add-more-e-detail').addEventListener('click', addNewEDetailForm);
            document.getElementById('add-more-e-relation').addEventListener('click', addNewERelationForm);

            // Attach input event to the existing nominal fields
            document.querySelectorAll('input[name="nominal_e_detail[]"]').forEach(input => {
                input.addEventListener('input', function() {
                    formatInput(this);
                });
            });

            // Attach change event to existing select fields for checkbox visibility
            document.querySelectorAll('select[name="enter_type_e_detail[]"]').forEach(select => {
                select.addEventListener('change', updateCheckboxVisibility);
            });

            calculateTotalNominalEDetail();
            updateCheckboxVisibility();
        });
    </script>
@endsection

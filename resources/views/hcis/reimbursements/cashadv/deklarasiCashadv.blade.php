@extends('layouts_.vertical', ['page_title' => 'Cash Advanced'])

@section('css')
    <!-- Sertakan CSS Bootstrap jika diperlukan -->
    {{-- <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-beta3/css/bootstrap.min.css"> --}}
    <style>
        th, td{
            vertical-align: top !important;
        }
    </style>
@endsection

@section('content')
    <style>
        .table > :not(caption) > * > * {
            padding: 0.2rem 0.2rem; /* Sesuaikan padding di sini */
        }
    </style>
    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('cashadvanced') }}">{{ $parentLink }}</a></li>
                            <li class="breadcrumb-item active">{{ $link }}</li>
                        </ol>
                    </div>
                    <h4 class="page-title">{{ $link }}</h4>
                </div>
            </div>
        </div>
            <div class="card shadow-none">
                <div class="card-header text-center">
                    <h4>Cash Advance No <b>"{{ $transactions->no_ca }}"</b></h4>
                    {{-- <a href="{{ route('cashadvanced') }}" type="button" class="btn btn-close"></a> --}}
                </div>
                <div class="card-body p-2">
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <table class="table" style="border: none; border-collapse: collapse; padding: 1%;">
                                <tr>
                                    <th class="label" style="border: none; width:30%;">Employee ID</th>
                                    <td class="colon" style="border: none;">:</td>
                                    <td class="value" style="border: none;">{{ $employee_data->employee_id }}</td>
                                </tr>
                                <tr>
                                    <th class="label" style="width: 20%; border: none;">Employee Name</th>
                                    <td class="colon" style="width: 3%; border: none;">:</td>
                                    <td class="value" style="border: none;">{{ $employee_data->fullname }}</td>
                                </tr>
                                <tr>
                                    <th class="label" style="border: none;">Unit</th>
                                    <td class="colon" style="border: none;">:</td>
                                    <td class="value" style="border: none;">{{ $employee_data->unit }}</td>
                                </tr>
                                <tr>
                                    <th class="label" style="border: none;">Job Level</th>
                                    <td class="colon" style="border: none;">:</td>
                                    <td class="value" style="border: none;">{{ $employee_data->job_level }}</td>
                                </tr>
                                <tr>
                                    <th class="label" style="border: none;">Costing Company</th>
                                    <td class="colon" style="border: none;">:</td>
                                    <td class="value" style="border: none;">{{ $transactions->companies->contribution_level }} ({{ $transactions->companies->contribution_level_code }})</td>
                                </tr>
                                <tr>
                                    <th class="label" style="border: none;">Destination</th>
                                    <td class="colon" style="border: none;">:</td>
                                    <td class="value" style="border: none;">
                                        @if ($transactions->destination == 'Others')
                                            {{ $transactions->others_location }}
                                        @else
                                            {{ $transactions->destination }}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th class="label" style="border: none;">CA Purposes</th>
                                    <td class="colon" style="border: none;">:</td>
                                    <td class="value" style="border: none;">{{ $transactions->ca_needs }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6 mb-2">
                            <table class="table" style="border: none; border-collapse: collapse; padding: 1%;">
                                <tr>
                                    <th class="label" style="border: none; width:40%;">Start Date</th>
                                    <td class="colon" style="border: none; width:1%;">:</td>
                                    <td class="value" style="border: none;">{{ $transactions->start_date }}</td>
                                </tr>
                                <tr>
                                    <th class="label" style="border: none;">End Date</th>
                                    <td class="colon" style="border: none;">:</td>
                                    <td class="value" style="border: none;">{{$transactions->end_date}}</td>
                                </tr>
                                <tr>
                                    <th class="label" style="border: none;">Total Date</th>
                                    <td class="colon" style="border: none;">:</td>
                                    <td class="value" style="border: none;">{{ $transactions->total_days }} Days</td>
                                </tr>
                                <tr>
                                    <th class="label" style="border: none;">CA Date Required</th>
                                    <td class="colon" style="border: none;">:</td>
                                    <td class="value" style="border: none;">{{ \Carbon\Carbon::parse($transactions->date_required)->format('d-M-y') }}</td>
                                </tr>
                                <tr>
                                    <th class="label" style="border: none;">Declaration Estimate</th>
                                    <td class="colon" style="border: none;">:</td>
                                    <td class="value" style="border: none;">{{ \Carbon\Carbon::parse($transactions->declare_estimate)->format('d-M-y') }}</td>
                                </tr>
                                <tr>
                                    <th class="label" style="border: none;">Cash Advanced Type</th>
                                    <td class="colon" style="border: none;">:</td>
                                    <td class="value" style="border: none;">
                                        @if ($transactions->type_ca == 'dns')
                                            Bussiness Trip
                                        @elseif ($transactions->type_ca == 'entr')
                                            Entertainment
                                        @else
                                            Non Business Trip
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <form enctype="multipart/form-data" id="scheduleForm" method="post"
                        action="{{ route('cashadvanced.declare', encrypt($transactions->id)) }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-4 mb-2" style="display: none;">
                                <label class="form-label" for="type">CA Type</label>
                                <select name="ca_type_disabled" id="ca_type" class="form-control bg-light" disabled>
                                    <option value="">-</option>
                                    <option value="dns" {{ $transactions->type_ca == 'dns' ? 'selected' : '' }}>
                                        Business Trip
                                    </option>
                                    <option value="ndns" {{ $transactions->type_ca == 'ndns' ? 'selected' : '' }}>
                                        Non Business Trip
                                    </option>
                                    <option value="entr" {{ $transactions->type_ca == 'entr' ? 'selected' : '' }}>
                                        Entertainment
                                    </option>
                                </select>

                                <input type="hidden" name="ca_type" value="{{ $transactions->type_ca }}">
                                <input class="form-control" id="perdiem" name="perdiem" type="hidden" value="{{ $perdiem->amount }}" readonly>
                                <input type="date" name="start_date" id="start_date" class="form-control"
                                    placeholder="mm/dd/yyyy" value="{{ $transactions->start_date }}">
                                <input type="date" name="end_date" id="end_date" class="form-control"
                                    placeholder="mm/dd/yyyy" value="{{ $transactions->end_date }}">
                            </div>
                            @php
                                $detailCA = json_decode($transactions->detail_ca, true) ?? [];
                                $declareCA = json_decode($transactions->declare_ca, true) ?? [];
                            @endphp
                            <script>
                                // Pass the PHP array into a JavaScript variable
                                const initialDetailCA = @json($declareCA);
                            </script>
                            <br>
                            <div class="row" id="ca_bt" style="display: none;">
                                @if ($transactions->type_ca == 'dns')
                                    <div class="col-md-12">
                                        <div class="table-responsive-sm">
                                            <div class="d-flex flex-column gap-2">
                                                <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                                                    <li class="nav-item" role="presentation">
                                                        <button class="nav-link {{ isset($detailCA['detail_perdiem'][0]['start_date']) && $detailCA['detail_perdiem'][0]['start_date'] ? 'active' : '' }}" id="pills-perdiem-tab"
                                                            data-bs-toggle="pill" data-bs-target="#pills-perdiem" type="button"
                                                            role="tab" aria-controls="pills-perdiem"
                                                            aria-selected="{{ isset($detailCA['detail_perdiem'][0]['start_date']) && $detailCA['detail_perdiem'][0]['start_date'] ? 'true' : 'false' }}">Perdiem Plan</button>
                                                    </li>
                                                    <li class="nav-item" role="presentation">
                                                        <button class="nav-link {{ !isset($detailCA['detail_perdiem'][0]['start_date']) && isset($detailCA['detail_transport'][0]['tanggal']) && $detailCA['detail_transport'][0]['tanggal'] ? 'active' : '' }}" id="pills-transport-tab"
                                                            data-bs-toggle="pill" data-bs-target="#pills-transport" type="button" role="tab"
                                                            aria-controls="pills-transport"
                                                            aria-selected="{{ isset($detailCA['detail_transport'][0]['tanggal']) && $detailCA['detail_transport'][0]['tanggal'] ? 'true' : 'false' }}">Transport Plan</button>
                                                    </li>
                                                    <li class="nav-item" role="presentation">
                                                        <button class="nav-link {{ !isset($detailCA['detail_perdiem'][0]['start_date']) && !isset($detailCA['detail_transport'][0]['tanggal']) && isset($detailCA['detail_penginapan'][0]['start_date']) && $detailCA['detail_penginapan'][0]['start_date'] ? 'active' : '' }}" id="pills-accomodation-tab"
                                                            data-bs-toggle="pill" data-bs-target="#pills-accomodation"
                                                            type="button" role="tab" aria-controls="pills-accomodation"
                                                            aria-selected="{{ isset($detailCA['detail_penginapan'][0]['start_date']) && $detailCA['detail_penginapan'][0]['start_date'] ? 'true' : 'false' }}">Accomodation Plan</button>
                                                    </li>
                                                    <li class="nav-item" role="presentation">
                                                        <button class="nav-link {{ !isset($detailCA['detail_perdiem'][0]['start_date']) && !isset($detailCA['detail_transport'][0]['tanggal']) && !isset($detailCA['detail_penginapan'][0]['start_date']) && isset($detailCA['detail_lainnya'][0]['tanggal']) && $detailCA['detail_lainnya'][0]['tanggal'] ? 'active' : '' }}" id="pills-other-tab" data-bs-toggle="pill"
                                                            data-bs-target="#pills-other" type="button" role="tab"
                                                            aria-controls="pills-other" aria-selected="{{ isset($detailCA['detail_lainnya'][0]['tanggal']) && $detailCA['detail_lainnya'][0]['tanggal'] ? 'true' : 'false' }}">Other Plan</button>
                                                    </li>
                                                </ul>
                                                <div class="tab-content" id="pills-tabContent">
                                                    <div class="tab-pane fade {{ isset($detailCA['detail_perdiem'][0]['start_date']) && $detailCA['detail_perdiem'][0]['start_date'] ? 'show active' : '' }}"
                                                        id="pills-perdiem" role="tabpanel"
                                                        aria-labelledby="pills-perdiem-tab">
                                                        @include('hcis.reimbursements.cashadv.form_dec.perdiem')
                                                    </div>
                                                    <div class="tab-pane fade {{ !isset($detailCA['detail_perdiem'][0]['start_date']) && isset($detailCA['detail_transport'][0]['tanggal']) && $detailCA['detail_transport'][0]['tanggal'] ? 'show active' : '' }}"
                                                        id="pills-transport" role="tabpanel"
                                                        aria-labelledby="pills-transport-tab">
                                                        @include('hcis.reimbursements.cashadv.form_dec.transport')
                                                    </div>
                                                    <div class="tab-pane fade {{ !isset($detailCA['detail_perdiem'][0]['start_date']) && !isset($detailCA['detail_transport'][0]['tanggal']) && isset($detailCA['detail_penginapan'][0]['start_date']) && $detailCA['detail_penginapan'][0]['start_date'] ? 'show active' : '' }}"
                                                        id="pills-accomodation" role="tabpanel"
                                                        aria-labelledby="pills-accomodation-tab">
                                                        @include('hcis.reimbursements.cashadv.form_dec.penginapan')
                                                    </div>
                                                    <div class="tab-pane fade {{ !isset($detailCA['detail_perdiem'][0]['start_date']) && !isset($detailCA['detail_transport'][0]['tanggal']) && !isset($detailCA['detail_penginapan'][0]['start_date']) && isset($detailCA['detail_lainnya'][0]['tanggal']) && $detailCA['detail_lainnya'][0]['tanggal'] ? 'show active' : '' }}" id="pills-other" role="tabpanel"
                                                        aria-labelledby="pills-other-tab">
                                                        @include('hcis.reimbursements.cashadv.form_dec.others')
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                </div>
                            @endif
                        </div>
                        <div class="row" id="ca_nbt" style="display: none;">
                            @if ($transactions->type_ca == 'ndns')
                                <div class="col-md-12">
                                    <div class="table-responsive-sm">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="text-bg-danger mb-3 p-2" style="text-align:center">Estimated
                                                    Cash Advanced</div>
                                                <div class="card">
                                                    <div class="card-body p-0">
                                                        <div class="accordion" id="accordionPanelsStayOpenExample">
                                                            <div class="accordion-item">
                                                                <h2 class="accordion-header" id="enter-headingOne">
                                                                    <button class="accordion-button fw-medium"
                                                                        type="button" data-bs-toggle="collapse"
                                                                        data-bs-target="#enter-collapseOne"
                                                                        aria-expanded="true"
                                                                        aria-controls="enter-collapseOne">
                                                                        Non Business Trip
                                                                    </button>
                                                                </h2>
                                                                @foreach ($detailCA as $item)
                                                                    <div id="enter-collapseOne"
                                                                        class="accordion-collapse show"
                                                                        aria-labelledby="enter-headingOne">
                                                                        <div class="accordion-body">
                                                                            <div id="form-container">
                                                                                <div class="mb-2">
                                                                                    <label
                                                                                        class="form-label">Tanggal</label>
                                                                                    <input type="date"
                                                                                        name="tanggal_nbt_decla[]"
                                                                                        class="form-control bg-light"
                                                                                        value="{{ $item['tanggal_nbt'] }}"
                                                                                        readonly>
                                                                                </div>
                                                                                <div class="mb-2">
                                                                                    <label
                                                                                        class="form-label">Keterangan</label>
                                                                                    <textarea name="keterangan_nbt_decla[]" class="form-control bg-light" readonly>{{ $item['keterangan_nbt'] }}</textarea>
                                                                                </div>
                                                                                <div class="mb-2">
                                                                                    <label
                                                                                        class="form-label">Accommodation</label>
                                                                                </div>
                                                                                <div class="input-group mb-3">
                                                                                    <div class="input-group-append">
                                                                                        <span
                                                                                            class="input-group-text">Rp</span>
                                                                                    </div>
                                                                                    <input class="form-control bg-light"
                                                                                        name="nominal_nbt_decla[]"
                                                                                        id="nominal_nbt" type="text"
                                                                                        min="0"
                                                                                        value="{{ number_format($item['nominal_nbt'], 0, ',', '.') }}"
                                                                                        readonly>
                                                                                </div>
                                                                                <hr
                                                                                    class="border border-primary border-1 opacity-50">
                                                                            </div>
                                                                            {{-- <button type="button" id="add-more" class="btn btn-primary mb-3">Add More</button> --}}
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="text-bg-danger mb-3 p-2" style="text-align:center">Estimated
                                                    Cash Advanced Deklarasi</div>
                                                <div class="card">
                                                    <div class="card-body p-0">
                                                        <div class="accordion" id="accordionPanelsStayOpenExample">
                                                            <div class="accordion-item">
                                                                <h2 class="accordion-header" id="enter-headingOne">
                                                                    <button class="accordion-button fw-medium"
                                                                        type="button" data-bs-toggle="collapse"
                                                                        data-bs-target="#enter-collapseOne"
                                                                        aria-expanded="true"
                                                                        aria-controls="enter-collapseOne">
                                                                        Deklarasi Non Business Trip
                                                                    </button>
                                                                </h2>
                                                                <div id="enter-collapseOne"
                                                                    class="accordion-collapse show"
                                                                    aria-labelledby="enter-headingOne">
                                                                    <div class="accordion-body">
                                                                        <div id="form-container-nbt"></div>
                                                                        <button type="button" id="add-more"
                                                                            class="btn btn-primary mb-3">Add More</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="row" id="ca_e" style="display: none;">
                            @if ($transactions->type_ca == 'entr')
                                <div class="col-md-12">
                                    <div class="table-responsive-sm">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="text-bg-danger mb-3 p-2" style="text-align:center">Estimated
                                                    Entertainment</div>
                                                <div class="card">
                                                    <div id="entertain-card-deklarasi" class="card-body mb-3 p-0">
                                                        <div class="accordion" id="accordionEntertain">
                                                            <div class="accordion-item">
                                                                <h2 class="accordion-header" id="headingEntertain">
                                                                    <button
                                                                        class="accordion-button @if ($detailCA['detail_e'][0]['type'] === null) collapsed @endif fw-medium"
                                                                        type="button" data-bs-toggle="collapse"
                                                                        data-bs-target="#collapseEntertain"
                                                                        aria-expanded="@if ($detailCA['detail_e'][0]['type'] !== null) true @else false @endif"
                                                                        aria-controls="collapseEntertain">
                                                                        Entertain Plan
                                                                    </button>
                                                                </h2>
                                                                <div id="collapseEntertain"
                                                                    class="accordion-collapse collapse @if ($detailCA['detail_e'][0]['type'] !== null) show @endif"
                                                                    aria-labelledby="headingEntertain">
                                                                    <div class="accordion-body">
                                                                        <div id="form-container-e-detail-deklarasi">
                                                                            @foreach ($detailCA['detail_e'] as $detail)
                                                                                <div class="mb-2">
                                                                                    <label class="form-label">Entertainment
                                                                                        Type</label>
                                                                                    <select
                                                                                        name="enter_type_e_detail_deklarasi[]"
                                                                                        id="enter_type_e_detail_deklarasi[]"
                                                                                        class="form-select bg-light"
                                                                                        disabled>
                                                                                        <option value="">-</option>
                                                                                        <option value="food"
                                                                                            {{ $detail['type'] == 'food' ? 'selected' : '' }}>
                                                                                            Food/Beverages/Souvenir</option>
                                                                                        <option value="transport"
                                                                                            {{ $detail['type'] == 'transport' ? 'selected' : '' }}>
                                                                                            Transport</option>
                                                                                        <option value="accommodation"
                                                                                            {{ $detail['type'] == 'accommodation' ? 'selected' : '' }}>
                                                                                            Accommodation</option>
                                                                                        <option value="gift"
                                                                                            {{ $detail['type'] == 'gift' ? 'selected' : '' }}>
                                                                                            Gift</option>
                                                                                        <option value="fund"
                                                                                            {{ $detail['type'] == 'fund' ? 'selected' : '' }}>
                                                                                            Fund</option>
                                                                                    </select>
                                                                                </div>
                                                                                <div class="mb-2">
                                                                                    <label class="form-label">Entertainment
                                                                                        Fee Detail</label>
                                                                                    <textarea name="enter_fee_e_detail_deklarasi[]" id="enter_fee_e_detail_deklarasi[]" class="form-control bg-light"
                                                                                        readonly>{{ $detail['fee_detail'] }}<</textarea>
                                                                                </div>
                                                                                <div class="input-group">
                                                                                    <div class="input-group-append">
                                                                                        <span
                                                                                            class="input-group-text">Rp</span>
                                                                                    </div>
                                                                                    <input class="form-control bg-light"
                                                                                        name="nominal_e_detail_deklarasi[]"
                                                                                        id="nominal_e_detail_deklarasi[]"
                                                                                        type="text" min="0"
                                                                                        value="{{ number_format($detail['nominal'], 0, ',', '.') }}"
                                                                                        readonly>
                                                                                </div>
                                                                                <hr
                                                                                    class="border border-primary border-1 opacity-50">
                                                                            @endforeach
                                                                        </div>
                                                                        <div class="mb-2">
                                                                            <label class="form-label">Total
                                                                                Entertain</label>
                                                                            <div class="input-group">
                                                                                <div class="input-group-append">
                                                                                    <span
                                                                                        class="input-group-text">Rp</span>
                                                                                </div>
                                                                                <input class="form-control bg-light"
                                                                                    name="total_e_detail_deklarasi[]"
                                                                                    id="total_e_detail_deklarasi[]"
                                                                                    type="text" min="0"
                                                                                    value="0" readonly>
                                                                            </div>
                                                                        </div>
                                                                        {{-- <button type="button" id="add-more-e-detail" class="btn btn-primary mb-3">Add More</button> --}}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                <div id="relation-card-deklarasi" class="card-body mb-3 p-0">
                                                    <div class="accordion" id="accordionRelation">
                                                        <div class="accordion-item">
                                                            <h2 class="accordion-header" id="headingRelationDec">
                                                                <button
                                                                    class="accordion-button @if ($detailCA['relation_e'][0]['name'] === null) collapsed @endif fw-medium"
                                                                    type="button" data-bs-toggle="collapse"
                                                                    data-bs-target="#collapseRelationDec"
                                                                    aria-expanded="@if ($detailCA['relation_e'][0]['name'] !== null) true @else false @endif"
                                                                    aria-controls="collapseRelationDec">
                                                                    Relation Plan
                                                                </button>
                                                            </h2>
                                                            <div id="collapseRelationDec"
                                                                class="accordion-collapse collapse @if ($detailCA['relation_e'][0]['name'] !== null) show @endif"
                                                                aria-labelledby="headingRelationDec">
                                                                <div class="accordion-body">
                                                                    <div id="form-container-e-relation-deklarasi">
                                                                        @foreach ($detailCA['relation_e'] as $relation)
                                                                            <div class="mb-2">
                                                                                <label class="form-label">Relation
                                                                                    Type</label>
                                                                                <div class="form-check">
                                                                                    <input class="form-check-input"
                                                                                        type="checkbox"
                                                                                        name="accommodation_e_relation-deklarasi[]"
                                                                                        id="accommodation_e_relation-deklarasi[]"
                                                                                        value="accommodation"
                                                                                        {{ isset($relation['relation_type']['Accommodation']) && $relation['relation_type']['Accommodation'] ? 'checked' : '' }}
                                                                                        disabled>
                                                                                    <label class="form-check-label"
                                                                                        for="accommodation_e_relation-deklarasi[]">Accommodation</label>
                                                                                </div>
                                                                                <div class="form-check">
                                                                                    <input class="form-check-input"
                                                                                        name="transport_e_relation_deklarasi[]"
                                                                                        type="checkbox"
                                                                                        id="transport_e_relation_deklarasi[]"
                                                                                        value="transport"
                                                                                        {{ isset($relation['relation_type']['Transport']) && $relation['relation_type']['Transport'] ? 'checked' : '' }}
                                                                                        disabled>
                                                                                    <label class="form-check-label"
                                                                                        for="transport_e_relation_deklarasi[]">Transport</label>
                                                                                </div>
                                                                                <div class="form-check">
                                                                                    <input class="form-check-input"
                                                                                        name="gift_e_relation_deklarasi[]"
                                                                                        type="checkbox"
                                                                                        id="gift_e_relation_deklarasi[]"
                                                                                        value="gift"
                                                                                        {{ isset($relation['relation_type']['Gift']) && $relation['relation_type']['Gift'] ? 'checked' : '' }}
                                                                                        disabled>
                                                                                    <label class="form-check-label"
                                                                                        for="gift_e_relation_deklarasi[]">Gift</label>
                                                                                </div>
                                                                                <div class="form-check">
                                                                                    <input class="form-check-input"
                                                                                        name="fund_e_relation_deklarasi[]"
                                                                                        type="checkbox"
                                                                                        id="fund_e_relation_deklarasi[]"
                                                                                        value="fund"
                                                                                        {{ isset($relation['relation_type']['Fund']) && $relation['relation_type']['Fund'] ? 'checked' : '' }}
                                                                                        disabled>
                                                                                    <label class="form-check-label"
                                                                                        for="fund_e_relation_deklarasi[]">Fund</label>
                                                                                </div>
                                                                                <div class="form-check">
                                                                                    <input class="form-check-input"
                                                                                        name="food_e_relation_deklarasi[]"
                                                                                        type="checkbox"
                                                                                        id="food_e_relation_deklarasi[]"
                                                                                        value="food"
                                                                                        {{ isset($relation['relation_type']['Food']) && $relation['relation_type']['Food'] ? 'checked' : '' }}
                                                                                        disabled>
                                                                                    <label class="form-check-label"
                                                                                        for="food_e_relation_deklarasi[]">Food/Beverages/Souvenir</label>
                                                                                </div>
                                                                            </div>
                                                                            <div class="mb-2">
                                                                                <label class="form-label"
                                                                                    for="start">Name</label>
                                                                                <input type="text"
                                                                                    name="rname_e_relation_deklarasi[]"
                                                                                    id="rname_e_relation_deklarasi[]"
                                                                                    value="{{ $relation['name'] }}"
                                                                                    class="form-control bg-light" readonly>
                                                                            </div>
                                                                            <div class="mb-2">
                                                                                <label class="form-label"
                                                                                    for="start">Position</label>
                                                                                <input type="text"
                                                                                    name="rposition_e_relation_deklarasi[]"
                                                                                    id="rposition_e_relation_deklarasi[]"
                                                                                    value="{{ $relation['position'] }}"
                                                                                    class="form-control bg-light" readonly>
                                                                            </div>
                                                                            <div class="mb-2">
                                                                                <label class="form-label"
                                                                                    for="start">Company</label>
                                                                                <input type="text"
                                                                                    name="rcompany_e_relation_deklarasi[]"
                                                                                    id="rcompany_e_relation_deklarasi[]"
                                                                                    value="{{ $relation['company'] }}"
                                                                                    class="form-control bg-light" readonly>
                                                                            </div>
                                                                            <div class="mb-2">
                                                                                <label class="form-label"
                                                                                    for="start">Purpose</label>
                                                                                <input type="text"
                                                                                    name="rpurpose_e_relation_deklarasi[]"
                                                                                    id="rpurpose_e_relation_deklarasi[]"
                                                                                    value="{{ $relation['purpose'] }}"
                                                                                    class="form-control bg-light" readonly>
                                                                            </div>
                                                                            <hr
                                                                                class="border border-primary border-1 opacity-50">
                                                                        @endforeach
                                                                    </div>
                                                                    {{-- <button type="button" id="add-more-e-relation" class="btn btn-primary mb-3">Add More</button> --}}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="text-bg-danger mb-3 p-2" style="text-align:center">Estimated
                                                Entertainment Deklarasi</div>
                                            <div class="card">
                                                <div id="entertain-card" class="card-body mb-3 p-0">
                                                    <div class="accordion" id="accordionEntertain">
                                                        <div class="accordion-item">
                                                            <h2 class="accordion-header" id="headingEntertain">
                                                                <button
                                                                    class="accordion-button @if ($declareCA['detail_e'][0]['type'] === null) collapsed @endif fw-medium"
                                                                    type="button" data-bs-toggle="collapse"
                                                                    data-bs-target="#collapseEntertain"
                                                                    aria-expanded="@if ($declareCA['detail_e'][0]['type'] !== null) true @else false @endif"
                                                                    aria-controls="collapseEntertain">
                                                                    Declaration Detail Entertain
                                                                </button>
                                                            </h2>
                                                            <div id="collapseEntertain"
                                                                class="accordion-collapse collapse @if ($declareCA['detail_e'][0]['type'] !== null) show @endif"
                                                                aria-labelledby="headingEntertain">
                                                                <div class="accordion-body">
                                                                    <div id="form-container-e-detail">
                                                                        @foreach ($declareCA['detail_e'] as $detail)
                                                                            <div class="mb-2">
                                                                                <label class="form-label">Entertainment
                                                                                    Type</label>
                                                                                <select name="enter_type_e_detail[]"
                                                                                    id="enter_type_e_detail[]"
                                                                                    class="form-select">
                                                                                    <option value="">-</option>
                                                                                    <option value="food"
                                                                                        {{ $detail['type'] == 'food' ? 'selected' : '' }}>
                                                                                        Food/Beverages/Souvenir</option>
                                                                                    <option value="transport"
                                                                                        {{ $detail['type'] == 'transport' ? 'selected' : '' }}>
                                                                                        Transport</option>
                                                                                    <option value="accommodation"
                                                                                        {{ $detail['type'] == 'accommodation' ? 'selected' : '' }}>
                                                                                        Accommodation</option>
                                                                                    <option value="gift"
                                                                                        {{ $detail['type'] == 'gift' ? 'selected' : '' }}>
                                                                                        Gift</option>
                                                                                    <option value="fund"
                                                                                        {{ $detail['type'] == 'fund' ? 'selected' : '' }}>
                                                                                        Fund</option>
                                                                                </select>
                                                                            </div>
                                                                            <div class="mb-2">
                                                                                <label class="form-label">Entertainment
                                                                                    Fee Detail</label>
                                                                                <textarea name="enter_fee_e_detail[]" id="enter_fee_e_detail[]" class="form-control">{{ $detail['fee_detail'] }}<</textarea>
                                                                            </div>
                                                                            <div class="input-group">
                                                                                <div class="input-group-append">
                                                                                    <span
                                                                                        class="input-group-text">Rp</span>
                                                                                </div>
                                                                                <input class="form-control"
                                                                                    name="nominal_e_detail[]"
                                                                                    id="nominal_e_detail[]" type="text"
                                                                                    min="0"
                                                                                    value="{{ number_format($detail['nominal'], 0, ',', '.') }}">
                                                                            </div>
                                                                            <hr
                                                                                class="border border-primary border-1 opacity-50">
                                                                        @endforeach
                                                                    </div>
                                                                    <div class="mb-2">
                                                                        <label class="form-label">Total
                                                                            Entertain</label>
                                                                        <div class="input-group">
                                                                            <div class="input-group-append">
                                                                                <span class="input-group-text">Rp</span>
                                                                            </div>
                                                                            <input class="form-control bg-light"
                                                                                name="total_e_detail[]"
                                                                                id="total_e_detail[]" type="text"
                                                                                min="0" value="0" readonly>
                                                                        </div>
                                                                    </div>
                                                                    <button type="button" id="add-more-e-detail"
                                                                        class="btn btn-primary mb-3">Add More</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div id="relation-card" class="card-body mb-3 p-0">
                                                    <div class="accordion" id="accordionRelation">
                                                        <div class="accordion-item">
                                                            <h2 class="accordion-header" id="headingRelation">
                                                                <button
                                                                    class="accordion-button @if ($declareCA['relation_e'][0]['name'] === null) collapsed @endif fw-medium"
                                                                    type="button" data-bs-toggle="collapse"
                                                                    data-bs-target="#collapseRelation"
                                                                    aria-expanded="@if ($declareCA['relation_e'][0]['name'] !== null) true @else false @endif"
                                                                    aria-controls="collapseRelation">
                                                                    Relation Plan
                                                                </button>
                                                            </h2>
                                                            <div id="collapseRelation"
                                                                class="accordion-collapse collapse @if ($declareCA['relation_e'][0]['name'] !== null) show @endif"
                                                                aria-labelledby="headingRelation">
                                                                <div class="accordion-body">
                                                                    <div id="form-container-e-relation">
                                                                        @foreach ($declareCA['relation_e'] as $relation)
                                                                            <div class="mb-2">
                                                                                <label class="form-label">Relation
                                                                                    Type</label>
                                                                                <div class="form-check">
                                                                                    <input class="form-check-input"
                                                                                        type="checkbox"
                                                                                        name="accommodation_e_relation[]"
                                                                                        id="accommodation_e_relation[]"
                                                                                        value="accommodation"
                                                                                        {{ isset($relation['relation_type']['Accommodation']) && $relation['relation_type']['Accommodation'] ? 'checked' : '' }}>
                                                                                    <label class="form-check-label"
                                                                                        for="accommodation_e_relation[]">Accommodation</label>
                                                                                </div>
                                                                                <div class="form-check">
                                                                                    <input class="form-check-input"
                                                                                        name="transport_e_relation[]"
                                                                                        type="checkbox"
                                                                                        id="transport_e_relation[]"
                                                                                        value="transport"
                                                                                        {{ isset($relation['relation_type']['Transport']) && $relation['relation_type']['Transport'] ? 'checked' : '' }}>
                                                                                    <label class="form-check-label"
                                                                                        for="transport_e_relation[]">Transport</label>
                                                                                </div>
                                                                                <div class="form-check">
                                                                                    <input class="form-check-input"
                                                                                        name="gift_e_relation[]"
                                                                                        type="checkbox"
                                                                                        id="gift_e_relation[]"
                                                                                        value="gift"
                                                                                        {{ isset($relation['relation_type']['Gift']) && $relation['relation_type']['Gift'] ? 'checked' : '' }}>
                                                                                    <label class="form-check-label"
                                                                                        for="gift_e_relation[]">Gift</label>
                                                                                </div>
                                                                                <div class="form-check">
                                                                                    <input class="form-check-input"
                                                                                        name="fund_e_relation[]"
                                                                                        type="checkbox"
                                                                                        id="fund_e_relation[]"
                                                                                        value="fund"
                                                                                        {{ isset($relation['relation_type']['Fund']) && $relation['relation_type']['Fund'] ? 'checked' : '' }}>
                                                                                    <label class="form-check-label"
                                                                                        for="fund_e_relation[]">Fund</label>
                                                                                </div>
                                                                                <div class="form-check">
                                                                                    <input class="form-check-input"
                                                                                        name="food_e_relation[]"
                                                                                        type="checkbox"
                                                                                        id="food_e_relation[]"
                                                                                        value="food"
                                                                                        {{ isset($relation['relation_type']['Food']) && $relation['relation_type']['Food'] ? 'checked' : '' }}>
                                                                                    <label class="form-check-label"
                                                                                        for="food_e_relation[]">Food/Beverages/Souvenir</label>
                                                                                </div>
                                                                            </div>
                                                                            <div class="mb-2">
                                                                                <label class="form-label"
                                                                                    for="start">Name</label>
                                                                                <input type="text"
                                                                                    name="rname_e_relation[]"
                                                                                    id="rname_e_relation[]"
                                                                                    value="{{ $relation['name'] }}"
                                                                                    class="form-control">
                                                                            </div>
                                                                            <div class="mb-2">
                                                                                <label class="form-label"
                                                                                    for="start">Position</label>
                                                                                <input type="text"
                                                                                    name="rposition_e_relation[]"
                                                                                    id="rposition_e_relation[]"
                                                                                    value="{{ $relation['position'] }}"
                                                                                    class="form-control">
                                                                            </div>
                                                                            <div class="mb-2">
                                                                                <label class="form-label"
                                                                                    for="start">Company</label>
                                                                                <input type="text"
                                                                                    name="rcompany_e_relation[]"
                                                                                    id="rcompany_e_relation[]"
                                                                                    value="{{ $relation['company'] }}"
                                                                                    class="form-control">
                                                                            </div>
                                                                            <div class="mb-2">
                                                                                <label class="form-label"
                                                                                    for="start">Purpose</label>
                                                                                <input type="text"
                                                                                    name="rpurpose_e_relation[]"
                                                                                    id="rpurpose_e_relation[]"
                                                                                    value="{{ $relation['purpose'] }}"
                                                                                    class="form-control">
                                                                            </div>
                                                                            <hr
                                                                                class="border border-primary border-1 opacity-50">
                                                                        @endforeach
                                                                    </div>
                                                                    <button type="button" id="add-more-e-relation"
                                                                        class="btn btn-primary mb-3">Add More</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-2">
                            <label for="prove_declare" class="form-label">Upload Document</label>

                            <!-- Input file -->
                            <input type="file" id="prove_declare" name="prove_declare"
                                accept="image/*, application/pdf" class="form-control" onchange="previewFile()"
                                required>
                            <input type="hidden" name="existing_prove_declare"
                                value="{{ $transactions->prove_declare }}">

                            <!-- Show existing file -->
                            <div id="existing-file-preview" class="mt-2" style="display:none">
                                @if ($transactions->prove_declare)
                                    @php
                                        $extension = pathinfo($transactions->prove_declare, PATHINFO_EXTENSION);
                                    @endphp

                                    @if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif']))
                                        <!-- Tampilkan gambar -->
                                        <a href="{{ asset('uploads/proofs/' . $transactions->prove_declare) }}"
                                            target="_blank">
                                            <img id="existing-image"
                                                src="{{ asset('uploads/proofs/' . $transactions->prove_declare) }}"
                                                alt="Proof Image" style="max-width: 200px;">
                                        </a>
                                        <p>Click on the image to view the full size</p>
                                    @elseif($extension == 'pdf')
                                        <!-- Tampilkan tautan untuk PDF -->
                                        <a id="existing-pdf"
                                            href="{{ asset('uploads/proofs/' . $transactions->prove_declare) }}"
                                            target="_blank">
                                            <img src="https://img.icons8.com/color/48/000000/pdf.png" alt="PDF File"
                                                style="max-width: 48px;">
                                            <p>Click to view PDF</p>
                                        </a>
                                    @else
                                        <p>File type not supported.</p>
                                    @endif
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Total Cash Advanced</label>
                            <div class="input-group">
                                <div class="input-group-append">
                                    <span class="input-group-text">Rp</span>
                                </div>
                                <input class="form-control bg-light" name="totalca" id="totalca_declarasi"
                                    type="text" min="0"
                                    value="{{ number_format($transactions->total_ca, 0, ',', '.') }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Total Cash Advanced Deklarasi</label>
                            <div class="input-group">
                                <div class="input-group-append">
                                    <span class="input-group-text">Rp</span>
                                </div>
                                <input class="form-control bg-light" name="totalca_deklarasi" id="totalca"
                                    type="text" min="0" value="{{ $transactions->total_cost }}" readonly>
                            </div>

                        </div>
                    </div>
                    <input type="hidden" name="no_id" id="no_id" value="{{ $transactions->id }}"
                        class="form-control bg-light" readonly>
                    <input type="hidden" name="no_ca" id="no_ca" value="{{ $transactions->no_ca }}"
                        class="form-control bg-light" readonly>
                    <input type="hidden" name="bisnis_numb" id="bisnis_numb" value="{{ $transactions->no_sppd }}"
                        class="form-control bg-light" readonly>
                    <br>
                    <div class="row">
                        <div class="p-4 col-md d-md-flex justify-content-end text-center">
                            <input type="hidden" name="repeat_days_selected" id="repeatDaysSelected">
                            <a href="{{ route('cashadvanced') }}" type="button"
                                class="btn btn-outline-secondary px-4 me-2">Cancel</a>
                            <button type="submit" name="action_ca_draft" value="Draft"
                                class=" btn btn-secondary btn-pill px-4 me-2">Draft</button>
                            <button type="submit" name="action_ca_submit" value="Pending"
                                class=" btn btn-primary btn-pill px-4 me-2">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    </div>
@endsection
<!-- Tambahkan script JavaScript untuk mengumpulkan nilai repeat_days[] -->
@push('scripts')
    <script>
        function cleanNumber(value) {
            return parseFloat(value.replace(/\./g, '').replace(/,/g, '')) || 0;
        }

        function formatNumber(num) {
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        function formatNumberPerdiem(num) {
            return num.toLocaleString('id-ID');
        }

        function parseNumberPerdiem(value) {
            return parseFloat(value.replace(/\./g, '').replace(/,/g, '')) || 0;
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

        function calculateTotalNominalBTTotal() {
            let total = 0;
            document.querySelectorAll('input[name="total_bt_perdiem"]').forEach(input => {
                total += parseNumber(input.value);
            });
            document.querySelectorAll('input[name="total_bt_transport"]').forEach(input => {
                total += parseNumber(input.value);
            });
            document.querySelectorAll('input[name="total_bt_penginapan"]').forEach(input => {
                total += parseNumber(input.value);
            });
            document.querySelectorAll('input[name="total_bt_lainnya"]').forEach(input => {
                total += parseNumber(input.value);
            });
            document.querySelector('input[name="totalca"]').value = formatNumber(total);
        }
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // ca_type ca_nbt ca_e
            var ca_type = document.getElementById("ca_type");
            var ca_nbt = document.getElementById("ca_nbt");
            var ca_e = document.getElementById("ca_e");
            var div_bisnis_numb = document.getElementById("div_bisnis_numb");
            var bisnis_numb = document.getElementById("bisnis_numb");
            var div_allowance = document.getElementById("div_allowance");

            function toggleDivs() {
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

            toggleDivs();
            ca_type.addEventListener("change", toggleDivs);
        });

        function toggleOthers() {
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
        function previewFile() {
            const fileInput = document.getElementById('prove_declare');
            const file = fileInput.files[0];
            const preview = document.getElementById('existing-file-preview');
            preview.innerHTML = ''; // Kosongkan preview sebelumnya

            if (file) {
                const fileExtension = file.name.split('.').pop().toLowerCase();

                if (['jpg', 'jpeg', 'png', 'gif'].includes(fileExtension)) {
                    const img = document.createElement('img');
                    img.style.maxWidth = '200px';
                    img.src = URL.createObjectURL(file);
                    preview.appendChild(img);
                } else if (fileExtension === 'pdf') {
                    const link = document.createElement('a');
                    link.href = URL.createObjectURL(file);
                    link.target = '_blank';
                    const icon = document.createElement('img');
                    icon.src = "https://img.icons8.com/color/48/000000/pdf.png";
                    icon.style.maxWidth = '48px';
                    link.appendChild(icon);
                    const text = document.createElement('p');
                    text.textContent = "Click to view PDF";
                    preview.appendChild(link);
                    preview.appendChild(text);
                } else {
                    preview.textContent = 'File type not supported.';
                }
            }
        }
    </script>

    <script>
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
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-beta3/js/bootstrap.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        console.log('jQuery available?', typeof $ !== 'undefined');
        console.log('select2 available?', typeof $.fn.select2 !== 'undefined');
    </script>
@endpush

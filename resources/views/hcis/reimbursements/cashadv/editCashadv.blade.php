@extends('layouts_.vertical', ['page_title' => 'Cash Advanced'])

@section('css')
    <!-- Sertakan CSS Bootstrap jika diperlukan -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-beta3/css/bootstrap.min.css">
@endsection

@section('content')
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
        <div class="d-sm-flex align-items-center justify-content-center">
            <div class="card col-md-12">
                <div class="card-header d-flex bg-primary justify-content-between">
                    <h4 class="modal-title text-white" id="viewFormEmployeeLabel">Edit Cash Advance -
                        <b>{{ $transactions->no_ca }}</b></h4>
                    <a href="{{ route('cashadvanced') }}" type="button" class="btn btn-close btn-close-white"></a>
                </div>
                <div class="card-body" @style('overflow-y: auto;')>
                    <div class="container-fluid">
                        <form id="cashadvancedForm" method="post"
                            action="{{ route('cashadvanced.update', encrypt($transactions->id)) }}">@csrf
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <label class="form-label" for="start">Employee ID</label>
                                    <input type="text" name="name" id="name"
                                        value="{{ $employee_data->employee_id }}" class="form-control bg-light" readonly>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label class="form-label" for="start">Employee Name</label>
                                    <input type="text" name="name" id="name"
                                        value="{{ $employee_data->fullname }}" class="form-control bg-light" readonly>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <label class="form-label" for="start">Unit</label>
                                    <input type="text" name="unit" id="unit" value="{{ $employee_data->unit }}"
                                        class="form-control bg-light" readonly>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label class="form-label" for="start">Job Level</label>
                                    <input type="text" name="grade" id="grade"
                                        value="{{ $employee_data->job_level }}" class="form-control bg-light" readonly>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <label class="form-label" for="name">Costing Company</label>
                                    <select class="form-control select2" id="companyFilter" name="companyFilter" required>
                                        <option value="">Select Company...</option>
                                        @foreach ($companies as $company)
                                            <option value="{{ $company->contribution_level_code }}"
                                                {{ $company->contribution_level_code == $transactions->contribution_level_code ? 'selected' : '' }}>
                                                {{ $company->contribution_level . ' (' . $company->contribution_level_code . ')' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label class="form-label" for="name">Destination</label>
                                    <select class="form-control select2" id="locationFilter" name="locationFilter"
                                        onchange="toggleOthers()" required>
                                        <option value="">Select location...</option>
                                        <p>{{ $transactions->destination }}</p>
                                        @foreach ($locations as $location)
                                            <option value="{{ $location->area }}"
                                                {{ $location->area == $transactions->destination ? 'selected' : '' }}>
                                                {{ $location->area . ' (' . $location->company_name . ')' }}
                                            </option>
                                        @endforeach
                                        <option value="Others"
                                            {{ $transactions->destination == 'Others' ? 'selected' : '' }}>Others</option>
                                    </select>
                                    <br><br><input type="text" name="others_location" id="others_location"
                                        class="form-control" placeholder="Other Location"
                                        value="{{ $transactions->others_location }}"
                                        style="{{ $transactions->destination == 'Others' ? 'display: block;' : 'display: none;' }}">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 mb-2">
                                    <label class="form-label" for="name">CA Purposes</label>
                                    <textarea name="ca_needs" id="ca_needs" class="form-control">{{ $transactions->ca_needs }}</textarea>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 mb-2">
                                    <label class="form-label" for="start">Start Date</label>
                                    <input type="date" name="start_date" id="start_date" class="form-control"
                                        value="{{ $transactions->start_date }}" required>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="form-label" for="start">End Date</label>
                                    <input type="date" name="end_date" id="end_date" class="form-control"
                                        value="{{ $transactions->end_date }}" required>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="form-label" for="start">Total Days</label>
                                    <div class="input-group">
                                        <input class="form-control bg-light" id="totaldays" name="totaldays"
                                            type="text" min="0" value="{{ $transactions->total_days }}"
                                            readonly>
                                        <div class="input-group-append">
                                            <span class="input-group-text">days</span>
                                        </div>
                                    </div>
                                    <input class="form-control" id="perdiem" name="perdiem" type="hidden"
                                        value="{{ $perdiem->amount }}" readonly>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <label class="form-label" for="start">CA Date Required</label>
                                    <input type="date" name="ca_required" id="ca_required" class="form-control"
                                        value="{{ $transactions->date_required }}" required>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <div class="mb-2">
                                        <label class="form-label" for="start">Declaration Estimate</label>
                                        <input type="date" name="ca_decla" id="ca_decla" class="form-control bg-light" value="{{ $transactions->declare_estimate }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label class="form-label" for="type">CA Type</label>
                                    <select name="ca_type_disabled" id="ca_type_disabled" class="form-control bg-light" disabled>
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
                                    <input type="hidden" name="ca_type" id="ca_type" value="{{ $transactions->type_ca }}">
                                </div>
                                <div class="col-md-6 mb-2">
                                    <div class="mb-2">
                                        <label class="form-label" for="bisnis_numb">Business Trip Number</label>
                                        <input type="text" name="bisnis_numb" id="bisnis_numb" class="form-control bg-light" value="{{ $transactions->no_sppd ?? 'Tidak ada Bussiness Trip Number' }}" readonly>
                                    </div>
                                </div>
                            </div>
                            @php
                                $detailCA = json_decode($transactions->detail_ca, true) ?? [];
                            @endphp
                            <script>
                                // Pass the PHP array into a JavaScript variable
                                const initialDetailCA = @json($detailCA);
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
                                                        @include('hcis.reimbursements.cashadv.form.perdiem')
                                                    </div>
                                                    <div class="tab-pane fade {{ !isset($detailCA['detail_perdiem'][0]['start_date']) && isset($detailCA['detail_transport'][0]['tanggal']) && $detailCA['detail_transport'][0]['tanggal'] ? 'show active' : '' }}"
                                                        id="pills-transport" role="tabpanel"
                                                        aria-labelledby="pills-transport-tab">
                                                        @include('hcis.reimbursements.cashadv.form.transport')
                                                    </div>
                                                    <div class="tab-pane fade {{ !isset($detailCA['detail_perdiem'][0]['start_date']) && !isset($detailCA['detail_transport'][0]['tanggal']) && isset($detailCA['detail_penginapan'][0]['start_date']) && $detailCA['detail_penginapan'][0]['start_date'] ? 'show active' : '' }}"
                                                        id="pills-accomodation" role="tabpanel"
                                                        aria-labelledby="pills-accomodation-tab">
                                                        @include('hcis.reimbursements.cashadv.form.penginapan')
                                                    </div>
                                                    <div class="tab-pane fade {{ !isset($detailCA['detail_perdiem'][0]['start_date']) && !isset($detailCA['detail_transport'][0]['tanggal']) && !isset($detailCA['detail_penginapan'][0]['start_date']) && isset($detailCA['detail_lainnya'][0]['tanggal']) && $detailCA['detail_lainnya'][0]['tanggal'] ? 'show active' : '' }}" id="pills-other" role="tabpanel"
                                                        aria-labelledby="pills-other-tab">
                                                        @include('hcis.reimbursements.cashadv.form.others')
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
                                            <div class="d-flex flex-column gap-2">
                                                @include('hcis.reimbursements.cashadv.form.nbt')
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="row" id="ca_e" style="display: none;">
                                @if ($transactions->type_ca == 'entr')
                                    <div class="col-md-12">
                                        <div class="table-responsive-sm">
                                            <div class="d-flex flex-column gap-2">
                                                <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                                                    <li class="nav-item" role="presentation">
                                                        <button class="nav-link active" id="pills-detail-tab"
                                                            data-bs-toggle="pill" data-bs-target="#pills-detail" type="button"
                                                            role="tab" aria-controls="pills-detail"
                                                            aria-selected="true">Detail Entertain Plan</button>
                                                    </li>
                                                    <li class="nav-item" role="presentation">
                                                        <button class="nav-link" id="pills-relation-tab" data-bs-toggle="pill"
                                                            data-bs-target="#pills-relation" type="button" role="tab"
                                                            aria-controls="pills-relation" aria-selected="false">Relation Entertain
                                                            Plan</button>
                                                    </li>
                                                </ul>
                                                <div class="tab-content" id="pills-tabContent">
                                                    <div class="tab-pane fade show active" id="pills-detail" role="tabpanel"
                                                        aria-labelledby="pills-detail-tab">
                                                        @include('hcis.reimbursements.cashadv.form.detail')
                                                    </div>
                                                    <div class="tab-pane fade" id="pills-relation" role="tabpanel"
                                                        aria-labelledby="pills-relation-tab">
                                                        @include('hcis.reimbursements.cashadv.form.relation')
                                                    </div>
                                                </div>
                                                <button type="button" id="add-more-e-detail" style="display: none"
                                                    class="btn btn-primary mt-3">Add More</button>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="row" id="ca_e" style="display: none;">
                                @if ($transactions->type_ca == 'entr')
                                    <div class="col-md-12">
                                        <div class="table-responsive-sm">
                                            <div class="d-flex flex-column gap-2">
                                                <div class="text-bg-danger p-2" style="text-align:center">Estimated Entertainment</div>
                                                    <div class="card">
                                                        <div class="card-body text-center">
                                                            <button type="button" style="width: 60%" id="toggle-e-detail" class="btn btn-primary mt-3" data-state="false"><i class="bi bi-plus-circle"></i> Entertain</button>
                                                        </div>
                                                        <div id="entertain-card" class="card-body" style="display: none;">
                                                            <div class="accordion" id="accordionEntertain">
                                                                <div class="accordion-item">
                                                                    <h2 class="accordion-header" id="headingEntertain">
                                                                        <button class="accordion-button fw-medium" type="button" data-bs-toggle="collapse" data-bs-target="#collapseEntertain" aria-expanded="true" aria-controls="collapseEntertain">
                                                                            Rencana Entertain
                                                                        </button>
                                                                    </h2>
                                                                    <div id="collapseEntertain" class="accordion-collapse collapse show" aria-labelledby="headingEntertain">
                                                                        <div class="accordion-body">
                                                                            <div id="form-container-e-detail">
                                                                                @foreach ($detailCA['detail_e'] as $detail)
                                                                                    <div class="mb-2">
                                                                                        <label class="form-label">Entertainment Type</label>
                                                                                        <select name="enter_type_e_detail[]" id="enter_type_e_detail[]" class="form-select">
                                                                                            <option value="">-</option>
                                                                                            <option value="food" {{ $detail['type'] == 'food' ? 'selected' : '' }}>Food/Beverages/Souvenir</option>
                                                                                            <option value="transport" {{ $detail['type'] == 'transport' ? 'selected' : '' }}>Transport</option>
                                                                                            <option value="accommodation" {{ $detail['type'] == 'accommodation' ? 'selected' : '' }}>Accommodation</option>
                                                                                            <option value="gift" {{ $detail['type'] == 'gift' ? 'selected' : '' }}>Gift</option>
                                                                                            <option value="fund" {{ $detail['type'] == 'fund' ? 'selected' : '' }}>Fund</option>
                                                                                        </select>
                                                                                    </div>
                                                                                    <div class="mb-2">
                                                                                        <label class="form-label">Entertainment Fee Detail</label>
                                                                                        <textarea name="enter_fee_e_detail[]" id="enter_fee_e_detail[]" class="form-control">{{ $detail['fee_detail'] }}<</textarea>
                                                                                    </div>
                                                                                    <div class="input-group">
                                                                                        <div class="input-group-append">
                                                                                            <span class="input-group-text">Rp</span>
                                                                                        </div>
                                                                                        <input class="form-control" name="nominal_e_detail[]" id="nominal_e_detail[]" type="text" min="0" value="{{ number_format($detail['nominal'], 0, ',', '.') }}">
                                                                                    </div>
                                                                                    <hr class="border border-primary border-1 opacity-50">
                                                                                @endforeach
                                                                            </div>
                                                                            <div class="mb-2">
                                                                                <label class="form-label">Total Entertain</label>
                                                                                <div class="input-group">
                                                                                    <div class="input-group-append">
                                                                                        <span class="input-group-text">Rp</span>
                                                                                    </div>
                                                                                    <input class="form-control bg-light" name="total_e_detail[]" id="total_e_detail[]" type="text" min="0" value="0" readonly>
                                                                                </div>
                                                                            </div>
                                                                            <button type="button" id="add-more-e-detail" class="btn btn-primary mt-3">Add More</button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="card-body text-center">
                                                            <button type="button" style="width: 60%" id="toggle-e-relation" class="btn btn-primary mt-3" data-state="false"><i class="bi bi-plus-circle"></i> Relation</button>
                                                        </div>
                                                        <div id="relation-card" class="card-body" style="display: none;">
                                                            <div class="accordion" id="accordionRelation">
                                                                <div class="accordion-item">
                                                                    <h2 class="accordion-header" id="headingRelation">
                                                                        <button class="accordion-button fw-medium" type="button" data-bs-toggle="collapse" data-bs-target="#collapseRelation" aria-expanded="true" aria-controls="collapseRelation">
                                                                            Rencana Relation
                                                                        </button>
                                                                    </h2>
                                                                    <div id="collapseRelation" class="accordion-collapse collapse show" aria-labelledby="headingRelation">
                                                                        <div class="accordion-body">
                                                                            <div id="form-container-e-relation">
                                                                                @foreach($detailCA['relation_e'] as $relation)
                                                                                    <div class="mb-2">
                                                                                        <label class="form-label">Relation Type</label>
                                                                                        <div class="form-check">
                                                                                            <input class="form-check-input" type="checkbox" name="accommodation_e_relation[]" id="accommodation_e_relation[]" value="accommodation" {{ isset($relation['relation_type']['Accommodation']) && $relation['relation_type']['Accommodation'] ? 'checked' : '' }}>
                                                                                            <label class="form-check-label" for="accommodation_e_relation[]">Accommodation</label>
                                                                                        </div>
                                                                                        <div class="form-check">
                                                                                            <input class="form-check-input" name="transport_e_relation[]" type="checkbox" id="transport_e_relation[]" value="transport" {{ isset($relation['relation_type']['Transport']) && $relation['relation_type']['Transport'] ? 'checked' : '' }}>
                                                                                            <label class="form-check-label" for="transport_e_relation[]">Transport</label>
                                                                                        </div>
                                                                                        <div class="form-check">
                                                                                            <input class="form-check-input" name="gift_e_relation[]" type="checkbox" id="gift_e_relation[]" value="gift" {{ isset($relation['relation_type']['Gift']) && $relation['relation_type']['Gift'] ? 'checked' : '' }}>
                                                                                            <label class="form-check-label" for="gift_e_relation[]">Gift</label>
                                                                                        </div>
                                                                                        <div class="form-check">
                                                                                            <input class="form-check-input" name="fund_e_relation[]" type="checkbox" id="fund_e_relation[]" value="fund" {{ isset($relation['relation_type']['Fund']) && $relation['relation_type']['Fund'] ? 'checked' : '' }}>
                                                                                            <label class="form-check-label" for="fund_e_relation[]">Fund</label>
                                                                                        </div>
                                                                                        <div class="form-check">
                                                                                            <input class="form-check-input" name="food_e_relation[]" type="checkbox" id="food_e_relation[]" value="food" {{ isset($relation['relation_type']['Food']) && $relation['relation_type']['Food'] ? 'checked' : '' }}>
                                                                                            <label class="form-check-label" for="food_e_relation[]">Food/Beverages/Souvenir</label>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="mb-2">
                                                                                        <label class="form-label" for="start">Name</label>
                                                                                        <input type="text" name="rname_e_relation[]" id="rname_e_relation[]" value="{{ $relation['name'] }}" class="form-control">
                                                                                    </div>
                                                                                    <div class="mb-2">
                                                                                        <label class="form-label" for="start">Position</label>
                                                                                        <input type="text" name="rposition_e_relation[]" id="rposition_e_relation[]" value="{{ $relation['position'] }}" class="form-control">
                                                                                    </div>
                                                                                    <div class="mb-2">
                                                                                        <label class="form-label" for="start">Company</label>
                                                                                        <input type="text" name="rcompany_e_relation[]" id="rcompany_e_relation[]" value="{{ $relation['company'] }}" class="form-control">
                                                                                    </div>
                                                                                    <div class="mb-2">
                                                                                        <label class="form-label" for="start">Purpose</label>
                                                                                        <input type="text" name="rpurpose_e_relation[]" id="rpurpose_e_relation[]" value="{{ $relation['purpose'] }}" class="form-control">
                                                                                    </div>
                                                                                    <hr class="border border-primary border-1 opacity-50">
                                                                                @endforeach
                                                                            </div>
                                                                            <button type="button" id="add-more-e-relation" class="btn btn-primary mt-3">Add More</button>
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
                                <div class="col-md-12 mb-2 mt-3">
                                    <label class="form-label">Total Cash Advanced</label>
                                    <div class="input-group">
                                        <div class="input-group-append">
                                            <span class="input-group-text">Rp</span>
                                        </div>
                                        <input class="form-control bg-light" name="totalca" id="totalca"
                                            type="text" min="0" value="{{ number_format( $transactions->total_ca , 0, ',', '.') }}" readonly>
                                    </div>
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
                            <a href="{{ route('cashadvanced') }}" type="button" class="btn mb-2 btn-outline-secondary px-4 me-2">Cancel</a>
                            <button type="submit" name="action_ca_draft" value="Draft" class="btn mb-2 btn-secondary btn-pill px-4 me-2 submit-button">Draft</button>
                            <button type="submit" name="action_ca_submit" value="Pending" class="btn mb-2 btn-primary btn-pill px-4 me-2 submit-button">Submit</button>
                        </div>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>

    @include('hcis.reimbursements.cashadv.navigation.modalCashadv')
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
                } else if (ca_type.value === "ndns"){
                    ca_bt.style.display = "none";
                    ca_nbt.style.display = "block";
                    ca_e.style.display = "none";
                    div_bisnis_numb.style.display = "none";
                    bisnis_numb.style.value = "";
                    div_allowance.style.display = "none";
                } else if (ca_type.value === "entr"){
                    ca_bt.style.display = "none";
                    ca_nbt.style.display = "none";
                    ca_e.style.display = "block";
                    div_bisnis_numb.style.display = "block";
                } else{
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

            function calculateTotalDays() {
                const startDate = new Date(startDateInput.value);
                const endDate = new Date(endDateInput.value);

                // Memastikan kedua tanggal valid
                if (startDate && endDate && !isNaN(startDate) && !isNaN(endDate)) {
                    const timeDiff = endDate - startDate;
                    const daysDiff = Math.ceil(timeDiff / (1000 * 60 * 60 * 24));
                    const totalDays = daysDiff > 0 ? daysDiff + 1 : 0; // Menambahkan 1 untuk menghitung hari awal
                    totalDaysInput.value = totalDays;
                } else {
                    totalDaysInput.value = 0; // Mengatur ke 0 jika tidak valid
                }
                calculateTotalCA();
            }

            // function formatInput(input) {
            //     let value = input.value.replace(/\./g, '');
            //     value = parseFloat(value);
            //     if (!isNaN(value)) {
            //         // input.value = formatNumber(value);
            //         input.value = formatNumber(Math.floor(value));
            //     } else {
            //         input.value = formatNumber(0);
            //     }

            //     calculateTotalCA();
            // }

            // function calculateTotalCA() {
            //     const allowance = parseNumber(allowanceInput.value);
            //     const transport = parseNumber(transportInput.value);
            //     const accommodation = parseNumber(accommodationInput.value);
            //     const other = parseNumber(otherInput.value);
            //     const nominal_1 = parseNumber(nominal_1Input.value);
            //     const nominal_2 = parseNumber(nominal_2Input.value);
            //     const nominal_3 = parseNumber(nominal_3Input.value);
            //     const nominal_4 = parseNumber(nominal_4Input.value);
            //     const nominal_5 = parseNumber(nominal_5Input.value);

            //     // Perbaiki penulisan caTypeInput.value
            //     const ca_type = caTypeInput.value;

            //     let totalca = 0;
            //     if (ca_type === 'dns') {
            //         totalca = allowance + transport + accommodation + other;
            //     } else if (ca_type === 'ndns') {
            //         totalca = transport + accommodation + other;
            //         allowanceInput.value = 0;
            //     } else if (ca_type === 'entr') {
            //         totalca = nominal_1 + nominal_2 + nominal_3 + nominal_4 + nominal_5;
            //         allowanceInput.value = 0;
            //     }

            //     // totalcaInput.value = formatNumber(totalca.toFixed(2));
            //     totalcaInput.value = formatNumber(Math.floor(totalca));
            // }

            // Menambahkan event listener untuk perubahan di input tanggal
            startDateInput.addEventListener('change', calculateTotalDays);
            endDateInput.addEventListener('change', calculateTotalDays);
        });

        document.getElementById('end_date').addEventListener('change', function() {
            const endDate = new Date(this.value);
            const declarationEstimateDate = new Date(endDate);

            // Menambahkan 3 hari kerja
            let daysToAdd = 0;
            while (daysToAdd < 3) {
                declarationEstimateDate.setDate(declarationEstimateDate.getDate() + 1);
                // Jika bukan Sabtu (6) dan bukan Minggu (0), kita tambahkan hari
                if (declarationEstimateDate.getDay() !== 6 && declarationEstimateDate.getDay() !== 0) {
                    daysToAdd++;
                }
            }

            const year = declarationEstimateDate.getFullYear();
            const month = String(declarationEstimateDate.getMonth() + 1).padStart(2, '0');
            const day = String(declarationEstimateDate.getDate()).padStart(2, '0');

            document.getElementById('ca_decla').value = `${year}-${month}-${day}`;
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
                Swal.fire({
                    title: 'Cannot Sett Date!',
                    text: 'End Date cannot be earlier than Start Date.',
                    icon: 'warning',
                    confirmButtonColor: "#9a2a27",
                    confirmButtonText: 'Ok',
                });
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
@endpush

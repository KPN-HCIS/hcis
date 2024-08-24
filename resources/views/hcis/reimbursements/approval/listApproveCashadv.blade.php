@extends('layouts_.vertical', ['page_title' => 'Approve Cash Advanced'])

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
                            <li class="breadcrumb-item"><a href="{{ route('approval.cashadvanced') }}">{{ $parentLink }}</a></li>
                            <li class="breadcrumb-item active">{{ $link }}</li>
                        </ol>
                    </div>
                    <h4 class="page-title">{{ $link }}</h4>
                </div>
            </div>
        </div>
        <div class="d-sm-flex align-items-center justify-content-center">
            <div class="card col-md-12">
                <div class="card-header d-flex bg-white justify-content-between">
                    <a href=""></a>
                    <h4 class="modal-title" id="viewFormEmployeeLabel">Approval Data - <b>"{{ $transactions->no_ca }}"</h4>
                    <a href="{{ route('approval.cashadvanced') }}" type="button" class="btn btn-close"></a>
                </div>
                <div class="card-body" @style('overflow-y: auto;')>
                    <div class="container-fluid">
                        <form id="scheduleForm" method="post" action="{{ route('approval.cashadvancedApproved', $transactions->id) }}">@csrf
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <label class="form-label" for="start">Employee ID</label>
                                    <input type="text" name="name" id="name"
                                        value="{{ $employee_data->employee_id }}" class="form-control bg-light"
                                        style="cursor: none;" readonly>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label class="form-label" for="start">Employee Name</label>
                                    <input type="text" name="name" id="name"
                                        value="{{ $employee_data->fullname }}" class="form-control bg-light"
                                        style="cursor: none;" readonly>
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
                                    <select class="form-control bg-light" id="companyFilter" name="companyFilter" disabled>
                                        <option value="">Select Company...</option>
                                        @foreach($companies as $company)
                                        <option value="{{ $company->contribution_level_code }}" {{ $company->contribution_level_code == $transactions->contribution_level_code ? 'selected' : '' }}>
                                            {{ $company->contribution_level." (".$company->contribution_level_code.")" }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label class="form-label" for="name">Destination</label>
                                    <select class="form-control bg-light" id="locationFilter" name="locationFilter" onchange="toggleOthers()" disabled>
                                        <option value="">Select location...</option>
                                        <p>{{ $transactions->destination }}</p>
                                        @foreach($locations as $location)
                                        <option value="{{ $location->area }}" {{ $location->area == $transactions->destination ? 'selected' : '' }}>
                                            {{ $location->area." (".$location->company_name.")" }}
                                        </option>
                                        @endforeach
                                        <option value="Others" {{ $transactions->destination == 'Others' ? 'selected' : '' }}>Others</option>
                                    </select>
                                    <br><input type="text" name="others_location" id="others_location" class="form-control bg-light" placeholder="Other Location" value="{{ $transactions->others_location }}" readonly style="{{ $transactions->destination == 'Others' ? 'display: block;' : 'display: none;' }}">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 mb-2">
                                    <label class="form-label" for="name">CA Purposes</label>
                                    <textarea name="ca_needs" id="ca_needs" class="form-control bg-light" disabled>{{ $transactions->ca_needs }}</textarea>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 mb-2">
                                    <label class="form-label" for="start">Start Date</label>
                                    <input type="date" name="start_date" id="start_date" class="form-control bg-light" value="{{ $transactions->start_date }}" readonly>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="form-label" for="start">End Date</label>
                                    <input type="date" name="end_date" id="end_date" class="form-control bg-light" value="{{ $transactions->end_date }}" readonly>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="form-label" for="start">Total Days</label>
                                    <div class="input-group">
                                        <input class="form-control bg-light" id="totaldays" name="totaldays" type="text" min="0" value="{{ $transactions->total_days }}" readonly>
                                        <div class="input-group-append">
                                            <span class="input-group-text">days</span>
                                        </div>
                                    </div>
                                    <input class="form-control" id="perdiem" name="perdiem" type="hidden" value="{{ $perdiem->amount }}" readonly>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <label class="form-label" for="start">CA Date Required</label>
                                    <input type="date" name="ca_required" id="ca_required" class="form-control bg-light" value="{{ $transactions->date_required }}" readonly>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <div class="mb-2">
                                        <label class="form-label" for="start">Declaration Estimate</label>
                                        <input type="date" name="ca_decla" id="ca_decla" class="form-control bg-light" value="{{ $transactions->declare_estimate }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label class="form-label" for="type">CA Type</label>
                                    <select name="ca_type" id="ca_type" class="form-control bg-light" disabled>
                                        <option value="">-</option>
                                        <option value="dns" {{ $transactions->type_ca == 'dns' ? 'selected' : '' }}>Business Trip</option>
                                        <option value="ndns" {{ $transactions->type_ca == 'ndns' ? 'selected' : '' }}>Non Business Trip</option>
                                        <option value="entr" {{ $transactions->type_ca == 'entr' ? 'selected' : '' }}>Entertainment</option>
                                    </select>
                                </div>
                            </div>
                            @php
                                $details = json_decode($transactions->detail_ca, true) ?? [];
                            @endphp
                            <div class="row" id="div_bisnis_numb" style="display: none;">
                                <div class="col-md-12 mb-2">
                                    <label class="form-label" for="name">Business Trip Number</label>
                                    <select class="form-control select2" id="bisnis_numb" name="bisnis_numb">
                                        <option value="">Select</option>
                                        @foreach ($no_sppds as $no_sppd)
                                            <option value="{{ $no_sppd->no_sppd }}">{{ $no_sppd->no_sppd }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @php
                                $detailCA = json_decode($transactions->detail_ca, true) ?? [];
                            @endphp
                            <br>

                            <div class="row" id="ca_bt" style="display: none;">
                                <div class="col-md-12">
                                    <div class="table-responsive-sm">
                                        <div class="d-flex flex-column gap-2">
                                            <div class="text-bg-danger p-2" style="text-align:center">Estimated Cash Advanced</div>
                                            <div class="card">
                                                <div class="card-body text-center">
                                                    <button type="button" style="width: 60%" id="toggle-bt-perdiem" class="btn btn-primary mt-3" data-state="false"><i class="bi bi-plus-circle"></i> Perdiem</button>
                                                </div>
                                                <div id="perdiem-card" class="card-body" style="display: none;">
                                                    <div class="accordion" id="accordionPerdiem">
                                                        <div class="accordion-item">
                                                            <h2 class="accordion-header" id="enter-headingOne">
                                                                <button class="accordion-button fw-medium" type="button" data-bs-toggle="collapse" data-bs-target="#enter-collapseOne" aria-expanded="true" aria-controls="enter-collapseOne">
                                                                    Rencana Perdiem
                                                                </button>
                                                            </h2>
                                                            <div id="enter-collapseOne" class="accordion-collapse show" aria-labelledby="enter-headingOne">
                                                                <div class="accordion-body">
                                                                    <div id="form-container-bt-perdiem">
                                                                        <div class="mb-2">
                                                                            <label class="form-label">Start Perdiem</label>
                                                                            <input type="date" name="start_bt_perdiem[]" class="form-control start-perdiem" placeholder="mm/dd/yyyy">
                                                                        </div>
                                                                        <div class="mb-2">
                                                                            <label class="form-label">End Perdiem</label>
                                                                            <input type="date" name="end_bt_perdiem[]" class="form-control end-perdiem" placeholder="mm/dd/yyyy">
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
                                                                        <!-- HTML -->
                                                                        <div class="mb-2">
                                                                            <label class="form-label" for="name">Location Agency</label>
                                                                            <select class="form-control select2 location-select" name="location_bt_perdiem[]">
                                                                                <option value="">Select location...</option>
                                                                                @foreach($locations as $location)
                                                                                    <option value="{{ $location->area }}">{{ $location->area." (".$location->company_name.")" }}</option>
                                                                                @endforeach
                                                                                <option value="Others">Others</option>
                                                                            </select>
                                                                            <br>
                                                                            <input type="text" name="other_location_bt_perdiem[]" class="form-control other-location" placeholder="Other Location" value="" style="display: none;">
                                                                        </div>
                                                                        <div class="mb-2">
                                                                            <label class="form-label" for="name">Company Code</label>
                                                                            <select class="form-control select2" id="companyFilter" name="company_bt_perdiem[]">
                                                                                <option value="">Select Company...</option>
                                                                                @foreach($companies as $company)
                                                                                    <option value="{{ $company->contribution_level_code }}">{{ $company->contribution_level." (".$company->contribution_level_code.")" }}</option>
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
                                                                            <input class="form-control" name="nominal_bt_perdiem[]" id="nominal_bt_perdiem" type="text" min="0" value="0">
                                                                        </div>
                                                                        <hr class="border border-primary border-1 opacity-50">
                                                                    </div>
                                                                    <div class="mb-2">
                                                                        <label class="form-label">Total Perdiem</label>
                                                                        <div class="input-group">
                                                                            <div class="input-group-append">
                                                                                <span class="input-group-text">Rp</span>
                                                                            </div>
                                                                            <input class="form-control bg-light" name="total_bt_perdiem[]" id="total_bt_perdiem[]" type="text" min="0" value="0" readonly>
                                                                        </div>
                                                                    </div>
                                                                    <button type="button" id="add-more-bt-perdiem" class="btn btn-primary mt-3">Add More</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Button and Card for Transport -->
                                                <div class="card-body text-center">
                                                    <button type="button" style="width: 60%" id="toggle-bt-transport" class="btn btn-primary mt-3" data-state="false"><i class="bi bi-plus-circle"></i> Transport</button>
                                                </div>
                                                <div id="transport-card" class="card-body" style="display: none;">
                                                    <div class="accordion" id="accordionTransport">
                                                        <div class="accordion-item">
                                                            <h2 class="accordion-header" id="headingTransport">
                                                                <button class="accordion-button fw-medium" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTransport" aria-expanded="true" aria-controls="collapseTransport">
                                                                    Rencana Transport
                                                                </button>
                                                            </h2>
                                                            <div id="collapseTransport" class="accordion-collapse collapse show" aria-labelledby="headingTransport">
                                                                <div class="accordion-body">
                                                                    <div id="form-container-bt-transport">
                                                                        <div class="mb-2">
                                                                            <label class="form-label">Tanggal Transport</label>
                                                                            <input type="date" name="tanggal_bt_transport[]" class="form-control" placeholder="mm/dd/yyyy">
                                                                        </div>
                                                                        <div class="mb-2">
                                                                            <label class="form-label" for="name">Company Code</label>
                                                                            <select class="form-control select2" id="companyFilter" name="company_bt_transport[]">
                                                                                <option value="">Select Company...</option>
                                                                                @foreach($companies as $company)
                                                                                    <option value="{{ $company->contribution_level_code }}">{{ $company->contribution_level." (".$company->contribution_level_code.")" }}</option>
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
                                                                            <input class="form-control" name="nominal_bt_transport[]" id="nominal_bt_transport[]" type="text" min="0" value="0">
                                                                        </div>
                                                                        <hr class="border border-primary border-1 opacity-50">
                                                                    </div>
                                                                    <div class="mb-2">
                                                                        <label class="form-label">Total Transport</label>
                                                                        <div class="input-group">
                                                                            <div class="input-group-append">
                                                                                <span class="input-group-text">Rp</span>
                                                                            </div>
                                                                            <input class="form-control bg-light" name="total_bt_transport[]" id="total_bt_transport[]" type="text" min="0" value="0" readonly>
                                                                        </div>
                                                                    </div>
                                                                    <button type="button" id="add-more-bt-transport" class="btn btn-primary mt-3">Add More</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Button and Card for Penginapan -->
                                                <div class="card-body text-center">
                                                    <button type="button" style="width: 60%" id="toggle-bt-penginapan" class="btn btn-primary mt-3" data-state="false"><i class="bi bi-plus-circle"></i> Penginapan</button>
                                                </div>
                                                <div id="penginapan-card" class="card-body" style="display: none;">
                                                    <div class="accordion" id="accordionPenginapan">
                                                        <div class="accordion-item">
                                                            <h2 class="accordion-header" id="headingPenginapan">
                                                                <button class="accordion-button fw-medium" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePenginapan" aria-expanded="true" aria-controls="collapsePenginapan">
                                                                    Rencana Penginapan
                                                                </button>
                                                            </h2>
                                                            <div id="collapsePenginapan" class="accordion-collapse collapse show" aria-labelledby="headingPenginapan">
                                                                <div class="accordion-body">
                                                                    <div id="form-container-bt-penginapan">
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
                                                                                @foreach($companies as $company)
                                                                                    <option value="{{ $company->contribution_level_code }}">{{ $company->contribution_level." (".$company->contribution_level_code.")" }}</option>
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
                                                                        <hr class="border border-primary border-1 opacity-50">
                                                                    </div>
                                                                    <div class="mb-2">
                                                                        <label class="form-label">Total Penginapan</label>
                                                                        <div class="input-group">
                                                                            <div class="input-group-append">
                                                                                <span class="input-group-text">Rp</span>
                                                                            </div>
                                                                            <input class="form-control bg-light" name="total_bt_penginapan[]" id="total_bt_penginapan" type="text" min="0" value="0" readonly>
                                                                        </div>
                                                                    </div>
                                                                    <button type="button" id="add-more-bt-penginapan" class="btn btn-primary mt-3">Add More</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>


                                                <!-- Button and Card for Lainnya -->
                                                <div class="card-body text-center">
                                                    <button type="button" style="width: 60%" id="toggle-bt-lainnya" class="btn btn-primary mt-3" data-state="false"><i class="bi bi-plus-circle"></i> Lainnya</button>
                                                </div>
                                                <div id="lainnya-card" class="card-body" style="display: none;">
                                                    <div class="accordion" id="accordionLainnya">
                                                        <div class="accordion-item">
                                                            <h2 class="accordion-header" id="headingLainnya">
                                                                <button class="accordion-button fw-medium" type="button" data-bs-toggle="collapse" data-bs-target="#collapseLainnya" aria-expanded="true" aria-controls="collapseLainnya">
                                                                    Rencana Lainnya
                                                                </button>
                                                            </h2>
                                                            <div id="collapseLainnya" class="accordion-collapse collapse show" aria-labelledby="headingLainnya">
                                                                <div class="accordion-body">
                                                                    <div id="form-container-bt-lainnya">
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
                                                                            <input class="form-control" name="nominal_bt_lainnya[]" id="nominal_bt_lainnya" type="text" min="0" value="0">
                                                                        </div>
                                                                        <hr class="border border-primary border-1 opacity-50">
                                                                    </div>
                                                                    <div class="mb-2">
                                                                        <label class="form-label">Total Lainnya</label>
                                                                        <div class="input-group">
                                                                            <div class="input-group-append">
                                                                                <span class="input-group-text">Rp</span>
                                                                            </div>
                                                                            <input class="form-control bg-light" name="total_bt_lainnya[]" id="total_bt_lainnya" type="text" min="0" value="0" readonly>
                                                                        </div>
                                                                    </div>
                                                                    <button type="button" id="add-more-bt-lainnya" class="btn btn-primary mt-3">Add More</button>
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

                            <div class="row" id="ca_nbt" style="display: none;">
                                @if ($transactions->type_ca == 'ndns')
                                    <div class="col-md-12">
                                        <div class="table-responsive-sm">
                                            <div class="d-flex flex-column gap-2">
                                                <div class="text-bg-danger p-2" style="text-align:center">Estimated Cash Advanced</div>
                                                <div class="card">
                                                    <div class="card-body">
                                                        <div class="accordion" id="accordionPanelsStayOpenExample">
                                                            <div class="accordion-item">
                                                                <h2 class="accordion-header" id="enter-headingOne">
                                                                    <button class="accordion-button fw-medium" type="button" data-bs-toggle="collapse" data-bs-target="#enter-collapseOne" aria-expanded="true" aria-controls="enter-collapseOne">
                                                                        Non Business Trip
                                                                    </button>
                                                                </h2>
                                                                @foreach ($detailCA as $item)
                                                                <div id="enter-collapseOne" class="accordion-collapse show" aria-labelledby="enter-headingOne">
                                                                    <div class="accordion-body">
                                                                        <div id="form-container">
                                                                            <div class="mb-2">
                                                                                <label class="form-label">Tanggal</label>
                                                                                <input type="date" name="tanggal_nbt[]" class="form-control bg-light" value="{{ $item['tanggal_nbt'] }}" readonly>
                                                                            </div>
                                                                            <div class="mb-2">
                                                                                <label class="form-label">Keterangan</label>
                                                                                <textarea name="keterangan_nbt[]" class="form-control bg-light" readonly>{{ $item['keterangan_nbt'] }}</textarea>
                                                                            </div>
                                                                            <div class="mb-2">
                                                                                <label class="form-label">Accommodation</label>
                                                                            </div>
                                                                            <div class="input-group mb-3">
                                                                                <div class="input-group-append">
                                                                                    <span class="input-group-text">Rp</span>
                                                                                </div>
                                                                                <input class="form-control bg-light" name="nominal_nbt[]" id="nominal_nbt" type="text" min="0" value="{{ number_format($item['nominal_nbt'], 0, ',', '.') }}" readonly>
                                                                            </div>
                                                                            <hr class="border border-primary border-1 opacity-50">
                                                                        </div>
                                                                        {{-- <button type="button" id="add-more" class="btn btn-primary mt-3">Add More</button> --}}
                                                                    </div>
                                                                </div>
                                                                @endforeach
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
                                                                            <div class="mb-2">
                                                                                <label class="form-label">Entertainment Type</label>
                                                                                <select name="enter_type_e_detail[]" id="enter_type_e_detail[]" class="form-select">
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
                                                                                <textarea name="enter_fee_e_detail[]" id="enter_fee_e_detail[]" class="form-control"></textarea>
                                                                            </div>
                                                                            <div class="input-group">
                                                                                <div class="input-group-append">
                                                                                    <span class="input-group-text">Rp</span>
                                                                                </div>
                                                                                <input class="form-control" name="nominal_e_detail[]" id="nominal_e_detail[]" type="text" min="0" value="0">
                                                                            </div>
                                                                            <hr class="border border-primary border-1 opacity-50">
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
                                                                            <div class="mb-2">
                                                                                <label class="form-label">Relation Type</label>
                                                                                <div class="form-check">
                                                                                    <input class="form-check-input" type="checkbox" id="food_cost_e_relation[]" value="Food/Beverages/Souvenir">
                                                                                    <label class="form-check-label" for="food_cost_e_relation[]">Food/Beverages/Souvenir</label>
                                                                                </div>
                                                                                <div class="form-check">
                                                                                    <input class="form-check-input" type="checkbox" id="accommodation_e_relation[]" value="Accommodation">
                                                                                    <label class="form-check-label" for="accommodation_e_relation[]">Accommodation</label>
                                                                                </div>
                                                                                <div class="form-check">
                                                                                    <input class="form-check-input" type="checkbox" id="transport_e_relation[]" value="Transport">
                                                                                    <label class="form-check-label" for="transport_e_relation[]">Transport</label>
                                                                                </div>
                                                                                <div class="form-check">
                                                                                    <input class="form-check-input" type="checkbox" id="gift_e_relation[]" value="Gift">
                                                                                    <label class="form-check-label" for="gift_e_relation[]">Gift</label>
                                                                                </div>
                                                                                <div class="form-check">
                                                                                    <input class="form-check-input" type="checkbox" id="fund_e_relation[]" value="Fund">
                                                                                    <label class="form-check-label" for="fund_e_relation[]">Fund</label>
                                                                                </div>
                                                                            </div>
                                                                            <div class="mb-2">
                                                                                <label class="form-label" for="start">Name</label>
                                                                                <input type="text" name="rname_e_relation[]" id="rname_e_relation[]" class="form-control">
                                                                            </div>
                                                                            <div class="mb-2">
                                                                                <label class="form-label" for="start">Position</label>
                                                                                <input type="text" name="rposition_e_relation[]" id="rposition_e_relation[]" class="form-control" readonly>
                                                                            </div>
                                                                            <div class="mb-2">
                                                                                <label class="form-label" for="start">Company</label>
                                                                                <input type="text" name="rcompany_e_relation[]" id="rcompany_e_relation[]" class="form-control">
                                                                            </div>
                                                                            <div class="mb-2">
                                                                                <label class="form-label" for="start">Purpose</label>
                                                                                <input type="text" name="rpurpose_e_relation[]" id="rpurpose_e_relation[]" class="form-control">
                                                                            </div>
                                                                            <hr class="border border-primary border-1 opacity-50">
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
                            </div>

                            <br>

                            <div class="row">
                                <div class="col-md-12 mb-2">
                                    <label class="form-label">Total Cash Advanced</label>
                                    <div class="input-group">
                                        <div class="input-group-append">
                                            <span class="input-group-text">Rp</span>
                                        </div>
                                        <input class="form-control bg-light" name="totalca" id="totalca"
                                            type="text" min="0" value="{{ number_format($transactions->total_cost, 0, ',', '.') }}" readonly>
                                    </div>
                                </div>
                            </div>
                    </div>

                    <br>
                    <div class="row">
                        <div class="p-3 col-md d-md-flex justify-content-end text-center">
                            <input type="hidden" name="repeat_days_selected" id="repeatDaysSelected">
                            <a href="{{ route('approval.cashadvanced') }}" type="button"
                                class="btn btn-outline-secondary px-4 me-2">Cancel</a>
                            <button type="submit" name="action_ca_reject" value="Reject" class=" btn btn-primary btn-pill px-4 me-2">Reject</button>
                            <button type="submit" name="action_ca_approve" value="Approve" class=" btn btn-success btn-pill px-4 me-2">Approve</button>
                        </div>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection
<!-- Tambahkan script JavaScript untuk mengumpulkan nilai repeat_days[] -->
@push('scripts')
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

    {{-- <script>
        $(document).ready(function() {
            $('.select2').select2({
                theme: "bootstrap-5",

            });
        });

        $(document).ready(function() {
            function toggleCard(buttonId, cardId) {
                var $button = $(buttonId);
                var $card = $(cardId);
                var isVisible = $card.is(':visible');

                $card.slideToggle('fast', function() {
                    if (isVisible) {
                        $card.find('input[type="text"], input[type="date"], textarea').val('');
                        $card.find('select').prop('selectedIndex', 0);
                        $card.find('input[readonly]').val(0);
                        $card.find('input[type="number"]').val(0);

                        $button.html('<i class="bi bi-plus-circle"></i> ' + $button.text().split(' ')[1]);
                        $button.data('state', 'false');
                    } else {
                        $button.html('<i class="bi bi-dash-circle"></i> ' + $button.text().split(' ')[1]);
                        $button.data('state', 'true');
                    }
                });
            }

            $('#toggle-bt-perdiem').click(function() {
                toggleCard('#toggle-bt-perdiem', '#perdiem-card');
            });

            $('#toggle-bt-transport').click(function() {
                toggleCard('#toggle-bt-transport', '#transport-card');
            });

            $('#toggle-bt-penginapan').click(function() {
                toggleCard('#toggle-bt-penginapan', '#penginapan-card');
            });

            $('#toggle-bt-lainnya').click(function() {
                toggleCard('#toggle-bt-lainnya', '#lainnya-card');
            });

            $('#toggle-e-detail').click(function() {
                toggleCard('#toggle-e-detail', '#entertain-card');
            });

            $('#toggle-e-relation').click(function() {
                toggleCard('#toggle-e-relation', '#relation-card');
            });
        });

    </script> --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-beta3/js/bootstrap.min.js"></script>
@endpush

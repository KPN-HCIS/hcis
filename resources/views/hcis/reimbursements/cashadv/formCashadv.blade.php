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
            <div class="card col-md-8">
                <div class="card-header d-flex bg-white justify-content-between">
                    <h4 class="modal-title" id="viewFormEmployeeLabel">Add Data</h4>
                    <a href="{{ route('cashadvanced') }}" type="button" class="btn btn-close"></a>
                </div>
                <div class="card-body" @style('overflow-y: auto;')>
                    <div class="container-fluid">
                        <form id="scheduleForm" method="post" action="{{ route('cashadvanced.submit') }}">@csrf
                            <div class="row my-2">
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <label class="form-label" for="start">Name</label>
                                        <input type="text" name="name" id="name" value="{{ $employee_data->fullname }}" class="form-control bg-light" style="cursor: none;" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row my-2">
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <label class="form-label" for="start">Unit</label>
                                        <input type="text" name="unit" id="unit" value="{{ $employee_data->unit }}" class="form-control bg-light" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row my-2">
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <label class="form-label" for="start">Grade</label>
                                        <input type="text" name="grade" id="grade" value="{{ $employee_data->job_level }}" class="form-control bg-light" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row my-2">
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <label class="form-label" for="name">Costing Company</label>
                                        <select class="form-control select2" id="companyFilter" name="companyFilter" required>
                                            <option value="">Select Company...</option>
                                            @foreach($companies as $company)
                                                <option value="{{ $company->contribution_level_code }}">{{ $company->contribution_level." (".$company->contribution_level_code.")" }}</option>
                                            @endforeach
                                        </select>

                                    </div>
                                </div>
                            </div>
                            <div class="row my-2">
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <label class="form-label" for="name">Destination</label>
                                        <select class="form-control select2" id="locationFilter" name="locationFilter" onchange="toggleOthers()" required>
                                            <option value="">Select location...</option>
                                            @foreach($locations as $location)
                                                <option value="{{ $location->area }}">{{ $location->area." (".$location->company_name.")" }}</option>
                                            @endforeach
                                            <option value="Others">Others</option>
                                        </select>
                                        <br><input type="text" name="others_location" id="others_location" class="form-control" placeholder="Other Location" value="" style="display: none;">
                                    </div>
                                </div>
                            </div>
                            <div class="row my-2">
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <label class="form-label" for="name">CA Purposes</label>
                                        <textarea name="ca_needs" id="ca_needs" class="form-control"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row my-2">
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <label class="form-label" for="start">Start Date</label>
                                        <input type="date" name="start_date" id="start_date" class="form-control" placeholder="mm/dd/yyyy" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row my-2">
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <label class="form-label" for="start">End Date</label>
                                        <input type="date" name="end_date" id="end_date" class="form-control" placeholder="mm/dd/yyyy" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row my-2">
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <label class="form-label" for="start">Total Days</label>
                                        <div class="input-group">
                                            <input class="form-control bg-light" id="totaldays" name="totaldays" type="text" min="0" value="0" readonly>
                                            <div class="input-group-append">
                                                <span class="input-group-text">days</span>
                                            </div>
                                        </div>
                                        <input class="form-control" id="perdiem" name="perdiem" type="hidden" value="{{ $perdiem->amount }}" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row my-2">
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <label class="form-label" for="start">CA Date Required</label>
                                        <input type="date" name="ca_required" id="ca_required" class="form-control" placeholder="mm/dd/yyyy" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row my-2">
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <label class="form-label" for="start">Declaration Estimate</label>
                                        <input type="date" name="ca_decla" id="ca_decla" class="form-control bg-light" placeholder="mm/dd/yyyy" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row my-2">
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <label class="form-label" for="type">CA Type</label>
                                        <select name="ca_type" id="ca_type" class="form-select" onchange="toggleDivs()" readonly>
                                            <option value="">-</option>
                                            <option value="dns">Business Trip</option>
                                            <option value="ndns">Non Business Trip</option>
                                            <option value="entr">Entertainment</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row my-2" id="div_bisnis_numb" style="display: none;">
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <label class="form-label" for="name">Business Trip Number</label>
                                        <select class="form-control select2" id="bisnis_numb" name="bisnis_numb">
                                            <option value="">Select</option>
                                            @foreach($no_sppds as $no_sppd)
                                                <option value="{{ $no_sppd->no_sppd }}">{{ $no_sppd->no_sppd }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <br>

                            <div class="row" id="ca_bt" style="display: none;">
                                <div class="col-md-12">
                                    <div class="table-responsive-sm">
                                        <div class="d-flex flex-column gap-2">
                                            <div class="text-bg-danger p-2" style="text-align:center">Estimated Cash Advanced</div>
                                            <div class="card">
                                                <button type="button" style="width: 45%" id="toggle-bt-perdiem" class="btn btn-primary mt-3" data-state="false">Tambah Rencana Perdiem</button>
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
                                                                            <input type="date" name="start_bt_perdiem[]" class="form-control start-perdiem" placeholder="mm/dd/yyyy" required>
                                                                        </div>
                                                                        <div class="mb-2">
                                                                            <label class="form-label">End Perdiem</label>
                                                                            <input type="date" name="end_bt_perdiem[]" class="form-control end-perdiem" placeholder="mm/dd/yyyy" required>
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
                                                                            <select class="form-control select2" id="locationFilter" name="location_bt_perdiem[]" onchange="toggleOthers()" required>
                                                                                <option value="">Select location...</option>
                                                                                @foreach($locations as $location)
                                                                                    <option value="{{ $location->area }}">{{ $location->area." (".$location->company_name.")" }}</option>
                                                                                @endforeach
                                                                                <option value="Others">Others</option>
                                                                            </select>
                                                                            <br><input type="text" name="others_location" id="others_location" class="form-control" placeholder="Other Location" value="" style="display: none;">
                                                                        </div>
                                                                        <div class="mb-2">
                                                                            <label class="form-label" for="name">Company Code</label>
                                                                            <select class="form-control select2" id="companyFilter" name="company_bt_perdiem[]" required>
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
                                                <button type="button" style="width: 45%" id="toggle-bt-transport" class="btn btn-primary mt-3" data-state="false">Tambah Rencana Transport</button>
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
                                                                            <input type="date" name="tanggal_bt_transport[]" class="form-control" placeholder="mm/dd/yyyy" required>
                                                                        </div>
                                                                        <div class="mb-2">
                                                                            <label class="form-label" for="name">Company Code</label>
                                                                            <select class="form-control select2" id="companyFilter" name="company_bt_transport[]" required>
                                                                                <option value="">Select Company...</option>
                                                                                @foreach($companies as $company)
                                                                                    <option value="{{ $company->contribution_level_code }}">{{ $company->contribution_level." (".$company->contribution_level_code.")" }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                        <div class="mb-2">
                                                                            <label class="form-label">Keterangan</label>
                                                                            <textarea name="keterangan_nb_transport[]" class="form-control"></textarea>
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
                                                <button type="button" style="width: 45%" id="toggle-bt-penginapan" class="btn btn-primary mt-3" data-state="false">Tambah Rencana Penginapan</button>
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
                                                                            <input type="date" name="start_bt_penginapan[]" class="form-control start-penginapan" placeholder="mm/dd/yyyy" required>
                                                                        </div>
                                                                        <div class="mb-2">
                                                                            <label class="form-label">End Penginapan</label>
                                                                            <input type="date" name="end_bt_penginapan[]" class="form-control end-penginapan" placeholder="mm/dd/yyyy" required>
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
                                                                            <input type="text" name="hotel_name_bt_penginapan[]" class="form-control" placeholder="Hotel" required>
                                                                        </div>
                                                                        <div class="mb-2">
                                                                            <label class="form-label" for="name">Company Code</label>
                                                                            <select class="form-control select2" id="companyFilter" name="company_bt_penginapan[]" required>
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
                                                <button type="button" style="width: 45%" id="toggle-bt-lainnya" class="btn btn-primary mt-3" data-state="false">Tambah Rencana Lainnya</button>
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
                                                                            <input type="date" name="tanggal_bt_lainnya[]" class="form-control" placeholder="mm/dd/yyyy" required>
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
                                                            <div id="enter-collapseOne" class="accordion-collapse show" aria-labelledby="enter-headingOne">
                                                                <div class="accordion-body">
                                                                    <div id="form-container">
                                                                        <div class="mb-2">
                                                                            <label class="form-label">Tanggal</label>
                                                                            <input type="date" name="tanggal_nbt[]" class="form-control" placeholder="mm/dd/yyyy" required>
                                                                        </div>
                                                                        <div class="mb-2">
                                                                            <label class="form-label">Keterangan</label>
                                                                            <textarea name="keterangan_nbt[]" class="form-control"></textarea>
                                                                        </div>
                                                                        <div class="mb-2">
                                                                            <label class="form-label">Accommodation</label>
                                                                        </div>
                                                                        <div class="input-group mb-3">
                                                                            <div class="input-group-append">
                                                                                <span class="input-group-text">Rp</span>
                                                                            </div>
                                                                            <input class="form-control" name="nominal_nbt[]" id="nominal_nbt" type="text" min="0" value="0">
                                                                        </div>
                                                                        <hr class="border border-primary border-1 opacity-50">
                                                                    </div>
                                                                    <button type="button" id="add-more" class="btn btn-primary mt-3">Add More</button>
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

                            <div class="row" id="ca_e" style="display: none;">
                                <div class="col-md-12">
                                    <div class="table-responsive-sm">
                                        <div class="d-flex flex-column gap-2">
                                            <div class="text-bg-danger p-2" style="text-align:center">Estimated Entertainment</div>
                                                <div class="card">
                                                    <div class="card-body">
                                                        <div class="accordion" id="accordionPanelsStayOpenExample">
                                                            <div class="accordion-item">
                                                                <h2 class="accordion-header" id="enter-headingOne">
                                                                    <button class="accordion-button fw-medium" type="button" data-bs-toggle="collapse" data-bs-target="#enter-collapseOne" aria-expanded="true" aria-controls="enter-collapseOne">
                                                                        Entertainment Detail #1
                                                                    </button>
                                                                </h2>
                                                                <div id="enter-collapseOne" class="accordion-collapse collapse show" aria-labelledby="enter-headingOne">
                                                                    <div class="accordion-body">
                                                                        <div class="mb-2">
                                                                            <label class="form-label">Entertainment Type</label>
                                                                            <select name="enter_type_1" id="enter_type_1" class="form-select">
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
                                                                            <textarea name="enter_fee_1" id="enter_fee_1" class="form-control"></textarea>
                                                                        </div>
                                                                        <div class="input-group">
                                                                            <div class="input-group-append">
                                                                                <span class="input-group-text">Rp</span>
                                                                            </div>
                                                                            <input class="form-control" name="nominal_1" id="nominal_1" type="text" min="0" value="0">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="accordion-item">
                                                                <h2 class="accordion-header" id="enter-headingTwo">
                                                                    <button class="accordion-button fw-medium collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#enter-collapseTwo" aria-expanded="false" aria-controls="enter-collapseTwo">
                                                                        Entertainment Detail #2
                                                                    </button>
                                                                </h2>
                                                                <div id="enter-collapseTwo" class="accordion-collapse collapse" aria-labelledby="enter-headingTwo">
                                                                    <div class="accordion-body">
                                                                        <div class="mb-2">
                                                                            <label class="form-label">Entertainment Type</label>
                                                                            <select name="enter_type_2" id="enter_type_2" class="form-select">
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
                                                                            <textarea name="enter_fee_2" id="enter_fee_2" class="form-control"></textarea>
                                                                        </div>
                                                                        <div class="input-group">
                                                                            <div class="input-group-append">
                                                                                <span class="input-group-text">Rp</span>
                                                                            </div>
                                                                            <input class="form-control" name="nominal_2" id="nominal_2" type="text" min="0" value="0">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="accordion-item">
                                                                <h2 class="accordion-header" id="enter-headingThree">
                                                                    <button class="accordion-button fw-medium collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#enter-collapseThree" aria-expanded="false" aria-controls="enter-collapseThree">
                                                                        Entertainment Detail #3
                                                                    </button>
                                                                </h2>
                                                                <div id="enter-collapseThree" class="accordion-collapse collapse" aria-labelledby="enter-headingThree">
                                                                    <div class="accordion-body">
                                                                        <div class="mb-2">
                                                                            <label class="form-label">Entertainment Type</label>
                                                                            <select name="enter_type_3" id="enter_type_3" class="form-select">
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
                                                                            <textarea name="enter_fee_3" id="enter_fee_3" class="form-control"></textarea>
                                                                        </div>
                                                                        <div class="input-group">
                                                                            <div class="input-group-append">
                                                                                <span class="input-group-text">Rp</span>
                                                                            </div>
                                                                            <input class="form-control" name="nominal_3" id="nominal_3" type="text" min="0" value="0">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="accordion-item">
                                                                <h2 class="accordion-header" id="enter-headingFour">
                                                                    <button class="accordion-button fw-medium collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#enter-collapseFour" aria-expanded="false" aria-controls="enter-collapseFour">
                                                                        Entertainment Detail #4
                                                                    </button>
                                                                </h2>
                                                                <div id="enter-collapseFour" class="accordion-collapse collapse" aria-labelledby="enter-headingFour">
                                                                    <div class="accordion-body">
                                                                        <div class="mb-2">
                                                                            <label class="form-label">Entertainment Type</label>
                                                                            <select name="enter_type_4" id="enter_type_4" class="form-select">
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
                                                                            <textarea name="enter_fee_4" id="enter_fee_4" class="form-control"></textarea>
                                                                        </div>
                                                                        <div class="input-group">
                                                                            <div class="input-group-append">
                                                                                <span class="input-group-text">Rp</span>
                                                                            </div>
                                                                            <input class="form-control" name="nominal_4" id="nominal_4" type="text" min="0" value="0">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="accordion-item">
                                                                <h2 class="accordion-header" id="enter-headingFive">
                                                                    <button class="accordion-button fw-medium collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#enter-collapseFive" aria-expanded="false" aria-controls="enter-collapseFive">
                                                                        Entertainment Detail #5
                                                                    </button>
                                                                </h2>
                                                                <div id="enter-collapseFive" class="accordion-collapse collapse" aria-labelledby="enter-headingFive">
                                                                    <div class="accordion-body">
                                                                        <div class="mb-2">
                                                                            <label class="form-label">Entertainment Type</label>
                                                                            <select name="enter_type_5" id="enter_type_5" class="form-select">
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
                                                                            <textarea name="enter_fee_5" id="enter_fee_5" class="form-control"></textarea>
                                                                        </div>
                                                                        <div class="input-group">
                                                                            <div class="input-group-append">
                                                                                <span class="input-group-text">Rp</span>
                                                                            </div>
                                                                            <input class="form-control" name="nominal_5" id="nominal_5" type="text" min="0" value="0">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        {{-- ------------------------------------------------- --}}
                                                        <br>
                                                        <div class="accordion" id="accordionPanelsStayOpenExample">
                                                            <div class="accordion-item">
                                                                <h2 class="accordion-header" id="relation-headingOne">
                                                                    <button class="accordion-button fw-medium" type="button" data-bs-toggle="collapse" data-bs-target="#relation-collapseOne" aria-expanded="true" aria-controls="relation-collapseOne">
                                                                        Relation Detail #1
                                                                    </button>
                                                                </h2>
                                                                <div id="relation-collapseOne" class="accordion-collapse collapse show" aria-labelledby="relation-headingOne">
                                                                    <div class="accordion-body">
                                                                        <div class="mb-2">
                                                                            <label class="form-label" for="start">Name</label>
                                                                            <input type="text" name="rname_1" id="rname_1" class="form-control">
                                                                        </div>
                                                                        <div class="mb-2">
                                                                            <label class="form-label" for="start">Position</label>
                                                                            <input type="text" name="rposition_1" id="rposition_1" class="form-control">
                                                                        </div>
                                                                        <div class="mb-2">
                                                                            <label class="form-label" for="start">Company</label>
                                                                            <input type="text" name="rcompany_1" id="rcompany_1" class="form-control">
                                                                        </div>
                                                                        <div class="mb-2">
                                                                            <label class="form-label" for="start">Purpose</label>
                                                                            <input type="text" name="rpurpose_1" id="rpurpose_1" class="form-control">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="accordion-item">
                                                                <h2 class="accordion-header" id="relation-headingTwo">
                                                                    <button class="accordion-button fw-medium collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#relation-collapseTwo" aria-expanded="false" aria-controls="relation-collapseTwo">
                                                                        Relation Detail #2
                                                                    </button>
                                                                </h2>
                                                                <div id="relation-collapseTwo" class="accordion-collapse collapse" aria-labelledby="relation-headingTwo">
                                                                    <div class="accordion-body">
                                                                        <div class="mb-2">
                                                                            <label class="form-label" for="start">Name</label>
                                                                            <input type="text" name="rname_2" id="rname_2" class="form-control">
                                                                        </div>
                                                                        <div class="mb-2">
                                                                            <label class="form-label" for="start">Position</label>
                                                                            <input type="text" name="rposition_2" id="rposition_2" class="form-control">
                                                                        </div>
                                                                        <div class="mb-2">
                                                                            <label class="form-label" for="start">Company</label>
                                                                            <input type="text" name="rcompany_2" id="rcompany_2" class="form-control">
                                                                        </div>
                                                                        <div class="mb-2">
                                                                            <label class="form-label" for="start">Purpose</label>
                                                                            <input type="text" name="rpurpose_2" id="rpurpose_2" class="form-control">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="accordion-item">
                                                                <h2 class="accordion-header" id="relation-headingThree">
                                                                    <button class="accordion-button fw-medium collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#relation-collapseThree" aria-expanded="false" aria-controls="relation-collapseThree">
                                                                        Relation Detail #3
                                                                    </button>
                                                                </h2>
                                                                <div id="relation-collapseThree" class="accordion-collapse collapse" aria-labelledby="relation-headingThree">
                                                                    <div class="accordion-body">
                                                                        <div class="mb-2">
                                                                            <label class="form-label" for="start">Name</label>
                                                                            <input type="text" name="rname_3" id="rname_3" class="form-control">
                                                                        </div>
                                                                        <div class="mb-2">
                                                                            <label class="form-label" for="start">Position</label>
                                                                            <input type="text" name="rposition_3" id="rposition_3" class="form-control">
                                                                        </div>
                                                                        <div class="mb-2">
                                                                            <label class="form-label" for="start">Company</label>
                                                                            <input type="text" name="rcompany_3" id="rcompany_3" class="form-control">
                                                                        </div>
                                                                        <div class="mb-2">
                                                                            <label class="form-label" for="start">Purpose</label>
                                                                            <input type="text" name="rpurpose_3" id="rpurpose_3" class="form-control">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="accordion-item">
                                                                <h2 class="accordion-header" id="relation-headingFour">
                                                                    <button class="accordion-button fw-medium collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#relation-collapseFour" aria-expanded="false" aria-controls="relation-collapseFour">
                                                                        Relation Detail #4
                                                                    </button>
                                                                </h2>
                                                                <div id="relation-collapseFour" class="accordion-collapse collapse" aria-labelledby="relation-headingFour">
                                                                    <div class="accordion-body">
                                                                        <div class="mb-2">
                                                                            <label class="form-label" for="start">Name</label>
                                                                            <input type="text" name="rname_4" id="rname_4" class="form-control">
                                                                        </div>
                                                                        <div class="mb-2">
                                                                            <label class="form-label" for="start">Position</label>
                                                                            <input type="text" name="rposition_4" id="rposition_4" class="form-control">
                                                                        </div>
                                                                        <div class="mb-2">
                                                                            <label class="form-label" for="start">Company</label>
                                                                            <input type="text" name="rcompany_4" id="rcompany_4" class="form-control">
                                                                        </div>
                                                                        <div class="mb-2">
                                                                            <label class="form-label" for="start">Purpose</label>
                                                                            <input type="text" name="rpurpose_4" id="rpurpose_4" class="form-control">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="accordion-item">
                                                                <h2 class="accordion-header" id="relation-headingFive">
                                                                    <button class="accordion-button fw-medium collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#relation-collapseFive" aria-expanded="false" aria-controls="relation-collapseFive">
                                                                        Relation Detail #5
                                                                    </button>
                                                                </h2>
                                                                <div id="relation-collapseFive" class="accordion-collapse collapse" aria-labelledby="relation-headingFive">
                                                                    <div class="accordion-body">
                                                                        <div class="mb-2">
                                                                            <label class="form-label" for="start">Name</label>
                                                                            <input type="text" name="rname_5" id="rname_5" class="form-control">
                                                                        </div>
                                                                        <div class="mb-2">
                                                                            <label class="form-label" for="start">Position</label>
                                                                            <input type="text" name="rposition_5" id="rposition_5" class="form-control">
                                                                        </div>
                                                                        <div class="mb-2">
                                                                            <label class="form-label" for="start">Company</label>
                                                                            <input type="text" name="rcompany_5" id="rcompany_5" class="form-control">
                                                                        </div>
                                                                        <div class="mb-2">
                                                                            <label class="form-label" for="start">Purpose</label>
                                                                            <input type="text" name="rpurpose_5" id="rpurpose_5" class="form-control">
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
                                <div class="row my-2">
                                    <div class="col-md-6">
                                        <div class="mb-2">
                                            <label class="form-label">Total Cash Advanced</label>
                                            <div class="input-group">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">Rp</span>
                                                </div>
                                                <input class="form-control bg-light" name="totalca" id="total_nominal" type="text" min="0" value="0" readonly>
                                            </div>
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label">Total Keseluruhan</label>
                                            <div class="input-group">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">Rp</span>
                                                </div>
                                                <input class="form-control bg-light" id="total_all" type="text" value="0" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <br>
                            <div class="row">
                                <div class="col-md d-md-flex justify-content-end text-center">
                                    <input type="hidden" name="repeat_days_selected" id="repeatDaysSelected">
                                    <a href="{{ route('cashadvanced') }}" type="button" class="btn btn-danger rounded-pill shadow px-4 me-2">Cancel</a>
                                    <button type="submit" name="action" value="draft" class="btn btn-secondary rounded-pill shadow px-4 me-2">Draft</button>
                                    <button type="submit" name="action" value="submit" class="btn btn-primary rounded-pill shadow px-4">Submit</button>
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
    function toggleOthers() {
        // ca_type ca_nbt ca_e
        var locationFilter = document.getElementById("locationFilter");
        var others_location = document.getElementById("others_location");

        if (locationFilter.value === "Others") {
            others_location.style.display = "block";
        } else{
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
        const nominal_nbt = document.getElementById('nominal_nbt');
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
                const totalDays = daysDiff > 0 ? daysDiff+1 : 0+1;
                totalDaysInput.value = totalDays;

                const perdiem = parseFloat(perdiemInput.value) || 0;
                let allowance = totalDays * perdiem;

                if (othersLocationInput.value.trim() !== '') {
                    allowance *= 1; // allowance * 50%
                }else{
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
        [transportInput, accommodationInput, otherInput, allowanceInput, nominal_1, nominal_2, nominal_3, nominal_4, nominal_5].forEach(input => {
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
        // Mengosongkan form saat di-hide dan panggil updateTotalCA
        function toggleCard(buttonId, cardId) {
            var $button = $(buttonId);
            var $card = $(cardId);
            var isVisible = $card.is(':visible');

            $card.slideToggle('fast', function() {
                if (isVisible) {
                    $button.text('Tambah ' + $button.text().split(' ')[1]);
                    $button.data('state', 'false');

                    // Mengosongkan input form saat card disembunyikan
                    $card.find('input').val('');
                    $card.find('select').val('');
                    $card.find('textarea').val('');

                    updateTotalCA(); // Update total setelah form dikosongkan
                } else {
                    $button.text('Hapus ' + $button.text().split(' ')[1]);
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
    });

    document.addEventListener('DOMContentLoaded', function() {
        const formContainerBT = document.getElementById('form-container-bt-perdiem');

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
            calculateTotalNominalBT();
        }

        function calculateTotalNominalBT() {
            let total = 0;
            document.querySelectorAll('input[name="nominal_bt_perdiem[]"]').forEach(input => {
                total += parseNumber(input.value);
            });
            document.querySelector('input[name="total_bt_perdiem[]"]').value = formatNumber(total);

        }

        function calculateTotalDays(input) {
            const formGroup = input.closest('.mb-2').parentElement;
            const startDate = new Date(formGroup.querySelector('input[name="start_bt_perdiem[]"]').value);
            const endDate = new Date(formGroup.querySelector('input[name="end_bt_perdiem[]"]').value);

            if (!isNaN(startDate) && !isNaN(endDate) && startDate <= endDate) {
                const diffTime = Math.abs(endDate - startDate);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
                formGroup.querySelector('input[name="total_days_bt_perdiem[]"]').value = diffDays;
            } else {
                formGroup.querySelector('input[name="total_days_bt_perdiem[]"]').value = 0;
            }
        }

        function addNewPerdiemForm() {
            const newFormBT = document.createElement('div');
            newFormBT.classList.add('mb-2');

            newFormBT.innerHTML = `
                <div class="mb-2">
                    <label class="form-label">Start Perdiem</label>
                    <input type="date" name="start_bt_perdiem[]" class="form-control start-perdiem" placeholder="mm/dd/yyyy" required>
                </div>
                <div class="mb-2">
                    <label class="form-label">End Perdiem</label>
                    <input type="date" name="end_bt_perdiem[]" class="form-control end-perdiem" placeholder="mm/dd/yyyy" required>
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
                    <select class="form-control select2" name="location_bt_perdiem[]" onchange="toggleOthers()" required>
                        <option value="">Select location...</option>
                        @foreach($locations as $location)
                            <option value="{{ $location->area }}">{{ $location->area." (".$location->company_name.")" }}</option>
                        @endforeach
                        <option value="Others">Others</option>
                    </select>
                    <br><input type="text" name="others_location" class="form-control" placeholder="Other Location" value="" style="display: none;">
                </div>
                <div class="mb-2">
                    <label class="form-label" for="name">Company Code</label>
                    <select class="form-control select2" name="company_bt_perdiem[]" required>
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
                    <input class="form-control" name="nominal_bt_perdiem[]" type="text" min="0" value="0">
                </div>
                <button type="button" class="btn btn-danger remove-form">Remove</button>
                <hr class="border border-primary border-1 opacity-50">
            `;

            formContainerBT.appendChild(newFormBT);

            // Attach input event to the newly added nominal field
            newFormBT.querySelector('input[name="nominal_bt_perdiem[]"]').addEventListener('input', function() {
                formatInput(this);
            });

            // Attach change event to the date fields to calculate total days
            newFormBT.querySelector('input[name="start_bt_perdiem[]"]').addEventListener('change', function() {
                calculateTotalDays(this);
            });

            newFormBT.querySelector('input[name="end_bt_perdiem[]"]').addEventListener('change', function() {
                calculateTotalDays(this);
            });

            // Attach click event to the remove button
            newFormBT.querySelector('.remove-form').addEventListener('click', function() {
                newFormBT.remove();
                calculateTotalNominalBT();
            });

            // Update the date constraints for the new 'start_bt_perdiem[]' and 'end_bt_perdiem[]' input fields
            const startDateInput = document.getElementById('start_date').value;
            const endDateInput = document.getElementById('end_date').value;

            newFormBT.querySelectorAll('input[name="start_bt_perdiem[]"]').forEach(function(input) {
                input.min = startDateInput;
                input.max = endDateInput;
            });

            newFormBT.querySelectorAll('input[name="end_bt_perdiem[]"]').forEach(function(input) {
                input.min = startDateInput;
                input.max = endDateInput;
            });
        }

        document.getElementById('add-more-bt-perdiem').addEventListener('click', addNewPerdiemForm);

        // Attach input event to the existing nominal fields
        document.querySelectorAll('input[name="nominal_bt_perdiem[]"]').forEach(input => {
            input.addEventListener('input', function() {
                formatInput(this);
            });
        });

        // Attach change event to the existing start and end date fields to calculate total days
        document.querySelectorAll('input[name="start_bt_perdiem[]"], input[name="end_bt_perdiem[]"]').forEach(input => {
            input.addEventListener('change', function() {
                calculateTotalDays(this);
            });
        });

        // Initial calculation for the total nominal
        calculateTotalNominalBT();

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
                calculateTotalDays(input);
            });
        }

        // Attach click event to the remove button for existing forms
        document.querySelectorAll('.remove-form').forEach(button => {
            button.addEventListener('click', function() {
                this.closest('.mb-2').remove();
                calculateTotalNominalBT();
            });
        });
    });
    document.addEventListener('DOMContentLoaded', function() {
        const formContainerBT = document.getElementById('form-container-bt-transport');

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
            calculateTotalNominalBT();
        }

        function calculateTotalNominalBT() {
            let total = 0;
            document.querySelectorAll('input[name="nominal_bt_transport[]"]').forEach(input => {
                total += parseNumber(input.value);
            });
            document.querySelector('input[name="total_bt_transport[]"]').value = formatNumber(total);
        }

        function addNewTransportForm() {
            const newFormBT = document.createElement('div');
            newFormBT.classList.add('mb-2');

            newFormBT.innerHTML =`
                <div class="mb-2">
                    <label class="form-label">Tanggal Transport</label>
                    <input type="date" name="tanggal_bt_transport[]" class="form-control" placeholder="mm/dd/yyyy" required>
                </div>
                <div class="mb-2">
                    <label class="form-label" for="name">Company Code</label>
                    <select class="form-control select2" name="company_bt_transport[]" required>
                        <option value="">Select Company...</option>
                        <!-- Options will be populated dynamically or server-side -->
                    </select>
                </div>
                <div class="mb-2">
                    <label class="form-label">Keterangan</label>
                    <textarea name="keterangan_nb_transport[]" class="form-control"></textarea>
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

            formContainerBT.appendChild(newFormBT);

            // Attach input event to the newly added nominal field
            newFormBT.querySelector('input[name="nominal_bt_transport[]"]').addEventListener('input', function() {
                formatInput(this);
            });

            // Attach click event to the remove button
            newFormBT.querySelector('.remove-form').addEventListener('click', function() {
                newFormBT.remove();
                calculateTotalNominalBT();
            });
        }

        document.getElementById('add-more-bt-transport').addEventListener('click', addNewTransportForm);

        // Attach input event to the existing nominal fields
        document.querySelectorAll('input[name="nominal_bt_transport[]"]').forEach(input => {
            input.addEventListener('input', function() {
                formatInput(this);
            });
        });

        // Initial calculation for the total nominal
        calculateTotalNominalBT();

        // Attach click event to the remove button for existing forms
        document.querySelectorAll('.remove-form').forEach(button => {
            button.addEventListener('click', function() {
                this.closest('.mb-2').remove();
                calculateTotalNominalBT();
            });
        });
    });
    document.addEventListener('DOMContentLoaded', function() {
        const formContainerBT = document.getElementById('form-container-bt-penginapan');

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
            calculateTotalNominalBT();
        }

        function calculateTotalNominalBT() {
            let total = 0;
            document.querySelectorAll('input[name="nominal_bt_penginapan[]"]').forEach(input => {
                total += parseNumber(input.value);
            });
            document.querySelector('input[name="total_bt_penginapan[]"]').value = formatNumber(total);
        }

        function calculateTotalDays(input) {
            const formGroup = input.closest('.mb-2').parentElement;
            const startDate = new Date(formGroup.querySelector('input[name="start_bt_penginapan[]"]').value);
            const endDate = new Date(formGroup.querySelector('input[name="end_bt_penginapan[]"]').value);

            if (!isNaN(startDate) && !isNaN(endDate) && startDate <= endDate) {
                const diffTime = Math.abs(endDate - startDate);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
                formGroup.querySelector('input[name="total_days_bt_penginapan[]"]').value = diffDays;
            } else {
                formGroup.querySelector('input[name="total_days_bt_penginapan[]"]').value = 0;
            }
        }

        function addNewPenginapanForm() {
            const newFormBT = document.createElement('div');
            newFormBT.classList.add('mb-2');

            newFormBT.innerHTML =`
                <div class="mb-2">
                    <label class="form-label">Start Penginapan</label>
                    <input type="date" name="start_bt_penginapan[]" class="form-control start-penginapan" placeholder="mm/dd/yyyy" required>
                </div>
                <div class="mb-2">
                    <label class="form-label">End Penginapan</label>
                    <input type="date" name="end_bt_penginapan[]" class="form-control end-penginapan" placeholder="mm/dd/yyyy" required>
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
                    <input type="text" name="hotel_name_bt_penginapan[]" class="form-control" placeholder="Hotel" required>
                </div>
                <div class="mb-2">
                    <label class="form-label" for="name">Company Code</label>
                    <select class="form-control select2" id="companyFilter" name="company_bt_penginapan[]" required>
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
                <button type="button" class="btn btn-danger remove-form">Remove</button>
                <hr class="border border-primary border-1 opacity-50">
            `;

            formContainerBT.appendChild(newFormBT);

            // Attach input event to the newly added nominal field
            newFormBT.querySelector('input[name="nominal_bt_penginapan[]"]').addEventListener('input', function() {
                formatInput(this);
            });

            // Attach change event to the date fields to calculate total days
            newFormBT.querySelector('input[name="start_bt_penginapan[]"]').addEventListener('change', function() {
                calculateTotalDays(this);
            });

            newFormBT.querySelector('input[name="end_bt_penginapan[]"]').addEventListener('change', function() {
                calculateTotalDays(this);
            });

            // Attach click event to the remove button
            newFormBT.querySelector('.remove-form').addEventListener('click', function() {
                newFormBT.remove();
                calculateTotalNominalBT();
            });
        }

        document.getElementById('add-more-bt-penginapan').addEventListener('click', addNewPenginapanForm);

        // Attach input event to the existing nominal fields
        document.querySelectorAll('input[name="nominal_bt_penginapan[]"]').forEach(input => {
            input.addEventListener('input', function() {
                formatInput(this);
            });
        });

        // Attach change event to the existing start and end date fields to calculate total days
        document.querySelectorAll('input[name="start_bt_penginapan[]"], input[name="end_bt_penginapan[]"]').forEach(input => {
            input.addEventListener('change', function() {
                calculateTotalDays(this);
            });
        });

        // Initial calculation for existing fields
        calculateTotalNominalBT();
    });
    document.addEventListener('DOMContentLoaded', function() {
        const formContainerLainnya = document.getElementById('form-container-bt-lainnya');

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
            calculateTotalNominalLainnya();
        }

        function calculateTotalNominalLainnya() {
            let total = 0;
            document.querySelectorAll('input[name="nominal_bt_lainnya[]"]').forEach(input => {
                total += parseNumber(input.value);
            });
            document.querySelector('input[name="total_bt_lainnya[]"]').value = formatNumber(total);

        }

        function addNewLainnyaForm() {
            const newFormLainnya = document.createElement('div');
            newFormLainnya.classList.add('mb-2');

            newFormLainnya.innerHTML = `
                <div class="mb-2">
                    <label class="form-label">Tanggal</label>
                    <input type="date" name="tanggal_bt_lainnya[]" class="form-control" placeholder="mm/dd/yyyy" required>
                </div>
                <div class="mb-2">
                    <label class="form-label">Keterangan</label>
                    <textarea name="keterangan_bt_lainnya[]" class="form-control"></textarea>
                </div>
                <div class="mb-2">
                    <label class="form-label">Amount</label>
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

            formContainerLainnya.appendChild(newFormLainnya);

            // Attach input event to the newly added nominal field
            newFormLainnya.querySelector('input[name="nominal_bt_lainnya[]"]').addEventListener('input', function() {
                formatInput(this);
            });

            // Attach click event to the remove button
            newFormLainnya.querySelector('.remove-form').addEventListener('click', function() {
                newFormLainnya.remove();
                calculateTotalNominalLainnya();
            });
        }

        document.getElementById('add-more-bt-lainnya').addEventListener('click', addNewLainnyaForm);

        // Attach input event to the existing nominal fields
        document.querySelectorAll('input[name="nominal_bt_lainnya[]"]').forEach(input => {
            input.addEventListener('input', function() {
                formatInput(this);
            });
        });

        // Initial calculation for the total nominal
        calculateTotalNominalLainnya();

        // Attach click event to the remove button for existing forms
        document.querySelectorAll('.remove-form').forEach(button => {
            button.addEventListener('click', function() {
                this.closest('.mb-2').remove();
                calculateTotalNominalLainnya();
            });
        });
    });

    function calculateTotalNominalBT() {
        let totalPerdiem = 0;
        let totalTransport = 0;
        let totalLainnya = 0;
        let totalPenginapan = 0;

        // Hitung total_perdiem
        document.querySelectorAll('input[name="nominal_bt_perdiem[]"]').forEach(input => {
            totalPerdiem += parseNumber(input.value);
        });
        document.querySelector('input[name="total_bt_perdiem[]"]').value = formatNumber(totalPerdiem);

        // Hitung total_transport
        document.querySelectorAll('input[name="nominal_bt_transport[]"]').forEach(input => {
            totalTransport += parseNumber(input.value);
        });
        document.querySelector('input[name="total_bt_transport[]"]').value = formatNumber(totalTransport);

        // Hitung total_lainnya
        document.querySelectorAll('input[name="nominal_bt_lainnya[]"]').forEach(input => {
            totalLainnya += parseNumber(input.value);
        });
        document.querySelector('input[name="total_bt_lainnya[]"]').value = formatNumber(totalLainnya);

        // Hitung total_penginapan
        document.querySelectorAll('input[name="nominal_bt_penginapan[]"]').forEach(input => {
            totalPenginapan += parseNumber(input.value);
        });
        document.querySelector('input[name="total_bt_penginapan[]"]').value = formatNumber(totalPenginapan);

        // Hitung total keseluruhan
        let totalNominal = totalPerdiem + totalTransport + totalLainnya + totalPenginapan;
        document.getElementById('total_nominal').value = formatNumber(totalNominal);
    }

    function parseNumber(value) {
        return parseFloat(value.replace(/\./g, '')) || 0;
    }

    function formatNumber(number) {
        toLocaleString('en-US', {minimumFractionDigits: 0, maximumFractionDigits: 0});
    }

    // Panggil fungsi ini setiap kali ada perubahan
    document.querySelectorAll('input').forEach(input => {
        input.addEventListener('input', calculateTotalNominalBT);
    });

    calculateTotalNominalBT(); // Panggil sekali untuk inisialisasi





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
            document.getElementById('total_nominal').value = formatNumber(total);
        }

        document.getElementById('add-more').addEventListener('click', function () {
            const newForm = document.createElement('div');
            newForm.classList.add('mb-2', 'form-group');

            newForm.innerHTML = `
                <div class="mb-2">
                    <label class="form-label">Tanggal</label>
                    <input type="date" name="tanggal_nbt[]" class="form-control" placeholder="mm/dd/yyyy" required>
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

        // Note Kalo dpt Revisi di suruh di kunci
        // document.getElementById('start_date').addEventListener('change', handleDateChange);
        // document.getElementById('end_date').addEventListener('change', handleDateChange);

        // function handleDateChange() {
        //     const startDateInput = document.getElementById('start_date');
        //     const endDateInput = document.getElementById('end_date');

        //     const startDate = new Date(startDateInput.value);
        //     const endDate = new Date(endDateInput.value);

        //     // Set the min attribute of the end_date input to the selected start_date
        //     endDateInput.min = startDateInput.value;

        //     // Validate dates
        //     if (endDate < startDate) {
        //         alert("End Date cannot be earlier than Start Date.");
        //         endDateInput.value = '';
        //     }

        //     // Update the min and max attributes for all 'tanggal_nbt[]' inputs
        //     const tanggalNbtInputs = document.querySelectorAll('input[name="tanggal_nbt[]"]');
        //     tanggalNbtInputs.forEach(function(input) {
        //         input.min = startDateInput.value;
        //         input.max = endDateInput.value;

        //         // Reset the value if it's out of the allowed range
        //         if (input.value < startDateInput.value || input.value > endDateInput.value) {
        //             input.value = '';
        //         }
        //     });
        // }
    });






</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-beta3/js/bootstrap.min.js"></script>
@endpush

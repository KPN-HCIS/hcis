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
                    <h4 class="modal-title text-white" id="viewFormEmployeeLabel">Add Data</h4>
                    <a href="{{ route('cashadvanced') }}" type="button" class="btn btn-close btn-close-white"></a>
                </div>
                <div class="card-body" @style('overflow-y: auto;')>
                    <div class="container-fluid">
                        <form id="scheduleForm" method="post" action="{{ route('cashadvanced.submit') }}">@csrf
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
                                    <select class="form-control select2" id="companyFilter" name="companyFilter" required>
                                        <option value="">Select Company...</option>
                                        @foreach ($companies as $company)
                                            <option value="{{ $company->contribution_level_code }}">
                                                {{ $company->contribution_level . ' (' . $company->contribution_level_code . ')' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label class="form-label" for="locationFilter">Destination</label>
                                    <select class="form-control select2" id="locationFilter" name="locationFilter"
                                        onchange="toggleOthers()" required>
                                        <option value="">Select location...</option>
                                        @foreach ($locations as $location)
                                            <option value="{{ $location->area }}">
                                                {{ $location->area . ' (' . $location->company_name . ')' }}</option>
                                        @endforeach
                                        <option value="Others">Others</option>
                                    </select>
                                    <br><input type="text" name="others_location" id="others_location"
                                        class="form-control" placeholder="Other Location" value=""
                                        style="display: none;">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 mb-2">
                                    <label class="form-label" for="name">CA Purposes</label>
                                    <textarea name="ca_needs" id="ca_needs" class="form-control"></textarea>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 mb-2">
                                    <label class="form-label" for="start">Start Date</label>
                                    <input type="date" name="start_date" id="start_date" class="form-control"
                                        placeholder="mm/dd/yyyy" required>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="form-label" for="start">End Date</label>
                                    <input type="date" name="end_date" id="end_date" class="form-control"
                                        placeholder="mm/dd/yyyy" required>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="form-label" for="start">Total Days</label>
                                    <div class="input-group">
                                        <input class="form-control bg-light" id="totaldays" name="totaldays"
                                            type="text" min="0" value="0" readonly>
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
                                        placeholder="mm/dd/yyyy" required>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <div class="mb-2">
                                        <label class="form-label" for="start">Declaration Estimate</label>
                                        <input type="date" name="ca_decla" id="ca_decla" class="form-control bg-light" placeholder="mm/dd/yyyy" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label class="form-label" for="ca_type">CA Type</label>
                                    <select name="ca_type" id="ca_type" class="form-control" onchange="toggleDivs()">
                                        <option value="">-</option>
                                        <option value="dns">Business Trip</option>
                                        <option value="ndns">Non Business Trip</option>
                                        <option value="entr">Entertainment</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row" id="div_bisnis_numb_dns" style="display: none;">
                                <div class="col-md-12 mb-2">
                                    <label class="form-label" for="name">Business Trip Number</label>
                                    <select class="form-control select2" id="bisnis_numb_dns" name="bisnis_numb_dns">
                                        <option value="">Select</option>
                                        @foreach ($no_sppds as $no_sppd)
                                            <option value="{{ $no_sppd->no_sppd }}"
                                                @if ($noSppdListDNS->contains($no_sppd->no_sppd))
                                                    disabled title="No. SPPD already has CA Business Trip. Please apply for an extension if you want to add CA."
                                                @endif>
                                                {{ $no_sppd->no_sppd }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row" id="div_bisnis_numb_ent" style="display: none;">
                                <div class="col-md-12 mb-2">
                                    <label class="form-label" for="name">Business Trip Number</label>
                                    <select class="form-control select2" id="bisnis_numb_ent" name="bisnis_numb_ent">
                                        <option value="">Select</option>
                                        @foreach ($no_sppds as $no_sppd)
                                            <option value="{{ $no_sppd->no_sppd }}"
                                                @if ($noSppdListENT->contains($no_sppd->no_sppd))
                                                    disabled title="No. SPPD already has CA Entertain. Please apply for an extension if you want to add CA."
                                                @endif>
                                                {{ $no_sppd->no_sppd }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <br>

                            <div class="row" id="ca_bt" style="display: none;">
                                <div class="col-md-12">
                                    <div class="table-responsive-sm">
                                        <div class="d-flex flex-column gap-2">
                                            <div class="text-bg-danger p-2" style="text-align:center">Estimated Cash Advanced
                                            </div>
                                            <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link active" id="pills-perdiem-tab"
                                                        data-bs-toggle="pill" data-bs-target="#pills-perdiem" type="button"
                                                        role="tab" aria-controls="pills-perdiem"
                                                        aria-selected="true">Perdiem Plan</button>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link" id="pills-transport-tab" data-bs-toggle="pill"
                                                        data-bs-target="#pills-transport" type="button" role="tab"
                                                        aria-controls="pills-transport" aria-selected="false">Transport
                                                        Plan</button>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link" id="pills-accomodation-tab"
                                                        data-bs-toggle="pill" data-bs-target="#pills-accomodation"
                                                        type="button" role="tab" aria-controls="pills-accomodation"
                                                        aria-selected="false">Accomodation Plan</button>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link" id="pills-other-tab" data-bs-toggle="pill"
                                                        data-bs-target="#pills-other" type="button" role="tab"
                                                        aria-controls="pills-other" aria-selected="false">Other Plan</button>
                                                </li>
                                            </ul>
                                            <div class="tab-content" id="pills-tabContent">
                                                <div class="tab-pane fade show active" id="pills-perdiem" role="tabpanel"
                                                    aria-labelledby="pills-perdiem-tab">
                                                    @include('hcis.reimbursements.cashadv.form.perdiem')
                                                </div>
                                                <div class="tab-pane fade" id="pills-transport" role="tabpanel"
                                                    aria-labelledby="pills-transport-tab">
                                                    @include('hcis.reimbursements.cashadv.form.transport')
                                                </div>
                                                <div class="tab-pane fade" id="pills-accomodation" role="tabpanel"
                                                    aria-labelledby="pills-accomodation-tab">
                                                    @include('hcis.reimbursements.cashadv.form.penginapan')
                                                </div>
                                                <div class="tab-pane fade" id="pills-other" role="tabpanel"
                                                    aria-labelledby="pills-other-tab">
                                                    @include('hcis.reimbursements.cashadv.form.others')
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
                                            <div class="text-bg-danger p-2" style="text-align:center">Estimated Cash Advanced
                                            </div>
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="accordion" id="accordionPanelsStayOpenExample">
                                                        <div class="accordion-item">
                                                            <h2 class="accordion-header" id="enter-headingOne">
                                                                <button class="accordion-button fw-medium" type="button"
                                                                    data-bs-toggle="collapse"
                                                                    data-bs-target="#enter-collapseOne" aria-expanded="false"
                                                                    aria-controls="enter-collapseOne">
                                                                    Non Business Trip
                                                                </button>
                                                            </h2>
                                                            <div id="enter-collapseOne" class="accordion-collapse show"
                                                                aria-labelledby="enter-headingOne">
                                                                <div class="accordion-body">
                                                                    <div id="form-container">
                                                                        <div class="mb-2">
                                                                            <label class="form-label">Date</label>
                                                                            <input type="date" name="tanggal_nbt[]"
                                                                                class="form-control" placeholder="mm/dd/yyyy">
                                                                        </div>
                                                                        <div class="mb-2">
                                                                            <label class="form-label">Information</label>
                                                                            <textarea name="keterangan_nbt[]" class="form-control"></textarea>
                                                                        </div>
                                                                        <div class="mb-2">
                                                                            <label class="form-label">Amount</label>
                                                                        </div>
                                                                        <div class="input-group mb-3">
                                                                            <div class="input-group-append">
                                                                                <span class="input-group-text">Rp</span>
                                                                            </div>
                                                                            <input class="form-control" name="nominal_nbt[]"
                                                                                id="nominal_nbt" type="text"
                                                                                min="0" value="0">
                                                                        </div>
                                                                        <hr class="border border-primary border-1 opacity-50">
                                                                    </div>
                                                                    <button type="button" id="add-more"
                                                                        class="btn btn-primary mt-3">Add More</button>
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
                                            <div class="text-bg-danger p-2" style="text-align:center">Estimated Entertainment
                                            </div>
                                            <div class="card">
                                                <div id="entertain-card" class="card-body">
                                                    <div class="accordion" id="accordionEntertain">
                                                        <div class="accordion-item">
                                                            <h2 class="accordion-header" id="headingEntertain">
                                                                <button class="accordion-button fw-medium" type="button"
                                                                    data-bs-toggle="collapse"
                                                                    data-bs-target="#collapseEntertain" aria-expanded="true"
                                                                    aria-controls="collapseEntertain">
                                                                    Rencana Entertain
                                                                </button>
                                                            </h2>
                                                            <div id="collapseEntertain"
                                                                class="accordion-collapse collapse show"
                                                                aria-labelledby="headingEntertain">
                                                                <div class="accordion-body">
                                                                    <div id="form-container-e-detail">
                                                                        <div class="mb-2">
                                                                            <label class="form-label">Entertainment
                                                                                Type</label>
                                                                            <select name="enter_type_e_detail[]"
                                                                                id="enter_type_e_detail[]"
                                                                                class="form-select">
                                                                                <option value="">-</option>
                                                                                <option value="food">Food/Beverages/Souvenir
                                                                                </option>
                                                                                <option value="transport">Transport</option>
                                                                                <option value="accommodation">Accommodation
                                                                                </option>
                                                                                <option value="gift">Gift</option>
                                                                                <option value="fund">Fund</option>
                                                                            </select>
                                                                        </div>
                                                                        <div class="mb-2">
                                                                            <label class="form-label">Entertainment Fee
                                                                                Detail</label>
                                                                            <textarea name="enter_fee_e_detail[]" id="enter_fee_e_detail[]" class="form-control"></textarea>
                                                                        </div>
                                                                        <div class="mb-1">
                                                                            <label class="form-label">Amount</label>
                                                                        </div>
                                                                        <div class="input-group">
                                                                            <div class="input-group-append">
                                                                                <span class="input-group-text">Rp</span>
                                                                            </div>
                                                                            <input class="form-control"
                                                                                name="nominal_e_detail[]"
                                                                                id="nominal_e_detail[]" type="text"
                                                                                min="0" value="0">
                                                                        </div>
                                                                        <hr class="border border-primary border-1 opacity-50">
                                                                    </div>
                                                                    <div class="mb-2">
                                                                        <label class="form-label">Total Entertain</label>
                                                                        <div class="input-group">
                                                                            <div class="input-group-append">
                                                                                <span class="input-group-text">Rp</span>
                                                                            </div>
                                                                            <input class="form-control bg-light"
                                                                                name="total_e_detail[]" id="total_e_detail[]"
                                                                                type="text" min="0" value="0"
                                                                                readonly>
                                                                        </div>
                                                                    </div>
                                                                    <button type="button" id="add-more-e-detail"
                                                                        class="btn btn-primary mt-3">Add More</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div id="relation-card" class="card-body">
                                                    <div class="accordion" id="accordionRelation">
                                                        <div class="accordion-item">
                                                            <h2 class="accordion-header" id="headingRelation">
                                                                <button class="accordion-button collapsed fw-medium"
                                                                    type="button" data-bs-toggle="collapse"
                                                                    data-bs-target="#collapseRelation" aria-expanded="false"
                                                                    aria-controls="collapseRelation">
                                                                    Rencana Relation
                                                                </button>
                                                            </h2>
                                                            <div id="collapseRelation" class="accordion-collapse collapse"
                                                                aria-labelledby="headingRelation">
                                                                <div class="accordion-body">
                                                                    <div id="form-container-e-relation">
                                                                        <div class="mb-2">
                                                                            <label class="form-label">Relation Type</label>
                                                                            <div class="form-check">
                                                                                <input class="form-check-input"
                                                                                    type="checkbox"
                                                                                    name="accommodation_e_relation[]"
                                                                                    id="accommodation_e_relation[]"
                                                                                    value="accommodation">
                                                                                <label class="form-check-label"
                                                                                    for="accommodation_e_relation[]">Accommodation</label>
                                                                            </div>
                                                                            <div class="form-check">
                                                                                <input class="form-check-input"
                                                                                    name="transport_e_relation[]"
                                                                                    type="checkbox"
                                                                                    id="transport_e_relation[]"
                                                                                    value="transport">
                                                                                <label class="form-check-label"
                                                                                    for="transport_e_relation[]">Transport</label>
                                                                            </div>
                                                                            <div class="form-check">
                                                                                <input class="form-check-input"
                                                                                    name="gift_e_relation[]" type="checkbox"
                                                                                    id="gift_e_relation[]" value="gift">
                                                                                <label class="form-check-label"
                                                                                    for="gift_e_relation[]">Gift</label>
                                                                            </div>
                                                                            <div class="form-check">
                                                                                <input class="form-check-input"
                                                                                    name="fund_e_relation[]" type="checkbox"
                                                                                    id="fund_e_relation[]" value="fund">
                                                                                <label class="form-check-label"
                                                                                    for="fund_e_relation[]">Fund</label>
                                                                            </div>
                                                                            <div class="form-check">
                                                                                <input class="form-check-input"
                                                                                    name="food_e_relation[]" type="checkbox"
                                                                                    id="food_e_relation[]" value="food">
                                                                                <label class="form-check-label"
                                                                                    for="food_e_relation[]">Food/Beverages/Souvenir</label>
                                                                            </div>
                                                                        </div>
                                                                        <div class="mb-2">
                                                                            <label class="form-label"
                                                                                for="start">Name</label>
                                                                            <input type="text" name="rname_e_relation[]"
                                                                                id="rname_e_relation[]" class="form-control">
                                                                        </div>
                                                                        <div class="mb-2">
                                                                            <label class="form-label"
                                                                                for="start">Position</label>
                                                                            <input type="text"
                                                                                name="rposition_e_relation[]"
                                                                                id="rposition_e_relation[]"
                                                                                class="form-control">
                                                                        </div>
                                                                        <div class="mb-2">
                                                                            <label class="form-label"
                                                                                for="start">Company</label>
                                                                            <input type="text" name="rcompany_e_relation[]"
                                                                                id="rcompany_e_relation[]"
                                                                                class="form-control">
                                                                        </div>
                                                                        <div class="mb-2">
                                                                            <label class="form-label"
                                                                                for="start">Purpose</label>
                                                                            <input type="text" name="rpurpose_e_relation[]"
                                                                                id="rpurpose_e_relation[]"
                                                                                class="form-control">
                                                                        </div>
                                                                        <hr class="border border-primary border-1 opacity-50">
                                                                    </div>
                                                                    <button type="button" id="add-more-e-relation"
                                                                        class="btn btn-primary mt-3">Add More</button>
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
                                            type="text" min="0" value="0" readonly>
                                    </div>
                                </div>
                            </div>
                    </div>

                    <br>
                    <div class="row">
                        <div class="p-3 col-md d-md-flex justify-content-end text-center">
                            <input type="hidden" name="repeat_days_selected" id="repeatDaysSelected">
                            <a href="{{ route('cashadvanced') }}" type="button" class="btn btn-outline-secondary px-4 me-2">Cancel</a>
                            <button type="submit" name="action_ca_draft" value="Draft" class=" btn btn-secondary btn-pill px-4 me-2">Draft</button>
                            <button type="submit" name="action_ca_submit" value="Pending" class=" btn btn-primary btn-pill px-4 me-2">Submit</button>
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
        // Fungsi untuk membersihkan format angka, menghapus titik/koma agar bisa dihitung
        function cleanNumber(value) {
            return parseFloat(value.replace(/\./g, '')) || 0;
        }

        // Fungsi untuk format ulang angka ke format IDR (Rp)
        function formatNumber(number) {
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        function formatNumberPerdiem(num) {
            return num.toLocaleString('id-ID');
        }

        function parseNumberPerdiem(value) {
            return parseFloat(value.replace(/\./g, '').replace(/,/g, '')) || 0;
        }
    </script>
    <script>
        function toggleDivs() {
            // ca_type ca_nbt ca_e
            var ca_type = document.getElementById("ca_type");
            var ca_nbt = document.getElementById("ca_nbt");
            var ca_e = document.getElementById("ca_e");
            var div_bisnis_numb_dns = document.getElementById("div_bisnis_numb_dns");
            var div_bisnis_numb_ent = document.getElementById("div_bisnis_numb_ent");
            var bisnis_numb = document.getElementById("bisnis_numb");
            var div_allowance = document.getElementById("div_allowance");

            if (ca_type.value === "dns") {
                ca_bt.style.display = "block";
                ca_nbt.style.display = "none";
                ca_e.style.display = "none";
                div_bisnis_numb_dns.style.display = "block";
                div_bisnis_numb_ent.style.display = "none";
                div_allowance.style.display = "block";
            } else if (ca_type.value === "ndns"){
                ca_bt.style.display = "none";
                ca_nbt.style.display = "block";
                ca_e.style.display = "none";
                div_bisnis_numb_dns.style.display = "none";
                div_bisnis_numb_ent.style.display = "none";
                bisnis_numb.style.value = "";
                div_allowance.style.display = "none";
            } else if (ca_type.value === "entr"){
                ca_bt.style.display = "none";
                ca_nbt.style.display = "none";
                ca_e.style.display = "block";
                div_bisnis_numb_dns.style.display = "none";
                div_bisnis_numb_ent.style.display = "block";
            } else{
                ca_bt.style.display = "none";
                ca_nbt.style.display = "none";
                ca_e.style.display = "none";
                div_bisnis_numb_dns.style.display = "none";
                div_bisnis_numb_ent.style.display = "none";
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
                        <label class="form-label">Date</label>
                        <input type="date" name="tanggal_nbt[]" class="form-control" placeholder="mm/dd/yyyy">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Information</label>
                        <textarea name="keterangan_nbt[]" class="form-control"></textarea>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Amount</label>
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
                            <option value="food">Food/Beverages/Souvenir</option>
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

            let relationCounter = 0; // Counter untuk membuat ID unik

            // Function to add new ERelation form
            function addNewERelationForm() {
                relationCounter++; // Increment counter setiap kali form baru ditambahkan
                const newFormERelation = document.createElement('div');
                newFormERelation.classList.add('mb-2');

                newFormERelation.innerHTML = `
                    <div class="mb-2">
                        <label class="form-label">Relation Type</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="accommodation_e_relation[]" id="accommodation_e_relation_${relationCounter}" value="accommodation">
                            <label class="form-check-label" for="accommodation_e_relation_${relationCounter}">Accommodation</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="transport_e_relation[]" id="transport_e_relation_${relationCounter}" value="transport">
                            <label class="form-check-label" for="transport_e_relation_${relationCounter}">Transport</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="gift_e_relation[]" id="gift_e_relation_${relationCounter}" value="gift">
                            <label class="form-check-label" for="gift_e_relation_${relationCounter}">Gift</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="fund_e_relation[]" id="fund_e_relation_${relationCounter}" value="fund">
                            <label class="form-check-label" for="fund_e_relation_${relationCounter}">Fund</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="food_e_relation[]" id="food_e_relation_${relationCounter}" value="food">
                            <label class="form-check-label" for="food_e_relation_${relationCounter}">Food/Beverages/Souvenir</label>
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Name</label>
                        <input type="text" name="rname_e_relation[]" class="form-control" id="rname_e_relation_${relationCounter}">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Position</label>
                        <input type="text" name="rposition_e_relation[]" class="form-control" id="rposition_e_relation_${relationCounter}">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Company</label>
                        <input type="text" name="rcompany_e_relation[]" class="form-control" id="rcompany_e_relation_${relationCounter}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Purpose</label>
                        <input type="text" name="rpurpose_e_relation[]" class="form-control" id="rpurpose_e_relation_${relationCounter}">
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-beta3/js/bootstrap.min.js"></script>
@endpush

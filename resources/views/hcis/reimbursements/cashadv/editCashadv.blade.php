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
                <div class="card-header d-flex bg-white justify-content-between">
                    <h4 class="modal-title" id="viewFormEmployeeLabel">Edit Cash Advance -
                        <b>{{ $transactions->no_ca }}</b></h4>
                    <a href="{{ route('cashadvanced') }}" type="button" class="btn btn-close"></a>
                </div>
                <div class="card-body" @style('overflow-y: auto;')>
                    <div class="container-fluid">
                        <form id="scheduleForm" method="post"
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
                                    <br><input type="text" name="others_location" id="others_location"
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
                                </div>
                                <div class="col-md-12 mb-2">
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
                                                <div class="text-bg-danger p-2" style="text-align:center">Estimated Cash Advanced</div>
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
                                                <div class="text-bg-danger p-2" style="text-align:center">Estimated Cash Advanced Non Business Trip
                                                </div>
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
                                                <div class="text-bg-danger p-2" style="text-align:center">Estimated Entertainment
                                                </div>
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
                            <a href="{{ route('cashadvanced') }}" type="button"
                                class="btn btn-outline-secondary px-4 me-2">Cancel</a>
                            <button type="submit" name="action_ca_draft" value="Draft" class="btn btn-secondary btn-pill px-4 me-2">Draft</button>
                            <button type="submit" name="action_ca_submit" value="Pending" class="btn btn-primary btn-pill px-4 me-2">Submit</button>
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

        function formatInputENT(input) {
            let value = input.value.replace(/\./g, '');
            value = parseFloat(value);
            if (!isNaN(value)) {
                input.value = formatNumber(Math.floor(value));
            } else {
                input.value = formatNumber(0);
            }
            calculateTotalNominalEDetail();
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
            var others = document.getElementById("location_bt_perdiem[]");
            var others_loc = document.getElementById("other_location_bt_perdiem[]");

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

        document.addEventListener('DOMContentLoaded', function() {
            var collapseTransport = document.getElementById('collapseTransport');
            collapseTransport.addEventListener('show.bs.collapse', function() {
                var inputsToReset = collapseTransport.querySelectorAll('[data-clear-on-collapse]');
                inputsToReset.forEach(function(input) {
                    if (input.tagName === 'INPUT' || input.tagName === 'TEXTAREA') {
                        input.value = '';
                    } else if (input.tagName === 'SELECT') {
                        input.selectedIndex = 0;
                    }
                });
            });
        });

        // document.addEventListener('DOMContentLoaded', function() {
        //     const formContainerEDetail = document.getElementById('form-container-e-detail');
        //     const formContainerERelation = document.getElementById('form-container-e-relation');

        //     // Function to update checkboxes visibility based on selected options
        //     function updateCheckboxVisibility() {
        //         // Gather all selected options from enter_type_e_detail
        //         const selectedOptions = Array.from(document.querySelectorAll('select[name="enter_type_e_detail[]"]'))
        //             .map(select => select.value)
        //             .filter(value => value !== "");

        //         // Update visibility for each checkbox in enter_type_e_relation
        //         formContainerERelation.querySelectorAll('.form-check').forEach(checkDiv => {
        //             const checkbox = checkDiv.querySelector('input.form-check-input');
        //             const checkboxValue = checkbox.value.toLowerCase().replace(/\s/g, "_");
        //             if (selectedOptions.includes(checkboxValue)) {
        //                 checkDiv.style.display = 'block';
        //             } else {
        //                 checkDiv.style.display = 'none';
        //             }
        //         });
        //     }

        //     // Function to format number with thousands separator
        //     function formatNumber(num) {
        //         return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        //     }

        //     // Function to parse number from formatted string
        //     function parseNumber(value) {
        //         return parseFloat(value.replace(/\./g, '')) || 0;
        //     }

        //     // Function to format input fields
        //     function formatInput(input) {
        //         let value = input.value.replace(/\./g, '');
        //         value = parseFloat(value);
        //         if (!isNaN(value)) {
        //             input.value = formatNumber(Math.floor(value));
        //         } else {
        //             input.value = formatNumber(0);
        //         }
        //         calculateTotalNominalEDetail();
        //     }

        //     // Function to calculate the total nominal value for EDetail
        //     function calculateTotalNominalEDetail() {
        //         let total = 0;
        //         document.querySelectorAll('input[name="nominal_e_detail[]"]').forEach(input => {
        //             total += parseNumber(input.value);
        //         });
        //         document.querySelector('input[name="total_e_detail[]"]').value = formatNumber(total);
        //         document.getElementById('totalca').value = formatNumber(total);
        //     }

        //     // Function to add new EDetail form
        //     function addNewEDetailForm() {
        //         const newFormEDetail = document.createElement('div');
        //         newFormEDetail.classList.add('mb-2');

        //         newFormEDetail.innerHTML = `
        //             <div class="mb-2">
        //                 <label class="form-label">Entertainment Type</label>
        //                 <select name="enter_type_e_detail[]" class="form-select">
        //                     <option value="">-</option>
        //                     <option value="food">Food/Beverages/Souvenir</option>
        //                     <option value="transport">Transport</option>
        //                     <option value="accommodation">Accommodation</option>
        //                     <option value="gift">Gift</option>
        //                     <option value="fund">Fund</option>
        //                 </select>
        //             </div>
        //             <div class="mb-2">
        //                 <label class="form-label">Entertainment Fee Detail</label>
        //                 <textarea name="enter_fee_e_detail[]" class="form-control"></textarea>
        //             </div>
        //             <div class="input-group mb-3">
        //                 <div class="input-group-append">
        //                     <span class="input-group-text">Rp</span>
        //                 </div>
        //                 <input class="form-control" name="nominal_e_detail[]" type="text" min="0" value="0">
        //             </div>
        //             <button type="button" class="btn btn-danger remove-form-e-detail">Remove</button>
        //             <hr class="border border-primary border-1 opacity-50">
        //         `;

        //         formContainerEDetail.appendChild(newFormEDetail);

        //         // Attach input event to the newly added nominal field
        //         newFormEDetail.querySelector('input[name="nominal_e_detail[]"]').addEventListener('input', function() {
        //             formatInput(this);
        //         });

        //         // Attach change event to update checkbox visibility
        //         newFormEDetail.querySelector('select[name="enter_type_e_detail[]"]').addEventListener('change', updateCheckboxVisibility);

        //         // Attach click event to the remove button
        //         newFormEDetail.querySelector('.remove-form-e-detail').addEventListener('click', function() {
        //             newFormEDetail.remove();
        //             updateCheckboxVisibility();
        //             calculateTotalNominalEDetail();
        //         });
        //     }

        //     // Function to add new ERelation form
        //     function addNewERelationForm() {
        //         const newFormERelation = document.createElement('div');
        //         newFormERelation.classList.add('mb-2');

        //         newFormERelation.innerHTML = `
        //             <div class="mb-2">
        //                 <label class="form-label">Relation Type</label>
        //                 <div class="form-check">
        //                     <input class="form-check-input" type="checkbox" name="accommodation_e_relation[]" id="transport_e_relation[]" value="transport">
        //                     <label class="form-check-label" for="transport_e_relation[]">Transport</label>
        //                 </div>
        //                 <div class="form-check">
        //                     <input class="form-check-input" type="checkbox" name="transport_e_relation[]" id="accommodation_e_relation[]" value="accommodation">
        //                     <label class="form-check-label" for="accommodation_e_relation[]">Accommodation</label>
        //                 </div>
        //                 <div class="form-check">
        //                     <input class="form-check-input" type="checkbox" name="gift_e_relation[]" id="gift_e_relation[]" value="gift">
        //                     <label class="form-check-label" for="gift_e_relation[]">Gift</label>
        //                 </div>
        //                 <div class="form-check">
        //                     <input class="form-check-input" name="fund_e_relation[]" type="checkbox" id="fund_e_relation[]" value="fund">
        //                     <label class="form-check-label" for="fund_e_relation[]">Fund</label>
        //                 </div>
        //                 <div class="form-check">
        //                     <input class="form-check-input" type="checkbox" id="food_e_relation[]" name="food_e_relation[]" value="food">
        //                     <label class="form-check-label" for="food_e_relation[]">Food/Beverages/Souvenir</label>
        //                 </div>
        //             </div>
        //             <div class="mb-2">
        //                 <label class="form-label">Name</label>
        //                 <input type="text" name="rname_e_relation[]" class="form-control">
        //             </div>
        //             <div class="mb-2">
        //                 <label class="form-label">Position</label>
        //                 <input type="text" name="rposition_e_relation[]" class="form-control">
        //             </div>
        //             <div class="mb-2">
        //                 <label class="form-label">Company</label>
        //                 <input type="text" name="rcompany_e_relation[]" class="form-control">
        //             </div>
        //             <div class="mb-3">
        //                 <label class="form-label">Purpose</label>
        //                 <input type="text" name="rpurpose_e_relation[]" class="form-control">
        //             </div>
        //             <button type="button" class="btn btn-danger remove-form-e-relation">Remove</button>
        //             <hr class="border border-primary border-1 opacity-50">
        //         `;

        //         formContainerERelation.appendChild(newFormERelation);

        //         // Initial update of checkbox visibility
        //         updateCheckboxVisibility();

        //         // Attach click event to the remove button
        //         newFormERelation.querySelector('.remove-form-e-relation').addEventListener('click', function() {
        //             newFormERelation.remove();
        //             updateCheckboxVisibility();
        //         });
        //     }

        //     document.getElementById('add-more-e-detail').addEventListener('click', addNewEDetailForm);
        //     document.getElementById('add-more-e-relation').addEventListener('click', addNewERelationForm);

        //     // Attach input event to the existing nominal fields
        //     document.querySelectorAll('input[name="nominal_e_detail[]"]').forEach(input => {
        //         input.addEventListener('input', function() {
        //             formatInput(this);
        //         });
        //     });

        //     // Attach change event to existing select fields for checkbox visibility
        //     document.querySelectorAll('select[name="enter_type_e_detail[]"]').forEach(select => {
        //         select.addEventListener('change', updateCheckboxVisibility);
        //     });

        //     calculateTotalNominalEDetail();
        //     updateCheckboxVisibility();
        // });

        document.querySelector("#test").addEventListener("click", ()=>{
            Swal.fire("SweetAlert2 is working!");
        });

    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-beta3/js/bootstrap.min.js"></script>
@endpush

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
                <div class="mb-3">
                    {{-- <a href="{{ url()->previous() }}" class="btn btn-outline-primary">
                    <i class="bi bi-caret-left-fill"></i> Kembali
                </a> --}}
                </div>
                <div class="card">
                    <div class="card-header d-flex bg-primary text-white justify-content-between">
                        <h4 class="mb-0">Add Data</h4>
                        <a href="/businessTrip" type="button" class="btn-close btn-close-white"></a>
                    </div>
                    <div class="card-body">
                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        <form id="btFrom" action="/businessTrip/form/post" method="POST">
                            @csrf
                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <label for="nama" class="form-label">Name</label>
                                    <input type="text" class="form-control bg-light" id="nama" name="nama"
                                        style="cursor:not-allowed;" value="{{ $employee_data->fullname }}" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label for="divisi" class="form-label">Divison</label>
                                    <input type="text" class="form-control bg-light" id="divisi" name="divisi"
                                        style="cursor:not-allowed;" value="{{ $employee_data->unit }}" readonly>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-4">
                                    <label for="norek_krywn" class="form-label">Employee Account Number</label>
                                    <input type="number" class="form-control bg-light" id="norek_krywn" name="norek_krywn"
                                        value="{{ $employee_data->bank_account_number }}" readonly>
                                </div>
                                <div class="col-md-4">
                                    <label for="nama_pemilik_rek" class="form-label">Name of Account Owner</label>
                                    <input type="text" class="form-control bg-light" id="nama_pemilik_rek"
                                        name="nama_pemilik_rek" value="{{ $employee_data->bank_account_name }}" readonly>
                                </div>

                                <div class="col-md-4">
                                    <label for="nama_bank" class="form-label">Bank Name</label>
                                    <input type="text" class="form-control bg-light" id="nama_bank" name="nama_bank"
                                        placeholder="ex. BCA" value="{{ $employee_data->bank_name }}" readonly>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-4">
                                    <label for="mulai" class="form-label">Start Date</label>
                                    <input type="date" class="form-control datepicker" id="mulai" name="mulai"
                                        placeholder="Tanggal Mulai" required onchange="validateStartEndDates()">
                                </div>
                                <div class="col-md-4">
                                    <label for="kembali" class="form-label">End Date</label>
                                    <input type="date" class="form-control datepicker" id="kembali" name="kembali"
                                        placeholder="Tanggal Kembali" required onchange="validateStartEndDates()">
                                </div>
                                <input class="form-control" id="perdiem" name="perdiem" type="hidden"
                                    value="{{ $perdiem->amount }}" readonly>
                                <div class="col-md-4">
                                    <label for="tujuan" class="form-label">Destination</label>
                                    <select class="form-select select2" name="tujuan" id="tujuan"
                                        onchange="BTtoggleOthers()" required>
                                        <option value="">--- Choose Destination ---</option>
                                        @foreach ($locations as $location)
                                            <option value="{{ $location->area }}">
                                                {{ $location->area . ' (' . $location->city . ')' }}
                                            </option>
                                        @endforeach
                                        <option value="Others">Others</option>
                                    </select>
                                    <br><input type="text" name="others_location" id="others_location"
                                        class="form-control" placeholder="Other Location" value=""
                                        style="display: none;">
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-12">
                                    <label for="keperluan" class="form-label">Need (To be filled in according to visit
                                        service)</label>
                                    <textarea class="form-control" id="keperluan" name="keperluan" rows="3" placeholder="Fill your need"
                                        required></textarea>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <label for="bb_perusahaan" class="form-label">Company Cost Expenses (PT Service Needs
                                        /
                                        Not
                                        PT Payroll)</label>
                                    <select class="form-select select2" id="bb_perusahaan" name="bb_perusahaan" required>
                                        <option value="" disabled selected>--- Choose PT ---</option>
                                        @foreach ($companies as $company)
                                            <option value="{{ $company->contribution_level_code }}">
                                                {{ $company->contribution_level . ' (' . $company->contribution_level_code . ')' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="jns_dinas" class="form-label">Type of Service</label>
                                    <select class="form-select" id="jns_dinas" name="jns_dinas" required>
                                        <option value="" selected disabled>-- Choose Type of Service --</option>
                                        <option value="dalam kota">Dinas Dalam Kota</option>
                                        <option value="luar kota">Dinas Luar Kota</option>
                                    </select>
                                </div>
                            </div>

                            <div id="additional-fields" class="row mb-3" style="display: none;">
                                <label for="additional-fields-title" class="mb-1">Business Trip Needs</label>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="cashAdvancedCheckbox">
                                            <label class="form-check-label" for="cashAdvancedCheckbox">Cash
                                                Advanced</label>
                                        </div>

                                        <input type="hidden" name="ca" value="Tidak">
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="ticketCheckbox"
                                                name="tiket" value="Ya">
                                            <label class="form-check-label" for="ticketCheckbox">Ticket</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="hotelCheckbox"
                                                name="hotel" value="Ya">
                                            <label class="form-check-label" for="hotelCheckbox">Hotel</label>
                                        </div>
                                        <input type="hidden" name="hotel" value="Tidak">
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="taksiCheckbox"
                                                name="taksi" value="Ya">
                                            <label class="form-check-label" for="taksiCheckbox">Taxi Voucher</label>
                                        </div>
                                        <input type="hidden" name="taksi" value="Tidak">
                                    </div>
                                </div>


                                <div class="row mt-2" id="ca_div" style="display: none;">
                                    <div class="col-md-12">
                                        <div class="d-flex flex-column gap-2">
                                            <div class="text-bg-primary p-2 text-center rounded">Cash Advanced</div>

                                            <div class="row" id="ca_bt">
                                                <div class="col-md-12">
                                                    <div class="table-responsive-sm">
                                                        <div class="d-flex flex-column gap-2">
                                                            <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                                                                <li class="nav-item" role="presentation">
                                                                    <button class="nav-link active" id="pills-perdiem-tab"
                                                                        data-bs-toggle="pill"
                                                                        data-bs-target="#pills-perdiem" type="button"
                                                                        role="tab" aria-controls="pills-perdiem"
                                                                        aria-selected="true">Perdiem Plan</button>
                                                                </li>
                                                                <li class="nav-item" role="presentation">
                                                                    <button class="nav-link" id="pills-transport-tab"
                                                                        data-bs-toggle="pill"
                                                                        data-bs-target="#pills-transport" type="button"
                                                                        role="tab" aria-controls="pills-transport"
                                                                        aria-selected="false">Transport Plan</button>
                                                                </li>
                                                                <li class="nav-item" role="presentation">
                                                                    <button class="nav-link" id="pills-accomodation-tab"
                                                                        data-bs-toggle="pill"
                                                                        data-bs-target="#pills-accomodation"
                                                                        type="button" role="tab"
                                                                        aria-controls="pills-accomodation"
                                                                        aria-selected="false">Accomodation Plan</button>
                                                                </li>
                                                                <li class="nav-item" role="presentation">
                                                                    <button class="nav-link" id="pills-other-tab"
                                                                        data-bs-toggle="pill"
                                                                        data-bs-target="#pills-other" type="button"
                                                                        role="tab" aria-controls="pills-other"
                                                                        aria-selected="false">Other Plan</button>
                                                                </li>
                                                            </ul>

                                                            <div class="card">
                                                                <div class="tab-content" id="pills-tabContent">
                                                                    <div class="tab-pane fade show active"
                                                                        id="pills-perdiem" role="tabpanel"
                                                                        aria-labelledby="pills-perdiem-tab">
                                                                        @include('hcis.reimbursements.businessTrip.caPerdiem')
                                                                    </div>
                                                                    <div class="tab-pane fade" id="pills-transport"
                                                                        role="tabpanel"
                                                                        aria-labelledby="pills-transport-tab">
                                                                        @include('hcis.reimbursements.businessTrip.caTransport')
                                                                    </div>
                                                                    <div class="tab-pane fade" id="pills-accomodation"
                                                                        role="tabpanel"
                                                                        aria-labelledby="pills-accomodation-tab">
                                                                        @include('hcis.reimbursements.businessTrip.caAccommodation')
                                                                    </div>
                                                                    <div class="tab-pane fade" id="pills-other"
                                                                        role="tabpanel" aria-labelledby="pills-other-tab">
                                                                        @include('hcis.reimbursements.businessTrip.caOther')
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="col-md-12 mb-2">
                                                                <label class="form-label">Total Cash Advanced</label>
                                                                <div class="input-group">
                                                                    <span class="input-group-text">Rp</span>
                                                                    <input class="form-control bg-light" name="totalca"
                                                                        id="totalca" type="text" value="0"
                                                                        readonly>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>



                                    <ul class="nav nav-pills mb-3 mt-3" id="pills-tab" role="tablist">
                                        <li class="nav-item" role="presentation" style="display: none;"
                                            id="nav-cash-advanced">
                                            <button class="nav-link" id="pills-cash-advanced-tab" data-bs-toggle="pill"
                                                data-bs-target="#pills-cash-advanced" type="button" role="tab"
                                                aria-controls="pills-cash-advanced" aria-selected="false">Cash
                                                Advanced</button>
                                        </li>
                                        <li class="nav-item" role="presentation" style="display: none;" id="nav-ticket">
                                            <button class="nav-link" id="pills-ticket-tab" data-bs-toggle="pill"
                                                data-bs-target="#pills-ticket" type="button" role="tab"
                                                aria-controls="pills-ticket" aria-selected="false">Ticket</button>
                                        </li>
                                        <li class="nav-item" role="presentation" style="display: none;" id="nav-hotel">
                                            <button class="nav-link" id="pills-hotel-tab" data-bs-toggle="pill"
                                                data-bs-target="#pills-hotel" type="button" role="tab"
                                                aria-controls="pills-hotel" aria-selected="false">Hotel</button>
                                        </li>
                                        <li class="nav-item" role="presentation" style="display: none;" id="nav-taksi">
                                            <button class="nav-link" id="pills-taksi-tab" data-bs-toggle="pill"
                                                data-bs-target="#pills-taksi" type="button" role="tab"
                                                aria-controls="pills-taksi" aria-selected="false">Taxi</button>
                                        </li>
                                    </ul>

                                    <div class="tab-content" id="pills-tabContent">
                                        <div class="tab-pane fade" id="pills-cash-advanced" role="tabpanel"
                                            aria-labelledby="pills-cash-advanced-tab">Cash Advanced content</div>
                                        <div class="tab-pane fade" id="pills-ticket" role="tabpanel"
                                            aria-labelledby="pills-ticket-tab">
                                            @include('hcis.reimbursements.businessTrip.form.ticket')
                                        </div>
                                        <div class="tab-pane fade" id="pills-hotel" role="tabpanel"
                                            aria-labelledby="pills-hotel-tab">
                                            @include('hcis.reimbursements.businessTrip.form.hotel')
                                        </div>
                                        <div class="tab-pane fade" id="pills-taksi" role="tabpanel"
                                            aria-labelledby="pills-taksi-tab">
                                            @include('hcis.reimbursements.businessTrip.form.taxi')
                                        </div>
                                    </div>
                                </div>

                                <input type="hidden" name="status" value="Pending L1" id="status">

                                <div class="d-flex justify-content-end px-3 mb-3">
                                    <button type="button" class="btn btn-outline-primary rounded-pill me-2"
                                        name="action_ca_draft" id="save-draft">Save as Draft</button>
                                    <button type="submit" class="btn btn-primary rounded-pill"
                                        name="action_ca_submit">Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript Part -->
    <script src="{{ asset('/js/businessTrip.js') }}"></script>
    {{-- <script src="{{ asset('/js/ca.js') }}"></script> --}}
    <script>
        //CA JS
        document.addEventListener('DOMContentLoaded', function() {
            // Select all date input fields
            const startPerdiemInputs = document.querySelectorAll('input[name="start_bt_perdiem[]"]');
            const endPerdiemInputs = document.querySelectorAll('input[name="end_bt_perdiem[]"]');

            // Function to validate date ranges
            function validateDates() {
                startPerdiemInputs.forEach((startInput, index) => {
                    const endInput = endPerdiemInputs[index];
                    if (startInput.value && endInput.value) {
                        const startDate = new Date(startInput.value);
                        const endDate = new Date(endInput.value);

                        if (endDate < startDate) {
                            endInput.setCustomValidity("End date cannot be earlier than start date.");
                        } else {
                            endInput.setCustomValidity(""); // Clear the validation message
                        }
                    }
                });
            }

            // Add event listeners to date inputs for real-time validation
            startPerdiemInputs.forEach(input => input.addEventListener('change', validateDates));
            endPerdiemInputs.forEach(input => input.addEventListener('change', validateDates));

            // Initialize validation on page load
            validateDates();
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
@endsection

@extends('layouts_.vertical', ['page_title' => 'Business Trip'])

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-datepicker@1.9.0/dist/css/bootstrap-datepicker.min.css"
        rel="stylesheet">

    <style>
        .nav-link {
            color: black;
            border-bottom: 2px solid transparent;
            transition: color 0.3s ease, border-bottom 0.3s ease;
        }

        .nav-link.active {
            color: #AB2F2B;
            /* Primary color */
            border-bottom: 2px solid #AB2F2B;
            font-weight: bold;
            /* Underline with primary color */
        }

        .nav-link:hover {
            color: #AB2F2B;
            /* Change color on hover */
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('businessTrip') }}">{{ $parentLink }}</a></li>
                            <li class="breadcrumb-item active">{{ $link }}</li>
                        </ol>
                    </div>
                    <h4 class="page-title">{{ $link }}</h4>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex bg-primary text-white justify-content-between">
                        <h4 class="mb-0">Add Data</h4>
                        <a href="/businessTrip" type="button" class="btn-close btn-close-white"></a>
                    </div>
                    <div class="card-body">
                        <form id="btFrom" action="/businessTrip/form/post" method="POST">
                            @csrf
                            <div class="row mb-2">
                                <div class="col-md-6 mb-2">
                                    <label for="nama" class="form-label">Name</label>
                                    <input type="text" class="form-control form-control-sm bg-light" id="nama"
                                        name="nama" style="cursor:not-allowed;" value="{{ $employee_data->fullname }}"
                                        readonly>
                                </div>
                                <div class="col-md-6 mb-1">
                                    <label for="divisi" class="form-label">Divison</label>
                                    <input type="text" class="form-control form-control-sm bg-light" id="divisi"
                                        name="divisi" style="cursor:not-allowed;" value="{{ $employee_data->unit }}"
                                        readonly>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-4 mb-2">
                                    <label for="norek_krywn" class="form-label">Employee Account Number</label>
                                    <input type="number" class="form-control form-control-sm bg-light" id="norek_krywn"
                                        name="norek_krywn" value="{{ $employee_data->bank_account_number }}" readonly>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label for="nama_pemilik_rek" class="form-label">Name of Account Owner</label>
                                    <input type="text" class="form-control form-control-sm bg-light"
                                        id="nama_pemilik_rek" name="nama_pemilik_rek"
                                        value="{{ $employee_data->bank_account_name }}" readonly>
                                </div>

                                <div class="col-md-4 mb-2">
                                    <label for="nama_bank" class="form-label">Bank Name</label>
                                    <input type="text" class="form-control form-control-sm bg-light" id="nama_bank"
                                        name="nama_bank" placeholder="ex. BCA" value="{{ $employee_data->bank_name }}"
                                        readonly>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-4 mb-2">
                                    <label for="mulai" class="form-label">Start Date</label>
                                    <input type="date" class="form-control form-control-sm" id="mulai" name="mulai"
                                        placeholder="Tanggal Mulai" onchange="validateStartEndDates()" required>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label for="kembali" class="form-label">End Date</label>
                                    <input type="date" class="form-control form-control-sm" id="kembali" name="kembali"
                                        placeholder="Tanggal Kembali" onchange="validateStartEndDates()" required>
                                </div>
                                <input class="form-control" id="perdiem" name="perdiem" type="hidden"
                                    value="{{ $perdiem->amount }}" readonly>
                                <input class="form-control" id="group_company" name="group_company" type="hidden"
                                    value="{{ $employee_data->group_company }}" readonly>
                                <div class="col-md-4 mb-2">
                                    <label for="tujuan" class="form-label">Destination</label>
                                    <select class="form-select form-select-sm select2" name="tujuan" id="tujuan"
                                        onchange="BTtoggleOthers()" required>
                                        <option value="">--- Choose Destination ---</option>
                                        <option value="Others">Others</option>
                                        @foreach ($locations as $location)
                                            <option value="{{ $location->area }}">
                                                {{ $location->area . ' (' . $location->company_name . ')' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <br><input type="text" name="others_location" id="others_location"
                                        class="form-control form-control-sm mt-2" placeholder="Other Location"
                                        value="" style="display: none;">
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-12">
                                    <label for="keperluan" class="form-label">Need (To be filled in according to visit
                                        service)</label>
                                    <textarea class="form-control form-control-sm" id="keperluan" name="keperluan" rows="3"
                                        placeholder="Fill your need" required></textarea>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-6 mb-2">
                                    <label for="bb_perusahaan" class="form-label">Company Cost Expenses (PT Service Needs
                                        /
                                        Not
                                        PT Payroll)</label>
                                    <select class="form-select form-select-sm select2" id="bb_perusahaan"
                                        name="bb_perusahaan" required>
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
                                    <select class="form-select form-select-sm" id="jns_dinas" name="jns_dinas" required>
                                        <option value="" selected disabled>-- Choose Type of Service --</option>
                                        <option value="dalam kota">Dinas Dalam Kota</option>
                                        <option value="luar kota">Dinas Luar Kota</option>
                                    </select>
                                </div>
                            </div>

                            <div id="additional-fields" class="row mb-3" style="display: none;">
                                <div class="col-md-12">
                                    <label for="additional-fields-title" class="mb-3">
                                        Business Trip Needs <br>
                                        @if ($isAllowed)
                                        <span class="text-info fst-italic">* Your job
                                            level is above 8. No perdiem is required for your job level</span>
                                        @endif
                                    </label>
                                    <div class="row">
                                        <div class="col-md-2">
                                            <div class="form-check">
                                                <input type="hidden" name="ca" id="caHidden" value="Tidak">
                                                <input class="form-check-input" type="checkbox" id="perdiemCheckbox"
                                                    value="Ya" onchange="updateCAValue()">
                                                <label class="form-check-label" for="perdiemCheckbox">{{ $allowance }}</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="cashAdvancedCheckbox"
                                                    value="Ya" onchange="updateCAValue()">
                                                <label class="form-check-label" for="cashAdvancedCheckbox">Cash
                                                    Advanced</label>
                                            </div>
                                        </div>

                                        <div class="col-md-2">
                                            <div class="form-check">
                                                <input type="hidden" name="tiket" value="Tidak">
                                                <input class="form-check-input" type="checkbox" id="ticketCheckbox"
                                                    name="tiket" value="Ya">
                                                <label class="form-check-label" for="ticketCheckbox">
                                                    Ticket
                                                </label>
                                            </div>
                                        </div>

                                        <div class="col-md-2">
                                            <div class="form-check">
                                                <input type="hidden" name="hotel" value="Tidak">
                                                <input class="form-check-input" type="checkbox" id="hotelCheckbox"
                                                    name="hotel" value="Ya">
                                                <label class="form-check-label" for="hotelCheckbox">
                                                    Hotel
                                                </label>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input type="hidden" name="taksi" value="Tidak">
                                                <input class="form-check-input" type="checkbox" id="taksiCheckbox"
                                                    name="taksi" value="Ya">
                                                <label class="form-check-label" for="taksiCheckbox">
                                                    Taxi Voucher
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-3">
                                        <div class="col-md-12">
                                            <ul class="nav nav-tabs nav-pills mb-2" id="pills-tab" role="tablist">
                                                <li class="nav-item" role="presentation" id="nav-perdiem"
                                                    style="display: none;">
                                                    <button class="nav-link" id="pills-perdiem-tab" data-bs-toggle="pill"
                                                        data-bs-target="#pills-perdiem" type="button" role="tab"
                                                        aria-controls="pills-perdiem"
                                                        aria-selected="false">{{ $allowance }}</button>
                                                </li>
                                                <li class="nav-item" role="presentation" id="nav-cashAdvanced"
                                                    style="display: none;">
                                                    <button class="nav-link" id="pills-cashAdvanced-tab"
                                                        data-bs-toggle="pill" data-bs-target="#pills-cashAdvanced"
                                                        type="button" role="tab" aria-controls="pills-cashAdvanced"
                                                        aria-selected="false">Cash Advanced</button>
                                                </li>
                                                <li class="nav-item" role="presentation" id="nav-ticket"
                                                    style="display: none;">
                                                    <button class="nav-link" id="pills-ticket-tab" data-bs-toggle="pill"
                                                        data-bs-target="#pills-ticket" type="button" role="tab"
                                                        aria-controls="pills-ticket" aria-selected="false">Ticket</button>
                                                </li>
                                                <li class="nav-item" role="presentation" id="nav-hotel"
                                                    style="display: none;">
                                                    <button class="nav-link" id="pills-hotel-tab" data-bs-toggle="pill"
                                                        data-bs-target="#pills-hotel" type="button" role="tab"
                                                        aria-controls="pills-hotel" aria-selected="false">Hotel</button>
                                                </li>
                                                <li class="nav-item" role="presentation" id="nav-taksi"
                                                    style="display: none;">
                                                    <button class="nav-link" id="pills-taksi-tab" data-bs-toggle="pill"
                                                        data-bs-target="#pills-taksi" type="button" role="tab"
                                                        aria-controls="pills-taksi" aria-selected="false">Taxi</button>
                                                </li>
                                            </ul>

                                            <div class="tab-content" id="pills-tabContent">
                                                <div class="tab-pane fade" id="pills-perdiem" role="tabpanel"
                                                    aria-labelledby="pills-perdiem-tab">
                                                    {{-- ca perdiem content --}}
                                                    <div id="ca_perdiem">
                                                        <div class="row mb-2">
                                                            <div class="col-md-6 mb-2">
                                                                <label for="date_required" class="form-label">Date
                                                                    Required</label>
                                                                <input type="date" class="form-control form-control-sm"
                                                                    id="date_required_1" name="date_required"
                                                                    placeholder="Date Required"
                                                                    onchange="syncDateRequired(this)">
                                                            </div>
                                                            <div class="col-md-6 mb-2">
                                                                <label class="form-label" for="ca_decla">Declaration
                                                                    Estimate</label>
                                                                <input type="date" name="ca_decla" id="ca_decla_1"
                                                                    class="form-control form-control-sm bg-light"
                                                                    placeholder="mm/dd/yyyy" readonly>
                                                            </div>
                                                        </div>
                                                        @include('hcis.reimbursements.businessTrip.caPerdiem')
                                                    </div>
                                                </div>
                                                <div class="tab-pane fade" id="pills-cashAdvanced" role="tabpanel"
                                                    aria-labelledby="pills-cashAdvanced-tab">
                                                    {{-- Cash Advanced content --}}
                                                    @include('hcis.reimbursements.businessTrip.form.btCa')
                                                </div>
                                                <div class="tab-pane fade" id="pills-ticket" role="tabpanel"
                                                    aria-labelledby="pills-ticket-tab">
                                                    {{-- Ticket content --}}
                                                    @include('hcis.reimbursements.businessTrip.form.ticket')
                                                </div>
                                                <div class="tab-pane fade" id="pills-hotel" role="tabpanel"
                                                    aria-labelledby="pills-hotel-tab">
                                                    {{-- Hotel content --}}
                                                    @include('hcis.reimbursements.businessTrip.form.hotel')
                                                </div>
                                                <div class="tab-pane fade" id="pills-taksi" role="tabpanel"
                                                    aria-labelledby="pills-taksi-tab">
                                                    {{-- Taxi content --}}
                                                    @include('hcis.reimbursements.businessTrip.form.taxi')
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <input type="hidden" name="status" value="Pending L1" id="status">
                                <input type="hidden" id="formActionType" name="formActionType" value="">


                                <div class="d-flex justify-content-end mt-3">
                                    <button type="submit" class="btn btn-outline-primary rounded-pill me-2 draft-button"
                                        name="action_draft" id="save-draft" value="Draft" id="save-draft">Save as
                                        Draft</button>
                                    <button type="submit" class="btn btn-primary rounded-pill submit-button"
                                        name="action_submit" value="Pending L1" id="submit-btn">Submit</button>
                                </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript Part -->
    <script src="{{ asset('/js/businessTrip.js') }}"></script>
    <link href="{{ asset('vendor/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
    <script src="{{ asset('vendor/select2/dist/js/select2.min.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.submit-button').forEach(button => {
                button.addEventListener('click', (event) => {
                    event.preventDefault(); // Prevent immediate form submission

                    const form = document.getElementById('btFrom');

                    // Check if the form is valid before proceeding
                    if (!form.checkValidity()) {
                        form.reportValidity(); // Show validation messages if invalid
                        return; // Exit if the form is not valid
                    }

                    // Retrieve the values from the input fields
                    const dateReq = document.getElementById('date_required_1').value;
                    const dateReq2 = document.getElementById('date_required_2').value;
                    const totalBtPerdiem = document.getElementById('total_bt_perdiem').value;
                    const totalBtPenginapan = document.getElementById('total_bt_penginapan').value;
                    const totalBtTransport = document.getElementById('total_bt_transport').value;
                    const totalBtLainnya = document.getElementById('total_bt_lainnya').value;
                    const caCheckbox = document.getElementById('cashAdvancedCheckbox').checked;
                    const perdiemCheckbox = document.getElementById('perdiemCheckbox').checked;
                    const totalCa = document.getElementById('totalca').value;

                    if (perdiemCheckbox && !dateReq) {
                        Swal.fire({
                            title: "Warning!",
                            text: "Please select a Date Required.",
                            icon: "warning",
                            confirmButtonColor: "#AB2F2B",
                            confirmButtonText: "OK",
                        });
                        return;
                    }

                    if (caCheckbox && !dateReq2) {
                        Swal.fire({
                            title: "Warning!",
                            text: "Please select a Date Required.",
                            icon: "warning",
                            confirmButtonColor: "#AB2F2B",
                            confirmButtonText: "OK",
                        });
                        return;
                    }
                    // Check if CA is checked and all fields are zero
                    if (caCheckbox && totalBtPenginapan == 0 &&
                        totalBtTransport == 0 && totalBtLainnya == 0) {
                        Swal.fire({
                            title: "Warning!",
                            text: "Cash Advanced fields (Accommodation, Transport, Others) are 0.\nPlease fill in the values.",
                            icon: "warning",
                            confirmButtonColor: "#AB2F2B",
                            confirmButtonText: "OK",
                        });
                        return; // Exit without showing the confirmation if all fields are zero
                    }
                    if (perdiemCheckbox && totalBtPerdiem == 0) {
                        Swal.fire({
                            title: "Warning!",
                            text: "Total {{$allowance}} is 0. Please fill in the values.",
                            icon: "warning",
                            confirmButtonColor: "#AB2F2B",
                            confirmButtonText: "OK",
                        });
                        return; // Exit without showing the confirmation if all fields are zero
                    }

                    const caChecked = caCheckbox ? 'CA' : '';
                    const ticketChecked = document.getElementById('ticketCheckbox').checked ?
                        'Ticket' : '';
                    const hotelChecked = document.getElementById('hotelCheckbox').checked ?
                        'Hotel' : '';
                    const taksiChecked = document.getElementById('taksiCheckbox').checked ?
                        'Taxi Voucher' : '';

                    // Create a message with the input values, each on a new line with bold titles
                    const inputSummary = `
                            <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                            <tr>
                                <th style="width: 40%; text-align: left; padding: 8px;">Total {{ $allowance }}</th>
                                <td style="width: 10%; text-align: right; padding: 8px;">:</td>
                                <td style="width: 50%; text-align: left; padding: 8px;">Rp. <strong>${totalBtPerdiem}</strong></td>
                            </tr>
                            <tr>
                                <th style="width: 40%; text-align: left; padding: 8px;">Total Accommodation</th>
                                <td style="width: 10%; text-align: right; padding: 8px;">:</td>
                                <td style="width: 50%; text-align: left; padding: 8px;">Rp. <strong>${totalBtPenginapan}</strong></td>
                            </tr>
                            <tr>
                                <th style="width: 40%; text-align: left; padding: 8px;">Total Transport</th>
                                <td style="width: 10%; text-align: right; padding: 8px;">:</td>
                                <td style="width: 50%; text-align: left; padding: 8px;">Rp. <strong>${totalBtTransport}</strong></td>
                            </tr>
                            <tr>
                                <th style="width: 40%; text-align: left; padding: 8px;">Total Others</th>
                                <td style="width: 10%; text-align: right; padding: 8px;">:</td>
                                <td style="width: 50%; text-align: left; padding: 8px;">Rp. <strong>${totalBtLainnya}</strong></td>
                            </tr>
                        </table>
                        <hr style="margin: 20px 0;">
                        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                            <tr>
                                <th style="width: 40%; text-align: left; padding: 8px;">Total Cash Advanced</th>
                                <td style="width: 10%; text-align: right; padding: 8px;">:</td>
                                <td style="width: 50%; text-align: left; padding: 8px;">Rp. <strong>${totalCa}</strong></td>
                            </tr>
                        </table>
                                `;

                    // Show SweetAlert confirmation with the input summary
                    Swal.fire({
                        title: "Do you want to submit this request?",
                        html: `You won't be able to revert this!<br><br>${inputSummary}`, // Use 'html' instead of 'text'
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#AB2F2B",
                        cancelButtonColor: "#CCCCCC",
                        confirmButtonText: "Yes, submit it!"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Create a hidden input field to hold the action value
                            const input = document.createElement('input');
                            input.type =
                                'hidden'; // Hidden input so it doesn't show in the form
                            input.name = button.name; // Use the button's name attribute
                            input.value = button.value; // Use the button's value attribute

                            form.appendChild(input); // Append the hidden input to the form
                            form.submit(); // Submit the form only if confirmed
                        }
                    });
                });
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.draft-button').forEach(button => {
                button.addEventListener('click', (event) => {
                    event.preventDefault(); // Prevent immediate form submission

                    const form = document.getElementById('btFrom');

                    // Check if the form is valid before proceeding
                    if (!form.checkValidity()) {
                        form.reportValidity(); // Show validation messages if invalid
                        return; // Exit if the form is not valid
                    }

                    // Retrieve the values from the input fields
                    const dateReq = document.getElementById('date_required_1').value;
                    const dateReq2 = document.getElementById('date_required_2').value;
                    const totalBtPerdiem = document.getElementById('total_bt_perdiem').value;
                    const totalBtPenginapan = document.getElementById('total_bt_penginapan').value;
                    const totalBtTransport = document.getElementById('total_bt_transport').value;
                    const totalBtLainnya = document.getElementById('total_bt_lainnya').value;
                    const caCheckbox = document.getElementById('cashAdvancedCheckbox').checked;
                    const perdiemCheckbox = document.getElementById('perdiemCheckbox').checked;
                    const totalCa = document.getElementById('totalca').value;

                    if (perdiemCheckbox && !dateReq) {
                        Swal.fire({
                            title: "Warning!",
                            text: "Please select a Date Required.",
                            icon: "warning",
                            confirmButtonColor: "#AB2F2B",
                            confirmButtonText: "OK",
                        });
                        return;
                    }

                    if (caCheckbox && !dateReq2) {
                        Swal.fire({
                            title: "Warning!",
                            text: "Please select a Date Required.",
                            icon: "warning",
                            confirmButtonColor: "#AB2F2B",
                            confirmButtonText: "OK",
                        });
                        return;
                    }
                    // Check if CA is checked and all fields are zero
                    if (caCheckbox && totalBtPenginapan == 0 &&
                        totalBtTransport == 0 && totalBtLainnya == 0) {
                        Swal.fire({
                            title: "Warning!",
                            text: "Cash Advanced fields (Accommodation, Transport, Others) are 0.\nPlease fill in the values.",
                            icon: "warning",
                            confirmButtonColor: "#AB2F2B",
                            confirmButtonText: "OK",
                        });
                        return; // Exit without showing the confirmation if all fields are zero
                    }
                    if (perdiemCheckbox && totalBtPerdiem == 0) {
                        Swal.fire({
                            title: "Warning!",
                            text: "Total {{$allowance}} is 0. Please fill in the values.",
                            icon: "warning",
                            confirmButtonColor: "#AB2F2B",
                            confirmButtonText: "OK",
                        });
                        return; // Exit without showing the confirmation if all fields are zero
                    }
                    const input = document.createElement('input');
                    input.type =
                        'hidden'; // Hidden input so it doesn't show in the form
                    input.name = button.name; // Use the button's name attribute
                    input.value = button.value; // Use the button's value attribute

                    form.appendChild(input); // Append the hidden input to the form
                    form.submit(); // Submit the form only if confirmed
                });
            });
        });
    </script>
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
        function toggleDivs() {
            // ca_type ca_nbt ca_e
            var ca_type = document.getElementById("ca_type");
            var ca_nbt = document.getElementById("ca_nbt");
            var ca_e = document.getElementById("ca_e");
            var div_bisnis_numb_dns = document.getElementById("div_bisnis_numb_dns");
            var div_bisnis_numb_ent = document.getElementById("div_bisnis_numb_ent");
            var bisnis_numb = document.getElementById("bisnis_numb");

            if (ca_type.value === "dns") {
                ca_bt.style.display = "block";
                ca_nbt.style.display = "none";
                ca_e.style.display = "none";
                div_bisnis_numb_dns.style.display = "block";
                div_bisnis_numb_ent.style.display = "none";
            } else if (ca_type.value === "ndns") {
                ca_bt.style.display = "none";
                ca_nbt.style.display = "block";
                ca_e.style.display = "none";
                div_bisnis_numb_dns.style.display = "none";
                div_bisnis_numb_ent.style.display = "none";
                bisnis_numb.style.value = "";
            } else if (ca_type.value === "entr") {
                ca_bt.style.display = "none";
                ca_nbt.style.display = "none";
                ca_e.style.display = "block";
                div_bisnis_numb_dns.style.display = "none";
                div_bisnis_numb_ent.style.display = "block";
            } else {
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
            const startDateInput = document.getElementById('mulai');
            const endDateInput = document.getElementById('kembali');
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
            }

            // Menambahkan event listener untuk perubahan di input tanggal
            startDateInput.addEventListener('change', calculateTotalDays);
            endDateInput.addEventListener('change', calculateTotalDays);
        });

        document.getElementById('kembali').addEventListener('change', function() {
            const endDate = new Date(this.value);
            const declarationEstimateDate = new Date(endDate);

            // Check if the new date falls on a weekend
            let daysToAdd = 0;
            while (daysToAdd < 3) {
                declarationEstimateDate.setDate(declarationEstimateDate.getDate() + 1);
                // Jika bukan Sabtu (6) dan bukan Minggu (0), kita tambahkan hari
                if (declarationEstimateDate.getDay() !== 6 && declarationEstimateDate.getDay() !== 0) {
                    daysToAdd++;
                }
            }

            // Format the date into YYYY-MM-DD
            const year = declarationEstimateDate.getFullYear();
            const month = String(declarationEstimateDate.getMonth() + 1).padStart(2, '0');
            const day = String(declarationEstimateDate.getDate()).padStart(2, '0');

            // Set the value of ca_decla
            document.getElementById('ca_decla_1').value = `${year}-${month}-${day}`;
            document.getElementById('ca_decla_2').value = `${year}-${month}-${day}`;
        });
    </script>
@endsection

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
                <div class="mb-3">
                    {{-- <a href="{{ url()->previous() }}" class="btn btn-outline-primary">
                    <i class="bi bi-caret-left-fill"></i> Kembali
                </a> --}}
                </div>
                <div class="card">
                    <div class="card-header d-flex bg-primary text-white justify-content-between">
                        <h4 class="mb-0">Edit Data - {{ $n->no_sppd }}</h4>
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

                        <form action="{{ route('update.bt', ['id' => $n->id]) }}" method="POST" id="btEditForm">
                            @csrf
                            @method('PUT')
                            <div class="row mb-2">
                                <div class="col-md-6 mb-2">
                                    <label for="nama" class="form-label">Name</label>
                                    <input type="text" class="form-control bg-light" id="nama" name="nama"
                                        style="cursor:not-allowed;" value="{{ $employee_data->fullname }}" readonly>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label for="divisi" class="form-label">Divison</label>
                                    <input type="text" class="form-control bg-light" id="divisi" name="divisi"
                                        style="cursor:not-allowed;" value="{{ $employee_data->unit }}" readonly>

                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-4 mb-2">
                                    <label for="norek_krywn" class="form-label">Employee Account Number</label>
                                    <input type="number" class="form-control bg-light" id="norek_krywn" name="norek_krywn"
                                        value="{{ $employee_data->bank_account_number }}" readonly>
                                </div>

                                <div class="col-md-4 mb-2">
                                    <label for="nama_pemilik_rek" class="form-label">Name of Account Owner</label>
                                    <input type="text" class="form-control bg-light" id="nama_pemilik_rek"
                                        name="nama_pemilik_rek" value="{{ $employee_data->bank_account_name }}" readonly>
                                </div>

                                <div class="col-md-4 mb-2">
                                    <label for="nama_bank" class="form-label">Bank Name</label>
                                    <input type="text" class="form-control bg-light" id="nama_bank" name="nama_bank"
                                        value="{{ $employee_data->bank_name }}" placeholder="ex. BCA" readonly>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-4">
                                    <label for="mulai" class="form-label">Start Date</label>
                                    <input type="date" class="form-control datepicker" id="mulai" name="mulai"
                                        placeholder="Tanggal Mulai" value="{{ $n->mulai }}" onchange="validateStartEndDates()">
                                </div>
                                <div class="col-md-4">
                                    <label for="kembali" class="form-label">End Date</label>
                                    <input type="date" class="form-control datepicker" id="kembali" name="kembali"
                                        placeholder="Tanggal Kembali" value="{{ $n->kembali }}" onchange="validateStartEndDates()">
                                </div>

                                <input class="form-control" id="perdiem" name="perdiem" type="hidden"
                                    value="{{ $perdiem->amount }}" readonly>
                                <div class="col-md-4 mb-2">
                                    <label for="tujuan" class="form-label">Destination</label>
                                    <select class="form-select select2" name="tujuan" id="tujuan"
                                        onchange="BTtoggleOthers()" required>
                                        <option value="">--- Choose Destination ---</option>
                                        @foreach ($locations as $location)
                                            <option value="{{ $location->area }}"
                                                {{ $n->tujuan === $location->area ? 'selected' : '' }}>
                                                {{ $location->area . ' (' . $location->city . ')' }}
                                            </option>
                                        @endforeach
                                        <option value="Others"
                                            {{ !in_array($n->tujuan, $locations->pluck('area')->toArray()) ? 'selected' : '' }}>
                                            Others</option>
                                    </select>

                                    <br>
                                    <input type="text" name="others_location" id="others_location"
                                        class="form-control" placeholder="Other Location"
                                        value="{{ !in_array($n->tujuan, $locations->pluck('area')->toArray()) ? $n->tujuan : '' }}"
                                        style="{{ !in_array($n->tujuan, $locations->pluck('area')->toArray()) ? '' : 'display: none;' }}">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="keperluan" class="form-label">Need (To be filled in according to visit
                                    service)</label>
                                <textarea class="form-control" id="keperluan" name="keperluan" rows="3" placeholder="Fill your need"
                                    required>{{ $n->keperluan }}</textarea>
                            </div>

                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <label for="bb_perusahaan" class="form-label">
                                        Company Cost Expenses (PT Service Needs / Not PT Payroll)
                                    </label>
                                    <select class="form-select" id="bb_perusahaan" name="bb_perusahaan" required>
                                        <option value="">--- Choose PT ---</option>
                                        @foreach ($companies as $company)
                                            <option value="{{ $company->contribution_level_code }}"
                                                {{ $company->contribution_level_code == $n->bb_perusahaan ? 'selected' : '' }}>
                                                {{ $company->contribution_level . ' (' . $company->contribution_level_code . ')' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="jns_dinas" class="form-label">Type of Service</label>
                                    <select class="form-select" id="jns_dinas" name="jns_dinas" required
                                        onchange="toggleAdditionalFields()">
                                        <option value="" selected disabled>-- Choose Type of Service --</option>
                                        <option value="dalam kota" {{ $n->jns_dinas == 'dalam kota' ? 'selected' : '' }}>
                                            Dinas
                                            Dalam Kota</option>
                                        <option value="luar kota" {{ $n->jns_dinas == 'luar kota' ? 'selected' : '' }}>
                                            Dinas
                                            Luar Kota</option>
                                    </select>
                                </div>
                            </div>
                            @php
                                // Provide default empty arrays if caDetail or sections are not set
                                $detailPerdiem = $caDetail['detail_perdiem'] ?? [];
                                $detailTransport = $caDetail['detail_transport'] ?? [];
                                $detailPenginapan = $caDetail['detail_penginapan'] ?? [];
                                $detailLainnya = $caDetail['detail_lainnya'] ?? [];

                                // Calculate totals with default values
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
                                $totalCashAdvanced = $totalPerdiem + $totalTransport + $totalPenginapan + $totalLainnya;
                            @endphp
                            <div id="additional-fields" class="row mb-3" style="display: none;">
                                <div class="col-md-12">
                                    <label for="additional-fields-title" class="mb-3">Business Trip Needs</label>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input type="hidden" name="ca" value="Tidak">
                                                <input class="form-check-input" type="checkbox" id="cashAdvancedCheckbox"
                                                    name="ca" value="Ya" <?= $n->ca == 'Ya' ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="cashAdvancedCheckbox">
                                                    Cash Advanced
                                                </label>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input type="hidden" name="tiket" value="Tidak">
                                                <input class="form-check-input" type="checkbox" id="ticketCheckbox"
                                                    name="tiket" value="Ya"
                                                    <?= $n->tiket == 'Ya' ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="ticketCheckbox">
                                                    Ticket
                                                </label>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input type="hidden" name="hotel" value="Tidak">
                                                <input class="form-check-input" type="checkbox" id="hotelCheckbox"
                                                    name="hotel" value="Ya"
                                                    <?= $n->hotel == 'Ya' ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="hotelCheckbox">
                                                    Hotel
                                                </label>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input type="hidden" name="taksi" value="Tidak">
                                                <input class="form-check-input" type="checkbox" id="taksiCheckbox"
                                                    name="taksi" value="Ya"
                                                    <?= $n->taksi == 'Ya' ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="taksiCheckbox">
                                                    Taxi Voucher
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-3">
                                        <div class="col-md-12">
                                            <ul class="nav nav-tabs nav-pills mb-2" id="pills-tab" role="tablist">
                                                <li class="nav-item" role="presentation" id="nav-cashAdvanced"
                                                    style="display: <?= $n->ca == 'Ya' ? 'block' : 'none' ?>;">
                                                    <button class="nav-link" id="pills-cashAdvanced-tab"
                                                        data-bs-toggle="pill" data-bs-target="#pills-cashAdvanced"
                                                        type="button" role="tab" aria-controls="pills-cashAdvanced"
                                                        aria-selected="false">Cash Advanced</button>
                                                </li>
                                                <li class="nav-item" role="presentation" id="nav-ticket"
                                                    style="display: <?= $n->tiket == 'Ya' ? 'block' : 'none' ?>;">
                                                    <button class="nav-link" id="pills-ticket-tab" data-bs-toggle="pill"
                                                        data-bs-target="#pills-ticket" type="button" role="tab"
                                                        aria-controls="pills-ticket" aria-selected="false">Ticket</button>
                                                </li>
                                                <li class="nav-item" role="presentation" id="nav-hotel"
                                                    style="display: <?= $n->hotel == 'Ya' ? 'block' : 'none' ?>;">
                                                    <button class="nav-link" id="pills-hotel-tab" data-bs-toggle="pill"
                                                        data-bs-target="#pills-hotel" type="button" role="tab"
                                                        aria-controls="pills-hotel" aria-selected="false">Hotel</button>
                                                </li>
                                                <li class="nav-item" role="presentation" id="nav-taksi"
                                                    style="display: <?= $n->taksi == 'Ya' ? 'block' : 'none' ?>;">
                                                    <button class="nav-link" id="pills-taksi-tab" data-bs-toggle="pill"
                                                        data-bs-target="#pills-taksi" type="button" role="tab"
                                                        aria-controls="pills-taksi" aria-selected="false">Taxi</button>
                                                </li>
                                            </ul>
                                            @php
                                                $detailCA =
                                                    isset($ca) && $ca->detail_ca
                                                        ? json_decode($ca->detail_ca, true)
                                                        : [];
                                            @endphp
                                            <script>
                                                // Pass the PHP array into a JavaScript variable
                                                const initialDetailCA = @json($detailCA);
                                            </script>
                                            <div class="tab-content" id="pills-tabContent">
                                                <div class="tab-pane fade" id="pills-cashAdvanced" role="tabpanel"
                                                    aria-labelledby="pills-cashAdvanced-tab">
                                                    {{-- Cash Advanced content --}}
                                                    @include('hcis.reimbursements.businessTrip.editForm.btCaEdit')
                                                </div>
                                                <div class="tab-pane fade" id="pills-ticket" role="tabpanel"
                                                    aria-labelledby="pills-ticket-tab">
                                                    {{-- Ticket content --}}
                                                    @include('hcis.reimbursements.businessTrip.editForm.editTicket')
                                                </div>
                                                <div class="tab-pane fade" id="pills-hotel" role="tabpanel"
                                                    aria-labelledby="pills-hotel-tab">
                                                    {{-- Hotel content --}}
                                                    @include('hcis.reimbursements.businessTrip.editForm.editHotel')
                                                </div>
                                                <div class="tab-pane fade" id="pills-taksi" role="tabpanel"
                                                    aria-labelledby="pills-taksi-tab">
                                                    {{-- Taxi content --}}
                                                    @include('hcis.reimbursements.businessTrip.editForm.editTaxi')
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <input type="hidden" name="status" value="Pending L1" id="status">

                                <div class="d-flex justify-content-end mt-3">
                                    <button type="submit" class="btn btn-outline-primary rounded-pill me-2"
                                        name="action_draft" id="save-draft" value="Draft">Save as Draft</button>
                                    <button type="submit" class="btn btn-primary rounded-pill" name="action_submit"
                                        value="Pending L1">Submit</button>
                                </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript Part -->
    <script src="{{ asset('/js/editBusinessTrip.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

    <script>
        //CA JS
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
            function toggleCard(buttonId, cardId) {
                var $button = $(buttonId);
                var $card = $(cardId);
                var isVisible = $card.is(':visible');

                $card.slideToggle('fast', function() {
                    if (isVisible) {
                        // Clear form inputs
                        $card.find('input[type="text"], input[type="date"], textarea').val('');
                        $card.find('select').prop('selectedIndex', 0);
                        $card.find('input[readonly]').val(0);
                        $card.find('input[type="number"]').val(0);

                        // Set button text and icon
                        var buttonText = $button.data('text') || $button.text();
                        $button.html('<i class="bi bi-plus-circle"></i> ' + buttonText);
                        $button.data('state', 'false');
                    } else {
                        // Set button text and icon
                        var buttonText = $button.data('text') || $button.text();
                        $button.html('<i class="bi bi-dash-circle"></i> ' + buttonText);
                        $button.data('state', 'true');
                    }
                });
            }

            // Store the original button text
            $('#toggle-bt-perdiem, #toggle-bt-transport, #toggle-bt-penginapan, #toggle-bt-lainnya, #toggle-e-detail, #toggle-e-relation')
                .each(function() {
                    $(this).data('text', $(this).text().trim());
                });

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
            var caType = $('input[name="ca"]').val();
            var caType = $('#ca').val(); // Get the value of the <select> element

            // Check if the value of 'ca' is 'Ya'
            if (caType === 'Ya') {
                $('#toggle-bt-perdiem').click();
                $('#toggle-bt-transport').click();
                $('#toggle-bt-penginapan').click();
                $('#toggle-bt-lainnya').click();
            }

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

            function formatNumberPerdiem(num) {
                return num.toLocaleString('id-ID');
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
                calculateTotalNominalBTTotal();
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
                const totalDaysInput = formGroup.querySelector('input[name="total_days_bt_perdiem[]"]');
                const perdiemInput = document.getElementById('perdiem');
                const allowanceInput = formGroup.querySelector('input[name="nominal_bt_perdiem[]"]');
                const locationSelect = formGroup.querySelector('select[name="location_bt_perdiem[]"]');
                const otherLocationInput = formGroup.querySelector('input[name="other_location_bt_perdiem[]"]');

                const startDate = new Date(startDateInput.value);
                const endDate = new Date(endDateInput.value);

                if (!isNaN(startDate) && !isNaN(endDate) && startDate <= endDate) {
                    const diffTime = Math.abs(endDate - startDate);
                    const totalDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
                    totalDaysInput.value = totalDays;

                    const perdiem = parseFloat(perdiemInput.value) || 0;
                    let allowance = totalDays * perdiem;

                    // Memeriksa lokasi untuk menentukan persentase allowance
                    if (locationSelect.value === "Others" || otherLocationInput.value.trim() !== '') {
                        allowance *= 1; // allowance * 100%
                    } else {
                        allowance *= 0.5; // allowance * 50%
                    }

                    allowanceInput.value = formatNumberPerdiem(allowance);
                    calculateTotalNominalBTPerdiem();
                } else {
                    totalDaysInput.value = 0;
                    allowanceInput.value = 0;
                }

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
                        <input class="form-control bg-light" name="nominal_bt_perdiem[]" type="text" min="0" value="0" readonly>
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
                        <label class="form-label">Transport Date</label>
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
                        <label class="form-label">Information</label>
                        <textarea name="keterangan_bt_transport[]" class="form-control" placeholder="Write your information here ..."></textarea>
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
                        <label class="form-label">Accommodation Start</label>
                        <input type="date" name="start_bt_penginapan[]" class="form-control start-penginapan" placeholder="mm/dd/yyyy">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Accommodation End</label>
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
                        <label class="form-label">Date</label>
                        <input type="date" name="tanggal_bt_lainnya[]" class="form-control" placeholder="mm/dd/yyyy">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Information</label>
                        <textarea name="keterangan_bt_lainnya[]" class="form-control" placeholder="Write your information here ..."></textarea>
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

            function formatNumberPerdiem(num) {
                return num.toLocaleString('id-ID');
            }

            function parseNumber(value) {
                return parseFloat(value.replace(/\./g, '')) || 0;
            }

            function parseNumberPerdiem(value) {
                return parseFloat(value.replace(/\./g, '').replace(/,/g, '')) || 0;
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
                        <label class="form-label">Information</label>
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

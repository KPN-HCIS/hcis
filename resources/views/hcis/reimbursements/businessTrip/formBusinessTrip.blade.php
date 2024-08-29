@extends('layouts_.vertical', ['page_title' => 'Business Trip'])

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-datepicker@1.9.0/dist/css/bootstrap-datepicker.min.css"
        rel="stylesheet">
@endsection

@section('content')
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
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

                        <form action="/businessTrip/form/post" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label for="nama" class="form-label">Name</label>
                                <input type="text" class="form-control bg-light" id="nama" name="nama"
                                    style="cursor:not-allowed;" value="{{ $employee_data->fullname }}" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="divisi" class="form-label">Divison</label>
                                <input type="text" class="form-control bg-light" id="divisi" name="divisi"
                                    style="cursor:not-allowed;" value="{{ $employee_data->unit }}" readonly>

                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="mulai" class="form-label">Start Date</label>
                                    <input type="date" class="form-control datepicker" id="mulai" name="mulai"
                                        placeholder="Tanggal Mulai">
                                </div>
                                <div class="col-md-6">
                                    <label for="kembali" class="form-label">End Date</label>
                                    <input type="date" class="form-control datepicker" id="kembali" name="kembali"
                                        placeholder="Tanggal Kembali">
                                </div>
                            </div>
                            <div class="mb-2">
                                <label for="tujuan" class="form-label">Destination</label>
                                <select class="form-select" name="tujuan" id="tujuan" onchange="toggleOthers()"
                                    required>
                                    <option value="">--- Choose Destination ---</option>
                                    @foreach ($locations as $location)
                                        <option value="{{ $location->company_name }}">
                                            {{ $location->area . ' (' . $location->city . ')' }}
                                        </option>
                                    @endforeach
                                    <option value="Others">Others</option>
                                </select>
                                <br><input type="text" name="others_location" id="others_location" class="form-control"
                                    placeholder="Other Location" value="" style="display: none;">
                            </div>

                            <div class="mb-3">
                                <label for="keperluan" class="form-label">Need (To be filled in according to visit
                                    service)</label>
                                <textarea class="form-control" id="keperluan" name="keperluan" rows="3" placeholder="Fill your need" required></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="bb_perusahaan" class="form-label">Company Cost Expenses (PT Service Needs / Not
                                    PT Payroll)</label>
                                <select class="form-select" id="bb_perusahaan" name="bb_perusahaan" required>
                                    <option value="">--- Choose PT ---</option>
                                    @foreach ($companies as $company)
                                        <option value="{{ $company->contribution_level_code }}">
                                            {{ $company->contribution_level . ' (' . $company->contribution_level_code . ')' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="norek_krywn" class="form-label">Employee Account Number</label>
                                <input type="number" class="form-control bg-light" id="norek_krywn" name="norek_krywn"
                                    value="{{ $employee_data->bank_account_number }}" readonly>
                            </div>

                            <div class="mb-3">
                                <label for="nama_pemilik_rek" class="form-label">Name of Account Owner</label>
                                <input type="text" class="form-control bg-light" id="nama_pemilik_rek"
                                    name="nama_pemilik_rek" value="{{ $employee_data->bank_name }}" readonly>
                            </div>

                            <div class="mb-3">
                                <label for="nama_bank" class="form-label">Bank Name</label>
                                <input type="text" class="form-control" id="nama_bank" name="nama_bank"
                                    placeholder="ex. BCA" required>
                            </div>

                            <!-- HTML Part -->
                            <div class="col-md-14 mb-3">
                                <label for="jns_dinas" class="form-label">Type of Service</label>
                                <select class="form-select" id="jns_dinas" name="jns_dinas" required>
                                    <option value="" selected disabled>-- Choose Type of Service --</option>
                                    <option value="dalam kota">Dinas Dalam Kota</option>
                                    <option value="luar kota">Dinas Luar Kota</option>
                                </select>
                            </div>

                            <div id="additional-fields" class="row mb-3" style="display: none;">
                                <div class="col-md-12">
                                    <label for="ca" class="form-label">Cash Advanced</label>
                                    <select class="form-select" id="ca" name="ca">
                                        <option value="Tidak">Tidak</option>
                                        <option value="Ya">Ya</option>
                                    </select>

                                    <div class="row mt-2" id="ca_div" style="display: none;">
                                        <div class="col-md-12">
                                            <div class="table-responsive-sm">
                                                <div class="d-flex flex-column gap-2">
                                                    <div class="text-bg-primary p-2"
                                                        style="text-align:center; border-radius:4px;">Cash Advanced</div>
                                                    <div class="card">
                                                        <div class="card-body">
                                                            <div class="mb-2" id="div_allowance">
                                                                <label class="form-label">Allowance (Perdiem)</label>
                                                                <div class="input-group">
                                                                    <div class="input-group-append">
                                                                        <span class="input-group-text">Rp</span>
                                                                    </div>
                                                                    <input class="form-control bg-light" name="allowance"
                                                                        id="allowance" type="text" min="0"
                                                                        value="0" readonly>
                                                                </div>
                                                            </div>
                                                            <div class="mb-2">
                                                                <label class="form-label">Transportation</label>
                                                                <div class="input-group">
                                                                    <div class="input-group-append">
                                                                        <span class="input-group-text">Rp</span>
                                                                    </div>
                                                                    <input class="form-control" name="transport"
                                                                        id="transport" type="text" min="0"
                                                                        value="0">
                                                                </div>
                                                            </div>
                                                            <div class="mb-2">
                                                                <label class="form-label">Accommodation</label>
                                                                <div class="input-group">
                                                                    <div class="input-group-append">
                                                                        <span class="input-group-text">Rp</span>
                                                                    </div>
                                                                    <input class="form-control" name="accommodation"
                                                                        id="accommodation" type="text" min="0"
                                                                        value="0">
                                                                </div>
                                                            </div>
                                                            <div class="mb-2">
                                                                <label class="form-label">Other</label>
                                                                <div class="input-group">
                                                                    <div class="input-group-append">
                                                                        <span class="input-group-text">Rp</span>
                                                                    </div>
                                                                    <input class="form-control" name="other"
                                                                        id="other" type="text" min="0"
                                                                        value="0">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12 mt-3">
                                    <label for="tiket" class="form-label">Ticket</label>
                                    <select class="form-select" id="tiket" name="tiket">
                                        <option value="Tidak">Tidak</option>
                                        <option value="Ya">Ya</option>
                                    </select>
                                    <div class="row mt-2" id="tiket_div" style="display: none;">
                                        <div class="col-md-12">
                                            <div class="table-responsive-sm">
                                                <div class="d-flex flex-column gap-2" id="ticket_forms_container">
                                                    <?php for ($i = 1; $i <= 5; $i++) : ?>
                                                    <div class="ticket-form" id="ticket-form-<?php echo $i; ?>"
                                                        style="display: <?php echo $i === 1 ? 'block' : 'none'; ?>;">
                                                        <div class="text-bg-primary p-2"
                                                            style="text-align:center; border-radius:4px;">Ticket
                                                            <?php echo $i; ?></div>
                                                        <div class="card">
                                                            <div class="card-body">
                                                                <div class="mb-2">
                                                                    <label class="form-label">NIK</label>
                                                                    <div class="input-group">
                                                                        <input class="form-control" name="noktp_tkt[]"
                                                                            type="number">
                                                                    </div>
                                                                </div>
                                                                <div class="mb-2">
                                                                    <label class="form-label">From</label>
                                                                    <div class="input-group">
                                                                        <input class="form-control bg-white"
                                                                            name="dari_tkt[]" type="text"
                                                                            placeholder="ex. Yogyakarta (YIA)">
                                                                    </div>
                                                                </div>
                                                                <div class="mb-2">
                                                                    <label class="form-label">To</label>
                                                                    <div class="input-group">
                                                                        <input class="form-control bg-white"
                                                                            name="ke_tkt[]" type="text"
                                                                            placeholder="ex. Jakarta (CGK)">
                                                                    </div>
                                                                </div>
                                                                <div class="mb-2">
                                                                    <label class="form-label">Date</label>
                                                                    <div class="input-group">
                                                                        <input class="form-control bg-white"
                                                                            id="tgl_brkt_tkt_<?php echo $i; ?>"
                                                                            name="tgl_brkt_tkt[]" type="date"
                                                                            onchange="validateDates(<?php echo $i; ?>)">
                                                                    </div>
                                                                </div>
                                                                <div class="mb-2">
                                                                    <label class="form-label">Time</label>
                                                                    <div class="input-group">
                                                                        <input class="form-control bg-white"
                                                                            id="jam_brkt_tkt_<?php echo $i; ?>"
                                                                            name="jam_brkt_tkt[]" type="time" onchange="validateDates(<?php echo $i; ?>)">
                                                                    </div>
                                                                </div>
                                                                <div class="mb-2">
                                                                    <label for="type_tkt_<?php echo $i; ?>"
                                                                        class="form-label">Ticket Type</label>
                                                                    <select class="form-select" name="type_tkt[]"
                                                                        required>
                                                                        <option value="One Way">One Way</option>
                                                                        <option value="Round Trip">Round Trip</option>
                                                                    </select>
                                                                </div>
                                                                <div class="round-trip-options" style="display: none;">
                                                                    <div class="mb-2">
                                                                        <label class="form-label">Return Date</label>
                                                                        <div class="input-group">
                                                                            <input class="form-control bg-white"
                                                                                name="tgl_plg_tkt[]" type="date"
                                                                                id="tgl_plg_tkt_<?php echo $i; ?>"
                                                                                onchange="validateDates(<?php echo $i; ?>)">
                                                                        </div>
                                                                    </div>
                                                                    <div class="mb-2">
                                                                        <label class="form-label">Return Time</label>
                                                                        <div class="input-group">
                                                                            <input class="form-control bg-white"
                                                                                id="jam_plg_tkt_<?php echo $i; ?>"
                                                                                name="jam_plg_tkt[]" type="time" onchange="validateDates(<?php echo $i; ?>)">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="mb-2">
                                                                    <label class="form-label">Transportation Type</label>
                                                                    <div class="input-group">
                                                                        <input class="form-control bg-white"
                                                                            name="jenis_tkt[]" type="text"
                                                                            placeholder="ex. Train/Bus/Airplane etc.">
                                                                    </div>
                                                                </div>
                                                                <?php if ($i < 5) : ?>
                                                                <div class="mt-3">
                                                                    <label class="form-label">Add more ticket</label>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="radio"
                                                                            id="more_tkt_no_<?php echo $i; ?>"
                                                                            name="more_tkt_<?php echo $i; ?>"
                                                                            value="Tidak" checked>
                                                                        <label class="form-check-label"
                                                                            for="more_tkt_no_<?php echo $i; ?>">Tidak</label>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="radio"
                                                                            id="more_tkt_yes_<?php echo $i; ?>"
                                                                            name="more_tkt_<?php echo $i; ?>"
                                                                            value="Ya">
                                                                        <label class="form-check-label"
                                                                            for="more_tkt_yes_<?php echo $i; ?>">Ya</label>
                                                                    </div>
                                                                </div>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php endfor; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12 mt-3">
                                    <label for="hotel" class="form-label">Hotel</label>
                                    <select class="form-select" id="hotel" name="hotel">
                                        <option value="Tidak">Tidak</option>
                                        <option value="Ya">Ya</option>
                                    </select>
                                    <div class="row mt-2" id="hotel_div" style="display: none;">
                                        <div class="col-md-12">
                                            <div class="table-responsive-sm">
                                                <div class="d-flex flex-column gap-2" id="hotel_forms_container">
                                                    <?php for ($i = 1; $i <= 5; $i++) : ?>
                                                    <div class="hotel-form" id="hotel-form-<?php echo $i; ?>"
                                                        style="display: <?php echo $i === 1 ? 'block' : 'none'; ?>;">
                                                        <div class="text-bg-primary p-2"
                                                            style="text-align:center; border-radius:4px;">
                                                            Hotel <?php echo $i; ?>
                                                        </div>
                                                        <div class="card">
                                                            <div class="card-body">
                                                                <div class="mb-2">
                                                                    <label class="form-label">Hotel Name</label>
                                                                    <div class="input-group">
                                                                        <input class="form-control bg-white"
                                                                            name="nama_htl[]" type="text">
                                                                    </div>
                                                                </div>
                                                                <div class="mb-2">
                                                                    <label class="form-label">Hotel Location</label>
                                                                    <div class="input-group">
                                                                        <input class="form-control bg-white"
                                                                            name="lokasi_htl[]" type="text"
                                                                            placeholder="ex: Jakarta">
                                                                    </div>
                                                                </div>
                                                                <div class="mb-2">
                                                                    <label class="form-label">Total Room</label>
                                                                    <div class="input-group">
                                                                        <input class="form-control bg-white"
                                                                            name="jmlkmr_htl[]" type="number"
                                                                            min="1" placeholder="ex: 1">
                                                                    </div>
                                                                </div>
                                                                <div class="mb-2">
                                                                    <label class="form-label">Bed Size</label>
                                                                    <select class="form-select" name="bed_htl[]" required>
                                                                        <option value="King Size">King Size</option>
                                                                        <option value="Queen Size">Queen Size</option>
                                                                        <option value="Full">Full</option>
                                                                        <option value="Twin XL">Twin XL</option>
                                                                        <option value="Twin">Twin</option>
                                                                    </select>
                                                                </div>
                                                                <div class="mb-2">
                                                                    <label class="form-label">Check In Date</label>
                                                                    <input type="date" class="form-control datepicker"
                                                                        name="tgl_masuk_htl[]"
                                                                        onchange="calculateTotalDays(<?php echo $i; ?>)">
                                                                </div>
                                                                <div class="mb-2">
                                                                    <label class="form-label">Check Out Date</label>
                                                                    <input type="date" class="form-control datepicker"
                                                                        name="tgl_keluar_htl[]"
                                                                        onchange="calculateTotalDays(<?php echo $i; ?>)">
                                                                </div>
                                                                <div class="mb-2">
                                                                    <label class="form-label">Total Days</label>
                                                                    <input type="number"
                                                                        class="form-control datepicker bg-light"
                                                                        name="total_hari[]" readonly>
                                                                </div>
                                                                <?php if ($i < 5) : ?>
                                                                <div class="mt-3">
                                                                    <label class="form-label">Add more hotel</label>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="radio"
                                                                            id="more_htl_no_<?php echo $i; ?>"
                                                                            name="more_htl_<?php echo $i; ?>"
                                                                            value="Tidak" checked>
                                                                        <label class="form-check-label"
                                                                            for="more_htl_no_<?php echo $i; ?>">Tidak</label>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="radio"
                                                                            id="more_htl_yes_<?php echo $i; ?>"
                                                                            name="more_htl_<?php echo $i; ?>"
                                                                            value="Ya">
                                                                        <label class="form-check-label"
                                                                            for="more_htl_yes_<?php echo $i; ?>">Ya</label>
                                                                    </div>
                                                                </div>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php endfor; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12 mt-3">
                                    <label for="taksi" class="form-label">Voucher Taksi</label>
                                    <select class="form-select" id="taksi" name="taksi">
                                        <option value="Tidak">Tidak</option>
                                        <option value="Ya">Ya</option>
                                    </select>
                                    <div class="row mt-2" id="taksi_div" style="display: none;">
                                        <div class="col-md-12">
                                            <div class="table-responsive-sm">
                                                <div class="d-flex flex-column gap-2">
                                                    <div class="text-bg-primary p-2 r-3"
                                                        style="text-align:center; border-radius:4px;">Voucher
                                                        Taksi</div>
                                                    <div class="card">
                                                        <div class="card-body">
                                                            <div class="mb-2" id="taksi_div">
                                                                <label class="form-label">No. Voucher Taxi</label>
                                                                <div class="input-group">
                                                                    <div class="input-group-append">
                                                                    </div>
                                                                    <input class="form-control bg-white" name="no_vt"
                                                                        id="no_vt" type="text" min="0"
                                                                        placeholder="0">
                                                                </div>
                                                            </div>
                                                            <div class="mb-2">
                                                                <label class="form-label">Voucher Nominal</label>
                                                                <div class="input-group">
                                                                    <div class="input-group-append">
                                                                        <span class="input-group-text">Rp</span>
                                                                    </div>
                                                                    <input class="form-control" name="nominal_vt"
                                                                        id="nominal_vt" type="text" min="0"
                                                                        placeholder="ex. 12000">
                                                                </div>
                                                            </div>
                                                            <div class="mb-2">
                                                                <label class="form-label">Voucher Keeper</label>
                                                                <div class="input-group">
                                                                    <div class="input-group-append">
                                                                        <span class="input-group-text">Rp</span>
                                                                    </div>
                                                                    <input class="form-control" name="keeper_vt"
                                                                        id="keeper_vt" type="text" min="0"
                                                                        placeholder="ex. 12000">
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
                            <input type="hidden" name="status" value="Pending L1">

                            <div class="d-flex justify-content-end mt-3">
                                <button class="btn btn-outline-primary me-2">Save as Draft</button>
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript Part -->
    <script>
        function calculateTotalDays() {
            const checkInDate = new Date(document.getElementById('tgl_masuk_htl').value);
            const checkOutDate = new Date(document.getElementById('tgl_keluar_htl').value);

            if (checkInDate && checkOutDate) {
                // Calculate the difference in milliseconds
                const diffTime = Math.abs(checkOutDate - checkInDate);
                // Convert to days and add 1 to include both check-in and check-out days
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;

                // Set the result in the total_hari input
                document.getElementById('total_hari').value = diffDays;
            } else {
                // If either date is not set, clear the total
                document.getElementById('total_hari').value = '';
            }
        }
        document.addEventListener('DOMContentLoaded', function() {
            var jnsDinasSelect = document.getElementById('jns_dinas');
            var additionalFields = document.getElementById('additional-fields');

            jnsDinasSelect.addEventListener('change', function() {
                if (this.value === 'luar kota') {
                    additionalFields.style.display = 'block';
                } else {
                    additionalFields.style.display = 'none';
                    // Reset all fields to "Tidak"
                    document.getElementById('ca').value = 'Tidak';
                    document.getElementById('tiket').value = 'Tidak';
                    document.getElementById('hotel').value = 'Tidak';
                    document.getElementById('taksi').value = 'Tidak';
                }
            });
        });
        document.addEventListener('DOMContentLoaded', function() {
            const caSelect = document.getElementById('ca');
            const caNbtDiv = document.getElementById('ca_div');

            const hotelSelect = document.getElementById('hotel');
            const hotelDiv = document.getElementById('hotel_div');

            const taksiSelect = document.getElementById('taksi');
            const taksiDiv = document.getElementById('taksi_div');

            const tiketSelect = document.getElementById('tiket');
            const tiketDiv = document.getElementById('tiket_div');


            function toggleDisplay(selectElement, targetDiv) {
                if (selectElement.value === 'Ya') {
                    targetDiv.style.display = 'block';
                } else {
                    targetDiv.style.display = 'none';
                }
            }

            caSelect.addEventListener('change', function() {
                toggleDisplay(caSelect, caNbtDiv);
            });

            hotelSelect.addEventListener('change', function() {
                toggleDisplay(hotelSelect, hotelDiv);
            });

            taksiSelect.addEventListener('change', function() {
                toggleDisplay(taksiSelect, taksiDiv);
            });

            tiketSelect.addEventListener('change', function() {
                toggleDisplay(tiketSelect, tiketDiv);
            });


        });
        document.addEventListener('DOMContentLoaded', function() {
            const ticketSelect = document.getElementById('tiket');
            const ticketDiv = document.getElementById('tiket_div');

            // Hide/show ticket form based on select option
            ticketSelect.addEventListener('change', function() {
                if (this.value === 'Ya') {
                    ticketDiv.style.display = 'block';
                } else {
                    ticketDiv.style.display = 'none';
                }
            });

            // Rest of your existing code for handling multiple ticket forms
            for (let i = 1; i <= 4; i++) {
                const yesRadio = document.getElementById(`more_tkt_yes_${i}`);
                const noRadio = document.getElementById(`more_tkt_no_${i}`);
                const nextForm = document.getElementById(`ticket-form-${i + 1}`);

                yesRadio.addEventListener('change', function() {
                    if (this.checked) {
                        nextForm.style.display = 'block';
                    }
                });

                noRadio.addEventListener('change', function() {
                    if (this.checked) {
                        nextForm.style.display = 'none';
                        // Hide all subsequent forms
                        for (let j = i + 1; j <= 5; j++) {
                            document.getElementById(`ticket-form-${j}`).style.display = 'none';
                        }
                        // Reset radio buttons for subsequent forms
                        for (let j = i + 1; j <= 4; j++) {
                            document.getElementById(`more_tkt_no_${j}`).checked = true;
                        }
                    }
                });
            }

            // Handle Round Trip options
            const ticketTypes = document.querySelectorAll('select[name="type_tkt[]"]');
            ticketTypes.forEach((select, index) => {
                select.addEventListener('change', function() {
                    const roundTripOptions = this.closest('.card-body').querySelector(
                        '.round-trip-options');
                    if (this.value === 'Round Trip') {
                        roundTripOptions.style.display = 'block';
                    } else {
                        roundTripOptions.style.display = 'none';
                    }
                });
            });
        });
        document.addEventListener('DOMContentLoaded', function() {
            // Existing ticket code...

            // Hotel form handling
            for (let i = 1; i <= 4; i++) {
                const yesRadio = document.getElementById(`more_htl_yes_${i}`);
                const noRadio = document.getElementById(`more_htl_no_${i}`);
                const nextForm = document.getElementById(`hotel-form-${i + 1}`);

                yesRadio.addEventListener('change', function() {
                    if (this.checked) {
                        nextForm.style.display = 'block';
                    }
                });

                noRadio.addEventListener('change', function() {
                    if (this.checked) {
                        nextForm.style.display = 'none';
                        // Hide all subsequent forms
                        for (let j = i + 1; j <= 5; j++) {
                            document.getElementById(`hotel-form-${j}`).style.display = 'none';
                        }
                        // Reset radio buttons for subsequent forms
                        for (let j = i + 1; j <= 4; j++) {
                            document.getElementById(`more_htl_no_${j}`).checked = true;
                        }
                    }
                });
            }

            // Calculate total days for each hotel form
            function calculateTotalDays(index) {
                const checkIn = document.querySelector(`#hotel-form-${index} input[name="tgl_masuk_htl[]"]`);
                const checkOut = document.querySelector(`#hotel-form-${index} input[name="tgl_keluar_htl[]"]`);
                const totalDays = document.querySelector(`#hotel-form-${index} input[name="total_hari[]"]`);

                if (checkIn && checkOut && totalDays) {
                    const start = new Date(checkIn.value);
                    const end = new Date(checkOut.value);

                    if (checkIn.value && checkOut.value) {
                        // Calculate difference in milliseconds and convert to days, excluding the same day
                        const difference = Math.ceil((end - start) / (1000 * 60 * 60 * 24));
                        if (difference < 0) {
                            alert("Check out date cannot be earlier than check in date.");
                            checkOut.value = ''; // Clear the check-out date if invalid
                            totalDays.value = ''; // Clear the total days if check-out date is reset
                        } else {
                            totalDays.value = difference >= 0 ? difference : 0;
                        }
                    } else {
                        totalDays.value = ''; // Clear total days if dates are not set
                    }
                } else {
                    console.error("Elements not found. Check selectors.");
                }
            }

            // Add event listeners for date inputs
            for (let i = 1; i <= 5; i++) {
                const checkIn = document.querySelector(`#hotel-form-${i} input[name="tgl_masuk_htl[]"]`);
                const checkOut = document.querySelector(`#hotel-form-${i} input[name="tgl_keluar_htl[]"]`);

                if (checkIn && checkOut) {
                    checkIn.addEventListener('change', () => calculateTotalDays(i));
                    checkOut.addEventListener('change', () => calculateTotalDays(i));
                }
            }
        });

        document.getElementById('kembali').addEventListener('change', function() {
            var mulaiDate = document.getElementById('mulai').value;
            var kembaliDate = this.value;

            if (kembaliDate < mulaiDate) {
                alert('Return date cannot be earlier than Start date.');
                this.value = ''; // Reset the kembali field
            }
        });
        document.getElementById('tgl_keluar_htl').addEventListener('change', function() {
            var masukHtl = document.getElementById('tgl_masuk_htl').value;
            var keluarDate = this.value;

            if (masukHtl && keluarDate) {
                var checkInDate = new Date(masukHtl);
                var checkOutDate = new Date(keluarDate);

                if (checkOutDate < checkInDate) {
                    alert("Check out date cannot be earlier than check in date.");
                    this.value = ''; // Reset the check out date field
                }
            }
        });

        document.getElementById('type_tkt').addEventListener('change', function() {
            var roundTripOptions = document.getElementById('roundTripOptions');
            if (this.value === 'Round Trip') {
                roundTripOptions.style.display = 'block';
            } else {
                roundTripOptions.style.display = 'none';
            }
        });


        function toggleOthers() {
            // ca_type ca_nbt ca_e
            var locationFilter = document.getElementById("tujuan");
            var others_location = document.getElementById("others_location");

            if (locationFilter.value === "Others") {
                others_location.style.display = "block";
            } else {
                others_location.style.display = "none";
                others_location.value = "";
            }
        }

        function validateDates(index) {
            // Get the departure and return date inputs for the given form index
            const departureDate = document.querySelector(`#tgl_brkt_tkt_${index}`);
            const returnDate = document.querySelector(`#tgl_plg_tkt_${index}`);

            // Get the departure and return time inputs for the given form index
            const departureTime = document.querySelector(`#jam_brkt_tkt_${index}`);
            const returnTime = document.querySelector(`#jam_plg_tkt_${index}`);

            if (departureDate && returnDate) {
                const depDate = new Date(departureDate.value);
                const retDate = new Date(returnDate.value);

                // Check if both dates are valid
                if (depDate && retDate) {
                    // Validate if return date is earlier than departure date
                    if (retDate < depDate) {
                        alert("Return date cannot be earlier than departure date.");
                        returnDate.value = ''; // Reset the return date field
                    } else if (retDate.getTime() === depDate.getTime() && departureTime && returnTime) {
                        // If dates are the same, validate time
                        const depTime = departureTime.value;
                        const retTime = returnTime.value;

                        // Check if both times are set and validate
                        if (depTime && retTime) {
                            const depDateTime = new Date(`1970-01-01T${depTime}:00`);
                            const retDateTime = new Date(`1970-01-01T${retTime}:00`);

                            if (retDateTime < depDateTime) {
                                alert("Return time cannot be earlier than departure time on the same day.");
                                returnTime.value = ''; // Reset the return time field
                            }
                        }
                    }
                }
            }
        }




        document.getElementById('nik').addEventListener('change', function() {
            var nik = this.value;

            fetch('/get-employee-data?nik=' + nik)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('jk_tkt').value = data.jk_tkt;
                        document.getElementById('tlp_tkt').value = data.tlp_tkt;
                    } else {
                        alert('Employee data not found!');
                    }
                })
                .catch(error => console.error('Error:', error));
        });
    </script>
@endsection

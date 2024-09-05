@extends('layouts_.vertical', ['page_title' => 'Hotel'])

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
                            <li class="breadcrumb-item"><a href="{{ route('hotel') }}">{{ $parentLink }}</a></li>
                            <li class="breadcrumb-item active">{{ $link }}</li>
                        </ol>
                    </div>
                    <h4 class="page-title">{{ $link }}</h4>
                </div>
            </div>
        </div>
        <div class="d-sm-flex align-items-center justify-content-center">
            <div class="card col-md-8">
                <div class="card-header d-flex bg-primary text-white justify-content-between">
                    <h4 class="modal-title" id="viewFormEmployeeLabel">Add Data</h4>
                    <a href="{{ route('hotel') }}" type="button" class="btn btn-close btn-close-white"></a>
                </div>
                <div class="card-body" @style('overflow-y: auto;')>
                    <div class="container-fluid">
                        <form id="scheduleForm" method="post" action="{{ route('hotel.submit') }}">@csrf
                            <div class="row my-2">
                                <div class="col-md-5">
                                    <div class="mb-2">
                                        <label class="form-label" for="start">Name</label>
                                        <input type="text" name="name" id="name"
                                            value="{{ $employee_data->fullname }}" class="form-control bg-light" readonly>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="mb-2">
                                        <label class="form-label" for="start">Unit</label>
                                        <input type="text" name="unit" id="unit"
                                            value="{{ $employee_data->unit }}" class="form-control bg-light" readonly>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="mb-2">
                                        <label class="form-label" for="start">Grade</label>
                                        <input type="text" name="grade" id="grade"
                                            value="{{ $employee_data->job_level }}" class="form-control bg-light" readonly>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="col-md-12">
                                <div class="mb-2">
                                    <label class="form-label" for="name">Business Trip Number</label>
                                    <select class="form-control select2" id="bisnis_numb" name="bisnis_numb">
                                        <option value="-">No Business Trip</option>
                                        @foreach ($no_sppds as $no_sppd)
                                            <option value="{{ $no_sppd->no_sppd }}">{{ $no_sppd->no_sppd }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Dynamic Hotel Forms Start -->
                            <div class="d-flex flex-column gap-2" id="hotel_forms_container">
                                <?php for ($i = 1; $i <= 5; $i++) : ?>
                                <div class="hotel-form" id="hotel-form-<?php echo $i; ?>"
                                    style="display: <?php echo $i === 1 ? 'block' : 'none'; ?>;">
                                    <div class="text-bg-primary p-2 mb-1" style="text-align:center; border-radius:4px;">
                                        Hotel Data <?php echo $i; ?></div>
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="row my-2">
                                                <div class="col-md-6">
                                                    <div class="mb-2">
                                                        <label class="form-label" for="nama_htl_<?php echo $i; ?>">Hotel
                                                            Name</label>
                                                        <input type="text" name="nama_htl[]"
                                                            id="nama_htl_<?php echo $i; ?>" class="form-control"
                                                            placeholder="ex: Westin">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-2">
                                                        <label class="form-label"
                                                            for="lokasi_htl_<?php echo $i; ?>">Location</label>
                                                        <input type="text" name="lokasi_htl[]"
                                                            id="lokasi_htl_<?php echo $i; ?>" class="form-control"
                                                            placeholder="ex: Jakarta">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-2">
                                                        <label class="form-label"
                                                            for="jmlkmr_htl_<?php echo $i; ?>">Rooms</label>
                                                        <input type="number" name="jmlkmr_htl[]"
                                                            id="jmlkmr_htl_<?php echo $i; ?>" class="form-control"
                                                            placeholder="ex: 1">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-2">
                                                        <label class="form-label" for="bed_htl_<?php echo $i; ?>">Bed
                                                            Type</label>
                                                        <select class="form-control" name="bed_htl[]" required>
                                                            <option value="" selected disabled>--- Select Bed Type
                                                                ---</option>
                                                            <option value="Single Bed">Single Bed</option>
                                                            <option value="Twin Bed">Twin Bed</option>
                                                            <option value="King Bed">King Bed</option>
                                                            <option value="Super King Bed">Super King Bed</option>
                                                            <option value="Extra Bed">Extra Bed</option>
                                                            <option value="Baby Cot">Baby Cot</option>
                                                            <option value="Sofa Bed">Sofa Bed</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row my-2">
                                                <div class="col-md-6">
                                                    <div class="mb-2">
                                                        <label class="form-label"
                                                            for="tgl_masuk_htl_<?php echo $i; ?>">Start Date</label>
                                                        <input type="date" name="tgl_masuk_htl[]"
                                                            id="tgl_masuk_htl_<?php echo $i; ?>" class="form-control"
                                                            placeholder="mm/dd/yyyy" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-2">
                                                        <label class="form-label"
                                                            for="tgl_keluar_htl_<?php echo $i; ?>">End Date</label>
                                                        <input type="date" name="tgl_keluar_htl[]"
                                                            id="tgl_keluar_htl_<?php echo $i; ?>" class="form-control"
                                                            placeholder="mm/dd/yyyy" required>
                                                    </div>
                                                </div>
                                            </div>

                                            <?php if ($i < 5) : ?>
                                            <div class="mt-3">
                                                <label class="form-label">Add more hotel data</label>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio"
                                                        id="more_htl_no_<?php echo $i; ?>"
                                                        name="more_htl_<?php echo $i; ?>" value="Tidak" checked>
                                                    <label class="form-check-label"
                                                        for="more_htl_no_<?php echo $i; ?>">Tidak</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio"
                                                        id="more_htl_yes_<?php echo $i; ?>"
                                                        name="more_htl_<?php echo $i; ?>" value="Ya"
                                                        onchange="toggleNextForm(<?php echo $i; ?>)">
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
                            <!-- Dynamic Hotel Forms End -->

                            <br>
                            <div class="row">
                                <input type="hidden" name="status" value="Pending L1" id="status">
                                <div class="col-md d-md-flex justify-content-end text-center">
                                    <input type="hidden" name="repeat_days_selected" id="repeatDaysSelected">
                                    <button type="button" class="btn btn-outline-primary rounded-pill me-2"
                                        name="action_ca_draft" id="save-draft">Save as Draft</button>
                                    <button type="submit"
                                        class="btn btn-primary rounded-pill shadow px-4">Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script>
        document.addEventListener('DOMContentLoaded', function() {

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
                            const form = document.getElementById(`hotel-form-${j}`);
                            if (form) {
                                form.style.display = 'none';
                                // Reset the form when it is hidden
                                resetHotelFields(form);
                            }
                        }
                        // Reset radio buttons for subsequent forms
                        for (let j = i + 1; j <= 4; j++) {
                            const noRadioButton = document.getElementById(`more_htl_no_${j}`);
                            if (noRadioButton) {
                                noRadioButton.checked = true;
                            }
                        }
                    }
                });
            }

            // Function to reset hotel fields
            function resetHotelFields(container) {
                const inputs = container.querySelectorAll('input[type="text"], input[type="number"], textarea');
                inputs.forEach(input => {
                    input.value = '';
                });
                const selects = container.querySelectorAll('select');
                selects.forEach(select => {
                    select.selectedIndex = 0;
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

            // Handle date validation for the return date
            document.getElementById('kembali').addEventListener('change', function() {
                var mulaiDate = document.getElementById('mulai').value;
                var kembaliDate = this.value;

                if (kembaliDate < mulaiDate) {
                    alert('Return date cannot be earlier than Start date.');
                    this.value = ''; // Reset the kembali field
                }
            });
        });


        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('save-draft').addEventListener('click', function(event) {
                event.preventDefault();

                // Remove the existing status input
                const existingStatus = document.getElementById('status');
                if (existingStatus) {
                    existingStatus.remove();
                }

                // Create a new hidden input for "Draft"
                const draftInput = document.createElement('input');
                draftInput.type = 'hidden';
                draftInput.name = 'status';
                draftInput.value = 'Draft';
                draftInput.id = 'status';

                // Append the draft input to the form
                this.closest('form').appendChild(draftInput);

                // Submit the form
                this.closest('form').submit();
            });
        });

        document.getElementById('tgl_keluar_htl').addEventListener('change', function() {
            var startDate = document.getElementById('tgl_masuk_htl').value;
            var endDate = this.value;

            if (startDate && endDate && endDate < startDate) {
                alert('End Date cannot be earlier than Start Date.');
                this.value = '';
            }
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
                ca_nbt.style.display = "block";
                ca_e.style.display = "none";
                div_bisnis_numb.style.display = "block";
                div_allowance.style.display = "block";
            } else if (ca_type.value === "ndns") {
                ca_nbt.style.display = "block";
                ca_e.style.display = "none";
                div_bisnis_numb.style.display = "none";
                bisnis_numb.style.value = "";
                div_allowance.style.display = "none";
            } else if (ca_type.value === "entr") {
                ca_nbt.style.display = "none";
                ca_e.style.display = "block";
                div_bisnis_numb.style.display = "block";
            } else {
                ca_nbt.style.display = "none";
                ca_e.style.display = "none";
                div_bisnis_numb.style.display = "none";
                bisnis_numb.style.value = "";
            }
        }

        function validateInput(input) {
            //input.value = input.value.replace(/[^0-9,]/g, '');
            input.value = input.value.replace(/[^0-9]/g, '');
        }

        document.addEventListener('DOMContentLoaded', function() {
            const startDateInput = document.getElementById('tgl_masuk_htl');
            const endDateInput = document.getElementById('tgl_keluar_htl');
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
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-beta3/js/bootstrap.min.js"></script>
@endsection

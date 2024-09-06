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
                    <h4 class="modal-title" id="viewFormEmployeeLabel">Edit Data Hotel</h4>
                    <a href="{{ route('hotel') }}" class="btn btn-close btn-close-white"></a>
                </div>
                <div class="card-body" style="overflow-y: auto;">
                    <form id="btEditForm" method="POST" action="{{ route('hotel.update', encrypt($hotel->id)) }}">
                        @csrf
                        @method('PUT')
                        <!-- Employee Info -->
                        <div class="row my-2">
                            <div class="col-md-5">
                                <div class="mb-2">
                                    <label class="form-label" for="name">Name</label>
                                    <input type="text" name="name" id="name"
                                        value="{{ $employee_data->fullname }}" class="form-control bg-light" readonly>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="mb-2">
                                    <label class="form-label" for="unit">Unit</label>
                                    <input type="text" name="unit" id="unit" value="{{ $employee_data->unit }}"
                                        class="form-control bg-light" readonly>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="mb-2">
                                    <label class="form-label" for="grade">Grade</label>
                                    <input type="text" name="grade" id="grade"
                                        value="{{ $employee_data->job_level }}" class="form-control bg-light" readonly>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- Business Trip Info -->
                        <div class="mb-2">
                            <label class="form-label" for="bisnis_numb">Business Trip Number</label>
                            <select class="form-control select2" id="bisnis_numb" name="bisnis_numb">
                                <option value="-" {{ $hotel->no_sppd === '-' ? 'selected' : '' }}>No
                                    Business
                                    Trip</option>
                                @foreach ($no_sppds as $no_sppd)
                                    <option value="{{ $no_sppd->no_sppd }}"
                                        {{ $hotel->no_sppd == $no_sppd->no_sppd ? 'selected' : '' }}>
                                        {{ $no_sppd->no_sppd }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Dynamic Hotel Forms Start -->
                        <div class="d-flex flex-column gap-2" id="hotel_forms_container"
                            style="display: {{ count($hotelData) > 0 ? 'block' : 'none' }};">
                            @for ($i = 1; $i <= 5; $i++)
                                <div class="hotel-form" id="hotel-form-{{ $i }}"
                                    style="display: {{ isset($hotelData[$i - 1]) ? 'block' : 'none' }};">
                                    <div class="text-bg-primary p-2 mb-1" style="text-align:center; border-radius:4px;">
                                        Hotel {{ $i }}
                                    </div>
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="row my-2">
                                                <div class="col-md-12">
                                                    <div class="mb-2">
                                                        <label class="form-label" for="nama_htl_{{ $i }}">Hotel
                                                            Name</label>
                                                        <input type="text" name="nama_htl[]"
                                                            id="nama_htl_{{ $i }}" class="form-control"
                                                            placeholder="Hotel Name"
                                                            value="{{ isset($hotelData[$i - 1]) ? $hotelData[$i - 1]['nama_htl'] : '' }}">
                                                    </div>
                                                    <input type="hidden" name="hotel_ids[]"
                                                        value="{{ isset($hotelData[$i - 1]) ? $hotelData[$i - 1]['id'] : '' }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-2">
                                                        <label class="form-label"
                                                            for="lokasi_htl_{{ $i }}">Location</label>
                                                        <input type="text" name="lokasi_htl[]"
                                                            id="lokasi_htl_{{ $i }}" class="form-control"
                                                            placeholder="Location"
                                                            value="{{ isset($hotelData[$i - 1]) ? $hotelData[$i - 1]['lokasi_htl'] : '' }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-2">
                                                        <label class="form-label"
                                                            for="jmlkmr_htl_{{ $i }}">Rooms</label>
                                                        <input type="number" name="jmlkmr_htl[]"
                                                            id="jmlkmr_htl_{{ $i }}" class="form-control"
                                                            placeholder="Number of Rooms"
                                                            value="{{ isset($hotelData[$i - 1]) ? $hotelData[$i - 1]['jmlkmr_htl'] : '' }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="mb-2">
                                                        <label class="form-label" for="bed_htl_{{ $i }}">Bed
                                                            Type</label>
                                                        <select class="form-control" name="bed_htl[]"
                                                            id="bed_htl_{{ $i }}">
                                                            <option value="" disabled
                                                                {{ !isset($hotelData[$i - 1]) ? 'selected' : '' }}>-
                                                            </option>
                                                            <option value="Single Bed"
                                                                {{ isset($hotelData[$i - 1]) && $hotelData[$i - 1]['bed_htl'] == 'Single Bed' ? 'selected' : '' }}>
                                                                Single Bed</option>
                                                            <option value="Twin Bed"
                                                                {{ isset($hotelData[$i - 1]) && $hotelData[$i - 1]['bed_htl'] == 'Twin Bed' ? 'selected' : '' }}>
                                                                Twin Bed</option>
                                                            <option value="King Bed"
                                                                {{ isset($hotelData[$i - 1]) && $hotelData[$i - 1]['bed_htl'] == 'King Bed' ? 'selected' : '' }}>
                                                                King Bed</option>
                                                            <option value="Super King Bed"
                                                                {{ isset($hotelData[$i - 1]) && $hotelData[$i - 1]['bed_htl'] == 'Super King Bed' ? 'selected' : '' }}>
                                                                Super King Bed</option>
                                                            <option value="Extra Bed"
                                                                {{ isset($hotelData[$i - 1]) && $hotelData[$i - 1]['bed_htl'] == 'Extra Bed' ? 'selected' : '' }}>
                                                                Extra Bed</option>
                                                            <option value="Baby Cot"
                                                                {{ isset($hotelData[$i - 1]) && $hotelData[$i - 1]['bed_htl'] == 'Baby Cot' ? 'selected' : '' }}>
                                                                Baby Cot</option>
                                                            <option value="Sofa Bed"
                                                                {{ isset($hotelData[$i - 1]) && $hotelData[$i - 1]['bed_htl'] == 'Sofa Bed' ? 'selected' : '' }}>
                                                                Sofa Bed</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row my-2">
                                                <div class="col-md-5">
                                                    <div class="mb-2">
                                                        <label class="form-label"
                                                            for="tgl_masuk_htl_{{ $i }}">Start Date</label>
                                                        <input type="date" name="tgl_masuk_htl[]"
                                                            id="tgl_masuk_htl_{{ $i }}" class="form-control"
                                                            value="{{ isset($hotelData[$i - 1]) ? $hotelData[$i - 1]['tgl_masuk_htl'] : '' }}"
                                                            onchange="calculateDays({{ $i }})">
                                                    </div>
                                                </div>
                                                <div class="col-md-5">
                                                    <div class="mb-2">
                                                        <label class="form-label"
                                                            for="tgl_keluar_htl_{{ $i }}">End Date</label>
                                                        <input type="date" name="tgl_keluar_htl[]"
                                                            id="tgl_keluar_htl_{{ $i }}" class="form-control"
                                                            value="{{ isset($hotelData[$i - 1]) ? $hotelData[$i - 1]['tgl_keluar_htl'] : '' }}"
                                                            onchange="calculateDays({{ $i }})">
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="mb-2">
                                                        <label class="form-label"
                                                            for="totaldays_{{ $i }}">Total Days</label>
                                                        <div class="input-group">
                                                            <input class="form-control bg-light"
                                                                id="totaldays_{{ $i }}" name="totaldays[]"
                                                                type="text" min="0"
                                                                value="{{ isset($hotelData[$i - 1]) ? $hotelData[$i - 1]['total_hari'] : '' }}"
                                                                readonly>
                                                            <div class="input-group-append">
                                                                <span class="input-group-text">days</span>
                                                            </div>
                                                        </div>
                                                        {{-- <input class="form-control" id="perdiem_{{ $i }}"
                                                            name="perdiem[]" type="hidden"
                                                            value="{{ isset($hotelData[$i - 1]) ? $hotelData[$i - 1]['perdiem'] : '' }}"> --}}
                                                    </div>
                                                </div>
                                            </div>

                                            @if ($i < 5)
                                                <div class="mt-3">
                                                    <label class="form-label">Add more hotels</label>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio"
                                                            id="more_htl_no_{{ $i }}"
                                                            name="more_htl_{{ $i }}" value="No"
                                                            {{ ($hotelData[$i - 1]['more_htl'] ?? 'Tidak') == 'Tidak' ? 'checked' : '' }}>
                                                        <label class="form-check-label"
                                                            for="more_htl_no_{{ $i }}">No</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio"
                                                            id="more_htl_yes_{{ $i }}"
                                                            name="more_htl_{{ $i }}" value="Yes"
                                                            {{ ($hotelData[$i - 1]['more_htl'] ?? 'Tidak') == 'Ya' ? 'checked' : '' }}
                                                            onchange="toggleNextHotelForm({{ $i }})">
                                                        <label class="form-check-label"
                                                            for="more_htl_yes_{{ $i }}">Yes</label>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endfor
                        </div>


                        <br>

                        <!-- Buttons -->
                        <div class="row">
                            <input type="hidden" name="status" value="Pending L1" id="status">
                            <div class="col-md d-md-flex justify-content-end text-center">
                                <input type="hidden" name="repeat_days_selected" id="repeatDaysSelected">
                                <button type="button" class="btn btn-outline-primary rounded-pill me-2"
                                    name="action_ca_draft" id="save-draft">Save as Draft</button>
                                <button type="submit" class="btn btn-primary rounded-pill shadow px-4">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>



    <!-- Tambahkan script JavaScript untuk mengumpulkan nilai repeat_days[] -->
    <script>
        function calculateDays(index) {
            // Get the start and end date input fields
            const startDateInput = document.getElementById('tgl_masuk_htl_' + index);
            const endDateInput = document.getElementById('tgl_keluar_htl_' + index);
            const totalDaysInput = document.getElementById('totaldays_' + index);

            // Get the values of the start and end dates
            const startDate = new Date(startDateInput.value);
            const endDate = new Date(endDateInput.value);

            // Ensure the end date is not earlier than the start date
            if (endDate < startDate) {
                alert("End date cannot be earlier than start date.");
                endDateInput.value = ''; // Reset the end date
                totalDaysInput.value = '0'; // Reset the total days
                return;
            }

            // Calculate the difference in days
            const timeDiff = endDate.getTime() - startDate.getTime();
            const totalDays = Math.ceil(timeDiff / (1000 * 3600 * 24)); // Convert milliseconds to days

            // Update the total days field
            totalDaysInput.value = totalDays > 0 ? totalDays : 0;
        }

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

        function validateInput(input) {
            //input.value = input.value.replace(/[^0-9,]/g, '');
            input.value = input.value.replace(/[^0-9]/g, '');
        }
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-beta3/js/bootstrap.min.js"></script>
@endsection

@extends('layouts_.vertical', ['page_title' => 'Ticket'])

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
                            <li class="breadcrumb-item"><a href="{{ route('ticket') }}">{{ $parentLink }}</a></li>
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
                    <h4 class="modal-title" id="viewFormEmployeeLabel">Edit Ticket</h4>
                    <a href="{{ route('ticket') }}" type="button" class="btn btn-close btn-close-white"></a>
                </div>
                <div class="card-body" @style('overflow-y: auto;')>
                    <div class="container-fluid">
                        <form id="btEditForm" method="post" action="{{ route('ticket.update', ['id' => $ticket->id]) }}">
                            @csrf
                            @method('PUT')
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
                            <!-- Business Trip Number Selection -->
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="mb-2">
                                        <label class="form-label" for="bisnis_numb">Business Trip Number</label>
                                        <select class="form-control select2" id="bisnis_numb" name="bisnis_numb">
                                            <option value="-" {{ $ticket->no_sppd === '-' ? 'selected' : '' }}>No
                                                Business
                                                Trip</option>
                                            @foreach ($no_sppds as $no_sppd)
                                                <option value="{{ $no_sppd->no_sppd }}"
                                                    {{ $ticket->no_sppd == $no_sppd->no_sppd ? 'selected' : '' }}>
                                                    {{ $no_sppd->no_sppd }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-2">
                                        <label class="form-label" for="jns_dinas_tkt">Service Type</label>
                                        <input type="text" class="form-control bg-light" name="jns_dinas_tkt"
                                            id="jns_dinas_tkt" value="{{ $ticket->jns_dinas_tkt }}" readonly>
                                    </div>
                                </div>
                            </div>

                            <!-- Dynamic Ticket Forms Start -->
                            <div class="d-flex flex-column gap-2" id="ticket_forms_container"
                                style="display: {{ count($ticketData) > 0 ? 'block' : 'none' }};">
                                @for ($i = 1; $i <= 5; $i++)
                                    <div class="ticket-form" id="ticket-form-{{ $i }}"
                                        style="display: {{ isset($ticketData[$i - 1]) ? 'block' : 'none' }};">
                                        <div class="text-bg-primary p-2 mb-1" style="text-align:center; border-radius:4px;">
                                            Ticket {{ $i }}
                                        </div>
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="row my-2">
                                                    <label class="form-label" for="jk_tkt_{{ $i }}">Passengers
                                                        Name (No KTP)</label>
                                                    <div class="col-md-2">
                                                        <div class="mb-2 mr-0">
                                                            <select class="form-control" id="jk_tkt_{{ $i }}"
                                                                name="jk_tkt[]">
                                                                <option value="">-</option>
                                                                <option value="Male"
                                                                    {{ isset($ticketData[$i - 1]) && $ticketData[$i - 1]['jk_tkt'] == 'Male' ? 'selected' : '' }}>
                                                                    Mr</option>
                                                                <option value="Female"
                                                                    {{ isset($ticketData[$i - 1]) && $ticketData[$i - 1]['jk_tkt'] == 'Female' ? 'selected' : '' }}>
                                                                    Mrs</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="mb-2">
                                                            <input type="text" name="np_tkt[]"
                                                                id="np_tkt_{{ $i }}" class="form-control"
                                                                placeholder="Passenger's Name"
                                                                value="{{ isset($ticketData[$i - 1]) ? $ticketData[$i - 1]['np_tkt'] : '' }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="mb-2">
                                                            <input type="number" name="noktp_tkt[]"
                                                                id="noktp_tkt_{{ $i }}" class="form-control"
                                                                placeholder="No KTP"
                                                                value="{{ isset($ticketData[$i - 1]) ? $ticketData[$i - 1]['noktp_tkt'] : '' }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="mb-2">
                                                            <label class="form-label"
                                                                for="tlp_tkt_{{ $i }}">Phone Number</label>
                                                            <input type="number" name="tlp_tkt[]"
                                                                id="tlp_tkt_{{ $i }}" class="form-control"
                                                                maxlength="12" placeholder="ex: 08123123123"
                                                                value="{{ isset($ticketData[$i - 1]) ? $ticketData[$i - 1]['tlp_tkt'] : '' }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="mb-2">
                                                            <label class="form-label"
                                                                for="jenis_tkt_{{ $i }}">Transportation
                                                                Type</label>
                                                            <select class="form-select" name="jenis_tkt[]"
                                                                id="jenis_tkt_{{ $i }}">
                                                                <option value="" disabled selected>Select
                                                                    Transportation Type</option>
                                                                <option value="Train"
                                                                    {{ isset($ticketData[$i - 1]) && $ticketData[$i - 1]['jenis_tkt'] == 'Train' ? 'selected' : '' }}>
                                                                    Train</option>
                                                                <option value="Bus"
                                                                    {{ isset($ticketData[$i - 1]) && $ticketData[$i - 1]['jenis_tkt'] == 'Bus' ? 'selected' : '' }}>
                                                                    Bus</option>
                                                                <option value="Airplane"
                                                                    {{ isset($ticketData[$i - 1]) && $ticketData[$i - 1]['jenis_tkt'] == 'Airplane' ? 'selected' : '' }}>
                                                                    Airplane</option>
                                                                <option value="Car"
                                                                    {{ isset($ticketData[$i - 1]) && $ticketData[$i - 1]['jenis_tkt'] == 'Car' ? 'selected' : '' }}>
                                                                    Car</option>
                                                                <option value="Ferry"
                                                                    {{ isset($ticketData[$i - 1]) && $ticketData[$i - 1]['jenis_tkt'] == 'Ferry' ? 'selected' : '' }}>
                                                                    Ferry</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="mb-2">
                                                            <label for="ket_tkt_{{ $i }}"
                                                                class="form-label">Information</label>
                                                            <textarea class="form-control" id="ket_tkt_{{ $i }}" name="ket_tkt[]" rows="3"
                                                                placeholder="Add ticket details, e.g., Citilink, Garuda Indonesia, etc.">{{ isset($ticketData[$i - 1]) ? $ticketData[$i - 1]['ket_tkt'] : '' }}</textarea>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="mb-2">
                                                            <label class="form-label"
                                                                for="dari_tkt_{{ $i }}">Departure City</label>
                                                            <input type="text" name="dari_tkt[]"
                                                                id="dari_tkt_{{ $i }}" class="form-control"
                                                                placeholder="Kota Keberangkatan"
                                                                value="{{ isset($ticketData[$i - 1]) ? $ticketData[$i - 1]['dari_tkt'] : '' }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="mb-2">
                                                            <label class="form-label"
                                                                for="ke_tkt_{{ $i }}">Arrival City</label>
                                                            <input type="text" name="ke_tkt[]"
                                                                id="ke_tkt_{{ $i }}" class="form-control"
                                                                placeholder="Kota Kedatangan"
                                                                value="{{ isset($ticketData[$i - 1]) ? $ticketData[$i - 1]['ke_tkt'] : '' }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <div class="mb-2">
                                                            <label class="form-label"
                                                                for="tgl_brkt_tkt_{{ $i }}">Departure
                                                                Date</label>
                                                            <input type="date" name="tgl_brkt_tkt[]"
                                                                id="tgl_brkt_tkt_{{ $i }}"
                                                                class="form-control"
                                                                value="{{ isset($ticketData[$i - 1]) ? $ticketData[$i - 1]['tgl_brkt_tkt'] : '' }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="mb-2">
                                                            <label class="form-label"
                                                                for="jam_brkt_tkt_{{ $i }}">Departure
                                                                Time</label>
                                                            <input type="time" name="jam_brkt_tkt[]"
                                                                id="jam_brkt_tkt_{{ $i }}"
                                                                class="form-control"
                                                                value="{{ isset($ticketData[$i - 1]) ? $ticketData[$i - 1]['jam_brkt_tkt'] : '' }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="mb-1">
                                                            <label class="form-label"
                                                                for="type_tkt_{{ $i }}">Ticket Type</label>
                                                            <select name="type_tkt[]" id="type_tkt_{{ $i }}"
                                                                class="form-select"
                                                                onchange="toggleDivs({{ $i }})">
                                                                <option value="One-Way"
                                                                    {{ isset($ticketData[$i - 1]) && $ticketData[$i - 1]['type_tkt'] == 'One-Way' ? 'selected' : '' }}>
                                                                    One-Way</option>
                                                                <option value="Round Trip"
                                                                    {{ isset($ticketData[$i - 1]) && $ticketData[$i - 1]['type_tkt'] == 'Round Trip' ? 'selected' : '' }}>
                                                                    Round-Trip</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12 round-trip-options"
                                                        style="display: {{ isset($ticketData[$i - 1]) && $ticketData[$i - 1]['type_tkt'] == 'Round Trip' ? 'block' : 'none' }};">
                                                        <div class="row">
                                                            <div class="col-md-8">
                                                                <div class="mb-2">
                                                                    <label class="form-label"
                                                                        for="tgl_plg_tkt_{{ $i }}">Return
                                                                        Date</label>
                                                                    <input type="date" name="tgl_plg_tkt[]"
                                                                        id="tgl_plg_tkt_{{ $i }}"
                                                                        class="form-control"
                                                                        value="{{ isset($ticketData[$i - 1]) ? $ticketData[$i - 1]['tgl_plg_tkt'] : '' }}">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="mb-2">
                                                                    <label class="form-label"
                                                                        for="jam_plg_tkt_{{ $i }}">Return
                                                                        Time</label>
                                                                    <input type="time" name="jam_plg_tkt[]"
                                                                        id="jam_plg_tkt_{{ $i }}"
                                                                        class="form-control"
                                                                        value="{{ isset($ticketData[$i - 1]) ? $ticketData[$i - 1]['jam_plg_tkt'] : '' }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                @if ($i < 5)
                                                    <div class="mt-3">
                                                        <label class="form-label">Add more ticket</label>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio"
                                                                id="more_tkt_no_{{ $i }}"
                                                                name="more_tkt_{{ $i }}" value="Tidak"
                                                                {{ ($ticketData[$i - 1]['more_tkt'] ?? 'Tidak') == 'Tidak' ? 'checked' : '' }}>
                                                            <label class="form-check-label"
                                                                for="more_tkt_no_{{ $i }}">Tidak</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio"
                                                                id="more_tkt_yes_{{ $i }}"
                                                                name="more_tkt_{{ $i }}" value="Ya"
                                                                {{ ($ticketData[$i - 1]['more_tkt'] ?? 'Tidak') == 'Ya' ? 'checked' : '' }}
                                                                onchange="toggleNextForm({{ $i }})">
                                                            <label class="form-check-label"
                                                                for="more_tkt_yes_{{ $i }}">Ya</label>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endfor
                            </div>
                            <!-- Dynamic Ticket Forms End -->

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
@endsection
<!-- Tambahkan script JavaScript untuk mengumpulkan nilai repeat_days[] -->
@push('scripts')
    <script>
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

        document.addEventListener('DOMContentLoaded', function() {
            // Handling form visibility and reset for multiple ticket forms
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
                            const form = document.getElementById(`ticket-form-${j}`);
                            if (form) {
                                form.style.display = 'none';
                                // Reset the form when it is hidden
                                resetTicketFields(form);
                            }
                        }
                        // Reset radio buttons for subsequent forms
                        for (let j = i + 1; j <= 4; j++) {
                            const noRadioButton = document.getElementById(`more_tkt_no_${j}`);
                            if (noRadioButton) {
                                noRadioButton.checked = true;
                            }
                        }
                    }
                });
            }

            // Function to reset ticket fields
            function resetTicketFields(container) {
                const inputs = container.querySelectorAll(
                    'input[type="text"], input[type="number"], input[type="date"], input[type="time"], textarea'
                );
                inputs.forEach(input => {
                    input.value = '';
                });
                // Also reset select fields if needed
                const selects = container.querySelectorAll('select');
                selects.forEach(select => {
                    select.selectedIndex = 0; // or set to a specific default value
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
                        resetRoundTripFields(index + 1);
                    }
                });
            });

            function resetRoundTripFields(index) {
                document.getElementById(`tgl_plg_tkt_${index}`).value = '';
                document.getElementById(`jam_plg_tkt_${index}`).value = '';
            }
        });
        //elses
        document.getElementById('tgl_brkt_tkt').addEventListener('change', function() {
            var berangkat = this.value;
            var pulang = document.getElementById('tgl_plg_tkt').value;

            if (pulang && pulang < berangkat) {
                document.getElementById('tgl_plg_tkt').value = berangkat;
            }

            document.getElementById('tgl_plg_tkt').setAttribute('min', berangkat);
        });

        document.getElementById('tgl_plg_tkt').addEventListener('change', function() {
            var pulang = this.value;
            var berangkat = document.getElementById('tgl_brkt_tkt').value;

            if (pulang < berangkat) {
                alert("Return date can't be earlier than Depature Date.");
                this.value = berangkat;
            }
        });

        function toggleDivs() {
            var typeTkt = document.getElementById("type_tkt").value;
            var divTicket = document.getElementById("div_ticket");

            if (typeTkt === "One-Way") {
                divTicket.style.display = "none";
            } else if (typeTkt === "Round-Trip") {
                divTicket.style.display = "flex";
            }
        }

        // Call the function on page load to set the initial state
        window.onload = function() {
            toggleDivs();
        };
    </script>


    <script>
        $(document).ready(function() {
            $('.select2').select2({
                theme: "bootstrap-5",

            });
        });
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-beta3/js/bootstrap.min.js"></script>
@endpush

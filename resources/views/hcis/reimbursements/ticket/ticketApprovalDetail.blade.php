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
                    <h4 class="modal-title" id="viewFormEmployeeLabel">Detail Ticket</h4>
                    <a href="{{ route('ticket.approval') }}" type="button" class="btn btn-close btn-close-white"></a>
                </div>
                <div class="card-body" @style('overflow-y: auto;')>
                    <div class="container-fluid">
                        <form id="btEditForm" method="POST" action="{{ route('change.status.ticket', ['id' => $ticket->id]) }}">
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
                                        <select class="form-control select2" id="bisnis_numb" name="bisnis_numb" disabled>
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
                                                                name="jk_tkt[]" disabled>
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
                                                                value="{{ isset($ticketData[$i - 1]) ? $ticketData[$i - 1]['np_tkt'] : '' }}"
                                                                disabled>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="mb-2">
                                                            <input type="number" name="noktp_tkt[]"
                                                                id="noktp_tkt_{{ $i }}" class="form-control"
                                                                placeholder="No KTP"
                                                                value="{{ isset($ticketData[$i - 1]) ? $ticketData[$i - 1]['noktp_tkt'] : '' }}"
                                                                disabled>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="mb-2">
                                                            <label class="form-label"
                                                                for="tlp_tkt_{{ $i }}">Phone Number</label>
                                                            <input type="number" name="tlp_tkt[]"
                                                                id="tlp_tkt_{{ $i }}" class="form-control"
                                                                maxlength="12" placeholder="ex: 08123123123"
                                                                value="{{ isset($ticketData[$i - 1]) ? $ticketData[$i - 1]['tlp_tkt'] : '' }}"
                                                                disabled>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="mb-2">
                                                            <label class="form-label"
                                                                for="jenis_tkt_{{ $i }}">Transportation
                                                                Type</label>
                                                            <select class="form-select" name="jenis_tkt[]"
                                                                id="jenis_tkt_{{ $i }}" disabled>
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
                                                                placeholder="Add ticket details, e.g., Citilink, Garuda Indonesia, etc." disabled>{{ isset($ticketData[$i - 1]) ? $ticketData[$i - 1]['ket_tkt'] : '' }}</textarea>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="mb-2">
                                                            <label class="form-label"
                                                                for="dari_tkt_{{ $i }}">Departure City</label>
                                                            <input type="text" name="dari_tkt[]"
                                                                id="dari_tkt_{{ $i }}" class="form-control"
                                                                placeholder="Kota Keberangkatan"
                                                                value="{{ isset($ticketData[$i - 1]) ? $ticketData[$i - 1]['dari_tkt'] : '' }}"
                                                                disabled>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="mb-2">
                                                            <label class="form-label"
                                                                for="ke_tkt_{{ $i }}">Arrival City</label>
                                                            <input type="text" name="ke_tkt[]"
                                                                id="ke_tkt_{{ $i }}" class="form-control"
                                                                placeholder="Kota Kedatangan"
                                                                value="{{ isset($ticketData[$i - 1]) ? $ticketData[$i - 1]['ke_tkt'] : '' }}"
                                                                disabled>
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
                                                                value="{{ isset($ticketData[$i - 1]) ? $ticketData[$i - 1]['tgl_brkt_tkt'] : '' }}"
                                                                disabled>
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
                                                                value="{{ isset($ticketData[$i - 1]) ? $ticketData[$i - 1]['jam_brkt_tkt'] : '' }}"
                                                                disabled>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="mb-1">
                                                            <label class="form-label"
                                                                for="type_tkt_{{ $i }}">Ticket Type</label>
                                                            <select name="type_tkt[]" id="type_tkt_{{ $i }}"
                                                                class="form-select"
                                                                onchange="toggleDivs({{ $i }})" disabled>
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
                                                                        value="{{ isset($ticketData[$i - 1]) ? $ticketData[$i - 1]['tgl_plg_tkt'] : '' }}"
                                                                        disabled>
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
                                                                        value="{{ isset($ticketData[$i - 1]) ? $ticketData[$i - 1]['jam_plg_tkt'] : '' }}"
                                                                        disabled>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endfor
                            </div>
                        </form>
                        <div class="d-flex justify-content-end">
                            <!-- Decline Form -->
                            <form method="POST" action="{{ route('change.status.ticket', ['id' => $ticket->id]) }}"
                                style="display: inline-block;" class="status-form">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status_approval" value="Rejected">
                                <button type="submit" class="btn btn-primary rounded-pill"
                                    style="padding: 0.5rem 1rem; margin-right: 5px">
                                    Decline
                                </button>
                            </form>

                            <!-- Approve Form -->
                            <form method="POST" action="{{ route('change.status.ticket', ['id' => $ticket->id]) }}"
                                style="display: inline-block; margin-right: 5px;" class="status-form">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status_approval"
                                    value="{{ Auth::user()->id == $ticketOwnerEmployee->manager_l1_id ? 'Pending L2' : 'Approved' }}">
                                <button type="submit" class="btn btn-success rounded-pill"
                                    style="padding: 0.5rem 1rem;">
                                    Approve
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-light rounded-4 border-0 shadow" style="border-radius: 1rem;">
                <div class="modal-body text-center p-5" style="padding: 2rem;">
                    <div class="mb-4">
                        <i class="bi bi-check-circle-fill" style="font-size: 100px; color: #AB2F2B !important;"></i>
                    </div>
                    <h4 class="mb-3 fw-bold" style="font-size: 32px; color: #AB2F2B !important;">Success!</h4>
                    <p class="mb-4" id="successModalBody" style="font-size: 20px;">
                        <!-- The success message will be inserted here -->
                    </p>
                    <button type="button" class="btn btn-outline-primary rounded-pill px-4"
                        data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Tambahkan script JavaScript untuk mengumpulkan nilai repeat_days[] -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const forms = document.querySelectorAll('.status-form');
            forms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const action = this.querySelector('input[name="status_approval"]').value;
                    const confirmMessage = action === 'Rejected' ?
                        'Are you sure you want to reject this?' :
                        'Are you sure you want to confirm this?';

                    if (confirm(confirmMessage)) {
                        const formData = new FormData(this);
                        fetch(this.action, {
                                method: 'POST',
                                body: formData,
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    // Update the success modal content
                                    document.getElementById('successModalBody').textContent =
                                        data.message;

                                    // Show the success modal
                                    var successModal = new bootstrap.Modal(document
                                        .getElementById('successModal'));
                                    successModal.show();

                                    // Reload the page after modal is closed
                                    document.getElementById('successModal').addEventListener(
                                        'hidden.bs.modal',
                                        function() {
                                            window.location.href = '/ticket/approval';
                                        });
                                } else {
                                    alert('An error occurred. Please try again.');
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                alert('An error occurred. Please try again.');
                            });
                    }
                });
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
@endsection

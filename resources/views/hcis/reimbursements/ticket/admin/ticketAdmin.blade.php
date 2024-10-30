@extends('layouts_.vertical', ['page_title' => 'Ticket (Admin)'])

@section('css')
    @vite([
        'node_modules/select2/dist/css/select2.min.css',
        'node_modules/daterangepicker/daterangepicker.css',
        'node_modules/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.css',
        'node_modules/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css',
        'node_modules/bootstrap-timepicker/css/bootstrap-timepicker.min.css',
        'node_modules/flatpickr/dist/flatpickr.min.css'
    ])
    <style>
        th {
            color: white !important;
            text-align: center;
        }

        .table {
            border-collapse: separate;
            width: 100%;
            position: relative;
            overflow: auto;
        }

        .table thead th {
            position: -webkit-sticky !important;
            /* For Safari */
            position: sticky !important;
            top: 0 !important;
            z-index: 2 !important;
            background-color: #AB2F2B !important;
            border-bottom: 2px solid #AB2F2B !important;
            padding-right: 6px;
            box-shadow: inset 2px 0 0 #AB2F2B;
        }

        .table tbody td {
            background-color: #fff !important;
            padding-right: 10px;
            position: relative;
        }

        .table th.sticky-col-header {
            position: -webkit-sticky !important;
            /* For Safari */
            position: sticky !important;
            left: 0 !important;
            z-index: 3 !important;
            background-color: #AB2F2B !important;
            border-right: 2px solid #AB2F2B !important;
            padding-right: 10px;
            box-shadow: inset 2px 0 0 #AB2F2B;
        }

        .table td.sticky-col {
            position: -webkit-sticky !important;
            /* For Safari */
            position: sticky !important;
            left: 0 !important;
            z-index: 1 !important;
            background-color: #fff !important;
            border-right: 2px solid #ddd !important;
            padding-right: 10px;
            box-shadow: inset 6px 0 0 #fff;
        }
    </style>
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
                            <li class="breadcrumb-item">{{ $parentLink }}</li>
                            <li class="breadcrumb-item active">{{ $link }}</li>
                        </ol>
                    </div>
                    <a href="{{ route('reimbursements') }}" class="page-title"><i class="ri-arrow-left-circle-line"></i></a>
                    {{-- <button type="button" class="page-title btn btn-warning rounded-pill">Back</button> --}}
                </div>
            </div>
        </div>
        <!-- Content Row -->
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <form action="{{ route('ticket.admin') }}" method="GET">
                            <div class="input-group">
                                <label class="col-form-label">Departure Date : </label>

                                <input type="date" class="form-control mx-2" id="start_date" name="start_date" placeholder="Start Date" title="Start Date" value="{{ request()->get('start_date') }}">
                                <label class="col-form-label"> - </label>
                                <input type="date" class="form-control mx-2" id="end_date" name="end_date" placeholder="End Date" title="End Date" value="{{ request()->get('end_date') }}">

                                <div class="input-group-append mx-2">
                                    <button class="btn btn-primary" type="submit">Filter</button>
                                </div>
                                <div class="input-group-append">
                                    @if (isset($_GET['start_date']) && $_GET['start_date'] !== '')
                                        <button style="display: block" class="btn btn-success w-100" type="button" onclick="redirectToExportExcel()">
                                            <i class="ri-file-excel-2-line"></i> Export
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h3 class="card-title">{{ $link }}</h3>
                            <div class="input-group" style="width: 30%;">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-white w-border-dark-subtle"><i class="ri-search-line"></i></span>
                                </div>
                                <input type="text" name="customsearch" id="customsearch" class="form-control w-border-dark-subtle border-left-0" placeholder="search.." aria-label="search" aria-describedby="search" >
                                {{-- &nbsp;&nbsp;&nbsp; --}}
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover dt-responsive nowrap mt-2" id="defaultTable" width="100%"
                                cellspacing="0">
                                <thead class="thead-light">
                                    <tr class="text-center">
                                        <th>No</th>
                                        <th>No. SPPD</th>
                                        <th>No. Ticket</th>
                                        <th>Requestor Name</th>
                                        <th>Total Tickets</th>
                                        <th>Purposes</th>
                                        <th>From/To</th>
                                        <th>Details</th>
                                        <th>Status</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($transactions as $transaction)
                                        <tr>
                                            <td style="text-align: center">{{ $loop->index + 1 }}</td>
                                            <td>{{ $transaction->no_sppd }}</td>
                                            <td>{{ $transaction->no_tkt }}</td>
                                            <td style="text-align: left">
                                                {{ $ticketCounts[$transaction->no_tkt]['total'] ?? 1 }} Tickets</td>
                                            <td>{{ $transaction->employee->fullname }}</td>
                                            <td>{{ $transaction->jns_dinas_tkt }}</td>
                                            <td>{{ $transaction->dari_tkt . '/' . $transaction->ke_tkt }}</td>
                                            <td class="text-info">
                                                <a class="text-info btn-detail" data-toggle="modal"
                                                    data-target="#detailModal" style="cursor: pointer"
                                                    data-tiket="{{ json_encode(
                                                        $ticket[$transaction->no_tkt]->map(function ($ticket) {
                                                            return [
                                                                // 'No. Ticket' => $ticket->no_tkt ?? 'No Data',
                                                                'No. SPPD' => $ticket->no_sppd,
                                                                'No. Ticket' => $ticket->no_tkt,
                                                                'Passengers Name' => $ticket->np_tkt,
                                                                'Unit' => $ticket->unit,
                                                                'Gender' => $ticket->jk_tkt,
                                                                'NIK' => $ticket->noktp_tkt,
                                                                'Phone No.' => $ticket->tlp_tkt,
                                                                'Transport Type.' => $ticket->jenis_tkt,
                                                                'From' => $ticket->dari_tkt,
                                                                'To' => $ticket->ke_tkt,
                                                                'Information' => $ticket->ket_tkt ?? 'No Data',
                                                                'Purposes' => $ticket->jns_dinas_tkt,
                                                                'Ticket Type' => $ticket->type_tkt,
                                                                'Departure Date' => date('d-M-Y', strtotime($ticket->tgl_brkt_tkt)),
                                                                'Time' => !empty($ticket->jam_brkt_tkt) ? date('H:i', strtotime($ticket->jam_brkt_tkt)) : 'No Data',
                                                                'Return Date' => isset($ticket->tgl_plg_tkt) ? date('d-M-Y', strtotime($ticket->tgl_plg_tkt)) : 'No Data',
                                                                'Return Time' => !empty($ticket->jam_plg_tkt) ? date('H:i', strtotime($ticket->jam_plg_tkt)) : 'No Data',
                                                            ];
                                                        }),
                                                    ) }}">
                                                    <u>Details</u>
                                                </a>
                                            </td>

                                            <td style="align-content: center">
                                                <span
                                                    class="badge rounded-pill bg-{{ $transaction->approval_status == 'Approved' ||
                                                    $transaction->approval_status == 'Declaration Approved' ||
                                                    $transaction->approval_status == 'Verified'
                                                        ? 'success'
                                                        : ($transaction->approval_status == 'Rejected' ||
                                                        $transaction->approval_status == 'Return/Refund' ||
                                                        $transaction->approval_status == 'Declaration Rejected'
                                                            ? 'danger'
                                                            : (in_array($transaction->approval_status, [
                                                                'Pending L1',
                                                                'Pending L2',
                                                                'Declaration L1',
                                                                'Declaration L2',
                                                                'Waiting Submitted',
                                                            ])
                                                                ? 'warning'
                                                                : ($transaction->approval_status == 'Draft'
                                                                    ? 'secondary'
                                                                    : (in_array($transaction->approval_status, ['Doc Accepted'])
                                                                        ? 'info'
                                                                        : 'secondary')))) }}"
                                                    style="font-size: 12px; padding: 0.5rem 1rem; cursor: {{ ($transaction->approval_status == 'Rejected' || $transaction->approval_status == 'Declaration Rejected') && isset($ticketApprovals[$transaction->id]) ? 'pointer' : 'default' }};"
                                                    @if (
                                                        ($transaction->approval_status == 'Rejected' || $transaction->approval_status == 'Declaration Rejected') &&
                                                            isset($ticketApprovals[$transaction->id])) onclick="showRejectInfo('{{ $transaction->id }}')"
                                                    title="Click to see rejection reason" @endif
                                                    @if ($transaction->approval_status == 'Pending L1') title="L1 Manager: {{ $managerL1Name ?? 'Unknown' }}"
                                                    @elseif ($transaction->approval_status == 'Pending L2')
                                                    title="L2 Manager: {{ $managerL2Name ?? 'Unknown' }}" @endif>
                                                    {{ $transaction->approval_status == 'Approved' ? 'Approved' : $transaction->approval_status }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm btn-outline-success rounded-pill" data-bs-toggle="modal" data-bs-target="#bookingModal"
                                                        data-no-id="{{ $transaction->id }}"
                                                        data-no-tkt="{{ $transaction->no_tkt }}">
                                                    <i class="bi bi-ticket-perforated"></i>
                                                </button>
                                                <a href="{{ route('ticket.export', ['id' => $transaction->id]) }}"
                                                    class="btn btn-sm btn-outline-info rounded-pill" target="_blank">
                                                    <i class="bi bi-download"></i>
                                                </a>
                                                <form
                                                    action="{{ route('ticket.delete.admin', encrypt($transaction->id)) }}"
                                                    method="POST" style="display:inline;"
                                                    id="deleteForm_{{ $transaction->no_tkt }}">
                                                    @csrf
                                                    <input type="hidden" id="no_sppd_{{ $transaction->no_tkt }}"
                                                        value="{{ $transaction->no_tkt }}">
                                                    <button class="btn btn-sm rounded-pill btn-outline-danger delete-button"
                                                        title="Delete" data-id="{{ $transaction->no_tkt }}">
                                                        <i class="ri-delete-bin-line"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('hcis.reimbursements.ticket.navigation.modalTicket')

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/2.1.3/js/dataTables.min.js"></script>
@endsection
@section('script')
    {{-- @vite(['resources/js/pages/demo.form-advanced.js']) --}}

    <!-- Include jQuery -->
    {{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}}

    <!-- Include Bootstrap Date Range Picker -->
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/min/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <script src="{{ asset('/js/cashAdvanced/adminPage.js') }}"></script>
@endsection
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const rejectModal = new bootstrap.Modal(document.getElementById('rejectReasonModal'), {
                keyboard: true,
                backdrop: 'static'
            });

            const closeButtons = document.querySelectorAll('[data-bs-dismiss="modal"]');
            closeButtons.forEach(button => {
                button.addEventListener('click', () => {
                    rejectModal.hide();
                });
            });

            function formatDate(dateTimeString) {
                // Create a new Date object from the dateTimeString
                var date = new Date(dateTimeString);

                // Extract day, month, year, hours, and minutes
                var day = ('0' + date.getDate()).slice(-2); // Ensure two digits
                var month = ('0' + (date.getMonth() + 1)).slice(-2); // Month is 0-based, so we add 1
                var year = date.getFullYear();
                var hours = ('0' + date.getHours()).slice(-2);
                var minutes = ('0' + date.getMinutes()).slice(-2);

                // Format the date as d/m/Y H:I
                return `${day}/${month}/${year} ${hours}:${minutes}`;
            }

            window.showRejectInfo = function(transactionId) {
                var ticketApprovals = {!! json_encode($ticketApprovals) !!};
                var employeeName = {!! json_encode($employeeName) !!}; // Add this line

                var approval = ticketApprovals[transactionId];
                if (approval) {
                    var rejectedBy = employeeName[approval.employee_id] || 'N/A'; // Retrieve fullname
                    document.getElementById('rejectedBy').textContent = ': ' + rejectedBy;
                    document.getElementById('rejectionReason').textContent = ': ' + (approval.reject_info ||
                        'N/A');
                    var rejectionDate = approval.approved_at ? formatDate(approval.approved_at) : 'N/A';
                    document.getElementById('rejectionDate').textContent = ': ' + rejectionDate;

                    rejectModal.show();
                } else {
                    console.error('Approval information not found for transaction ID:', transactionId);
                }
            };

            // Add event listener for modal hidden event
            document.getElementById('rejectReasonModal').addEventListener('hidden.bs.modal', function() {
                console.log('Modal closed');
            });
        });

        $(document).ready(function() {
            $('.btn-detail').click(function() {
                var tiket = $(this).data('tiket');

                function createTableHtml(data, title) {
                    var tableHtml = '<h5>' + title + '</h5>';
                    tableHtml += `<div class="table-responsive">
                                    <table class="table table-sm scheduleTable">
                                        <thead>
                                            <tr>`;
                    var isArray = Array.isArray(data) && data.length > 0;

                    // Create headers
                    if (isArray) {
                        for (var key in data[0]) {
                            if (data[0].hasOwnProperty(key)) {
                                tableHtml += '<th>' + key + '</th>';
                            }
                        }
                    } else if (typeof data === 'object') {
                        for (var key in data) {
                            if (data.hasOwnProperty(key)) {
                                tableHtml += '<th>' + key + '</th>';
                            }
                        }
                    }

                    tableHtml += '</tr></thead><tbody>';

                    // Create rows
                    if (isArray) {
                        data.forEach(function(row) {
                            tableHtml += '<tr>';
                            for (var key in row) {
                                if (row.hasOwnProperty(key)) {
                                    tableHtml += '<td>' + row[key] + '</td>';
                                }
                            }
                            tableHtml += '</tr>';
                        });
                    } else if (typeof data === 'object') {
                        tableHtml += '<tr>';
                        for (var key in data) {
                            if (data.hasOwnProperty(key)) {
                                tableHtml += '<td>' + data[key] + '</td>';
                            }
                        }
                        tableHtml += '</tr>';
                    }

                    tableHtml += '</tbody></table></div>';
                    return tableHtml;
                }

                // Saat menggunakan fungsi createTableHtml dan menginisialisasi DataTable
                $(document).ready(function() {
                    var data = [ /* array atau objek data */ ];
                    var title = "Table Title";

                    // 1. Generate HTML tabel
                    var tableHtml = createTableHtml(data, title);

                    // 2. Masukkan tabel ke dalam DOM
                    $('#tableContainer').html(tableHtml);

                    // 3. Inisialisasi DataTable setelah tabel ada di DOM
                    $('.scheduleTable').DataTable();
                });


                // $('#detailTypeHeader').text('Detail Information');
                $('#detailContent').empty();

                try {
                    var content = '';

                    if (tiket && tiket !== 'undefined') {
                        var tiketData = typeof tiket === 'string' ? JSON.parse(tiket) : tiket;
                        content += createTableHtml(tiketData, 'Ticket Detail');
                    }

                    if (content !== '') {
                        $('#detailContent').html(content);
                    } else {
                        $('#detailContent').html('<p>No detail information available.</p>');
                    }

                    $('#detailModal').modal('show');
                } catch (e) {
                    $('#detailContent').html('<p>Error loading data</p>');
                }
            });

            $('#detailModal').on('hidden.bs.modal', function() {
                $('body').removeClass('modal-open').css({
                    overflow: '',
                    padding: ''
                });
                $('.modal-backdrop').remove();
            });
        });

        $(document).ready(function() {
            var table = $('#yourTableId').DataTable({
                "pageLength": 10 // Set default page length
            });
            // Set to 10 entries per page
            $('#dt-length-0').val(10);

            // Trigger the change event to apply the selected value
            $('#dt-length-0').trigger('change');
        });

        function redirectToExportExcel() {
            const route = "{{ route('ticket.excel') }}";

            const startDate = document.getElementById("start_date").value;
            const endDate = document.getElementById("end_date").value;

            // Create a form element
            const form = document.createElement("form");
            form.method = "GET";
            form.action = route;

            const startDateInput = document.createElement("input");
            startDateInput.type = "hidden";
            startDateInput.name = "start_date";
            startDateInput.value = startDate;

            const endDateInput = document.createElement("input");
            endDateInput.type = "hidden";
            endDateInput.name = "end_date";
            endDateInput.value = endDate;

            form.appendChild(startDateInput);
            form.appendChild(endDateInput);

            // Append the form to the body and submit it
            document.body.appendChild(form);
            form.submit();
        }

        const bookButtons = document.querySelectorAll('[data-bs-toggle="modal"]');
        bookButtons.forEach(button => {
            button.addEventListener('click', function() {
                const tktNumber = this.getAttribute('data-no-tkt');
                const idNumber = this.getAttribute('data-no-id');

                document.getElementById('book_no_tkt').textContent = tktNumber;
                document.getElementById('book_no_id').value = idNumber; // Mengisi input no_id
            });
        });
    </script>
@endpush

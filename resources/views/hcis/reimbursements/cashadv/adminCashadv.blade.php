@extends('layouts_.vertical', ['page_title' => 'Cash Advanced (Admin)'])

@section('css')
    @vite([
        'node_modules/select2/dist/css/select2.min.css',
        'node_modules/daterangepicker/daterangepicker.css', 
        'node_modules/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.css',
        'node_modules/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css',
        'node_modules/bootstrap-timepicker/css/bootstrap-timepicker.min.css',
        'node_modules/flatpickr/dist/flatpickr.min.css'
    ])
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
                        <form action="{{ route('cashadvanced.admin') }}" method="GET">
                            <div class="input-group">
                                <label class="col-form-label">Start Date : </label>
                                <input type="date" class="form-control mx-2" id="start_date" name="start_date" placeholder="Start Date" title="Start Date" value="{{ $startDate }}">
                                <label class="col-form-label"> - </label>
                                <input type="date" class="form-control mx-2" id="end_date" name="end_date" placeholder="End Date" title="End Date" value="{{ $endDate }}">
                                <div class="input-group-append mx-2">
                                    <button class="btn btn-primary" type="submit">Filter</button>
                                </div>
                                <div class="input-group-append">
                                    <button class="btn btn-success" type="button" onclick="redirectToExportExcel()">
                                        <i class="ri-file-excel-2-line"></i> Export to Excel
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>     
                </div>
            </div>
        </div>
        <div class="row" id="tableFilter">
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
                            <table class="table table-sm dt-responsive nowrap" id="scheduleTable" width="100%"
                                cellspacing="0">
                                <thead class="thead-light">
                                    <tr class="text-center">
                                        <th>No</th>
                                        <th>Type</th>
                                        <th>Cash Advance No</th>
                                        <th>Name</th>
                                        <th>Company</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Total CA</th>
                                        <th>Total Settlement</th>
                                        <th>Balance</th>
                                        <th>Request</th>
                                        <th>Settlement</th>
                                        <th>Status CA</th>
                                        <th>Actions</th>
                                        <th>Delete</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($ca_transactions as $ca_transaction)
                                        <tr>
                                            <td class="text-center">{{ $loop->index + 1 }}</td>
                                            @if ($ca_transaction->type_ca == 'dns')
                                                <td>Business Trip</td>
                                            @elseif($ca_transaction->type_ca == 'ndns')
                                                <td>Non Business Trip</td>
                                            @elseif($ca_transaction->type_ca == 'entr')
                                                <td>Entertainment</td>
                                            @endif
                                            <td class="text-center">{{ $ca_transaction->no_ca }}</td>
                                            <td>{{ $ca_transaction->employee->fullname }}</td>
                                            <td>{{ $ca_transaction->contribution_level_code }}</td>
                                            <td>{{ date('j M Y', strtotime($ca_transaction->formatted_start_date)) }}</td>
                                            <td>{{ date('j M Y', strtotime($ca_transaction->formatted_end_date)) }}</td>
                                            <td>Rp. {{ number_format($ca_transaction->total_ca) }}</td>
                                            <td>Rp. {{ number_format($ca_transaction->total_real) }}</td>
                                            <td>Rp. {{ number_format($ca_transaction->total_cost) }}</td>
                                            <td>
                                                <p class="badge text-bg-{{ $ca_transaction->approval_status == 'Approved' ? 'success' : ($ca_transaction->approval_status == 'Declaration' ? 'info' : ($ca_transaction->approval_status == 'Pending' ? 'warning' : ($ca_transaction->approval_status == 'Rejected' ? 'danger' : ($ca_transaction->approval_status == 'Draft' ? 'secondary' : 'default')))) }}" style="pointer-events: auto; cursor: default;" title="{{$ca_transaction->approval_status." - ".$ca_transaction->ReqName}}">
                                                    {{ $ca_transaction->approval_status }}
                                                </p>
                                            </td>
                                            <td>
                                                <p class="badge text-bg-{{ $ca_transaction->approval_sett == 'Approved' ? 'success' : ($ca_transaction->approval_sett == 'Declaration' ? 'info' : ($ca_transaction->approval_sett == 'Pending' ? 'warning' : ($ca_transaction->approval_sett == 'Rejected' ? 'danger' : ($ca_transaction->approval_sett == 'Draft' ? 'secondary' : 'default')))) }}" style="pointer-events: auto; cursor: default;" title="{{$ca_transaction->approval_sett." - ".$ca_transaction->settName}}">
                                                    {{ $ca_transaction->approval_sett }}
                                                </p>
                                            </td>
                                            <td>
                                                <p class="badge text-bg-{{ $ca_transaction->ca_status == 'Done' ? 'success' : 
                                                ($ca_transaction->ca_status == 'Refund' ? 'danger' : 
                                                ($ca_transaction->ca_status == 'On Progress' ? 'secondary' : 'default')) }}">
                                                    {{ $ca_transaction->ca_status }}
                                                </p>
                                            </td>
                                            <td class="text-left">
                                                <a href="{{ route('cashadvanced.download', $ca_transaction->id) }}" target="_blank" class="btn btn-outline-secondary" title="Print"><i class="bi bi-file-earmark-arrow-down"></i></a>
                                                @if($ca_transaction->approval_sett=='Approved')
                                                <button type="button" class="btn btn-outline-warning" data-bs-toggle="modal"  data-bs-target="#exampleModal" data-id="{{ $ca_transaction->id }}" data-status="{{ $ca_transaction->ca_status }}" title="Status Update"><i class="ri-file-edit-line"></i></button>
                                                @endif
                                                
                                            </td>
                                            <td class="text-center">
                                                <form action="{{ route('cashadvanced.delete', $ca_transaction->id) }}" method="POST" style="display:inline;">
                                                    @csrf
                                                    <button onclick="return confirm('Are you sure you want to delete this transaction?')" class="btn btn-outline-danger" title="Delete">
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
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Update Cash Advanced Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('cashadvanced.adupdate', ':id') }}" method="POST">@csrf
                    <div class="modal-body">
                            {{-- <label for="transaction-id-display" class="col-form-label">Transaction ID: </label> --}}
                            {{-- <input type="text" class="form-control" id="transaction-id-display" readonly> --}}
                            <input type="hidden" name="transaction_id" id="transaction_id">
                        <div class="mb-3">
                            <label for="recipient-name" class="col-form-label">Status : </label>
                            <select class="form-select" name="ca_status" id="ca_status">
                                <option value="On Progress">On Progress</option>
                                <option value="Refund">Refund</option>
                                <option value="Done">Done</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('script')
    {{-- @vite(['resources/js/pages/demo.form-advanced.js']) --}}

    <!-- Include jQuery -->
    {{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}}

    <!-- Include Bootstrap Date Range Picker -->
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/min/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
@endsection
@push('scripts')
    <script>
        // Periksa apakah ada pesan sukses
        var successMessage = "{{ session('success') }}";

        // Jika ada pesan sukses, tampilkan sebagai alert
        if (successMessage) {
            alert(successMessage);
        }
        function redirectToExportExcel() {
            const route = "{{ route('exportca.excel') }}";

            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;
            
            // Create a form element
            const form = document.createElement('form');
            form.method = 'GET';
            form.action = route;

            const startDateInput = document.createElement('input');
            startDateInput.type = 'hidden';
            startDateInput.name = 'start_date';
            startDateInput.value = startDate;

            const endDateInput = document.createElement('input');
            endDateInput.type = 'hidden';
            endDateInput.name = 'end_date';
            endDateInput.value = endDate;

            form.appendChild(startDateInput);
            form.appendChild(endDateInput);

            // Append the form to the body and submit it
            document.body.appendChild(form);
            form.submit();
        }

        // Event listener untuk menangkap date range yang dipilih
        $('#singledaterange').on('apply.daterangepicker', function(ev, picker) {
            var startDate = picker.startDate.format('YYYY-MM-DD');
            var endDate = picker.endDate.format('YYYY-MM-DD');
            
            // Panggil fungsi untuk mendapatkan data yang difilter
            filterTableByDateRange(startDate, endDate);
        });

        // Fungsi untuk mengirimkan tanggal yang dipilih ke server dan memperbarui tabel
        function filterTableByDateRange(startDate, endDate) {
            $.ajax({
                url: '{{ route("cashadvanced.admin") }}', // Route yang sudah Anda buat
                type: 'GET',
                data: {
                    start_date: startDate,
                    end_date: endDate
                },
                success: function(response) {
                    // Perbarui tabel dengan data yang difilter
                    // $('#scheduleTable tbody').html(response);
                    // $('#tableFilter').html(response);
                },
                error: function(xhr) {
                    // console.error(xhr);
                }
            });
        }
        //script modal
        document.addEventListener('DOMContentLoaded', function () {
            var exampleModal = document.getElementById('exampleModal');
            exampleModal.addEventListener('show.bs.modal', function (event) {
                // Dapatkan tombol yang men-trigger modal
                var button = event.relatedTarget;

                // Ambil data-id dan data-status dari tombol tersebut
                var transactionId = button.getAttribute('data-id');
                var transactionStatus = button.getAttribute('data-status');

                // Temukan form di dalam modal dan update action-nya
                var form = exampleModal.querySelector('form');
                var action = form.getAttribute('action');
                form.setAttribute('action', action.replace(':id', transactionId));

                // Set nilai transaction_id di input hidden
                var transactionInput = form.querySelector('#transaction_id');
                transactionInput.value = transactionId;

                // Pilih status yang sesuai di dropdown
                var statusSelect = form.querySelector('#ca_status');
                statusSelect.value = transactionStatus;
            });
        });
    </script>
@endpush
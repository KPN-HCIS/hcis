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
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="input-group" style="width: 50%;">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-white w-border-dark-subtle"><i class="ri-search-line"></i></span>
                                </div>
                                <input type="text" name="customsearch" id="customsearch" class="form-control w-border-dark-subtle border-left-0" placeholder="search.." aria-label="search" aria-describedby="search" >&nbsp;&nbsp;&nbsp;
                                <input type="text" class="form-control date" id="singledaterange" data-toggle="date-picker" data-cancel-class="btn-warning" name="daterange" title="Start Date Range">
                            </div>
                            <div class="input-group justify-content-end" style="width: 50%;">
                                <form action="{{ route('exportca.excel') }}" method="GET">
                                    <button type="submit"  class="btn btn-success">
                                        <i class="ri-file-excel-2-line"></i> Export to Excel
                                    </button>
                                </form>
                            </div>
                        </div>                        
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
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover table-sm dt-responsive nowrap" id="scheduleTable" width="100%"
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
                                        <th>Status</th>
                                        <th>Actions</th>
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
                                                <p class="badge text-bg-{{ $ca_transaction->approval_status == 'Approved' ? 'success' : ($ca_transaction->approval_status == 'Declaration' ? 'info' : ($ca_transaction->approval_status == 'Pending' ? 'warning' : ($ca_transaction->approval_status == 'Rejected' ? 'danger' : ($ca_transaction->approval_status == 'Draft' ? 'secondary' : 'success')))) }}" style="pointer-events: none">
                                                    {{ $ca_transaction->approval_status }}
                                                </p>
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('cashadvanced.download', $ca_transaction->id) }}" target="_blank" class="btn btn-outline-primary" title="Print"><i class="bi bi-file-earmark-arrow-down"></i></a>
                                                <form action="{{ route('cashadvanced.delete', $ca_transaction->id) }}" method="POST" style="display:inline;">
                                                    @csrf
                                                    <button onclick="return confirm('Apakah ingin Menghapus?')" class="btn btn-outline-danger" title="Delete">
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
                url: '{{ route("filter.ca.transactions") }}', // Route yang sudah Anda buat
                type: 'GET',
                data: {
                    start_date: startDate,
                    end_date: endDate
                },
                success: function(response) {
                    // Perbarui tabel dengan data yang difilter
                    $('#scheduleTable tbody').html(response);
                },
                error: function(xhr) {
                    console.error(xhr);
                }
            });
        }
    </script>
@endpush
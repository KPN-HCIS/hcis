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
                        <form action="{{ route('cashadvanced.admin') }}" method="GET">
                            <div class="input-group">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-outline-danger dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" id="dropdownButton">
                                        {{ request()->get('from_date') || request()->get('until_date') ? 'by Create Date' : 'by Start Date' }}
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#" onclick="updateDropdownText(this, 'start_date')">by Start Date</a></li>
                                        <li><a class="dropdown-item" href="#" onclick="updateDropdownText(this, 'create_date')">by Create Date</a></li>
                                    </ul>
                                </div>

                                <input type="date" class="form-control mx-2" style="{{ request()->get('from_date') || request()->get('until_date') ? 'display: none' : 'display: block' }}" id="start_date" name="start_date" placeholder="Start Date" title="Start Date" value="{{ request()->get('start_date') }}">
                                <input type="date" class="form-control mx-2" style="{{ request()->get('from_date') || request()->get('until_date') ? 'display: block' : 'display: none' }}" id="from_date" name="from_date" placeholder="From Date" title="From Date" value="{{ request()->get('from_date') }}">
                                <label class="col-form-label"> - </label>
                                <input type="date" class="form-control mx-2" style="{{ request()->get('from_date') || request()->get('until_date') ? 'display: none' : 'display: block' }}" id="end_date" name="end_date" placeholder="End Date" title="End Date" value="{{ request()->get('end_date') }}">
                                <input type="date" class="form-control mx-2" style="{{ request()->get('from_date') || request()->get('until_date') ? 'display: block' : 'display: none' }}" id="until_date" name="until_date" placeholder="Until Date" title="Until Date" value="{{ request()->get('until_date') }}">

                                <select class="form-select mx-2" aria-label="Status" id="stat" name="stat">
                                    <option value="-" {{ request()->get('stat') == '-' ? 'selected' : '' }}>All Status</option>
                                    <option value="Refund" {{ request()->get('stat') == 'Refund' ? 'selected' : '' }}>Refund</option>
                                    <option value="Done" {{ request()->get('stat') == 'Done' ? 'selected' : '' }}>Done</option>
                                    <option value="On Progress" {{ request()->get('stat') == 'On Progress' ? 'selected' : '' }}>On Progress</option>
                                </select>
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
                            <table class="table table-sm dt-responsive nowrap mt-2" id="scheduleTable" width="100%"
                                cellspacing="0">
                                <thead class="thead-light">
                                    <tr class="text-center">
                                        <th>No</th>
                                        <th class="sticky-col-header" style="background-color: white">CA No</th>
                                        <th>Type</th>
                                        <th>Name</th>
                                        <th>Company</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Total CA</th>
                                        <th>Total Declaration</th>
                                        <th>Balance</th>
                                        <th>Request</th>
                                        <th>Declaration</th>
                                        <th>Status CA</th>
                                        <th>Actions</th>
                                        <th>Export</th>
                                        <th>Delete</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($ca_transactions as $ca_transaction)
                                        <tr>
                                            <td class="text-center">{{ $loop->index + 1 }}</td>
                                            <td style="background-color: white;" class="sticky-col">{{ $ca_transaction->no_ca }}</td>
                                            @if ($ca_transaction->type_ca == 'dns')
                                                <td>Business Trip</td>
                                            @elseif($ca_transaction->type_ca == 'ndns')
                                                <td>Non Business Trip</td>
                                            @elseif($ca_transaction->type_ca == 'entr')
                                                <td>Entertainment</td>
                                            @endif

                                            <td>{{ $ca_transaction->employee->fullname }}</td>
                                            <td>{{ $ca_transaction->contribution_level_code }}</td>
                                            <td>{{ date('j M Y', strtotime($ca_transaction->formatted_start_date)) }}</td>
                                            <td>{{ date('j M Y', strtotime($ca_transaction->formatted_end_date)) }}</td>
                                            <td>Rp. {{ number_format($ca_transaction->total_ca) }}</td>
                                            <td>Rp. {{ number_format($ca_transaction->total_real) }}</td>
                                            <td>
                                                @if ($ca_transaction->total_cost < 0)
                                                    <span class="text-danger">Rp. -{{ number_format(abs($ca_transaction->total_cost)) }}</span>
                                                @else
                                                    <span class="text-success">Rp. {{ number_format($ca_transaction->total_cost) }}</span>
                                                @endif
                                            </td>
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
                                                ($ca_transaction->ca_status == 'On Progress' ? 'secondary' : 'default')) }}" title="{{$ca_transaction->paid_date}}">
                                                    {{ $ca_transaction->ca_status }}
                                                </p>
                                            </td>
                                            <!-- Button Action -->
                                            <td class="text-left">
                                                @if(($ca_transaction->approval_sett != '' || $ca_transaction->approval_sett == 'Pending' || $ca_transaction->approval_sett == 'Approved' ) && $ca_transaction->approval_status != 'Rejected' && $ca_transaction->approval_status != 'Draft' && ($ca_transaction->approval_extend == 'Pending' || $ca_transaction->approval_extend == 'Approved' && $ca_transaction->approval_sett !== 'Rejected'))
                                                    <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" title="Approval Deklarasi Update" data-bs-target="#approvalDecExtModal"
                                                        data-type="{{ $ca_transaction->type_ca }}"
                                                        data-total="{{ number_format($ca_transaction->total_ca, 0, ',', '.') }}"
                                                        data-id="{{ $ca_transaction->id }}"
                                                        data-no="{{ $ca_transaction->no_ca }}"
                                                        data-sppd="{{ $ca_transaction->no_sppd }}"
                                                        data-sett="{{ $ca_transaction->approval_sett }}"
                                                        data-status="{{ $ca_transaction->approval_status }}"
                                                        data-start-date="{{ $ca_transaction->start_date }}"
                                                        data-end-date="{{ $ca_transaction->end_date }}"
                                                        data-total-days="{{ $ca_transaction->total_days }}"
                                                        data-total-amount="{{ $ca_transaction->total_cost }}"
                                                        @foreach ($ca_extend as $ext)
                                                            data-ext-end="{{ $ext->ext_end_date }}"
                                                            data-ext-total="{{ $ext->ext_total_days }}"
                                                            data-ext-reason="{{ $ext->reason_extend }}"
                                                        @endforeach>
                                                        <i class="bi bi-list-check"></i>
                                                    </button>
                                                @elseif($ca_transaction->approval_extend == 'Pending' || $ca_transaction->approval_extend == 'Approved')
                                                    <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" title="Approval Extend Update" data-bs-target="#approvalExtModal"
                                                        data-type="{{ $ca_transaction->type_ca }}"
                                                        data-total="{{ number_format($ca_transaction->total_ca, 0, ',', '.') }}"
                                                        data-id="{{ $ca_transaction->id }}"
                                                        data-no="{{ $ca_transaction->no_ca }}"
                                                        data-sppd="{{ $ca_transaction->no_sppd }}"
                                                        data-sett="{{ $ca_transaction->approval_sett }}"
                                                        data-status="{{ $ca_transaction->approval_status }}"
                                                        data-start-date="{{ $ca_transaction->start_date }}"
                                                        data-end-date="{{ $ca_transaction->end_date }}"
                                                        data-total-days="{{ $ca_transaction->total_days }}"
                                                        data-total-amount="{{ $ca_transaction->total_cost }}"
                                                        @foreach ($ca_extend as $ext)
                                                            data-ext-end="{{ $ext->ext_end_date }}"
                                                            data-ext-total="{{ $ext->ext_total_days }}"
                                                            data-ext-reason="{{ $ext->reason_extend }}"
                                                        @endforeach>
                                                        <i class="bi bi-list-check"></i>
                                                    </button>
                                                @elseif(($ca_transaction->approval_status == 'Pending') ||
                                                    ($ca_transaction->approval_status == 'Approved' &&
                                                    ($ca_transaction->approval_sett == '' || $ca_transaction->approval_sett == 'Draft' || $ca_transaction->approval_sett == 'Rejected')))
                                                    <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" title="Approval Update" data-bs-target="#approvalModal"
                                                        data-type="{{ $ca_transaction->type_ca }}"
                                                        data-total="{{ number_format($ca_transaction->total_ca, 0, ',', '.') }}"
                                                        data-id="{{ $ca_transaction->id }}"
                                                        data-no="{{ $ca_transaction->no_ca }}"
                                                        data-sppd="{{ $ca_transaction->no_sppd }}"
                                                        data-sett="{{ $ca_transaction->approval_sett }}"
                                                        data-status="{{ $ca_transaction->approval_status }}"
                                                        data-start-date="{{ $ca_transaction->start_date }}"
                                                        data-end-date="{{ $ca_transaction->end_date }}"
                                                        data-total-days="{{ $ca_transaction->total_days }}">
                                                        <i class="bi bi-list-check"></i>
                                                    </button>
                                                @elseif(($ca_transaction->approval_sett == '' || $ca_transaction->approval_sett == 'Pending' || $ca_transaction->approval_sett == 'Approved') && $ca_transaction->approval_status != 'Rejected' && $ca_transaction->approval_status != 'Draft')
                                                    <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" title="Approval Declaration Update" data-bs-target="#approvalDecModal"
                                                        data-type="{{ $ca_transaction->type_ca }}"
                                                        data-total="{{ number_format($ca_transaction->total_ca, 0, ',', '.') }}"
                                                        data-id="{{ $ca_transaction->id }}"
                                                        data-no="{{ $ca_transaction->no_ca }}"
                                                        data-sppd="{{ $ca_transaction->no_sppd }}"
                                                        data-sett="{{ $ca_transaction->approval_sett }}"
                                                        data-status="{{ $ca_transaction->approval_status }}"
                                                        data-start-date="{{ $ca_transaction->start_date }}"
                                                        data-end-date="{{ $ca_transaction->end_date }}"
                                                        data-total-days="{{ $ca_transaction->total_days }}">
                                                        <i class="bi bi-list-check"></i>
                                                    </button>
                                                @endif
                                                @if($ca_transaction->approval_status=='Approved' && $ca_transaction->ca_status<>'Done')
                                                    <button type="button" class="btn btn-outline-warning" data-bs-toggle="modal" title="Status Update" data-bs-target="#statusModal"
                                                        data-id="{{ $ca_transaction->id }}"
                                                        data-status="{{ $ca_transaction->ca_status }}"
                                                        data-no="{{ $ca_transaction->no_ca }}"
                                                        data-appsett="{{ $ca_transaction->approval_sett }}"
                                                        data-datereq="{{ $ca_transaction->date_required }}"
                                                        data-capaiddate="{{ $ca_transaction->ca_paid_date }}"
                                                        data-paiddate="{{ $ca_transaction->paid_date }}">
                                                        <i class="ri-file-edit-line"></i>
                                                    </button>
                                                @endif
                                            </td>
                                            <!-- Button Export -->
                                            <td class="text-center">
                                                @if($ca_transaction->approval_status != 'Draft' && $ca_transaction->approval_status != 'Rejected')
                                                    <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal"  data-bs-target="#exportModal"
                                                        data-id="{{ $ca_transaction->id }}"
                                                        data-status="{{ $ca_transaction->approval_sett }}"
                                                        data-no="{{ $ca_transaction->no_ca }}"
                                                        title="Print">
                                                        <i class="bi bi-file-earmark-arrow-down"></i>
                                                    </button>
                                                @endif
                                            </td>
                                            <!-- Button Delete -->
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

    @include('hcis.reimbursements.cashadv.navigation.modalCashadv')

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/2.1.3/js/dataTables.min.js"></script>
@endsection
@section('script')
    {{-- @vite(['resources/js/pages/demo.form-advanced.js']) --}}

    <!-- Include jQuery -->
    {{-- <script src="{{ asset('vendor/bootstrap/js/jquery-3.6.0.min.js') }}"></script> --}}

    <!-- Include Bootstrap Date Range Picker -->
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/min/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <script src="{{ asset('/js/cashAdvanced/adminPage.js') }}"></script>
@endsection
@push('scripts')
    <script>
        function updateDropdownText(element, type) {
            document.getElementById('dropdownButton').textContent = element.textContent;

            if (type === 'start_date') {
                document.getElementById('start_date').style.display = 'block';
                document.getElementById('end_date').style.display = 'block';
                document.getElementById('from_date').style.display = 'none';
                document.getElementById('until_date').style.display = 'none';
                document.getElementById('from_date').value = '';
                document.getElementById('until_date').value = '';
            } else if (type === 'create_date') {
                document.getElementById('start_date').style.display = 'none';
                document.getElementById('end_date').style.display = 'none';
                document.getElementById('from_date').style.display = 'block';
                document.getElementById('until_date').style.display = 'block';
                document.getElementById('start_date').value = '';
                document.getElementById('end_date').value = '';
            }
        }
    </script>
    @if (session('success') && session('refresh'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var successMessage = "{{ session('success') }}";

                // Show the success message in a SweetAlert popup
                Swal.fire({
                    title: "Success!",
                    text: successMessage,
                    icon: "success",
                    confirmButtonColor: "#9a2a27",
                    confirmButtonText: "Ok",
                }).then((result) => {
                    if (result.isConfirmed) {
                        //window.location.reload();
                    }
                });
            });
        </script>
    @endif
    <script>
        function redirectToExportExcel() {
            const route = "{{ route('exportca.excel') }}";

            const startDate = document.getElementById("start_date").value;
            const endDate = document.getElementById("end_date").value;
            const fromDate = document.getElementById("from_date").value;
            const untilDate = document.getElementById("until_date").value;
            const stat = document.getElementById("stat").value;

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

            const fromDateInput = document.createElement("input");
            fromDateInput.type = "hidden";
            fromDateInput.name = "from_date";
            fromDateInput.value = fromDate;

            const untilDateInput = document.createElement("input");
            untilDateInput.type = "hidden";
            untilDateInput.name = "until_date";
            untilDateInput.value = untilDate;

            const statInput = document.createElement("input");
            statInput.type = "hidden";
            statInput.name = "stat";
            statInput.value = stat;

            form.appendChild(startDateInput);
            form.appendChild(endDateInput);
            form.appendChild(fromDateInput);
            form.appendChild(untilDateInput);
            form.appendChild(statInput);

            // Append the form to the body and submit it
            document.body.appendChild(form);
            form.submit();
        }

        // Event listener untuk menangkap date range yang dipilih
        $("#singledaterange").on("apply.daterangepicker", function (ev, picker) {
            var startDate = picker.startDate.format("YYYY-MM-DD");
            var endDate = picker.endDate.format("YYYY-MM-DD");

            // Panggil fungsi untuk mendapatkan data yang difilter
            filterTableByDateRange(startDate, endDate);
        });

        // Fungsi untuk mengirimkan tanggal yang dipilih ke server dan memperbarui tabel
        function filterTableByDateRange(startDate, endDate) {
            $.ajax({
                url: '{{ route("cashadvanced.admin") }}', // Route yang sudah Anda buat
                type: "GET",
                data: {
                    start_date: startDate,
                    end_date: endDate,
                },
                success: function (response) {
                    // Perbarui tabel dengan data yang difilter
                    // $('#scheduleTable tbody').html(response);
                    // $('#tableFilter').html(response);
                },
                error: function (xhr) {
                    // console.error(xhr);
                },
            });
        }
        //script modal

    </script>

    {{-- @if (session('refresh'))
        <script>
            // Refresh the page after 1 seconds
            setTimeout(function(){
                window.location.reload();
            }, 1000);
        </script>
    @endif --}}
@endpush

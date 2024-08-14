@extends('layouts_.vertical', ['page_title' => 'Business Trip'])

@section('css')
    <style>
        .breadcrumb-item+.breadcrumb-item::before {
            font-size: 28px !important;
            vertical-align: middle !important;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <!-- Breadcrumb Navigation -->
            <div class="col-md-6 mt-3">
                <div class="page-title-box d-flex align-items-center">
                    <ol class="breadcrumb mb-0" style="display: flex; align-items: center;">
                        <li class="breadcrumb-item">
                            <a href="/reimbursements" style="font-size: 32px; text-decoration: none; color: #007bff;">
                                <i class="bi bi-arrow-left" style="font-size: 32px;"></i>
                            </a>
                        </li>
                        <li class="breadcrumb-item" style="font-size: 24px;">{{ $parentLink }}</li>
                        <li class="breadcrumb-item active" style="font-size: 24px;">{{ $link }}</li>
                    </ol>
                </div>
            </div>

            <!-- Add Data Button -->
            <div class="col-md-6 mt-4 text-end">
                <a href="/businessTrip/form/add" class="btn btn-outline-primary rounded-pill" style="font-size: 18px">
                    <i class="bi bi-plus-circle"></i> Add Data
                </a>
            </div>
        </div>
    </div>


    <div class="card">
        <div class="card-body">
            <form class="date-range mb-2" method="GET" action="{{ route('businessTrip-filterDate') }}">
                <div class="row align-items-end">
                    <h3 class="card-title">Data SPPD</h3>
                    <div class="col-md-5">
                        <label for="start-date" class="mb-2">Departure Date:</label>
                        <input type="date" id="start-date" name="start-date" class="form-control"
                            value="{{ request()->query('start-date') }}">
                    </div>
                    <div class="col-md-5">
                        <label for="end-date" class="mb-2">To:</label>
                        <input type="date" id="end-date" name="end-date" class="form-control"
                            value="{{ request()->query('end-date') }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary rounded-pill w-100">Find</button>
                    </div>
                </div>
            </form>

            <div class="row">
                <div class="col-md-12">
                    <div class="card shadow mb-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h3 class="card-title">{{ $link }}</h3>
                                <div class="input-group" style="width: 30%;">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-white border-dark-subtle"><i
                                                class="ri-search-line"></i></span>
                                    </div>
                                    <input type="text" name="customsearch" id="customsearch"
                                        class="form-control w-  border-dark-subtle border-left-0" placeholder="Search.."
                                        aria-label="search" aria-describedby="search">
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-sm table-hover" id="scheduleTable" width="100%" cellspacing="0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>No</th>
                                            <th>No SPPD</th>
                                            <th>Destination</th>
                                            <th>Start</th>
                                            <th>End</th>
                                            <th>CA</th>
                                            <th>Ticket</th>
                                            <th>Hotel</th>
                                            <th>Taxi</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @foreach ($sppd as $idx => $n)
                                            <tr>
                                                <th scope="row" style="text-align: center;">
                                                    {{ $loop->iteration }}
                                                </th>
                                                <td>{{ $n->no_sppd }}</td>
                                                <td>{{ $n->tujuan }}</td>
                                                <td>{{ \Carbon\Carbon::parse($n->mulai)->format('d-m-Y') }}</td>
                                                <td>{{ \Carbon\Carbon::parse($n->kembali)->format('d-m-Y') }}</td>
                                                <td style="text-align: center">
                                                    @if ($n->ca == 'Ya' && isset($caTransactions[$n->no_sppd]))
                                                        <a class="text-info btn-detail" data-toggle="modal"
                                                            data-target="#detailModal" style="cursor: pointer"
                                                            data-ca="{{ json_encode([
                                                                'No. CA' => $caTransactions[$n->no_sppd]->no_ca,
                                                                'No. SPPD' => $caTransactions[$n->no_sppd]->no_sppd,
                                                                'Unit' => $caTransactions[$n->no_sppd]->unit,
                                                                'Destination' => $caTransactions[$n->no_sppd]->destination,
                                                                'CA Total' => $caTransactions[$n->no_sppd]->total_ca,
                                                                'Total Real' => $caTransactions[$n->no_sppd]->total_real,
                                                                'Total Cost' => $caTransactions[$n->no_sppd]->total_cost,
                                                                'Start' => date('d-m-Y', strtotime($caTransactions[$n->no_sppd]->start_date)),
                                                                'End' => date('d-m-Y', strtotime($caTransactions[$n->no_sppd]->end_date)),
                                                            ]) }}"><u>Detail</u></a>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td style="text-align: center">
                                                    @if ($n->tiket == 'Ya' && isset($tickets[$n->no_sppd]))
                                                        <a class="text-info btn-detail" data-toggle="modal"
                                                            data-target="#detailModal" style="cursor: pointer"
                                                            data-tiket="{{ json_encode([
                                                                'No. Ticket' => $tickets[$n->no_sppd]->no_tkt,
                                                                'No. SPPD' => $tickets[$n->no_sppd]->no_sppd,
                                                                'Unit' => $tickets[$n->no_sppd]->unit,
                                                                'Gender' => $tickets[$n->no_sppd]->jk_tkt,
                                                                // 'NP Ticket' => $tickets[$n->no_sppd]->np_tkt,
                                                                'No. KTP' => $tickets[$n->no_sppd]->noktp_tkt,
                                                                'Phone No.' => $tickets[$n->no_sppd]->tlp_tkt,
                                                                'From' => $tickets[$n->no_sppd]->dari_tkt,
                                                                'To' => $tickets[$n->no_sppd]->ke_tkt,
                                                                'Depature Date' => date('d-m-Y', strtotime($tickets[$n->no_sppd]->tgl_brkt_tkt)),
                                                                'Time' => !empty($tickets[$n->no_sppd]->jam_brkt_tkt)
                                                                    ? date('H:i', strtotime($tickets[$n->no_sppd]->jam_brkt_tkt))
                                                                    : 'No Data',
                                                                'Return Date' => isset($tickets[$n->no_sppd]->tgl_plg_tkt)
                                                                    ? date('d-m-Y', strtotime($tickets[$n->no_sppd]->tgl_plg_tkt))
                                                                    : 'No Data',
                                                                'Return Time' => !empty($tickets[$n->no_sppd]->jam_plg_tkt)
                                                                    ? date('H:i', strtotime($tickets[$n->no_sppd]->jam_plg_tkt))
                                                                    : 'No Data',
                                                            ]) }}"><u>Detail</u></a>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td style="text-align: center">
                                                    @if ($n->hotel == 'Ya' && isset($hotel[$n->no_sppd]))
                                                        <a class="text-info btn-detail" data-toggle="modal"
                                                            data-target="#detailModal" style="cursor: pointer"
                                                            data-hotel="{{ json_encode([
                                                                'No. Hotel' => $hotel[$n->no_sppd]->no_htl,
                                                                'No. SPPD' => $hotel[$n->no_sppd]->no_sppd,
                                                                'Unit' => $hotel[$n->no_sppd]->unit,
                                                                'Hotel Name' => $hotel[$n->no_sppd]->nama_htl,
                                                                'Location' => $hotel[$n->no_sppd]->lokasi_htl,
                                                                'Room' => $hotel[$n->no_sppd]->jmlkmr_htl,
                                                                'Bed' => $hotel[$n->no_sppd]->bed_htl,
                                                                'Check In' => date('d-m-Y', strtotime($hotel[$n->no_sppd]->tgl_masuk_htl)),
                                                                'Check Out' => date('d-m-Y', strtotime($hotel[$n->no_sppd]->tgl_keluar_htl)),
                                                                'Total Days' => $hotel[$n->no_sppd]->total_hari,
                                                            ]) }}"><u>Detail</u></a>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td style="text-align: center">
                                                    @if ($n->taksi == 'Ya' && isset($taksi[$n->no_sppd]))
                                                        <a class="text-info btn-detail" data-toggle="modal"
                                                            data-target="#detailModal" style="cursor: pointer"
                                                            data-taksi="{{ json_encode([
                                                                'No. Voucher Taxi' => $taksi[$n->no_sppd]->no_vt,
                                                                'No. SPPD' => $taksi[$n->no_sppd]->no_sppd,
                                                                'Unit' => $taksi[$n->no_sppd]->unit,
                                                                'Nominal' => 'Rp ' . number_format($taksi[$n->no_sppd]->nominal_vt, 0, ',', '.'),
                                                                'Keeper Voucher' => 'Rp' . number_format($taksi[$n->no_sppd]->keeper_vt, 0, ',', '.'),
                                                            ]) }}"><u>Detail<u></a>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    <p type="button"
                                                        class="btn btn-sm rounded-pill btn-{{ $n->status == 'Approved'
                                                            ? 'success'
                                                            : ($n->status == 'Return' || $n->status == 'return/refunds'
                                                                ? 'danger'
                                                                : (in_array($n->status, ['Pending L1', 'Pending L2', 'Pending Declaration', 'Waiting Submitted'])
                                                                    ? 'warning'
                                                                    : (in_array($n->status, ['Doc Accepted', 'verified'])
                                                                        ? 'primary'
                                                                        : 'secondary'))) }}"
                                                        style="pointer-events: none">
                                                        {{ $n->status }}
                                                    </p>
                                                </td>
                                                <td>
                                                    <a href="{{ route('export', ['id' => $n->id, 'types' => 'sppd,ca,tiket,hotel,taksi']) }}"
                                                        class="btn btn-outline-info rounded-pill">
                                                        <i class="bi bi-download"></i>
                                                    </a>

                                                    @php
                                                        $today = \Carbon\Carbon::today()->format('Y-m-d');
                                                    @endphp
                                                    @if ($n->kembali < $today && $n->status == 'Approved')
                                                        <form method="GET"
                                                            action="/businessTrip/deklarasi/{{ $n->id }}"
                                                            style="display: inline-block;">
                                                            <button type="submit"
                                                                class="btn btn-outline-success rounded-pill"
                                                                data-toggle="tooltip" title="Deklarasi">
                                                                <i class="bi bi-card-checklist"></i>
                                                            </button>
                                                        </form>
                                                    @else
                                                        {{-- <form method="GET"
                                                            action="/businessTrip/form/update/{{ $n->id }}"
                                                            style="display: inline-block;">
                                                            <button type="submit"
                                                                class="btn btn-outline-warning rounded-pill my-1"
                                                                {{ $n->status === 'Diterima' ? 'disabled' : '' }}
                                                                data-toggle="tooltip" title="Edit">
                                                                <i class="bi bi-pencil-square"></i>
                                                            </button>
                                                        </form> --}}
                                                        <form id="deleteForm_{{ $n->id }}" method="POST"
                                                            action="/businessTrip/delete/{{ $n->id }}"
                                                            style="display: inline-block;">
                                                            @csrf
                                                            @method('DELETE')

                                                            <button type="button"
                                                                class="btn btn-outline-danger rounded-pill"
                                                                onclick="confirmDelete('{{ $n->id }}')"
                                                                {{ $n->status === 'Diterima' ? 'disabled' : '' }}>
                                                                <i class="bi bi-trash-fill"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detail Modal -->
                <div class="modal fade" id="detailModal" tabindex="-1" role="dialog"
                    aria-labelledby="detailModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-xl" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="detailModalLabel">Detail Information</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                                    style="border: 0px; border-radius:4px;">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <h6 id="detailTypeHeader" class="mb-3"></h6>
                                <div id="detailContent"></div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>

                <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
                <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
                <script src="https://cdn.datatables.net/2.1.3/js/dataTables.min.js"></script>
                <script>
                    //    let table = new DataTable('#scheduleTable');

                    function getDate() {
                        var today = new Date();
                        var dd = today.getDate();
                        var mm = today.getMonth() + 1; // January is 0!
                        var yyyy = today.getFullYear();

                        if (dd < 10) {
                            dd = '0' + dd;
                        }
                        if (mm < 10) {
                            mm = '0' + mm;
                        }

                        // Correct date format for input fields
                        var formattedToday = yyyy + '-' + mm + '-' + dd;
                        console.log(formattedToday);

                        var startDateElement = document.getElementById("start-date");
                        var endDateElement = document.getElementById("end-date");

                        // Only set the value if it's not already set
                        if (!startDateElement.value) {
                            startDateElement.value = formattedToday;
                        }
                        if (!endDateElement.value) {
                            endDateElement.value = formattedToday;
                        }

                        document.addEventListener('DOMContentLoaded', function() {
                            getDate();
                        });

                        document.getElementById('recordsPerPage').addEventListener('change', function() {
                            const perPage = this.value;
                            const currentPage = new URLSearchParams(window.location.search).get('page') || 1;
                            window.location.search = `?per_page=${perPage}&page=${currentPage}`;
                        });

                        function confirmDelete(id) {
                            if (confirm("Are you sure you want to delete this item?")) {
                                document.getElementById('deleteForm_' + id).submit();
                            }
                        }

                    }

                    // Ensure the DOM is fully loaded before manipulating it
                    document.addEventListener('DOMContentLoaded', function() {
                        getDate();
                    });

                    function confirmDelete(id) {
                        if (confirm("Are you sure you want to delete this item?")) {
                            document.getElementById('deleteForm_' + id).submit();
                        }
                    }

                    $(document).ready(function() {
                        $('.btn-detail').click(function() {
                            var ca = $(this).data('ca');
                            var tiket = $(this).data('tiket');
                            var hotel = $(this).data('hotel');
                            var taksi = $(this).data('taksi');

                            function createTableHtml(data) {
                                var tableHtml = '<table class="table table-sm"><thead><tr>';
                                var isArray = Array.isArray(data) && data.length > 0;

                                // Assuming all objects in the data array have the same keys, use the first object to create headers
                                if (isArray) {
                                    for (var key in data[0]) {
                                        if (data[0].hasOwnProperty(key)) {
                                            tableHtml += '<th>' + key + '</th>';
                                        }
                                    }
                                } else if (typeof data === 'object') {
                                    // If data is a single object, create headers from its keys
                                    for (var key in data) {
                                        if (data.hasOwnProperty(key)) {
                                            tableHtml += '<th>' + key + '</th>';
                                        }
                                    }
                                }

                                tableHtml += '</tr></thead><tbody>';

                                // Loop through each item in the array and create a row for each
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
                                    // If data is a single object, create a single row
                                    tableHtml += '<tr>';
                                    for (var key in data) {
                                        if (data.hasOwnProperty(key)) {
                                            tableHtml += '<td>' + data[key] + '</td>';
                                        }
                                    }
                                    tableHtml += '</tr>';
                                }

                                tableHtml += '</tbody></table>';
                                return tableHtml;
                            }

                            $('#detailTypeHeader').text('');
                            $('#detailContent').empty();

                            try {
                                if (ca && ca !== 'undefined') {
                                    var caData = typeof ca === 'string' ? JSON.parse(ca) : ca;
                                    $('#detailTypeHeader').text('CA Detail');
                                    $('#detailContent').html(createTableHtml(caData));
                                } else if (tiket && tiket !== 'undefined') {
                                    var tiketData = typeof tiket === 'string' ? JSON.parse(tiket) : tiket;
                                    $('#detailTypeHeader').text('Ticket Detail');
                                    $('#detailContent').html(createTableHtml(tiketData));
                                } else if (hotel && hotel !== 'undefined') {
                                    var hotelData = typeof hotel === 'string' ? JSON.parse(hotel) : hotel;
                                    $('#detailTypeHeader').text('Hotel Detail');
                                    $('#detailContent').html(createTableHtml(hotelData));
                                } else if (taksi && taksi !== 'undefined') {
                                    var taksiData = typeof taksi === 'string' ? JSON.parse(taksi) : taksi;
                                    $('#detailTypeHeader').text('Taxi Detail');
                                    $('#detailContent').html(createTableHtml(taksiData));
                                } else {
                                    $('#detailTypeHeader').text('No Data Available');
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
                </script>
            @endsection

@extends('layouts_.vertical', ['page_title' => 'Business Trip'])

@section('css')
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row mt">
            <div class="col-12 mt-2">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <a href="/reimbursements">
                            <i class="bi bi-arrow-left" style="width: 32px; height: 32px; font-size: 32px;"></i>
                        </a>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item" style="font-size: 24px;">{{ $parentLink }}</li>
                            <li class="breadcrumb-item active" style="font-size: 16px;">{{ $link }}</li>
                        </ol>
                    </div>
                    <a href="/businessTrip/form/add" class="btn btn-outline-primary rounded-pill">
                        <i class="bi bi-plus-circle"></i> Add Data
                    </a>
                </div>
            </div>
        </div>
    </div>
    {{-- <div class="row mb-3">
        <div class="col">
            <a href="/reimbursements" class="btn btn-primary rounded-pill">
                <i class="bi bi-caret-left-fill"></i> Back
            </a>
            <a href="/businessTrip/form/add" class="btn btn-outline-primary rounded-pill">
                <i class="bi bi-plus-circle"></i> Add Data
            </a>
        </div>
    </div> --}}

    <div class="card">
        <div class="card-body">
            <form class="date-range mb-3" method="GET" action="{{ route('businessTrip-filterDate') }}">
                <div class="row align-items-end">
                    <h3 class="card-title">Data SPPD</h3>
                    <div class="col-md-5">
                        <label for="start-date">Departure Date:</label>
                        <input type="date" id="start-date" name="start-date" class="form-control"
                            value="{{ request()->query('start-date') }}">
                    </div>
                    <div class="col-md-5">
                        <label for="end-date">To:</label>
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
                                        class="form-control w-  border-dark-subtle border-left-0" placeholder="search.."
                                        aria-label="search" aria-describedby="search">
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-sm table-hover dt-responsive nowrap" id="scheduleTable"
                                    width="100%" cellspacing="0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>No</th>
                                            <th>No SPPD</th>
                                            <th>Tujuan</th>
                                            <th>Mulai</th>
                                            <th>Kembali</th>
                                            <th>CA</th>
                                            <th>Ticket</th>
                                            <th>Hotel</th>
                                            <th>Taksi</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>



                                        @foreach ($sppd as $idx => $n)
                                            <tr>
                                                <th scope="row">{{ $sppd->firstItem() + $idx }}
                                                </th>
                                                <td>{{ $n->no_sppd }}</td>
                                                <td>{{ $n->tujuan }}</td>
                                                <td>{{ $n->mulai }}</td>
                                                <td>{{ $n->kembali }}</td>
                                                <td>
                                                    @if ($n->ca == 'Ya' && isset($caTransactions[$n->no_sppd]))
                                                        <button class="btn btn-info btn-sm btn-detail" data-toggle="modal"
                                                            data-target="#detailModal"
                                                            data-ca="{{ json_encode([
                                                                'No. CA' => $caTransactions[$n->no_sppd]->no_ca,
                                                                'No. SPPD' => $caTransactions[$n->no_sppd]->no_sppd,
                                                                'Unit' => $caTransactions[$n->no_sppd]->unit,
                                                                'Destination' => $caTransactions[$n->no_sppd]->destination,
                                                                'CA Total' => $caTransactions[$n->no_sppd]->total_ca,
                                                                'Total Real' => $caTransactions[$n->no_sppd]->total_real,
                                                                'Total Cost' => $caTransactions[$n->no_sppd]->total_cost,
                                                                'Start' => $caTransactions[$n->no_sppd]->start_date,
                                                                'End' => $caTransactions[$n->no_sppd]->end_date,
                                                            ]) }}">Detail</button>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($n->tiket == 'Ya' && isset($tickets[$n->no_sppd]))
                                                        <button class="btn btn-info btn-sm btn-detail" data-toggle="modal"
                                                            data-target="#detailModal" data-ca=""
                                                            data-tiket="{{ json_encode([
                                                                'No. Tiket' => $tickets[$n->no_sppd]->no_tkt,
                                                                'No. SPPD' => $tickets[$n->no_sppd]->no_sppd,
                                                                'Unit' => $tickets[$n->no_sppd]->unit,
                                                                'Gender' => $tickets[$n->no_sppd]->jk_tkt,
                                                                'np_tkt' => $tickets[$n->no_sppd]->np_tkt,
                                                                'No. KTP' => $tickets[$n->no_sppd]->noktp_tkt,
                                                                'Phone Number' => $tickets[$n->no_sppd]->tlp_tkt,
                                                                'From' => $tickets[$n->no_sppd]->dari_tkt,
                                                                'To' => $tickets[$n->no_sppd]->ke_tkt,
                                                            ]) }}">Detail</button>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($n->hotel == 'Ya' && isset($hotel[$n->no_sppd]))
                                                        <button class="btn btn-info btn-sm btn-detail" data-toggle="modal"
                                                            data-target="#detailModal"
                                                            data-hotel="{{ json_encode([
                                                                'No. Hotel' => $hotel[$n->no_sppd]->no_htl,
                                                                'No. SPPD' => $hotel[$n->no_sppd]->no_sppd,
                                                                'Unit' => $hotel[$n->no_sppd]->unit,
                                                                'Hotel Name' => $hotel[$n->no_sppd]->nama_htl,
                                                                'Location' => $hotel[$n->no_sppd]->lokasi_htl,
                                                                'Room' => $hotel[$n->no_sppd]->jmlkmr_htl,
                                                                'Bed' => $hotel[$n->no_sppd]->bed_htl,
                                                                'Check In' => $hotel[$n->no_sppd]->tgl_masuk_htl,
                                                                'Check Out' => $hotel[$n->no_sppd]->tgl_keluar_htl,
                                                                'Total Days' => $hotel[$n->no_sppd]->total_hari,
                                                            ]) }}">Detail</button>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($n->taksi == 'Ya' && isset($taksi[$n->no_sppd]))
                                                        <button class="btn btn-info btn-sm btn-detail" data-toggle="modal"
                                                            data-target="#detailModal"
                                                            data-taksi="{{ json_encode([
                                                                'No. Voucher Taxi' => $taksi[$n->no_sppd]->no_vt,
                                                                'No. SPPD' => $taksi[$n->no_sppd]->no_sppd,
                                                                'Unit' => $taksi[$n->no_sppd]->unit,
                                                                'Nominal' => $taksi[$n->no_sppd]->nominal_vt,
                                                                'Keeper Voucher' => $taksi[$n->no_sppd]->keeper_vt,
                                                            ]) }}">Detail</button>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    <p type="button"
                                                        class="btn btn-sm rounded-pill btn-{{ $n->status == 'Diterima' ? 'success' : ($n->status == 'Ditolak' ? 'danger' : 'warning') }}"
                                                        style="pointer-events: none">
                                                        {{ $n->status }}
                                                    </p>
                                                </td>
                                                <td>
                                                    <a href="{{ route('pdf', $n->id) }}" class="btn btn-outline-primary"
                                                        target="_blank">
                                                        <i class="bi bi-file-earmark-arrow-down"></i>
                                                    </a>
                                                    @php
                                                        $today = \Carbon\Carbon::today()->format('Y-m-d');
                                                    @endphp
                                                    @if ($n->kembali <= $today && $n->status == 'Diterima')
                                                        <form method="GET"
                                                            action="/businessTrip/deklarasi/{{ $n->id }}"
                                                            style="display: inline-block;">
                                                            <button type="submit"
                                                                class="btn btn-outliine-success rounded-pill"
                                                                data-toggle="tooltip" title="Edit">
                                                                <i class="bi bi-card-checklist"></i>
                                                            </button>
                                                        </form>
                                                    @else
                                                        <form method="GET"
                                                            action="/businessTrip/form/update/{{ $n->id }}"
                                                            style="display: inline-block;">
                                                            <button type="submit" class="btn btn-outline-success"
                                                                {{ $n->status === 'Diterima' ? 'disabled' : '' }}
                                                                data-toggle="tooltip" title="Edit">
                                                                <i class="bi bi-pencil-square"></i>
                                                            </button>
                                                        </form>
                                                        <form id="deleteForm_{{ $n->id }}" method="POST"
                                                            action="/businessTrip/delete/{{ $n->id }}"
                                                            style="display: inline-block;">
                                                            @csrf
                                                            @method('DELETE')

                                                            <button type="button" class="btn btn-outline-danger"
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

                <!-- PDF Modal -->
                <div class="modal fade" id="pdfModal" tabindex="-1" aria-labelledby="pdfModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="pdfModalLabel">Download Files</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div id="loadingIndicator" style="display:none;">Loading...</div>
                                <div id="modalContent"></div>
                                <button class="download-button" data-id="1" data-type="sppd">Download SPPD</button>
                                <button class="download-button" data-id="1" data-type="ca">Download CA</button>
                                <button class="download-button" data-id="1" data-type="tiket">Download Ticket</button>
                                <button class="download-button" data-id="1" data-type="hotel">Download Hotel</button>
                                <button class="download-button" data-id="1" data-type="taksi">Download Taxi</button>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
                <form id="downloadForm" method="GET" style="display: none;">
                    @csrf
                    <input type="hidden" name="id" id="downloadId">
                    <input type="hidden" name="type" id="downloadType">
                </form>

                <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
                <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
                <script src="https://cdn.datatables.net/2.1.3/js/dataTables.min.js"></script>
                <script>
                    new DataTable('#btTable');

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

                        $(document).ready(function() {
                            $('.btn-detail').click(function() {
                                // Get all data
                                var ca = $(this).data('ca');
                                var tiket = $(this).data('tiket');
                                var hotel = $(this).data('hotel');
                                var taksi = $(this).data('taksi');

                                console.log('Button clicked');

                                // Function to create table HTML
                                function createTableHtml(data) {
                                    var tableHtml = '<table class="table"><thead><tr>';
                                    // Create table headers
                                    for (var key in data) {
                                        if (data.hasOwnProperty(key)) {
                                            tableHtml += '<th>' + key + '</th>';
                                        }
                                    }
                                    tableHtml += '</tr></thead><tbody>';
                                    // Create table rows
                                    var row = '<tr>';
                                    for (var key in data) {
                                        if (data.hasOwnProperty(key)) {
                                            row += '<td>' + data[key] + '</td>';
                                        }
                                    }
                                    row += '</tr>';
                                    tableHtml += row;
                                    tableHtml += '</tbody></table>';
                                    return tableHtml;
                                }


                                // Clear previous content
                                $('#detailTypeHeader').text('');
                                $('#detailContent').empty();

                                // Check and display data based on which button was clicked
                                if (ca && ca !== 'undefined') {
                                    try {
                                        var caData = typeof ca === 'string' ? JSON.parse(ca) : ca;
                                        console.log('CA data:', caData);
                                        $('#detailTypeHeader').text('CA Detail');
                                        $('#detailContent').html(createTableHtml(caData));
                                    } catch (e) {
                                        console.error('Error parsing CA data:', e);
                                        $('#detailContent').html('<p>Error loading CA data</p>');
                                    }
                                } else if (tiket && tiket !== 'undefined') {
                                    try {
                                        var tiketData = typeof tiket === 'string' ? JSON.parse(tiket) : tiket;
                                        console.log('Ticket data:', tiketData);
                                        $('#detailTypeHeader').text('Ticket Detail');
                                        $('#detailContent').html(createTableHtml(tiketData));
                                    } catch (e) {
                                        console.error('Error parsing Ticket data:', e);
                                        $('#detailContent').html('<p>Error loading Ticket data</p>');
                                    }
                                } else if (hotel && hotel !== 'undefined') {
                                    try {
                                        var hotelData = typeof hotel === 'string' ? JSON.parse(hotel) : hotel;
                                        console.log('Hotel data:', hotelData);
                                        $('#detailTypeHeader').text('Hotel Detail');
                                        $('#detailContent').html(createTableHtml(hotelData));
                                    } catch (e) {
                                        console.error('Error parsing Hotel data:', e);
                                        $('#detailContent').html('<p>Error loading Hotel data</p>');
                                    }
                                } else if (taksi && taksi !== 'undefined') {
                                    try {
                                        var taksiData = typeof taksi === 'string' ? JSON.parse(taksi) : taksi;
                                        console.log('Taxi data:', taksiData);
                                        $('#detailTypeHeader').text('Taxi Detail');
                                        $('#detailContent').html(createTableHtml(taksiData));
                                    } catch (e) {
                                        console.error('Error parsing Taxi data:', e);
                                        $('#detailContent').html('<p>Error loading Taxi data</p>');
                                    }
                                } else {
                                    $('#detailTypeHeader').text('No Data Available');
                                    $('#detailContent').html('<p>No detail information available.</p>');
                                }

                                // Ensure the modal is shown
                                $('#detailModal').modal('show');
                            });

                            // Ensure backdrop is removed when modal is hidden
                            $('#detailModal').on('hidden.bs.modal', function() {
                                $('body').removeClass('modal-open').css({
                                    overflow: '',
                                    padding: ''
                                });
                                $('.modal-backdrop').remove();
                            });
                        });
                    }

                    // Ensure the DOM is fully loaded before manipulating it
                    document.addEventListener('DOMContentLoaded', function() {
                        getDate();
                    });

                    // document.getElementById('recordsPerPage').addEventListener('change', function() {
                    //     const perPage = this.value;
                    //     const currentPage = new URLSearchParams(window.location.search).get('page') || 1;
                    //     window.location.search = `?per_page=${perPage}&page=${currentPage}`;
                    // });

                    function confirmDelete(id) {
                        if (confirm("Are you sure you want to delete this item?")) {
                            document.getElementById('deleteForm_' + id).submit();
                        }
                    }

                    $(document).ready(function() {
                        $('.btn-detail').click(function() {
                            // Get all data
                            var ca = $(this).data('ca');
                            var tiket = $(this).data('tiket');
                            var hotel = $(this).data('hotel');
                            var taksi = $(this).data('taksi');

                            console.log('Button clicked');

                            // Function to create table HTML
                            function createTableHtml(data) {
                                var tableHtml = '<table class="table table-sm"><thead><tr>';
                                // Create table headers
                                for (var key in data) {
                                    if (data.hasOwnProperty(key)) {
                                        tableHtml += '<th>' + key + '</th>';
                                    }
                                }
                                tableHtml += '</tr></thead><tbody>';
                                // Create table rows
                                var row = '<tr>';
                                for (var key in data) {
                                    if (data.hasOwnProperty(key)) {
                                        row += '<td>' + data[key] + '</td>';
                                    }
                                }
                                row += '</tr>';
                                tableHtml += row;
                                tableHtml += '</tbody></table>';
                                return tableHtml;
                            }


                            // Clear previous content
                            $('#detailTypeHeader').text('');
                            $('#detailContent').empty();

                            // Check and display data based on which button was clicked
                            if (ca && ca !== 'undefined') {
                                try {
                                    var caData = typeof ca === 'string' ? JSON.parse(ca) : ca;
                                    console.log('CA data:', caData);
                                    $('#detailTypeHeader').text('CA Detail');
                                    $('#detailContent').html(createTableHtml(caData));
                                } catch (e) {
                                    console.error('Error parsing CA data:', e);
                                    $('#detailContent').html('<p>Error loading CA data</p>');
                                }
                            } else if (tiket && tiket !== 'undefined') {
                                try {
                                    var tiketData = typeof tiket === 'string' ? JSON.parse(tiket) : tiket;
                                    console.log('Ticket data:', tiketData);
                                    $('#detailTypeHeader').text('Ticket Detail');
                                    $('#detailContent').html(createTableHtml(tiketData));
                                } catch (e) {
                                    console.error('Error parsing Ticket data:', e);
                                    $('#detailContent').html('<p>Error loading Ticket data</p>');
                                }
                            } else if (hotel && hotel !== 'undefined') {
                                try {
                                    var hotelData = typeof hotel === 'string' ? JSON.parse(hotel) : hotel;
                                    console.log('Hotel data:', hotelData);
                                    $('#detailTypeHeader').text('Hotel Detail');
                                    $('#detailContent').html(createTableHtml(hotelData));
                                } catch (e) {
                                    console.error('Error parsing Hotel data:', e);
                                    $('#detailContent').html('<p>Error loading Hotel data</p>');
                                }
                            } else if (taksi && taksi !== 'undefined') {
                                try {
                                    var taksiData = typeof taksi === 'string' ? JSON.parse(taksi) : taksi;
                                    console.log('Taxi data:', taksiData);
                                    $('#detailTypeHeader').text('Taxi Detail');
                                    $('#detailContent').html(createTableHtml(taksiData));
                                } catch (e) {
                                    console.error('Error parsing Taxi data:', e);
                                    $('#detailContent').html('<p>Error loading Taxi data</p>');
                                }
                            } else {
                                $('#detailTypeHeader').text('No Data Available');
                                $('#detailContent').html('<p>No detail information available.</p>');
                            }

                            // Ensure the modal is shown
                            $('#detailModal').modal('show');
                        });

                        // Ensure backdrop is removed when modal is hidden
                        $('#detailModal').on('hidden.bs.modal', function() {
                            $('body').removeClass('modal-open').css({
                                overflow: '',
                                padding: ''
                            });
                            $('.modal-backdrop').remove();
                        });
                    });
                </script>
            @endsection

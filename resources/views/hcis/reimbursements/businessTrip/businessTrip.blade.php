@extends('layouts_.vertical', ['page_title' => 'Business Trip'])

@section('css')
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">{{ $parentLink }}</li>
                            <li class="breadcrumb-item active">{{ $link }}</li>
                        </ol>
                    </div>
                    <h4 class="page-title">{{ $link }}</h4>
                </div>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col">
                <a href="/reimbursements" class="btn btn-primary rounded-pill">
                    <i class="bi bi-caret-left-fill"></i> Back
                </a>
                <a href="/businessTrip/form/add" class="btn btn-outline-primary rounded-pill">
                    <i class="bi bi-plus-circle"></i> Add Data
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <form class="date-range mb-3" method="GET" action="{{ route('businessTrip-filterDate') }}">
                    <div class="row align-items-end">
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


                <div class="card mt-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h3 class="card-title">Data SPPD</h3>
                            <form class="input-group" method="GET" id="searchForm" action="/businessTrip/search"
                                style="width: 300px;">
                                <input name="q" type="text" class="form-control" placeholder="Search...">
                                <button class="btn btn-outline-secondary" type="submit">
                                    <i class="bi bi-search"></i>
                                </button>
                            </form>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover dt-responsive nowrap" id="scheduleTable" width="100%"
                                cellspacing="0">
                                <thead class="thead-light">
                                    <tr class="text-center">
                                        <th>No</th>
                                        {{-- <th>Nama</th> --}}
                                        {{-- <th>Divisi</th> --}}
                                        <th>No SPPD</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>CA</th>
                                        <th>Ticket</th>
                                        <th>Hotel</th>
                                        <th>Taksi</th>
                                        <th>Status</th>
                                        <th>Export</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($sppd as $idx => $n)
                                        <tr>
                                            <th scope="row">{{ $sppd->firstItem() + $idx }}
                                            </th>
                                            {{-- <td>{{ $n->nama }}</td>
                                            <td>{{ $n->divisi }}</td> --}}
                                            <td>{{ $n->no_sppd }}</td>
                                            <td>{{ $n->mulai }}</td>
                                            <td>{{ $n->kembali }}</td>
                                            <td>
                                                @if ($n->ca == 'Ya' && isset($caTransactions[$n->no_sppd]))
                                                    <button class="btn btn-secondary btn-sm" data-toggle="modal"
                                                        data-target="#detailModal"
                                                        data-ca="{{ json_encode([
                                                            'no_ca' => $caTransactions[$n->no_sppd]->no_ca,
                                                            'no_sppd' => $caTransactions[$n->no_sppd]->no_sppd,
                                                            'unit' => $caTransactions[$n->no_sppd]->unit,
                                                            'destination' => $caTransactions[$n->no_sppd]->destination,
                                                            'total_ca' => $caTransactions[$n->no_sppd]->total_ca,
                                                            'total_real' => $caTransactions[$n->no_sppd]->total_real,
                                                            'total_cost' => $caTransactions[$n->no_sppd]->total_cost,
                                                            'start_date' => $caTransactions[$n->no_sppd]->start_date,
                                                            'end_date' => $caTransactions[$n->no_sppd]->end_date,
                                                        ]) }}">Detail</button>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                @if ($n->tiket == 'Ya' && isset($tickets[$n->no_sppd]))
                                                    <button class="btn btn-secondary btn-sm" data-toggle="modal"
                                                        data-target="#detailModal" data-ca=""
                                                        data-tiket="{{ json_encode([
                                                            'no_tkt' => $tickets[$n->no_sppd]->no_tkt,
                                                            'no_sppd' => $tickets[$n->no_sppd]->no_sppd,
                                                            'unit' => $tickets[$n->no_sppd]->unit,
                                                            'jk_tkt' => $tickets[$n->no_sppd]->jk_tkt,
                                                            'np_tkt' => $tickets[$n->no_sppd]->np_tkt,
                                                            'noktp_tkt' => $tickets[$n->no_sppd]->noktp_tkt,
                                                            'tlp_tkt' => $tickets[$n->no_sppd]->tlp_tkt,
                                                            'dari_tkt' => $tickets[$n->no_sppd]->dari_tkt,
                                                            'ke_tkt' => $tickets[$n->no_sppd]->ke_tkt,
                                                        ]) }}">Detail</button>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                @if ($n->hotel == 'Ya' && isset($hotel[$n->no_sppd]))
                                                    <button class="btn btn-secondary btn-sm" data-toggle="modal"
                                                        data-target="#detailModal"
                                                        data-hotel="{{ json_encode([
                                                            'no_htl' => $hotel[$n->no_sppd]->no_htl,
                                                            'no_sppd' => $hotel[$n->no_sppd]->no_sppd,
                                                            'unit' => $hotel[$n->no_sppd]->unit,
                                                            'nama_htl' => $hotel[$n->no_sppd]->nama_htl,
                                                            'lokasi_htl' => $hotel[$n->no_sppd]->lokasi_htl,
                                                            'jmlkmr_htl' => $hotel[$n->no_sppd]->jmlkmr_htl,
                                                            'bed_htl' => $hotel[$n->no_sppd]->bed_htl,
                                                            'tgl_masuk_htl' => $hotel[$n->no_sppd]->tgl_masuk_htl,
                                                            'tgl_keluar_htl' => $hotel[$n->no_sppd]->tgl_keluar_htl,
                                                            'total_hari' => $hotel[$n->no_sppd]->total_hari,
                                                        ]) }}">Detail</button>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                @if ($n->taksi == 'Ya' && isset($taksi[$n->no_sppd]))
                                                    <button class="btn btn-secondary btn-sm" data-toggle="modal"
                                                        data-target="#detailModal"
                                                        data-taksi="{{ json_encode([
                                                            'no_vt' => $taksi[$n->no_sppd]->no_vt,
                                                            'no_sppd' => $taksi[$n->no_sppd]->no_sppd,
                                                            'unit' => $taksi[$n->no_sppd]->unit,
                                                            'nominal_vt' => $taksi[$n->no_sppd]->nominal_vt,
                                                            'keeper_vt' => $taksi[$n->no_sppd]->keeper_vt,
                                                        ]) }}">Detail</button>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                @if ($n->status == 'Approved')
                                                    <div class="badge p-1 bg-success rounded-pill">
                                                        Approved
                                                    </div>
                                                @elseif ($n->status == 'Pending')
                                                    <div class="badge p-1 bg-warning rounded-pill">
                                                        Pending
                                                    </div>
                                                @elseif ($n->status == 'Rejected')
                                                    <div class="badge p-1 bg-warning rounded-pill>
                                                        Rejected
                                                    </div>
                                                @endif
                                            </td>

                                            <td>
                                                <button type="button" class="btn btn-primary rounded-pill"
                                                    data-bs-toggle="modal" data-bs-target="#pdfModal"
                                                    data-id="{{ $n->id }}">
                                                    <i class="bi bi-file-earmark-arrow-down"></i>
                                                </button>
                                            </td>
                                            <td>
                                                @php
                                                    $today = \Carbon\Carbon::today()->format('Y-m-d');
                                                @endphp
                                                @if ($n->kembali <= $today && $n->status == 'Diterima')
                                                    <form method="GET"
                                                        action="/businessTrip/deklarasi/{{ $n->id }}"
                                                        style="display: inline-block;">
                                                        <button type="submit" class="btn btn-success rounded-pill"
                                                            data-toggle="tooltip" title="Edit">
                                                            <i class="bi bi-card-checklist"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <form method="GET"
                                                        action="/businessTrip/form/update/{{ $n->id }}"
                                                        style="display: inline-block;">
                                                        <button type="submit" class="btn btn-success rounded-pill"
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

                                                        <button type="button" class="btn btn-outline-danger rounded-pill"
                                                            onclick="confirmDelete('{{ $n->id }}')"
                                                            {{ $n->status === 'Diterima' ? 'disabled' : '' }}>
                                                            <i class="bi bi-trash-fill"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="14" class="text-center">No data available in table</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detail Modal -->
        <div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel"
            aria-hidden="true">
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
        <div class="modal fade" id="pdfModal" tabindex="-1" aria-labelledby="pdfModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="pdfModalLabel">Download Files</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
        <script>
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
            }

            // Ensure the DOM is fully loaded before manipulating it
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
                    var ca = $(this).data('ca');
                    var tiket = $(this).data('tiket');
                    var hotel = $(this).data('hotel');
                    var taksi = $(this).data('taksi');

                    function createTableHtml(data) {
                        var tableHtml = '<table class="table"><thead><tr>';
                        for (var key in data) {
                            if (data.hasOwnProperty(key)) {
                                tableHtml += '<th>' + key + '</th>';
                            }
                        }
                        tableHtml += '</tr></thead><tbody><tr>';
                        for (var key in data) {
                            if (data.hasOwnProperty(key)) {
                                tableHtml += '<td>' + data[key] + '</td>';
                            }
                        }
                        tableHtml += '</tr></tbody></table>';
                        return tableHtml;
                    }

                    $('#detailTypeHeader').text('');
                    $('#detailContent').empty();

                    if (ca && ca !== 'undefined') {
                        try {
                            var caData = typeof ca === 'string' ? JSON.parse(ca) : ca;
                            $('#detailTypeHeader').text('CA Detail');
                            $('#detailContent').html(createTableHtml(caData));
                        } catch (e) {
                            $('#detailContent').html('<p>Error loading CA data</p>');
                        }
                    } else if (tiket && tiket !== 'undefined') {
                        try {
                            var tiketData = typeof tiket === 'string' ? JSON.parse(tiket) : tiket;
                            $('#detailTypeHeader').text('Ticket Detail');
                            $('#detailContent').html(createTableHtml(tiketData));
                        } catch (e) {
                            $('#detailContent').html('<p>Error loading Ticket data</p>');
                        }
                    } else if (hotel && hotel !== 'undefined') {
                        try {
                            var hotelData = typeof hotel === 'string' ? JSON.parse(hotel) : hotel;
                            $('#detailTypeHeader').text('Hotel Detail');
                            $('#detailContent').html(createTableHtml(hotelData));
                        } catch (e) {
                            $('#detailContent').html('<p>Error loading Hotel data</p>');
                        }
                    } else if (taksi && taksi !== 'undefined') {
                        try {
                            var taksiData = typeof taksi === 'string' ? JSON.parse(taksi) : taksi;
                            $('#detailTypeHeader').text('Taxi Detail');
                            $('#detailContent').html(createTableHtml(taksiData));
                        } catch (e) {
                            $('#detailContent').html('<p>Error loading Taxi data</p>');
                        }
                    } else {
                        $('#detailTypeHeader').text('No Data Available');
                        $('#detailContent').html('<p>No detail information available.</p>');
                    }

                    $('#detailModal').modal('show');
                });

                $('#detailModal').on('hidden.bs.modal', function() {
                    $('body').removeClass('modal-open').css({
                        overflow: '',
                        padding: ''
                    });
                    $('.modal-backdrop').remove();
                });
            });

            document.addEventListener('DOMContentLoaded', function() {
                const modal = document.getElementById('pdfModal');
                const loadingIndicator = document.getElementById('loadingIndicator');
                const modalBody = modal.querySelector('.modal-body');
                let cachedData = {};

                modal.removeEventListener('show.bs.modal', handleShowModal);
                modal.addEventListener('show.bs.modal', handleShowModal);

                function handleShowModal(event) {
                    const button = event.relatedTarget;
                    const id = button.getAttribute('data-id');

                    console.log(`Fetching data for SPPD ID: ${id}`);

                    loadingIndicator.style.display = 'block';
                    modalBody.innerHTML = '';

                    if (cachedData[id]) {
                        renderModalContent(cachedData[id]);
                    } else {
                        fetchData(id);
                    }
                }

                function fetchData(id) {
                    fetch(`/businessTrip/pdf/${id}`)
                        .then(response => response.json())
                        .then(data => {
                            console.log('Data fetched:', data);
                            cachedData[id] = data;
                            renderModalContent(data);
                        })
                        .catch(error => {
                            loadingIndicator.style.display = 'none';
                            modalBody.innerHTML =
                                '<div class="alert alert-danger">Error loading data. Please try again.</div>';
                            console.error('Error fetching data:', error);
                        });
                }

                function renderModalContent(data) {
                    loadingIndicator.style.display = 'none';

                    const content = [];

                    const documentTypes = [{
                            key: 'sppd',
                            label: 'SPPD Document',
                            id: data.sppd.id
                        },
                        {
                            key: 'caTransactions',
                            label: 'CA Document',
                            type: 'ca',
                            id: data.caTransactions?.id
                        },
                        {
                            key: 'tickets',
                            label: 'Ticket Document',
                            type: 'tiket',
                            id: data.tickets?.id
                        },
                        {
                            key: 'hotel',
                            label: 'Hotel Document',
                            id: data.hotel?.id
                        },
                        {
                            key: 'taksi',
                            label: 'Taxi Document',
                            id: data.taksi?.id
                        }
                    ];

                    documentTypes.forEach(doc => {
                        if (data[doc.key] && doc.id) {
                            content.push(`
                <div class="download-item">
                    <label>${doc.label}</label>
                    <button class="btn btn-primary download-button" data-type="${doc.type || doc.key}" data-id="${doc.id}">Download</button>
                </div>
            `);
                        }
                    });

                    modalBody.innerHTML = content.join('');

                    attachDownloadListeners();
                }

                function attachDownloadListeners() {
                    document.querySelectorAll('.download-button').forEach(button => {
                        button.addEventListener('click', function() {
                            const type = this.getAttribute('data-type');
                            const id = this.getAttribute('data-id');
                            downloadDocument(id, type);
                        });
                    });
                }

                function downloadDocument(id, type) {
                    console.log(`Attempting to download document: Type=${type}, ID=${id}`);

                    // Open a new tab immediately
                    const newTab = window.open('about:blank', '_blank');

                    fetch(`/businessTrip/export/${id}/${type}`, {
                            method: 'GET',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                        })
                        .then(response => {
                            if (!response.ok) {
                                return response.text().then(text => {
                                    try {
                                        return JSON.parse(text);
                                    } catch (e) {
                                        throw new Error(text);
                                    }
                                }).then(err => {
                                    throw err;
                                });
                            }
                            return response.blob();
                        })
                        .then(blob => {
                            const url = URL.createObjectURL(blob);
                            newTab.location.href = url;
                            console.log(`Document downloaded successfully: Type=${type}, ID=${id}`);
                        })
                        .catch(error => {
                            console.error('Error downloading document:', error);
                            newTab.close();
                            let errorMessage = error.error ||
                                `Error downloading ${type} document. Please try again later.`;
                            alert(errorMessage);
                            console.error('Detailed error:', JSON.stringify(error, null, 2));
                        });
                }
            });
        </script>
    @endsection

@extends('layouts_.vertical', ['page_title' => 'Medical'])

@section('css')
    <style>
        .container {
            margin-top: 20px;
        }

        .form-container {
            background-color: #fff;
            border: 1px solid #ddd;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 4px;
        }

        .form-header,
        .table-header {
            background-color: #007bff;
            padding: 10px;
            font-weight: bold;
            margin-bottom: 15px;
            color: white;
            border-radius: 4px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group input {
            border: none;
            background-color: transparent;
            pointer-events: none;
        }

        .form-group label {
            display: inline-block;
            width: 150px;
            padding-left: 4px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            border: 1px solid #dee2e6;
            padding: 8px;
            text-align: middle;
        }

        .table th {
            background-color: #ffffff;
        }
        .keluarga-thead{
            background-color:#007bff29;
        }

        .jenis-plafond,
        .periode-plafond {
            vertical-align: middle !important;
            text-align: center;
        }

        .flex-container {
            display: flex;
            justify-content: space-between;
        }

        .flex-item {
            width: 48%;
        }

        .export {
            background-color: #1D6F42;
            color: white;
            padding: 5px 10px;
            text-decoration: none;
            display: inline-block;
            margin-bottom: 20px;
            border-radius: 4px;
        }


        .table-container {
            background-color: white;
            padding: 20px;
            border: 1px solid #ddd;
        }

        .search-field {
            margin-bottom: 10px;
            border-radius: 4px;
            border-color: #ddd;
        }

        .pagination {
            margin-top: 10px;
            display: flex;
            align-items: center;
        }

        .pagination #paginationInfo {
            margin-right: auto;
        }

        .pagination .nextBtn,
        .prevBtn {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 8px 20px;
            cursor: pointer;
            margin-left: 10px;
            border-radius: 4px;
            align-items: center;
        }

        .pagination .nextBtn:disabled,
        .prevBtn:disabled {
            background-color: #CCCCCC;
            cursor: not-allowed;
        }
        .recordsPerPage{
            border-radius: 4px;
        }

        table {
            white-space: nowrap;
        }

        tr.sticky {
            position: sticky;
            top: 0;
            z-index: 1;
            background: var(--stickyBackground);
        }


        th.sticky,
        td.sticky {
            position: sticky;
            left: 0;
            background: var(--stickyBackground);
        }
    </style>
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
        <!-- Kembali button -->
        <div class="row mb-3">
            <div class="col">
                <a href="/reimbursements" class="btn btn-primary btn-action">
                    <i class="bi bi-caret-left-fill"></i> Back
                </a>
                {{-- <a href="/businessTrip" class="btn btn-info btn-action">
                    <i class="bi bi-arrow-clockwise"></i> Refresh
                </a> --}}
                <a href="#" class="btn btn-outline-success btn-action">
                    <i class="bi bi-file-earmark-spreadsheet"></i> Export to Excel
                </a>
            </div>
        </div>

        <div class="form-container">
            <div class="flex-container">
                <!-- Left side: Data Plafond Kesehatan -->
                <div class="flex-item">
                    <div class="form-header">Data Plafond Kesehatan</div>
                    <form>
                        <div class="form-group">
                            <label for="nik-lama">NIK Lama:</label>
                            <input type="text" id="nik-lama" value="testing" readonly>
                        </div>
                        <div class="form-group">
                            <label for="nik-baru">NIK Baru:</label>
                            <input type="text" id="nik-baru" value="testing" readonly>
                        </div>
                        <div class="form-group">
                            <label for="nama">Nama:</label>
                            <input type="text" id="nama" value="testing" readonly>
                        </div>
                        <div class="form-group">
                            <label for="tmk">TMK:</label>
                            <input type="text" id="tmk" value="" readonly>
                        </div>
                        <div class="form-group">
                            <label for="divisi">Divisi:</label>
                            <input type="text" id="divisi" value="Plantation" readonly>
                        </div>
                        <div class="form-group">
                            <label for="departemen">Departemen:</label>
                            <input type="text" id="departemen"  value="" readonly>
                        </div>
                        <div class="form-group">
                            <label for="pt">PT:</label>
                            <input type="text" id="pt"  value="" readonly>
                        </div>
                        <div class="form-group">
                            <label for="status-pernikahan">Status Pernikahan:</label>
                            <input type="text" id="status-pernikahan"  value="" readonly>
                        </div>
                    </form>
                </div>

                <!-- Right side: Data Keluarga Karyawan -->
                <div class="flex-item">
                    <div class="form-header">Data Keluarga Karyawan</div>
                    <table class="table">
                        <thead class="keluarga-thead">
                            <tr>
                                <th>No.</th>
                                <th>NIK</th>
                                <th>Nama</th>
                                <th>Hubungan</th>
                                <th>Tanggal Lahir</th>
                                <th>Umur</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($keluarga as $idx => $n)
                                <tr>
                                    <th scope="row">{{ $keluarga->firstItem() + $idx }}
                                    </th>
                                    <td>{{ $n->nik }}</td>
                                    <td>{{ $n->nama }}</td>
                                    <td>{{ $n->hubungan }}</td>
                                    <td>{{ $n->tanggal_lahir }}</td>
                                    <td>{{ $n->umur }}</td>
                                    <td>{{ $n->status }}</td>
                                </tr>
                                @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

<<<<<<< Updated upstream
            <!-- Plafond table -->
            <div class="form-header" style="margin-top: 20px;">Jenis Plafond</div>
            <table class="table" id="jenisPlafonTable">
                <thead>
                    <tr id="headerRow1">
                        <th class="jenis-plafond" rowspan="2">Jenis Plafond</th>
                        <th id="periodeHeader" class="periode-plafond" colspan="3">Periode</th>
                        <!-- Initial colspan is 3 -->
                    </tr>
                    <tr id="headerRow2" class="periode-plafond">
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Persalinan</td>
                    </tr>
                    <tr>
                        <td>Rawat Inap</td>
                    </tr>
                    <tr>
                        <td>Rawat Jalan</td>
                    </tr>
                    <tr>
                        <td>Kacamata</td>
                    </tr>
                </tbody>
            </table>
            <div class="table-header">Detail Penggunaan Plafond</div>
            <div>
                <select class="recordsPerPage" id="recordsPerPage">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select> records per page
                <input type="text" id="searchField" class="search-field" placeholder="Search" style="float: right;">
            </div>
            <table class="table" id="detailTable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Periode</th>
                        <th>No Medical</th>
                        <th>Nama RS</th>
                        <th>Pasien</th>
                        <th>Disease</th>
                        <th>Persalinan</th>
                        <th>Rawat Inap</th>
                        <th>Rawat Jalan</th>
                        <th>Lensa Kacamata</th>
                        <th>Bingkai Kacamata</th>
                        <th>Status</th>
                        <th>Delete</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <!-- Table rows will be populated dynamically -->
                </tbody>
            </table>
            <div class="pagination">
                <span id="paginationInfo">Showing 0 to 0 of 0 entries</span>
                <button class="prevBtn" id="prevButton"><i class="bi bi-caret-left-fill"></i> Previous</button>
                <button class="nextBtn" id="nextButton">Next <i class="bi bi-caret-right-fill"></i></button>
=======
            {{-- Jenis Plafond --}}
            <div class="card shadow-none">
                <div class="card-body">
                    <h4 class="card-title">Jenis Plafond</h4>
                    <div class="card-text">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover nowrap">
                                <thead class="bg-primary text-center align-middle">
                                    <tr class="sticky">
                                        <th class="text-center sticky" style="background-color: #AB2F2B !important;"
                                            rowspan="2">Periode</th>
                                        <th class="text-center" colspan="4">Jenis Plafond</th>
                                    </tr>
                                    <tr class="sticky" style="z-index: auto">
                                        <th class="text-center">Persalinan</th>
                                        <th class="text-center">Rawat Inap</th>
                                        <th class="text-center">Rawat Jalan</th>
                                        <th class="text-center">Kacamata</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-center sticky" style="background-color: white !important;">2022</td>
                                        <td class="text-center">Rp 16.000.000</td>
                                        <td class="text-center">Rp 10.000.000</td>
                                        <td class="text-center">Rp 7.000.000</td>
                                        <td class="text-center">Rp 750.000</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Detail Penggunaan Plafond --}}
            <div class="card shadow-none">
                <div class="card-body">
                    <h4 class="card-title">Riwayat Penggunaan Plafond</h4>
                    <div class="card-text">
                        <div class="table-responsive">
                            <table class="display nowrap responsive" id="example">
                                <thead class="bg-primary text-center align-middle">
                                    <tr>
                                        <th></th>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Periode</th>
                                        <th data-priority="0">No Medical</th>
                                        <th>Nama Rumah Sakit</th>
                                        <th>Pasien</th>
                                        <th>Disease</th>
                                        <th>Persalinan</th>
                                        <th>Rawat Inap</th>
                                        <th>Rawat Jalan</th>
                                        <th>Lensa Kacamata</th>
                                        <th>Bingkai Kacamata</th>
                                        <th data-priority="1">Status</th>
                                        <th data-priority="2">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-center"></td>
                                        <td class="text-center">1</td>
                                        <td class="text-center">01 Sept 2024</td>
                                        <td class="text-center">2024</td>
                                        <td class="text-center">011/MDCL-2024</td>
                                        <td>RS. Murni Teguh</td>
                                        <td>Metta Saputra</td>
                                        <td>Demam</td>
                                        <td class="text-center">-</td>
                                        <td class="text-center">-</td>
                                        <td class="text-center">Rp 200.0000</td>
                                        <td class="text-center">-</td>
                                        <td class="text-center">-</td>
                                        <td class="text-center">Selesai</td>
                                        <td class="text-center">Selesai</td>
                                    </tr>
                                    <tr>
                                        <td class="text-center"></td>
                                        <td class="text-center">1</td>
                                        <td class="text-center">01 Sept 2024</td>
                                        <td class="text-center">2024</td>
                                        <td class="text-center">011/MDCL-2024</td>
                                        <td>RS. Murni Teguh</td>
                                        <td>Metta Saputra</td>
                                        <td>Demam</td>
                                        <td class="text-center">-</td>
                                        <td class="text-center">-</td>
                                        <td class="text-center">Rp 200.0000</td>
                                        <td class="text-center">-</td>
                                        <td class="text-center">-</td>
                                        <td class="text-center">Selesai</td>
                                        <td class="text-center">Selesai</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
>>>>>>> Stashed changes
            </div>
            <!-- Auto added row according to years -->
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const currentYear = new Date().getFullYear();
                    const numYears = 3; // Number of years to display (2 past years and the current year)
                    const headerRow2 = document.getElementById('headerRow2');
                    const periodeHeader = document.getElementById('periodeHeader');
                    const rows = document.querySelectorAll('#jenisPlafonTable tbody tr');

                    // Update the colspan attribute of the Periode header
                    periodeHeader.setAttribute('colspan', numYears);

                    // Add header cells for the past years and current year
                    for (let year = currentYear - numYears + 1; year <= currentYear; year++) {
                        const th = document.createElement('th');
                        th.textContent = year;
                        headerRow2.appendChild(th);
                    }

                    // Add cells to each row in the tbody for each year
                    rows.forEach(row => {
                        for (let year = currentYear - numYears + 1; year <= currentYear; year++) {
                            const td = document.createElement('td');
                            td.classList.add(`year${year}`);
                            row.appendChild(td);
                        }
                    });
                });
            </script>
        </div>
    @endsection

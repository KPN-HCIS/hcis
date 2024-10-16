@extends('layouts_.vertical', ['page_title' => 'Medical'])

@section('css')
    <style>
        th {
            color: white !important;
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
        <div class="row">
            {{-- Data Keluarga --}}
            <div class="card shadow-none">
                <div class="card-body">
                    <h4 class="card-title">Data Keluarga</h4>
                    <div class="card-text">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm table-striped table-hover">
                                <thead class="bg-primary align-middle text-center">
                                    <th>No</th>
                                    <th>NIK</th>
                                    <th>Nama</th>
                                    <th>Hubungan</th>
                                    <th>Tanggal Lahir</th>
                                    <th>Umur</th>
                                    <th>Status</th>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-center">1</td>
                                        <td class="text-center">01124040023</td>
                                        <td>Metta Saputra</td>
                                        <td class="text-center">Anak</td>
                                        <td>Palembang, 02 Mei 2000</td>
                                        <td class="text-center">24 tahun</td>
                                        <td class="text-center">Pelajar</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Jenis Plafond --}}
            <div class="card shadow-none">
                <div class="card-body">
                    <h4 class="card-title">Jenis Plafond</h4>
                    <div class="card-text">
                        <div class="table-responsive">
                            <table class="display nowrap dataTable dtr-inline collapsed">
                                <thead class="bg-primary text-center align-middle">
                                    <tr>
                                        <th rowspan="2">Periode</th>
                                        <th colspan="4">Jenis Plafond</th>
                                    </tr>
                                    <tr>
                                        <th>Persalinan</th>
                                        <th>Rawat Inap</th>
                                        <th>Rawat Jalan</th>
                                        <th>Kacamata</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-center">2022</td>
                                        <td class="text-center">Rp 16.000.000</td>
                                        <td class="text-center">Rp 10.000.000</td>
                                        <td class="text-center">Rp 7.000.000</td>
                                        <td class="text-center">Rp 750.000</td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">2023</td>
                                        <td class="text-center">Rp 16.000.000</td>
                                        <td class="text-center">Rp 10.000.000</td>
                                        <td class="text-center">Rp 7.000.000</td>
                                        <td class="text-center">Rp 750.000</td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">2024</td>
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
                                        <th>Action</th>
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
            </div>
        </div>
    </div>

    <script>
        $("#example").DataTable({
            responsive: {
                details: {
                    type: 'column',
                    target: 'tr',
                },
            },
            columnDefs: [{
                    className: 'control',
                    orderable: false,
                    targets: 0
                },
                {
                    responsivePriority: 1,
                    targets: 0
                },
                {
                    responsivePriority: 4,
                    targets: 3
                }
            ],
            order: [1, 'asc']
        });
    </script>
@endsection

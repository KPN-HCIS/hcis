@extends('layouts_.vertical', ['page_title' => 'Medical'])

@section('css')
    <style>
        th {
            color: white !important;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6 mt-3">
                <div class="page-title-box d-flex align-items-center">
                    <ol class="breadcrumb mb-0" style="display: flex; align-items: center; padding-left: 0;">
                        <li class="breadcrumb-item" style="font-size: 32px; display: flex; align-items: center;">
                            <a href="/reimbursements" style="text-decoration: none;" class="text-primary">
                                <i class="bi bi-arrow-left"></i>
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            {{ $parentLink }}
                        </li>
                        <li class="breadcrumb-item">
                            {{ $link }}
                        </li>
                    </ol>
                </div>
            </div>
            <div class="col-md-6 mt-4 mb-2 text-end">
                <a href="{{ route('export.excel') }}" class="btn btn-outline-success rounded-pill btn-action me-1">
                    <i class="bi bi-file-earmark-spreadsheet-fill"></i> Export to Excel
                </a>
                {{-- Add Data Button --}}
                <a href="{{ route('medical-form.add') }}" class="btn btn-primary rounded-pill">
                    <i class="bi bi-plus-circle"></i> Add Medical
                </a>
            </div>
        </div>

        <div class="row">
            {{-- Data Keluarga --}}
            <div class="card shadow-none">
                <div class="card-body">
                    <h4 class="card-title">Family Data</h4>
                    <div class="card-text">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm table-striped table-hover">
                                <thead class="bg-primary align-middle text-center">
                                    <th>No</th>
                                    <th>NIK</th>
                                    <th>Name</th>
                                    <th>Relation</th>
                                    <th>Date of Birth</th>
                                    <th>Age</th>
                                    <th>Status</th>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-center">1</td>
                                        <td class="text-center">31231313131</td>
                                        <td>Shin Ryujin</td>
                                        <td class="text-center">Istri</td>
                                        <td>Surabaya, 12 September 2000</td>
                                        <td class="text-center">24 tahun</td>
                                        <td class="text-center">Wife House</td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">2</td>
                                        <td class="text-center">01124040023</td>
                                        <td>Metta Saputra</td>
                                        <td class="text-center">Anak</td>
                                        <td>Palembang, 02 Mei 2000</td>
                                        <td class="text-center">24 tahun</td>
                                        <td class="text-center">Pelajar</td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">3</td>
                                        <td class="text-center">352101313131</td>
                                        <td>Jocelyn Flores</td>
                                        <td class="text-center">Anak</td>
                                        <td>Surabaya, 17 September 2018</td>
                                        <td class="text-center">6 tahun</td>
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
                    <h4 class="card-title">Health Coverage Limit</h4>
                    <div class="card-text">
                        <div class="table-responsive">
                            <table class="display nowrap dataTable dtr-inline collapsed">
                                <thead class="bg-primary text-center align-middle">
                                    <tr>
                                        <th rowspan="2" class="text-center">Period</th>
                                        <th colspan="4" class="text-center">Type of Health Coverage</th>
                                    </tr>
                                    <tr>
                                        <th class="text-center">Labor</th>
                                        <th class="text-center">Inpatient</th>
                                        <th class="text-center">Outpatient</th>
                                        <th class="text-center">Glasses</th>
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
                                        <td class="text-center">Rp 0</td>
                                        <td class="text-center">Rp 7.000.000</td>
                                        <td class="text-center">Rp 3.000.000</td>
                                        <td class="text-center">Rp 250.000</td>
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
                    <h4 class="card-title">Health Coverage Usage History</h4>
                    <div class="card-text">
                        <div class="table-responsive">
                            <table class="display nowrap responsive" id="example">
                                <thead class="bg-primary text-center align-middle">
                                    <tr>
                                        <th></th>
                                        <th class="text-center">No</th>
                                        <th class="text-center">Date</th>
                                        <th class="text-center">Period</th>
                                        <th class="text-center" data-priority="0">No. Medical</th>
                                        <th class="text-center">Hospital Name</th>
                                        <th class="text-center">Patient Name</th>
                                        <th class="text-center">Disease</th>
                                        <th class="text-center">Labor</th>
                                        <th class="text-center">Inpatient</th>
                                        <th class="text-center">Outpatient</th>
                                        <th class="text-center">Glasses Lens</th>
                                        <th class="text-center">Glasses</th>
                                        <th class="text-center" data-priority="1">Status</th>
                                        <th class="text-center">Action</th>
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
                                        <td class="text-center">Demam Panggung</td>
                                        <td class="text-center">-</td>
                                        <td class="text-center">-</td>
                                        <td class="text-center">Rp 200.0000</td>
                                        <td class="text-center">-</td>
                                        <td class="text-center">-</td>
                                        <td style="align-content: center; text-align: center">
                                            <span class="badge rounded-pill bg-success text-center" style="font-size: 12px; padding: 0.5rem 1rem;">Done</span>
                                        </td>
                                        <td class="text-center">RAWR~</td>
                                    </tr>
                                    <tr>
                                        <td class="text-center"></td>
                                        <td class="text-center">2</td>
                                        <td class="text-center">02 Sept 2024</td>
                                        <td class="text-center">2024</td>
                                        <td class="text-center">012/MDCL-2024</td>
                                        <td>RS. Murni Teguh 2</td>
                                        <td>Jocelyn</td>
                                        <td class="text-center">Sakit Hati</td>
                                        <td class="text-center">-</td>
                                        <td class="text-center">-</td>
                                        <td class="text-center">Rp 300.0000</td>
                                        <td class="text-center">-</td>
                                        <td class="text-center">-</td>
                                        <td style="align-content: center; text-align: center">
                                            <span class="badge rounded-pill bg-warning text-center" style="font-size: 12px; padding: 0.5rem 1rem;">Pending</span>
                                        </td>
                                        <td class="text-center">RAWR~</td>
                                    </tr>
                                    <tr>
                                        <td class="text-center"></td>
                                        <td class="text-center">2</td>
                                        <td class="text-center">03 Sept 2024</td>
                                        <td class="text-center">2024</td>
                                        <td class="text-center">013/MDCL-2024</td>
                                        <td>RS. Murni Teguh 3</td>
                                        <td>Flores</td>
                                        <td class="text-center">Budeg</td>
                                        <td class="text-center">-</td>
                                        <td class="text-center">-</td>
                                        <td class="text-center">Rp 400.0000</td>
                                        <td class="text-center">-</td>
                                        <td class="text-center">-</td>
                                        <td style="align-content: center; text-align: center">
                                            <span class="badge rounded-pill bg-danger text-center" style="font-size: 12px; padding: 0.5rem 1rem;">Rejected</span>
                                        </td>
                                        <td class="text-center">RAWR~</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('/js/medical/medical.js') }}"></script>
@endsection

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
                                        <td class="text-center">01124040023</td>
                                        <td>Metta Saputra</td>
                                        <td class="text-center">Anak</td>
                                        <td>Palembang, 02 Mei 2000</td>
                                        <td class="text-center">24 tahun</td>
                                        <td class="text-center">Pelajar</td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">2</td>
                                        <td class="text-center">352101313131</td>
                                        <td>Jocelyn Flores</td>
                                        <td class="text-center">Anak</td>
                                        <td>Surabaya, 17 September 2004</td>
                                        <td class="text-center">20 tahun</td>
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
                    <h4 class="card-title">Health Coverage Usage History</h4>
                    <div class="card-text">
                        <div class="table-responsive">
                            <table class="display nowrap responsive" id="example">
                                <thead class="bg-primary text-center align-middle">
                                    <tr>
                                        <th></th>
                                        <th>No</th>
                                        <th>Date</th>
                                        <th>Period</th>
                                        <th data-priority="0">No. Medical</th>
                                        <th>Hospital Name</th>
                                        <th>Patient Name</th>
                                        <th>Disease</th>
                                        <th>Labor</th>
                                        <th>Inpatient</th>
                                        <th>Outpatient</th>
                                        <th>Glasses Lens</th>
                                        <th>Glasses</th>
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
                                        <td class="text-center">RAWR~</td>
                                    </tr>
                                    <tr>
                                        <td class="text-center"></td>
                                        <td class="text-center">2</td>
                                        <td class="text-center">02 Sept 2024</td>
                                        <td class="text-center">2024</td>
                                        <td class="text-center">012/MDCL-2024</td>
                                        <td>RS. Murni Teguh 2</td>
                                        <td>Metta Saputra</td>
                                        <td>Demam</td>
                                        <td class="text-center">-</td>
                                        <td class="text-center">-</td>
                                        <td class="text-center">Rp 300.0000</td>
                                        <td class="text-center">-</td>
                                        <td class="text-center">-</td>
                                        <td class="text-center">Selesai</td>
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

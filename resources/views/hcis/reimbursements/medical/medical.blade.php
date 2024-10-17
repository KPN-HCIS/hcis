@extends('layouts_.vertical', ['page_title' => 'Medical'])

@section('css')
    <style>
        th {
            color: white !important;
            text-align: center;
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

        table.dataTable>tbody>tr.child ul.dtr-details {
            width: 100%;
            vertical-align: middle !important;
        }

        table.dataTable>tbody>tr.child ul.dtr-details>li {
            display: flex;
            align-items: center !important;
        }

        table.dataTable>tbody>tr.child span.dtr-title {
            min-width: 120px !important;
            max-width: 120px !important;
            text-wrap: wrap !important;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <!-- Breadcrumb Section -->
            <div class="col-md-6 d-flex align-items-center">
                <ol class="breadcrumb mb-0" style="align-items: center; padding-left: 0;">
                    <li class="breadcrumb-item" style="font-size: 18px;">
                        <a href="/reimbursements">
                            {{ $parentLink }}
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        {{ $link }}
                    </li>
                </ol>
            </div>

            <!-- Button Section -->
            <div class="col-md-6 d-flex justify-content-center justify-content-md-end align-items-center">
                <a href="{{ route('export.excel') }}" class="btn btn-outline-success rounded-pill btn-action me-1">
                    <i class="bi bi-file-earmark-spreadsheet-fill"></i> Export to Excel
                </a>
                <a href="{{ route('medical-form.add') }}" class="btn btn-primary rounded-pill">
                    <i class="bi bi-plus-circle"></i> Add Medical
                </a>
            </div>
        </div>


        <div class="row mt-2">
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
                                    @foreach ($family as $item)
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td>{{ $item->name }}</td>
                                            <td>{{ $item->gender }}</td>
                                            <td>{{ $item->relation_type }}</td>
                                            <td class="text-center">
                                                {{ \Carbon\Carbon::parse($item->date_of_birth)->format('d F Y') }}
                                            </td>
                                            <td class="text-center">
                                                {{ \Carbon\Carbon::parse($item->date_of_birth)->age }} Years Old
                                            </td>
                                            <td class="text-center">{{ $item->jobs }}</td>
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
                                        <th rowspan="2" class="text-center sticky"
                                            style="z-index:auto !important;background-color:#AB2F2B !important;">Period</th>
                                        <th colspan="4" class="text-center">Type of Health Coverage</th>
                                    </tr>
                                    <tr>
                                        <th class="text-center">Child Birth</th>
                                        <th class="text-center">Inpatient</th>
                                        <th class="text-center">Outpatient</th>
                                        <th class="text-center">Glasses</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($medical_plan as $item)
                                        <tr>
                                            <td class="text-center">{{ $item->period }}</td>
                                            <td class="text-center">{{ 'Rp. ' . number_format($item->child_birth_balance, 0, ',', '.') }}</td>
                                            <td class="text-center">{{ 'Rp. ' . number_format($item->inpatient_balance, 0, ',', '.') }}</td>
                                            <td class="text-center">{{ 'Rp. ' . number_format($item->outpatient_balance, 0, ',', '.') }}</td>
                                            <td class="text-center">{{ 'Rp. ' . number_format($item->glasses_balance, 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
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
                            <table class="display nowrap responsive" id="example" width="100%">
                                <thead class="bg-primary text-center align-middle">
                                    <tr>
                                        <th></th>
                                        <th>No</th>
                                        <th>Date</th>
                                        <th>Period</th>
                                        <th data-priority="0">No. Medical</th>
                                        <th>Hospital Nameeeeeeeeee</th>
                                        <th>Patient Name</th>
                                        <th>Disease</th>
                                        <th>Labor</th>
                                        <th>Inpatient</th>
                                        <th>Outpatient</th>
                                        <th>Glasses Lens</th>
                                        <th>Glasses</th>
                                        <th data-priority="1">Status</th>
                                        <th data-priority="2">Action</th>
                                    </tr>

                                </thead>
                                <tbody>
                                    {{-- @foreach ($medical_plan as $item) --}}
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
                                        <td style="align-content: center; text-align: center">
                                            <span class="badge rounded-pill bg-success text-center"
                                                style="font-size: 12px; padding: 0.5rem 1rem;">Done</span>
                                        </td>
                                        <td class="text-center">RAWR~</td>
                                    </tr>
                                    {{-- @endforeach --}}
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

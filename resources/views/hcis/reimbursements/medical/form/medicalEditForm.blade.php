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
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('medical') }}">{{ $parentLink }}</a></li>
                            <li class="breadcrumb-item active">{{ $link }}</li>
                        </ol>
                    </div>
                    <h4 class="page-title">{{ $link }}</h4>
                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex bg-primary text-white justify-content-between">
                        <h4 class="mb-0">Update Medical Data - {{ $medic->no_medic }}</h4>
                        <a href="/medical" type="button" class="btn-close btn-close-white" aria-label="Close"></a>
                    </div>
                    <div class="card-body">
                        <form id="medicForm" action="/medical/form-add/post" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row mb-2">
                                <div class="col-md-4 mb-2">
                                    <label for="patient_name" class="form-label">Patient Name</label>
                                    <select class="form-select form-select-sm select2" id="patient_name" name="patient_name"
                                        required>
                                        <option value="" disabled selected>--- Choose Patient ---</option>
                                        <option value="{{ $employee_name->fullname }}">
                                            {{ $employee_name->fullname }} (Me)
                                        </option>
                                        @foreach ($families as $family)
                                            <option value="{{ $family->name }}"
                                                {{ $family->name  ? 'selected' : '' }}>
                                                {{ $family->name }} ({{ $family->relation_type }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label for="nama" class="form-label">Hospital Name</label>
                                    <input type="text" class="form-control form-control-sm" id="hospital_name"
                                        name="hospital_name" placeholder="ex: RS. Murni Teguh" value="{{ $medic->hospital_name }}" required>
                                </div>

                                <div class="col-md-4 mb-2">
                                    <label for="disease" class="form-label">Disease</label>
                                    <select class="form-select form-select-sm select2" id="disease" name="disease"
                                        required>
                                        <option value="" disabled selected>--- Choose Disease ---</option>
                                        @foreach ($diseases as $disease)
                                            <option value="{{ $disease->disease_name }}"
                                                {{ $disease->disease_name  ? 'selected' : '' }}>
                                                {{ $disease->disease_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-md-6 mb-2">
                                    <label for="keperluan" class="form-label">No. Invoice</label>
                                    <input type="text" class="form-control form-control-sm" id="no_invoice"
                                        name="no_invoice" rows="3" placeholder="Please add your invoice number ..." value="{{ $medic->no_invoice }}"
                                        required></input>
                                </div>
                                <div class="col-md-6 mb-1">
                                    <label for="medical_date" class="form-label">Medical Date</label>
                                    <input type="date" class="form-control form-control-sm" id="date" name="date" value="{{ $medic->date }}"
                                        required>
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label for="rawatInap" class="form-label">Inpatient</label>
                                    <div class="input-group input-group-sm" id="rawatInap">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" id="inputRawatInap" name="inpatient" class="form-control"
                                            placeholder="0" oninput="formatCurrency(this)" value="{{ $medic->inpatient }}"/>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label for="rawatJalan" class="form-label">Outpatient</label>
                                    <div class="input-group input-group-sm" id="rawatJalan">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" id="inputRawatJalan" name="outpatient" class="form-control"
                                            placeholder="0" oninput="formatCurrency(this)" value="{{ $medic->outpatient }}"/>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label for="persalinan" class="form-label">Child Birth</label>
                                    <div class="input-group input-group-sm" id="persalinan">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" id="inputPersalinan" name="child_birth" class="form-control"
                                            placeholder="0" oninput="formatCurrency(this)" value="{{ $medic->child_birth }}"/>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label for="kacamata" class="form-label">Glasses</label>
                                    <div class="input-group input-group-sm" id="kacamata">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" id="inputKacamata" name="glasses" class="form-control"
                                            placeholder="0" oninput="formatCurrency(this)" value="{{ $medic->glasses }}"/>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-12 mt-2">
                                    <label for="" class="form-label">Detail Information</label>
                                    <textarea class="form-control form-control-sm" id="coverage_detail" name="coverage_detail" rows="3"
                                        placeholder="Please add more detail of disease ..." required  value="{{ $medic->coverage_detail }}"></textarea>
                                </div>
                            </div>
                            @php
                                use Illuminate\Support\Facades\Storage;
                            @endphp

                            <div class="col-md-8 mt-2">
                                <label for="medical_proof" class="form-label">Upload Proof</label>
                                <div class="d-flex align-items-center">
                                    <input type="file" id="medical_proof" name="medical_proof"
                                        accept="image/*,application/pdf" class="form-control me-2">

                                    @if (isset($medic->medical_proof) && $medic->medical_proof)
                                        <a href="{{ Storage::url($medic->medical_proof) }}" target="_blank"
                                            class="btn btn-primary rounded-pill">
                                            View
                                        </a>
                                    @endif
                                </div>
                            </div>
                            {{-- <div class="row g-3">
                                <div class="col-md-3">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="rawatInap"
                                            onchange="toggleInput('rawatInap', 'rawatInapInputGroup')" />
                                        <label class="form-check-label" for="rawatInap">Rawat Inap</label>
                                    </div>
                                    <div class="input-group input-group-sm" id="rawatInapInputGroup" style="display: none;">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" id="inputRawatInap" class="form-control" placeholder="0" oninput="formatCurrency(this)"/>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="rawatJalan"
                                            onchange="toggleInput('rawatJalan', 'rawatJalanInputGroup')" />
                                        <label class="form-check-label" for="rawatJalan">Rawat Jalan</label>
                                    </div>
                                    <div class="input-group input-group-sm" id="rawatJalanInputGroup"
                                        style="display: none;">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" id="inputRawatJalan" class="form-control"
                                            placeholder="0" oninput="formatCurrency(this)"/>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="persalinan"
                                            onchange="toggleInput('persalinan', 'persalinanInputGroup')" />
                                        <label class="form-check-label" for="persalinan">Persalinan</label>
                                    </div>
                                    <div class="input-group input-group-sm" id="persalinanInputGroup"
                                        style="display: none;">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" id="inputPersalinan" class="form-control"
                                            placeholder="0" oninput="formatCurrency(this)"/>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="kacamata"
                                            onchange="toggleInput('kacamata', 'kacamataInputGroup')" />
                                        <label class="form-check-label" for="kacamata">Kacamata</label>
                                    </div>
                                    <div class="input-group input-group-sm" id="kacamataInputGroup"
                                        style="display: none;">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" id="inputKacamata" class="form-control" placeholder="0" oninput="formatCurrency(this)"/>
                                    </div>
                                </div>
                            </div> --}}


                            <input type="hidden" name="status" value="Pending L1" id="status">

                            <div class="d-flex justify-content-end mt-4">
                                <button type="submit" class="btn btn-outline-primary rounded-pill me-2 draft-button"
                                    name="action_draft" id="save-draft" value="Draft" id="save-draft">Save as
                                    Draft</button>
                                <button type="submit" class="btn btn-primary rounded-pill submit-button"
                                    name="action_submit" value="Pending" id="submit-btn">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('/js/medical/medical.js') }}"></script>
@endsection

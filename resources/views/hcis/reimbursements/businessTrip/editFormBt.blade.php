@extends('layouts_.vertical', ['page_title' => 'Business Trip'])

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-datepicker@1.9.0/dist/css/bootstrap-datepicker.min.css"
        rel="stylesheet">
@endsection

@section('content')
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="mb-3">
                    {{-- <a href="{{ url()->previous() }}" class="btn btn-outline-primary">
                    <i class="bi bi-caret-left-fill"></i> Kembali
                </a> --}}
                </div>
                <div class="card">
                    <div class="card-header d-flex bg-primary text-white justify-content-between">
                        <h4 class="mb-0">Edit Data</h4>
                        <a href="{{ url()->previous() }}" type="button" class="btn-close btn-close-white"></a>
                    </div>
                    <div class="card-body">
                        <form action="/businessTrip/update/{{ $n->id }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label for="nama" class="form-label">Nama</label>
                                <input type="text" class="form-control bg-light" id="nama" name="nama"
                                    style="cursor:not-allowed;" value="{{ $n->nama }}" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="divisi" class="form-label">Divisi</label>
                                <input type="text" class="form-control bg-light" id="divisi" name="divisi"
                                    style="cursor:not-allowed;" value="{{ $n->divisi }}" readonly>

                                {{-- <select class="form-select" id="divisi" name="divisi">
                                <option value="Plantation">Plantation</option>
                                <option value="Plantation">Personalia</option>
                                <option value="Plantation">IT</option>
                                <option value="Plantation">HR</option>
                                <!-- Add more options if needed -->
                            </select>--}}
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="unit_1" class="form-label">Unit/Lokasi Kerja</label>
                                    <select class="form-select" id="unit_1" name="unit_1">
                                        <option selected disabled>-- Pilih Unit --</option>
                                        <option value="unit 1" {{ $n->unit_1 == 'unit 1' ? 'selected' : '' }}>Unit 1</option>
                                        <option value="unit 2" {{ $n->unit_1 == 'unit 2' ? 'selected' : '' }}>Unit 2</option>
                                        <option value="unit 3" {{ $n->unit_1 == 'unit 3' ? 'selected' : '' }}>Unit 3</option>
                                        <option value="unit 4" {{ $n->unit_1 == 'unit 4' ? 'selected' : '' }}>Unit 4</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="atasan_1" class="form-label">Atasan 1</label>
                                    <select class="form-select" id="atasan_1" name="atasan_1">
                                        <option selected disabled>-- Pilih Atasan --</option>
                                        <option value="atasan 1" {{ $n->atasan_1 == 'atasan 1' ? 'selected' : '' }}>Atasan 1</option>
                                        <option value="atasan 2" {{ $n->atasan_1 == 'atasan 2' ? 'selected' : '' }}>Atasan 2</option>
                                        <option value="atasan 3" {{ $n->atasan_1 == 'atasan 3' ? 'selected' : '' }}>Atasan 3</option>
                                        <option value="atasan 4" {{ $n->atasan_1 == 'atasan 4' ? 'selected' : '' }}>Atasan 4</option>

                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="email_1" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email_1" name="email_1"
                                        placeholder="Email Atasan 1" value="{{ $n->email_1 }}">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="unit_2" class="form-label">Unit/Lokasi Kerja</label>
                                    <select class="form-select" id="unit_2" name="unit_2">
                                        <option selected disabled>-- Pilih Unit --</option>
                                        <option value="unit 1" {{ $n->unit_2 == 'unit 1' ? 'selected' : '' }}>Unit 1</option>
                                        <option value="unit 2" {{ $n->unit_2 == 'unit 2' ? 'selected' : '' }}>Unit 2</option>
                                        <option value="unit 3" {{ $n->unit_2 == 'unit 3' ? 'selected' : '' }}>Unit 3</option>
                                        <option value="unit 4" {{ $n->unit_2 == 'unit 4' ? 'selected' : '' }}>Unit 4</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="atasan_2" class="form-label">Atasan 2</label>
                                    <select class="form-select" id="atasan_2" name="atasan_2">
                                        <option selected disabled>-- Pilih Atasan --</option>
                                        <option value="atasan 1" {{ $n->atasan_2 == 'atasan 1' ? 'selected' : '' }}>Atasan 1</option>
                                        <option value="atasan 2" {{ $n->atasan_2 == 'atasan 2' ? 'selected' : '' }}>Atasan 2</option>
                                        <option value="atasan 3" {{ $n->atasan_2 == 'atasan 3' ? 'selected' : '' }}>Atasan 3</option>
                                        <option value="atasan 4" {{ $n->atasan_2 == 'atasan 4' ? 'selected' : '' }}>Atasan 4</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="email_2" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email_2" name="email_2"
                                        placeholder="Email Atasan 2" value="{{ $n->email_2 }}">
                                </div>
                            </div>



                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="mulai" class="form-label">Tanggal Mulai</label>
                                    <input type="date" class="form-control datepicker" id="mulai" name="mulai"
                                        placeholder="Tanggal Mulai" value="{{ $n->mulai }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="kembali" class="form-label">Tanggal Kembali</label>
                                    <input type="date" class="form-control datepicker" id="kembali" name="kembali"
                                        placeholder="Tanggal Kembali" value="{{ $n->kembali }}">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="tujuan" class="form-label">Tujuan</label>
                                <input type="text" class="form-control" id="tujuan" name="tujuan"
                                    placeholder="Tujuan" value="{{ $n->tujuan }}">
                            </div>

                            <div class="mb-3">
                                <label for="keperluan" class="form-label">Keperluan (Agar diisi sesuai kunjungan
                                    dinas)</label>
                                <textarea class="form-control" id="keperluan" name="keperluan" rows="3">{{ $n->keperluan }}</textarea>
                            </div>

                            <div class="mb-3">
                                <label for="bb_perusahaan_{{ $n->id }}" class="form-label">Beban Biaya Perusahaan (PT Keperluan Dinas / Bukan PT Payroll)</label>
                                <select class="form-select" id="bb_perusahaan_{{ $n->id }}" name="bb_perusahaan">
                                    <option value="">--- Pilih PT ---</option>
                                    @foreach ($companies as $company)
                                        <option value="{{ $company->contribution_level_code }}" {{ $company->contribution_level_code == $n->bb_perusahaan ? 'selected' : '' }}>
                                            {{ $company->contribution_level . ' (' . $company->contribution_level_code . ')' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="norek_krywn" class="form-label">No Rekening Karyawan</label>
                                <input type="number" class="form-control" id="norek_krywn" name="norek_krywn"
                                    placeholder="No Rekening" value="{{ $n->norek_krywn }}">
                            </div>

                            <div class="mb-3">
                                <label for="nama_bank" class="form-label">Nama Bank Karyawan</label>
                                <input type="text" class="form-control" id="nama_bank" name="nama_bank"
                                    placeholder="Nama Bank" value="{{ $n->nama_bank }}">
                            </div>

                            <div class="mb-3">
                                <label for="nama_pemilik_rek" class="form-label">Nama Pemilik Rekening</label>
                                <input type="text" class="form-control" id="nama_pemilik_rek" name="nama_pemilik_rek"
                                    placeholder="Nama Pemilik Rekening" value="{{ $n->nama_pemilik_rek }}">
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="ca" class="form-label">Cash Advanced</label>
                                    <select class="form-select" id="ca" name="ca">
                                        <option value="Tidak" {{ $n->ca == 'Tidak' ? 'selected' : '' }}>Tidak</option>
                                        <option value="Ya" {{ $n->ca == 'Ya' ? 'selected' : '' }}>Ya</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="tiket" class="form-label">Ticket</label>
                                    <select class="form-select" id="tiket" name="tiket">
                                        <option value="Tidak" {{ $n->tiket == 'Tidak' ? 'selected' : '' }}>Tidak</option>
                                        <option value="Ya" {{ $n->tiket == 'Ya' ? 'selected' : '' }}>Ya</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mt-3">
                                    <label for="hotel" class="form-label">Hotel</label>
                                    <select class="form-select" id="hotel" name="hotel">
                                        <option value="Tidak" {{ $n->hotel == 'Tidak' ? 'selected' : '' }}>Tidak</option>
                                        <option value="Ya" {{ $n->hotel == 'Ya' ? 'selected' : '' }}>Ya</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mt-3">
                                    <label for="taksi" class="form-label">Voucher Taksi</label>
                                    <select class="form-select" id="taksi" name="taksi">
                                        <option value="Tidak" {{ $n->taksi == 'Tidak' ? 'selected' : '' }}s>Tidak</option>
                                        <option value="Ya" {{ $n->taksi == 'Ya' ? 'selected' : '' }}>Ya</option>
                                    </select>
                                </div>
                            </div>
                            <input type="hidden" name="status" value="Pending">

                            <div class="text-end">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

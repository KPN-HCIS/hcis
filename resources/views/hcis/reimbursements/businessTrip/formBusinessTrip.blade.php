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
                        <h4 class="mb-0">Add Data</h4>
                        <a href="{{ url()->previous() }}" type="button" class="btn-close btn-close-white"></a>
                    </div>
                    <div class="card-body">
                        <form action="/businessTrip/form/post" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label for="nama" class="form-label">Nama</label>
                                <input type="text" class="form-control bg-light" id="nama" name="nama"
                                    style="cursor:not-allowed;" value="{{ $employee_data->fullname }}" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="divisi" class="form-label">Divisi</label>
                                <input type="text" class="form-control bg-light" id="divisi" name="divisi"
                                    style="cursor:not-allowed;" value="{{ $employee_data->unit }}" readonly>

                            </div>
                            {{-- <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="unit_1" class="form-label" hidden>Unit/Lokasi Kerja</label>
                                    <select class="form-select" id="unit_1" name="unit_1" hidden>
                                        <option selected disabled>-- Pilih Unit --</option>
                                        <option value="unit 1">Unit 1</option>
                                        <option value="unit 2">Unit 2</option>
                                        <option value="unit 3">Unit 3</option>
                                        <option value="unit 4">Unit 4</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="atasan_1" class="form-label" hidden>Atasan 1</label>
                                    <select class="form-select" id="atasan_1" name="atasan_1" hidden>
                                        <option selected disabled>-- Pilih Atasan --</option>
                                        <option value="atasan 1">Atasan 1</option>
                                        <option value="atasan 2">Atasan 2</option>
                                        <option value="atasan 3">Atasan 3</option>
                                        <option value="atasan 4">Atasan 4</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="email_1" class="form-label" hidden>Email</label>
                                    <input type="email" class="form-control" id="email_1" name="email_1"
                                        placeholder="Email Atasan 1" hidden>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="unit_2" class="form-label" hidden>Unit/Lokasi Kerja</label>
                                    <select class="form-select" id="unit_2" name="unit_2" hidden>
                                        <option selected disabled>-- Pilih Unit --</option>
                                        <option value="unit 1">Unit 1</option>
                                        <option value="unit 2">Unit 2</option>
                                        <option value="unit 3">Unit 3</option>
                                        <option value="unit 4">Unit 4</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="atasan_2" class="form-label" hidden>Atasan 2</label>
                                    <select class="form-select" id="atasan_2" name="atasan_2" hidden>
                                        <option selected disabled>-- Pilih Atasan --</option>
                                        <option value="atasan 1">Atasan 1</option>
                                        <option value="atasan 2">Atasan 2</option>
                                        <option value="atasan 3">Atasan 3</option>
                                        <option value="atasan 4">Atasan 4</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="email_2" class="form-label" hidden>Email</label>
                                    <input type="email" class="form-control" id="email_2" name="email_2"
                                        placeholder="Email Atasan 2" hidden>
                                </div>
                            </div> --}}
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="mulai" class="form-label">Tanggal Mulai</label>
                                    <input type="date" class="form-control datepicker" id="mulai" name="mulai"
                                        placeholder="Tanggal Mulai">
                                </div>
                                <div class="col-md-6">
                                    <label for="kembali" class="form-label">Tanggal Kembali</label>
                                    <input type="date" class="form-control datepicker" id="kembali" name="kembali"
                                        placeholder="Tanggal Kembali">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="tujuan" class="form-label">Tujuan</label>
                                <input type="text" class="form-control" id="tujuan" name="tujuan"
                                    placeholder="Tujuan">
                            </div>

                            <div class="mb-3">
                                <label for="keperluan" class="form-label">Keperluan (Agar diisi sesuai kunjungan
                                    dinas)</label>
                                <textarea class="form-control" id="keperluan" name="keperluan" rows="3"></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="bb_perusahaan" class="form-label">Beban Biaya Perusahaan (PT Keperluan Dinas /
                                    Bukan PT Payroll)</label>
                                <select class="form-select" id="bb_perusahaan" name="bb_perusahaan">
                                    <option value="">--- Pilih PT ---</option>
                                    @foreach ($companies as $company)
                                        <option value="{{ $company->contribution_level_code }}">
                                            {{ $company->contribution_level . ' (' . $company->contribution_level_code . ')' }}
                                        </option>
                                    @endforeach
                                    {{-- <option selected disabled>-- Pilih --</option>
                                <option value="PT. SKRTTT">PT. SKRTTT</option>
                                <option value="PT. JMK48">PT. JMK48</option>
                                <option value="PT. Tirta Investma">PT. Tirta Investma</option> --}}
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="norek_krywn" class="form-label">No Rekening Karyawan</label>
                                <input type="number" class="form-control" id="norek_krywn" name="norek_krywn"
                                    placeholder="No Rekening">
                            </div>

                            <div class="mb-3">
                                <label for="nama_bank" class="form-label">Nama Bank Karyawan</label>
                                <input type="text" class="form-control" id="nama_bank" name="nama_bank"
                                    placeholder="Nama Bank">
                            </div>

                            <div class="mb-3">
                                <label for="nama_pemilik_rek" class="form-label">Nama Pemilik Rekening</label>
                                <input type="text" class="form-control" id="nama_pemilik_rek" name="nama_pemilik_rek"
                                    placeholder="Nama Pemilik Rekening">
                            </div>

                            <!-- HTML Part -->
                            <div class="col-md-14 mb-3">
                                <label for="jns_dinas" class="form-label">Jenis Dinas</label>
                                <select class="form-select" id="jns_dinas" name="jns_dinas">
                                    <option selected disabled>-- Pilih Jenis Dinas --</option>
                                    <option value="dalam kota">Dinas Dalam Kota</option>
                                    <option value="luar kota">Dinas Luar Kota</option>
                                </select>
                            </div>

                            <div id="additional-fields" class="row mb-3" style="display: none;">
                                <div class="col-md-6">
                                    <label for="ca" class="form-label">Cash Advanced</label>
                                    <select class="form-select" id="ca" name="ca">
                                        <option value="Tidak">Tidak</option>
                                        <option value="Ya">Ya</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mt-3">
                                    <label for="tiket" class="form-label">Ticket</label>
                                    <select class="form-select" id="tiket" name="tiket">
                                        <option value="Tidak">Tidak</option>
                                        <option value="Ya">Ya</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mt-3">
                                    <label for="hotel" class="form-label">Hotel</label>
                                    <select class="form-select" id="hotel" name="hotel">
                                        <option value="Tidak">Tidak</option>
                                        <option value="Ya">Ya</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mt-3">
                                    <label for="taksi" class="form-label">Voucher Taksi</label>
                                    <select class="form-select" id="taksi" name="taksi">
                                        <option value="Tidak">Tidak</option>
                                        <option value="Ya">Ya</option>
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
    <!-- JavaScript Part -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var jnsDinasSelect = document.getElementById('jns_dinas');
            var additionalFields = document.getElementById('additional-fields');

            jnsDinasSelect.addEventListener('change', function() {
                if (this.value === 'luar kota') {
                    additionalFields.style.display = 'block';
                } else {
                    additionalFields.style.display = 'none';
                    // Reset all fields to "Tidak"
                    document.getElementById('ca').value = 'Tidak';
                    document.getElementById('tiket').value = 'Tidak';
                    document.getElementById('hotel').value = 'Tidak';
                    document.getElementById('taksi').value = 'Tidak';
                }
            });
        });
    </script>
@endsection

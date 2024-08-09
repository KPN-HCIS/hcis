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
                        <a href="/businessTrip" type="button" class="btn-close btn-close-white"></a>
                    </div>
                    <div class="card-body">
                        <form action="/businessTrip/form/post" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label for="nama" class="form-label">Name</label>
                                <input type="text" class="form-control bg-light" id="nama" name="nama"
                                    style="cursor:not-allowed;" value="{{ $employee_data->fullname }}" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="divisi" class="form-label">Divison</label>
                                <input type="text" class="form-control bg-light" id="divisi" name="divisi"
                                    style="cursor:not-allowed;" value="{{ $employee_data->unit }}" readonly>

                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="mulai" class="form-label">Start Date</label>
                                    <input type="date" class="form-control datepicker" id="mulai" name="mulai"
                                        placeholder="Tanggal Mulai">
                                </div>
                                <div class="col-md-6">
                                    <label for="kembali" class="form-label">End Date</label>
                                    <input type="date" class="form-control datepicker" id="kembali" name="kembali"
                                        placeholder="Tanggal Kembali">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="tujuan" class="form-label">Destination</label>
                                <select class="form-select" name="tujuan" id="tujuan" required>
                                    <option value="">--- Choose Destination ---</option>
                                    @foreach ($locations as $location)
                                        <option value="{{ $location->company_name }}">
                                            {{ $location->area . ' (' . $location->city . ')' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="keperluan" class="form-label">Need (To be filled in according to visit
                                    service)</label>
                                <textarea class="form-control" id="keperluan" name="keperluan" rows="3" required></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="bb_perusahaan" class="form-label">Company Cost Expenses (PT Service Needs / Not
                                    PT Payroll)</label>
                                <select class="form-select" id="bb_perusahaan" name="bb_perusahaan" required>
                                    <option value="">--- Choose PT ---</option>
                                    @foreach ($companies as $company)
                                        <option value="{{ $company->contribution_level_code }}">
                                            {{ $company->contribution_level . ' (' . $company->contribution_level_code . ')' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="norek_krywn" class="form-label">Employee Account Number</label>
                                <input type="number" class="form-control" id="norek_krywn" name="norek_krywn"
                                    placeholder="Account Number" required>
                            </div>

                            <div class="mb-3">
                                <label for="nama_bank" class="form-label">Bank Name</label>
                                <input type="text" class="form-control" id="nama_bank" name="nama_bank"
                                    placeholder="Bank Name" required>
                            </div>

                            <div class="mb-3">
                                <label for="nama_pemilik_rek" class="form-label">Name of Account Owner</label>
                                <input type="text" class="form-control" id="nama_pemilik_rek" name="nama_pemilik_rek"
                                    placeholder="Name of account owner" required>
                            </div>

                            <!-- HTML Part -->
                            <div class="col-md-14 mb-3">
                                <label for="jns_dinas" class="form-label">Type of Service</label>
                                <select class="form-select" id="jns_dinas" name="jns_dinas" required>
                                    <option value="" selected disabled>-- Choose Type of Service --</option>
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

                                    <div class="row mt-2" id="ca_div" style="display: none;">
                                        <div class="col-md-12">
                                            <div class="table-responsive-sm">
                                                <div class="d-flex flex-column gap-2">
                                                    <div class="text-bg-danger p-2" style="text-align:center">Estimated
                                                        Cash Advanced</div>
                                                    <div class="card">
                                                        <div class="card-body">
                                                            <div class="mb-2" id="div_allowance">
                                                                <label class="form-label">Allowance (Perdiem)</label>
                                                                <div class="input-group">
                                                                    <div class="input-group-append">
                                                                        <span class="input-group-text">Rp</span>
                                                                    </div>
                                                                    <input class="form-control bg-light" name="allowance"
                                                                        id="allowance" type="text" min="0"
                                                                        value="0" readonly>
                                                                </div>
                                                            </div>
                                                            <div class="mb-2">
                                                                <label class="form-label">Transportation</label>
                                                                <div class="input-group">
                                                                    <div class="input-group-append">
                                                                        <span class="input-group-text">Rp</span>
                                                                    </div>
                                                                    <input class="form-control" name="transport"
                                                                        id="transport" type="text" min="0"
                                                                        value="0">
                                                                </div>
                                                            </div>
                                                            <div class="mb-2">
                                                                <label class="form-label">Accommodation</label>
                                                                <div class="input-group">
                                                                    <div class="input-group-append">
                                                                        <span class="input-group-text">Rp</span>
                                                                    </div>
                                                                    <input class="form-control" name="accommodation"
                                                                        id="accommodation" type="text" min="0"
                                                                        value="0">
                                                                </div>
                                                            </div>
                                                            <div class="mb-2">
                                                                <label class="form-label">Other</label>
                                                                <div class="input-group">
                                                                    <div class="input-group-append">
                                                                        <span class="input-group-text">Rp</span>
                                                                    </div>
                                                                    <input class="form-control" name="other"
                                                                        id="other" type="text" min="0"
                                                                        value="0">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 mt-3">
                                    <label for="tiket" class="form-label">Ticket</label>
                                    <select class="form-select" id="tiket" name="tiket">
                                        <option value="Tidak">Tidak</option>
                                        <option value="Ya">Ya</option>
                                    </select>
                                    <div class="row mt-2" id="tiket_div" style="display: none;">
                                        <div class="col-md-12">
                                            <div class="table-responsive-sm">
                                                <div class="d-flex flex-column gap-2">
                                                    <div class="text-bg-danger p-2" style="text-align:center">Estimated
                                                        Cash Advanced</div>
                                                    <div class="card">
                                                        <div class="card-body">
                                                            <div class="mb-2" id="div_allowance">
                                                                <label class="form-label">Allowance (Perdiem)</label>
                                                                <div class="input-group">
                                                                    <div class="input-group-append">
                                                                        <span class="input-group-text">Rp</span>
                                                                    </div>
                                                                    <input class="form-control bg-light" name="allowance"
                                                                        id="allowance" type="text" min="0"
                                                                        value="0" readonly>
                                                                </div>
                                                            </div>
                                                            <div class="mb-2">
                                                                <label class="form-label">Transportation</label>
                                                                <div class="input-group">
                                                                    <div class="input-group-append">
                                                                        <span class="input-group-text">Rp</span>
                                                                    </div>
                                                                    <input class="form-control" name="transport"
                                                                        id="transport" type="text" min="0"
                                                                        value="0">
                                                                </div>
                                                            </div>
                                                            <div class="mb-2">
                                                                <label class="form-label">Accommodation</label>
                                                                <div class="input-group">
                                                                    <div class="input-group-append">
                                                                        <span class="input-group-text">Rp</span>
                                                                    </div>
                                                                    <input class="form-control" name="accommodation"
                                                                        id="accommodation" type="text" min="0"
                                                                        value="0">
                                                                </div>
                                                            </div>
                                                            <div class="mb-2">
                                                                <label class="form-label">Other</label>
                                                                <div class="input-group">
                                                                    <div class="input-group-append">
                                                                        <span class="input-group-text">Rp</span>
                                                                    </div>
                                                                    <input class="form-control" name="other"
                                                                        id="other" type="text" min="0"
                                                                        value="0">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mt-3">
                                    <label for="hotel" class="form-label">Hotel</label>
                                    <select class="form-select" id="hotel" name="hotel">
                                        <option value="Tidak">Tidak</option>
                                        <option value="Ya">Ya</option>
                                    </select>
                                    <div class="row mt-2" id="hotel_div" style="display: none;">
                                        <div class="col-md-12">
                                            <div class="table-responsive-sm">
                                                <div class="d-flex flex-column gap-2">
                                                    <div class="text-bg-primary p-2" style="text-align:center">Add Hotel
                                                    </div>
                                                    <div class="card">
                                                        <div class="card-body">
                                                            <div class="mb-2" id="hotel_div">
                                                                <label class="form-label">No Hotel</label>
                                                                <div class="input-group">
                                                                    <input class="form-control bg-white" name="no_htl"
                                                                        id="no_htl" type="number" min="0"
                                                                        placeholder="0" required>
                                                                </div>
                                                            </div>
                                                            <div class="mb-2">
                                                                <label class="form-label">Hotel Name</label>
                                                                <div class="input-group">
                                                                    <input class="form-control bg-white" name="nama_htl"
                                                                        id="nama_htl" type="text" min="0"
                                                                        required>
                                                                </div>
                                                            </div>
                                                            <div class="mb-2">
                                                                <label class="form-label">Hotel Location</label>
                                                                <div class="input-group">
                                                                    <input class="form-control bg-white" name="lokasi_htl"
                                                                        id="lokasi_htl" type="text" min="0"
                                                                        placeholder="ex: Jakarta" required>
                                                                </div>
                                                            </div>
                                                            <div class="mb-2">
                                                                <label class="form-label">Total Room</label>
                                                                <div class="input-group">
                                                                    <input class="form-control bg-white" name="jmlkmr_htl"
                                                                        id="jmlkmr_htl" type="text" min="0"
                                                                        placeholder="ex: 1" required>
                                                                </div>
                                                            </div>
                                                            <div class="mb-2">
                                                                <label for="bed_htl" class="form-label">Bed Size</label>
                                                                <select class="form-select" id="bed_htl" name="bed_htl"
                                                                    required>
                                                                    <option value="King Size">King Size</option>
                                                                    <option value="Queen Size">Queen Size</option>
                                                                    <option value="Full">Full</option>
                                                                    <option value="Twin XL">Twin XL</option>
                                                                    <option value="Twin">Twin</option>
                                                                </select>
                                                            </div>
                                                            <div class="mb-2">
                                                                <label for="tgl_masuk_htl" class="form-label">Check In
                                                                    Date</label>
                                                                <input type="date" class="form-control datepicker"
                                                                    id="tgl_masuk_htl" name="tgl_masuk_htl"
                                                                    onchange="calculateTotalDays()">
                                                            </div>
                                                            <div class="mb-2">
                                                                <label for="tgl_keluar_htl" class="form-label">Check Out
                                                                    Date</label>
                                                                <input type="date" class="form-control datepicker"
                                                                    id="tgl_keluar_htl" name="tgl_keluar_htl"
                                                                    onchange="calculateTotalDays()">
                                                            </div>
                                                            <div class="mb-2">
                                                                <label for="total_hari" class="form-label">Total
                                                                    Days</label>
                                                                <input type="number" class="form-control datepicker"
                                                                    id="total_hari" name="total_hari" readonly>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mt-3">
                                    <label for="taksi" class="form-label">Voucher Taksi</label>
                                    <select class="form-select" id="taksi" name="taksi">
                                        <option value="Tidak">Tidak</option>
                                        <option value="Ya">Ya</option>
                                    </select>
                                    <div class="row mt-2" id="taksi_div" style="display: none;">
                                        <div class="col-md-12">
                                            <div class="table-responsive-sm">
                                                <div class="d-flex flex-column gap-2">
                                                    <div class="text-bg-primary p-2 r-3" style="text-align:center">Voucher
                                                        Taksi</div>
                                                    <div class="card">
                                                        <div class="card-body">
                                                            <div class="mb-2" id="taksi_div">
                                                                <label class="form-label">No. Voucher Taxi</label>
                                                                <div class="input-group">
                                                                    <div class="input-group-append">
                                                                    </div>
                                                                    <input class="form-control bg-white" name="no_vt"
                                                                        id="no_vt" type="text" min="0"
                                                                        value="0">
                                                                </div>
                                                            </div>
                                                            <div class="mb-2">
                                                                <label class="form-label">Voucher Nominal</label>
                                                                <div class="input-group">
                                                                    <div class="input-group-append">
                                                                        <span class="input-group-text">Rp</span>
                                                                    </div>
                                                                    <input class="form-control" name="nominal_vt"
                                                                        id="nominal_vt" type="text" min="0"
                                                                        value="0">
                                                                </div>
                                                            </div>
                                                            <div class="mb-2">
                                                                <label class="form-label">Voucher Keeper</label>
                                                                <div class="input-group">
                                                                    <div class="input-group-append">
                                                                        <span class="input-group-text">Rp</span>
                                                                    </div>
                                                                    <input class="form-control" name="keeper_vt"
                                                                        id="keeper_vt" type="text" min="0"
                                                                        value="0">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <input type="hidden" name="status" value="Pending">

                            <div class="d-flex justify-content-end mt-3">
                                <button type="submit" class="btn btn-outline-primary me-2">Save as Draft</button>
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
        function calculateTotalDays() {
            const checkInDate = new Date(document.getElementById('tgl_masuk_htl').value);
            const checkOutDate = new Date(document.getElementById('tgl_keluar_htl').value);

            if (checkInDate && checkOutDate) {
                // Calculate the difference in milliseconds
                const diffTime = Math.abs(checkOutDate - checkInDate);
                // Convert to days and add 1 to include both check-in and check-out days
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;

                // Set the result in the total_hari input
                document.getElementById('total_hari').value = diffDays;
            } else {
                // If either date is not set, clear the total
                document.getElementById('total_hari').value = '';
            }
        }
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
        document.addEventListener('DOMContentLoaded', function() {
            const caSelect = document.getElementById('ca');
            const caNbtDiv = document.getElementById('ca_div');

            const hotelSelect = document.getElementById('hotel');
            const hotelDiv = document.getElementById('hotel_div');

            const taksiSelect = document.getElementById('taksi');
            const taksiDiv = document.getElementById('taksi_div');

            const tiketSelect = document.getElementById('tiket');
            const tiketDiv = document.getElementById('tiket_div');

            function toggleDisplay(selectElement, targetDiv) {
                if (selectElement.value === 'Ya') {
                    targetDiv.style.display = 'block';
                } else {
                    targetDiv.style.display = 'none';
                }
            }

            caSelect.addEventListener('change', function() {
                toggleDisplay(caSelect, caNbtDiv);
            });

            hotelSelect.addEventListener('change', function() {
                toggleDisplay(hotelSelect, hotelDiv);
            });

            taksiSelect.addEventListener('change', function() {
                toggleDisplay(taksiSelect, taksiDiv);
            });

            tiketSelect.addEventListener('change', function() {
                toggleDisplay(tiketSelect, tiketDiv);
            });
        });

        document.getElementById('kembali').addEventListener('change', function() {
            var mulaiDate = document.getElementById('mulai').value;
            var kembaliDate = this.value;

            if (kembaliDate < mulaiDate) {
                alert('Return date cannot be earlier than Start date.');
                this.value = ''; // Reset the kembali field
            }
        });
        document.getElementById('tgl_keluar_htl').addEventListener('change', function() {
            var masukHtl = document.getElementById('tgl_masuk_htl').value;
            var keluarDate = this.value;

            if (masukHtl && keluarDate) {
                var checkInDate = new Date(masukHtl);
                var checkOutDate = new Date(keluarDate);

                if (checkOutDate < checkInDate) {
                    alert("Check out date cannot be earlier than check in date.");
                    this.value = ''; // Reset the check out date field
                }
            }
        });
    </script>
@endsection

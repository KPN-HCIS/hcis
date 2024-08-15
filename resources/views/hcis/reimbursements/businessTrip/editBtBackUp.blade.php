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
                            </div>

                            {{-- <div class="row mb-3">
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
                            </div> --}}



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
                                <label for="bb_perusahaan_{{ $n->id }}" class="form-label">Beban Biaya Perusahaan
                                    (PT Keperluan Dinas / Bukan PT Payroll)</label>
                                <select class="form-select" id="bb_perusahaan_{{ $n->id }}" name="bb_perusahaan">
                                    <option value="">--- Pilih PT ---</option>
                                    @foreach ($companies as $company)
                                        <option value="{{ $company->contribution_level_code }}"
                                            {{ $company->contribution_level_code == $n->bb_perusahaan ? 'selected' : '' }}>
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

                            <div class="col-md-14 mb-3">
                                <label for="jns_dinas" class="form-label">Jenis Dinas</label>
                                <select class="form-select" id="jns_dinas" name="jns_dinas">
                                    <option selected disabled>-- Pilih Jenis Dinas --</option>
                                    <option value="dalam kota" {{ $n->jns_dinas == 'dalam kota' ? 'selected' : '' }}>Dinas
                                        Dalam Kota</option>
                                    <option value="luar kota" {{ $n->jns_dinas == 'luar kota' ? 'selected' : '' }}>Dinas
                                        Luar Kota</option>
                                </select>
                            </div>

                            <div id="additional-fields" class="row mb-3">
                                <div class="col-md-6">
                                    <label for="ca" class="form-label">Cash Advanced</label>
                                    <select class="form-select" id="ca" name="ca">
                                        <option value="Tidak" {{ $n->ca == 'Tidak' ? 'selected' : '' }}>Tidak</option>
                                        <option value="Ya" {{ $n->ca == 'Ya' ? 'selected' : '' }}>Ya</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mt-3">
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
                                        <option value="Tidak" {{ $n->taksi == 'Tidak' ? 'selected' : '' }}>Tidak</option>
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
    <!-- JavaScript Part -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('save-draft').addEventListener('click', function(event) {
                event.preventDefault();

                // Remove the existing status input
                const existingStatus = document.getElementById('status');
                if (existingStatus) {
                    existingStatus.remove();
                }

                // Create a new hidden input for "Draft"
                const draftInput = document.createElement('input');
                draftInput.type = 'hidden';
                draftInput.name = 'status';
                draftInput.value = 'Draft';
                draftInput.id = 'status';

                // Append the draft input to the form
                this.closest('form').appendChild(draftInput);

                // Submit the form
                this.closest('form').submit();
            });
        });


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
        document.addEventListener('DOMContentLoaded', function() {
            const ticketSelect = document.getElementById('tiket');
            const ticketDiv = document.getElementById('tiket_div');

            // Hide/show ticket form based on select option
            ticketSelect.addEventListener('change', function() {
                if (this.value === 'Ya') {
                    ticketDiv.style.display = 'block';
                } else {
                    ticketDiv.style.display = 'none';
                }
            });

            // Rest of your existing code for handling multiple ticket forms
            for (let i = 1; i <= 4; i++) {
                const yesRadio = document.getElementById(`more_tkt_yes_${i}`);
                const noRadio = document.getElementById(`more_tkt_no_${i}`);
                const nextForm = document.getElementById(`ticket-form-${i + 1}`);

                yesRadio.addEventListener('change', function() {
                    if (this.checked) {
                        nextForm.style.display = 'block';
                    }
                });

                noRadio.addEventListener('change', function() {
                    if (this.checked) {
                        nextForm.style.display = 'none';
                        // Hide all subsequent forms
                        for (let j = i + 1; j <= 5; j++) {
                            document.getElementById(`ticket-form-${j}`).style.display = 'none';
                        }
                        // Reset radio buttons for subsequent forms
                        for (let j = i + 1; j <= 4; j++) {
                            document.getElementById(`more_tkt_no_${j}`).checked = true;
                        }
                    }
                });
            }

            // Handle Round Trip options
            const ticketTypes = document.querySelectorAll('select[name="type_tkt[]"]');
            ticketTypes.forEach((select, index) => {
                select.addEventListener('change', function() {
                    const roundTripOptions = this.closest('.card-body').querySelector(
                        '.round-trip-options');
                    if (this.value === 'Round Trip') {
                        roundTripOptions.style.display = 'block';
                    } else {
                        roundTripOptions.style.display = 'none';
                    }
                });
            });
        });
        document.addEventListener('DOMContentLoaded', function() {
            // Existing ticket code...

            // Hotel form handling
            for (let i = 1; i <= 4; i++) {
                const yesRadio = document.getElementById(`more_htl_yes_${i}`);
                const noRadio = document.getElementById(`more_htl_no_${i}`);
                const nextForm = document.getElementById(`hotel-form-${i + 1}`);

                yesRadio.addEventListener('change', function() {
                    if (this.checked) {
                        nextForm.style.display = 'block';
                    }
                });

                noRadio.addEventListener('change', function() {
                    if (this.checked) {
                        nextForm.style.display = 'none';
                        // Hide all subsequent forms
                        for (let j = i + 1; j <= 5; j++) {
                            document.getElementById(`hotel-form-${j}`).style.display = 'none';
                        }
                        // Reset radio buttons for subsequent forms
                        for (let j = i + 1; j <= 4; j++) {
                            document.getElementById(`more_htl_no_${j}`).checked = true;
                        }
                    }
                });
            }

            // Calculate total days for each hotel form
            function calculateTotalDays(index) {
                const checkIn = document.querySelector(`#hotel-form-${index} input[name="tgl_masuk_htl[]"]`);
                const checkOut = document.querySelector(`#hotel-form-${index} input[name="tgl_keluar_htl[]"]`);
                const totalDays = document.querySelector(`#hotel-form-${index} input[name="total_hari[]"]`);

                if (checkIn && checkOut && totalDays) {
                    const start = new Date(checkIn.value);
                    const end = new Date(checkOut.value);

                    if (checkIn.value && checkOut.value) {
                        // Calculate difference in milliseconds and convert to days, excluding the same day
                        const difference = Math.ceil((end - start) / (1000 * 60 * 60 * 24));
                        if (difference < 0) {
                            alert("Check out date cannot be earlier than check in date.");
                            checkOut.value = ''; // Clear the check-out date if invalid
                            totalDays.value = ''; // Clear the total days if check-out date is reset
                        } else {
                            totalDays.value = difference >= 0 ? difference : 0;
                        }
                    } else {
                        totalDays.value = ''; // Clear total days if dates are not set
                    }
                } else {
                    console.error("Elements not found. Check selectors.");
                }
            }

            // Add event listeners for date inputs
            for (let i = 1; i <= 5; i++) {
                const checkIn = document.querySelector(`#hotel-form-${i} input[name="tgl_masuk_htl[]"]`);
                const checkOut = document.querySelector(`#hotel-form-${i} input[name="tgl_keluar_htl[]"]`);

                if (checkIn && checkOut) {
                    checkIn.addEventListener('change', () => calculateTotalDays(i));
                    checkOut.addEventListener('change', () => calculateTotalDays(i));
                }
            }
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

        document.getElementById('type_tkt').addEventListener('change', function() {
            var roundTripOptions = document.getElementById('roundTripOptions');
            if (this.value === 'Round Trip') {
                roundTripOptions.style.display = 'block';
            } else {
                roundTripOptions.style.display = 'none';
            }
        });


        function toggleOthers() {
            // ca_type ca_nbt ca_e
            var locationFilter = document.getElementById("tujuan");
            var others_location = document.getElementById("others_location");

            if (locationFilter.value === "Others") {
                others_location.style.display = "block";
            } else {
                others_location.style.display = "none";
                others_location.value = "";
            }
        }

        function validateDates(index) {
            // Get the departure and return date inputs for the given form index
            const departureDate = document.querySelector(`#tgl_brkt_tkt_${index}`);
            const returnDate = document.querySelector(`#tgl_plg_tkt_${index}`);

            // Get the departure and return time inputs for the given form index
            const departureTime = document.querySelector(`#jam_brkt_tkt_${index}`);
            const returnTime = document.querySelector(`#jam_plg_tkt_${index}`);

            if (departureDate && returnDate) {
                const depDate = new Date(departureDate.value);
                const retDate = new Date(returnDate.value);

                // Check if both dates are valid
                if (depDate && retDate) {
                    // Validate if return date is earlier than departure date
                    if (retDate < depDate) {
                        alert("Return date cannot be earlier than departure date.");
                        returnDate.value = ''; // Reset the return date field
                    } else if (retDate.getTime() === depDate.getTime() && departureTime && returnTime) {
                        // If dates are the same, validate time
                        const depTime = departureTime.value;
                        const retTime = returnTime.value;

                        // Check if both times are set and validate
                        if (depTime && retTime) {
                            const depDateTime = new Date(`1970-01-01T${depTime}:00`);
                            const retDateTime = new Date(`1970-01-01T${retTime}:00`);

                            if (retDateTime < depDateTime) {
                                alert("Return time cannot be earlier than departure time on the same day.");
                                returnTime.value = ''; // Reset the return time field
                            }
                        }
                    }
                }
            }
        }




        document.getElementById('nik').addEventListener('change', function() {
            var nik = this.value;

            fetch('/get-employee-data?nik=' + nik)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('jk_tkt').value = data.jk_tkt;
                        document.getElementById('tlp_tkt').value = data.tlp_tkt;
                    } else {
                        alert('Employee data not found!');
                    }
                })
                .catch(error => console.error('Error:', error));
        });
    </script>
@endsection

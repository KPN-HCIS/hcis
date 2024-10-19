@extends('layouts_.vertical', ['page_title' => 'Cash Advanced'])

@section('css')
    <!-- Sertakan CSS Bootstrap jika diperlukan -->
    {{-- <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-beta3/css/bootstrap.min.css"> --}}
    <style>
        th, td{
            vertical-align: top !important;
        }

        .table > :not(caption) > * > * {
            /* padding: 0.2rem 0.2rem; Sesuaikan padding di sini */
        }
    </style>
@endsection

@section('content')
    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('cashadvanced') }}">{{ $parentLink }}</a></li>
                            <li class="breadcrumb-item active">{{ $link }}</li>
                        </ol>
                    </div>
                    <h4 class="page-title">{{ $link }}</h4>
                </div>
            </div>
        </div>
            <div class="card shadow-none">
                <div class="card-header mb-2 d-flex bg-primary justify-content-between">
                    <p></p>
                    <h4 class="modal-title text-white" id="viewFormEmployeeLabel">Cash Advance No "<b>{{ $transactions->no_ca }}</b>"</h4>
                    <a href="{{ route('cashadvanced') }}" type="button" class="btn btn-close btn-close-white"></a>
                </div>
                <div class="card-body p-2">
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <table style="border: none; border-collapse: collapse;">
                                <tr>
                                    <th style="width:40%;">Employee ID</th>
                                    <td>:</td>
                                    <td>{{ $employee_data->employee_id }}</td>
                                </tr>
                                <tr>
                                    <th>Employee Name</th>
                                    <td>:</td>
                                    <td>{{ $employee_data->fullname }}</td>
                                </tr>
                                <tr>
                                    <th >Unit</th>
                                    <td >:</td>
                                    <td >{{ $employee_data->unit }}</td>
                                </tr>
                                <tr>
                                    <th >Job Level</th>
                                    <td >:</td>
                                    <td >{{ $employee_data->job_level }}</td>
                                </tr>
                                <tr>
                                    <th >Costing Company</th>
                                    <td >:</td>
                                    <td >{{ $transactions->companies->contribution_level }} ({{ $transactions->companies->contribution_level_code }})</td>
                                </tr>
                                <tr>
                                    <th >Destination</th>
                                    <td >:</td>
                                    <td >
                                        @if ($transactions->destination == 'Others')
                                            {{ $transactions->others_location }}
                                        @else
                                            {{ $transactions->destination }}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th >CA Purposes</th>
                                    <td >:</td>
                                    <td >{{ $transactions->ca_needs }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6 mb-2">
                            <table style="border: none; border-collapse: collapse; padding: 1%;">
                                <tr>
                                    <th class="label" style="border: none; width:40%;">Start Date</th>
                                    <td class="block" style="border: none;">:</td>
                                    <td >{{ $transactions->start_date }}</td>
                                </tr>
                                <tr>
                                    <th >End Date</th>
                                    <td >:</td>
                                    <td >{{$transactions->end_date}}</td>
                                </tr>
                                <tr>
                                    <th >Total Date</th>
                                    <td >:</td>
                                    <td >{{ $transactions->total_days }} Days</td>
                                </tr>
                                <tr>
                                    <th >CA Date Required</th>
                                    <td >:</td>
                                    <td >{{ \Carbon\Carbon::parse($transactions->date_required)->format('d-M-y') }}</td>
                                </tr>
                                <tr>
                                    <th >Declaration Estimate</th>
                                    <td >:</td>
                                    <td >{{ \Carbon\Carbon::parse($transactions->declare_estimate)->format('d-M-y') }}</td>
                                </tr>
                                <tr>
                                    <th >Cash Advanced Type</th>
                                    <td >:</td>
                                    <td >
                                        @if ($transactions->type_ca == 'dns')
                                            Bussiness Trip
                                        @elseif ($transactions->type_ca == 'entr')
                                            Entertainment
                                        @else
                                            Non Business Trip
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th >No. SPPD</th>
                                    <td >:</td>
                                    <td >
                                        @if ($transactions->no_sppd == NULL)
                                            -
                                        @else
                                            {{ $transactions->no_sppd }}
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <form enctype="multipart/form-data" id="cashadvancedForm" method="post"
                        action="{{ route('cashadvanced.declare', encrypt($transactions->id)) }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-4 mb-2" style="display: none;">
                                <label class="form-label" for="type">CA Type</label>
                                <select name="ca_type_disabled" id="ca_type" class="form-control bg-light" disabled>
                                    <option value="">-</option>
                                    <option value="dns" {{ $transactions->type_ca == 'dns' ? 'selected' : '' }}>
                                        Business Trip
                                    </option>
                                    <option value="ndns" {{ $transactions->type_ca == 'ndns' ? 'selected' : '' }}>
                                        Non Business Trip
                                    </option>
                                    <option value="entr" {{ $transactions->type_ca == 'entr' ? 'selected' : '' }}>
                                        Entertainment
                                    </option>
                                </select>
                                <input type="hidden" name="contribution_level_code" value="{{ $transactions->contribution_level_code }}">
                                <input type="hidden" name="ca_type" value="{{ $transactions->type_ca }}">
                                <input class="form-control" id="perdiem" name="perdiem" type="hidden" value="{{ $perdiem->amount }}" readonly>
                                <input type="date" name="start_date" id="start_date" class="form-control"
                                    placeholder="mm/dd/yyyy" value="{{ $transactions->start_date }}">
                                <input type="date" name="end_date" id="end_date" class="form-control"
                                    placeholder="mm/dd/yyyy" value="{{ $transactions->end_date }}">
                            </div>
                            <div id="routeInfo" data-route="true"></div>
                            @php
                                $detailCA = json_decode($transactions->detail_ca, true) ?? [];
                                $declareCA = json_decode($transactions->declare_ca, true) ?? [];
                            @endphp
                            <br>
                            <div class="row" id="ca_bt" style="display: none;">
                                @if ($transactions->type_ca == 'dns')
                                    <div class="col-md-12">
                                        <div class="table-responsive-sm">
                                            <div class="d-flex flex-column">
                                                <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                                                    <li class="nav-item" role="presentation">
                                                        <button class="nav-link {{ isset($declareCA['detail_perdiem'][0]['start_date']) && $declareCA['detail_perdiem'][0]['start_date'] ? 'active' : '' }}" id="pills-perdiem-tab"
                                                            data-bs-toggle="pill" data-bs-target="#pills-perdiem" type="button"
                                                            role="tab" aria-controls="pills-perdiem"
                                                            aria-selected="{{ isset($declareCA['detail_perdiem'][0]['start_date']) && $declareCA['detail_perdiem'][0]['start_date'] ? 'true' : 'false' }}">Perdiem Plan</button>
                                                    </li>
                                                    <li class="nav-item" role="presentation">
                                                        <button class="nav-link {{ !isset($declareCA['detail_perdiem'][0]['start_date']) && isset($declareCA['detail_transport'][0]['tanggal']) && $declareCA['detail_transport'][0]['tanggal'] ? 'active' : '' }}" id="pills-transport-tab"
                                                            data-bs-toggle="pill" data-bs-target="#pills-transport" type="button" role="tab"
                                                            aria-controls="pills-transport"
                                                            aria-selected="{{ isset($declareCA['detail_transport'][0]['tanggal']) && $declareCA['detail_transport'][0]['tanggal'] ? 'true' : 'false' }}">Transport Plan</button>
                                                    </li>
                                                    <li class="nav-item" role="presentation">
                                                        <button class="nav-link {{ !isset($declareCA['detail_perdiem'][0]['start_date']) && !isset($declareCA['detail_transport'][0]['tanggal']) && isset($declareCA['detail_penginapan'][0]['start_date']) && $declareCA['detail_penginapan'][0]['start_date'] ? 'active' : '' }}" id="pills-accomodation-tab"
                                                            data-bs-toggle="pill" data-bs-target="#pills-accomodation"
                                                            type="button" role="tab" aria-controls="pills-accomodation"
                                                            aria-selected="{{ isset($declareCA['detail_penginapan'][0]['start_date']) && $declareCA['detail_penginapan'][0]['start_date'] ? 'true' : 'false' }}">Accomodation Plan</button>
                                                    </li>
                                                    <li class="nav-item" role="presentation">
                                                        <button class="nav-link {{ !isset($declareCA['detail_perdiem'][0]['start_date']) && !isset($declareCA['detail_transport'][0]['tanggal']) && !isset($declareCA['detail_penginapan'][0]['start_date']) && isset($declareCA['detail_lainnya'][0]['tanggal']) && $declareCA['detail_lainnya'][0]['tanggal'] ? 'active' : '' }}" id="pills-other-tab" data-bs-toggle="pill"
                                                            data-bs-target="#pills-other" type="button" role="tab"
                                                            aria-controls="pills-other" aria-selected="{{ isset($declareCA['detail_lainnya'][0]['tanggal']) && $declareCA['detail_lainnya'][0]['tanggal'] ? 'true' : 'false' }}">Other Plan</button>
                                                    </li>
                                                </ul>
                                                <div class="tab-content" id="pills-tabContent">
                                                    <div class="tab-pane fade {{ isset($declareCA['detail_perdiem'][0]['start_date']) && $declareCA['detail_perdiem'][0]['start_date'] ? 'show active' : '' }}"
                                                        id="pills-perdiem" role="tabpanel"
                                                        aria-labelledby="pills-perdiem-tab">
                                                        @include('hcis.reimbursements.cashadv.form_dec.perdiem')
                                                    </div>
                                                    <div class="tab-pane fade {{ !isset($declareCA['detail_perdiem'][0]['start_date']) && isset($declareCA['detail_transport'][0]['tanggal']) && $declareCA['detail_transport'][0]['tanggal'] ? 'show active' : '' }}"
                                                        id="pills-transport" role="tabpanel"
                                                        aria-labelledby="pills-transport-tab">
                                                        @include('hcis.reimbursements.cashadv.form_dec.transport')
                                                    </div>
                                                    <div class="tab-pane fade {{ !isset($declareCA['detail_perdiem'][0]['start_date']) && !isset($declareCA['detail_transport'][0]['tanggal']) && isset($declareCA['detail_penginapan'][0]['start_date']) && $declareCA['detail_penginapan'][0]['start_date'] ? 'show active' : '' }}"
                                                        id="pills-accomodation" role="tabpanel"
                                                        aria-labelledby="pills-accomodation-tab">
                                                        @include('hcis.reimbursements.cashadv.form_dec.penginapan')
                                                    </div>
                                                    <div class="tab-pane fade {{ !isset($declareCA['detail_perdiem'][0]['start_date']) && !isset($declareCA['detail_transport'][0]['tanggal']) && !isset($declareCA['detail_penginapan'][0]['start_date']) && isset($declareCA['detail_lainnya'][0]['tanggal']) && $declareCA['detail_lainnya'][0]['tanggal'] ? 'show active' : '' }}" id="pills-other" role="tabpanel"
                                                        aria-labelledby="pills-other-tab">
                                                        @include('hcis.reimbursements.cashadv.form_dec.others')
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                </div>
                            @endif
                        </div>
                        <div class="row" id="ca_nbt" style="display: none;">
                            @if ($transactions->type_ca == 'ndns')
                                <div class="col-md-12">
                                    <div class="table-responsive-sm">
                                        <div class="d-flex flex-column">
                                            @include('hcis.reimbursements.cashadv.form_dec.nbt')
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="row" id="ca_e" style="display: none;">
                            @if ($transactions->type_ca == 'entr')
                                <div class="col-md-12">
                                    <div class="table-responsive-sm">
                                        <div class="d-flex flex-column">
                                            <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link active" id="pills-detail-tab"
                                                        data-bs-toggle="pill" data-bs-target="#pills-detail" type="button"
                                                        role="tab" aria-controls="pills-detail"
                                                        aria-selected="true">Detail Entertain Plan</button>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link" id="pills-relation-tab" data-bs-toggle="pill"
                                                        data-bs-target="#pills-relation" type="button" role="tab"
                                                        aria-controls="pills-relation" aria-selected="false">Relation Entertain
                                                        Plan</button>
                                                </li>
                                            </ul>
                                            <div class="tab-content" id="pills-tabContent">
                                                <div class="tab-pane fade show active" id="pills-detail" role="tabpanel"
                                                    aria-labelledby="pills-detail-tab">
                                                    @include('hcis.reimbursements.cashadv.form_dec.detail')
                                                </div>
                                                <div class="tab-pane fade" id="pills-relation" role="tabpanel"
                                                    aria-labelledby="pills-relation-tab">
                                                    @include('hcis.reimbursements.cashadv.form_dec.relation')
                                                </div>
                                            </div>
                                            <button type="button" id="add-more-e-detail" style="display: none"
                                                class="btn btn-primary mt-3">Add More</button>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-2 mt-2">
                            <label for="prove_declare" class="form-label">Upload Document</label>

                            <!-- Input file -->
                            <input type="file" id="prove_declare" name="prove_declare"
                                accept="image/*, application/pdf" class="form-control" onchange="previewFile()"
                                required>
                            <input type="hidden" name="existing_prove_declare"
                                value="{{ $transactions->prove_declare }}">

                            <!-- Show existing file -->
                            <div id="existing-file-preview" class="mt-2" style="display:none">
                                @if ($transactions->prove_declare)
                                    @php
                                        $extension = pathinfo($transactions->prove_declare, PATHINFO_EXTENSION);
                                    @endphp

                                    @if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif']))
                                        <!-- Tampilkan gambar -->
                                        <a href="{{ asset('uploads/proofs/' . $transactions->prove_declare) }}"
                                            target="_blank">
                                            <img id="existing-image"
                                                src="{{ asset('uploads/proofs/' . $transactions->prove_declare) }}"
                                                alt="Proof Image" style="max-width: 200px;">
                                        </a>
                                        <p>Click on the image to view the full size</p>
                                    @elseif($extension == 'pdf')
                                        <!-- Tampilkan tautan untuk PDF -->
                                        <a id="existing-pdf"
                                            href="{{ asset('uploads/proofs/' . $transactions->prove_declare) }}"
                                            target="_blank">
                                            <img src="https://img.icons8.com/color/48/000000/pdf.png" alt="PDF File"
                                                style="max-width: 48px;">
                                            <p>Click to view PDF</p>
                                        </a>
                                    @else
                                        <p>File type not supported.</p>
                                    @endif
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="form-label">Total Cash Advanced Request</label>
                            <div class="input-group">
                                <div class="input-group-append">
                                    <span class="input-group-text">Rp</span>
                                </div>
                                <input class="form-control bg-light" name="totalca_deklarasi" id="totalca_deklarasi"
                                    type="text" min="0"
                                    value="{{ number_format($transactions->total_ca, 0, ',', '.') }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="form-label">Total Cash Advanced Deklarasi</label>
                            <div class="input-group">
                                <div class="input-group-append">
                                    <span class="input-group-text">Rp</span>
                                </div>
                                <input class="form-control bg-light" name="totalca" id="totalca"
                                    type="text" min="0"
                                    value="{{ number_format($transactions->total_real, 0, ',', '.') }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-4 mb-2" >
                            <label class="form-label">Total Cash Advanced Balance</label>
                            <div class="input-group">
                                <div class="input-group-append">
                                    <span class="input-group-text">Rp</span>
                                </div>
                                <input class="form-control bg-light" name="totalca_real" id="totalca_real"
                                    type="text"
                                    value="{{ number_format($transactions->total_cost, 0, ',', '.') }}" readonly>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="no_id" id="no_id" value="{{ $transactions->id }}"
                        class="form-control bg-light" readonly>
                    <input type="hidden" name="no_ca" id="no_ca" value="{{ $transactions->no_ca }}"
                        class="form-control bg-light" readonly>
                    <input type="hidden" name="bisnis_numb" id="bisnis_numb" value="{{ $transactions->no_sppd }}"
                        class="form-control bg-light" readonly>
                    <br>
                    <div class="row">
                        <div class="p-4 col-md d-md-flex justify-content-end text-center">
                            <input type="hidden" name="repeat_days_selected" id="repeatDaysSelected">
                            <a href="{{ route('cashadvanced') }}" type="button"
                                class="btn mb-2 btn-outline-secondary px-4 me-2">Cancel</a>
                            <button type="submit" name="action_ca_draft" value="Draft"
                                class=" btn mb-2 btn-secondary btn-pill px-4 me-2 declaration-button-draft">Draft</button>
                            <button type="submit" name="action_ca_submit" value="Pending"
                                class=" btn mb-2 btn-primary btn-pill px-4 me-2 declaration-button">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    </div>

    @include('hcis.reimbursements.cashadv.navigation.modalCashadv')
@endsection
<!-- Tambahkan script JavaScript untuk mengumpulkan nilai repeat_days[] -->
@push('scripts')
    <script>
        function cleanNumber(value) {
            return parseFloat(value.replace(/\./g, '').replace(/,/g, '')) || 0;
        }

        function formatNumber(num) {
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        function formatNumberPerdiem(num) {
            return num.toLocaleString('id-ID');
        }

        function parseNumberPerdiem(value) {
            return parseFloat(value.replace(/\./g, '').replace(/,/g, '')) || 0;
        }

        function parseNumber(value) {
            return parseFloat(value.replace(/\./g, '')) || 0;
        }

        function formatInput(input) {
            let value = input.value.replace(/\./g, '');
            value = parseFloat(value);
            if (!isNaN(value)) {
                input.value = formatNumber(Math.floor(value));
            } else {
                input.value = formatNumber(0);
            }
            calculateTotalNominalBTPerdiem();
            calculateTotalNominalBTTransport();
            calculateTotalNominalBTPenginapan();
            calculateTotalNominalBTLainnya();
            calculateTotalNominalBTTotal();
            calculateTotalNominalBTBalance();
        }

        function calculateTotalNominalBTTotal() {
            let total = 0;
            document.querySelectorAll('input[name="total_bt_perdiem"]').forEach(input => {
                total += parseNumber(input.value);
            });
            document.querySelectorAll('input[name="total_bt_transport"]').forEach(input => {
                total += parseNumber(input.value);
            });
            document.querySelectorAll('input[name="total_bt_penginapan"]').forEach(input => {
                total += parseNumber(input.value);
            });
            document.querySelectorAll('input[name="total_bt_lainnya"]').forEach(input => {
                total += parseNumber(input.value);
            });
            document.querySelector('input[name="totalca"]').value = formatNumber(total);
        }

        function calculateTotalNominalBTBalance() {
            const totaldec = cleanNumber(document.getElementById('totalca_deklarasi').value); // Dapatkan nilai dari input pertama
            const totalca = document.getElementById('totalca').value;
            const total = totaldec - cleanNumber(totalca);
            document.getElementById('totalca_real').value = formatNumber(total);
        }
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // ca_type ca_nbt ca_e
            var ca_type = document.getElementById("ca_type");
            var ca_nbt = document.getElementById("ca_nbt");
            var ca_e = document.getElementById("ca_e");
            var bisnis_numb = document.getElementById("bisnis_numb");

            function toggleDivs() {
                if (ca_type.value === "dns") {
                    ca_bt.style.display = "block";
                    ca_nbt.style.display = "none";
                    ca_e.style.display = "none";
                } else if (ca_type.value === "ndns") {
                    ca_bt.style.display = "none";
                    ca_nbt.style.display = "block";
                    ca_e.style.display = "none";
                    bisnis_numb.style.value = "";
                } else if (ca_type.value === "entr") {
                    ca_bt.style.display = "none";
                    ca_nbt.style.display = "none";
                    ca_e.style.display = "block";
                } else {
                    ca_bt.style.display = "none";
                    ca_nbt.style.display = "none";
                    ca_e.style.display = "none";
                    bisnis_numb.style.value = "";
                }
            }

            toggleDivs();
            ca_type.addEventListener("change", toggleDivs);
        });

        function toggleOthers() {
            var locationFilter = document.getElementById("locationFilter");
            var others_location = document.getElementById("others_location");

            if (locationFilter.value === "Others") {
                others_location.style.display = "block";
            } else {
                others_location.style.display = "none";
                others_location.value = "";
            }
        }

        function validateInput(input) {
            input.value = input.value.replace(/[^0-9]/g, '');
        }

        document.addEventListener('DOMContentLoaded', function() {
            const startDateInput = document.getElementById('start_date');
            const endDateInput = document.getElementById('end_date');
            const totalDaysInput = document.getElementById('totaldays');

            function calculateTotalDays() {
                const startDate = new Date(startDateInput.value);
                const endDate = new Date(endDateInput.value);

                // Memastikan kedua tanggal valid
                if (startDate && endDate && !isNaN(startDate) && !isNaN(endDate)) {
                    const timeDiff = endDate - startDate;
                    const daysDiff = Math.ceil(timeDiff / (1000 * 60 * 60 * 24));
                    const totalDays = daysDiff > 0 ? daysDiff + 1 : 0; // Menambahkan 1 untuk menghitung hari awal
                    totalDaysInput.value = totalDays;
                } else {
                    totalDaysInput.value = 0; // Mengatur ke 0 jika tidak valid
                }
            }

            // Menambahkan event listener untuk perubahan di input tanggal
            startDateInput.addEventListener('change', calculateTotalDays);
            endDateInput.addEventListener('change', calculateTotalDays);
        });;

        document.getElementById('end_date').addEventListener('change', function() {
            const endDate = new Date(this.value);
            const declarationEstimateDate = new Date(endDate);
            declarationEstimateDate.setDate(declarationEstimateDate.getDate() + 3);

            const year = declarationEstimateDate.getFullYear();
            const month = String(declarationEstimateDate.getMonth() + 1).padStart(2, '0');
            const day = String(declarationEstimateDate.getDate()).padStart(2, '0');

            document.getElementById('ca_decla').value = `${year}-${month}-${day}`;
        });
    </script>

    <script>
        function previewFile() {
            const fileInput = document.getElementById('prove_declare');
            const file = fileInput.files[0];
            const preview = document.getElementById('existing-file-preview');
            preview.innerHTML = ''; // Kosongkan preview sebelumnya

            if (file) {
                const fileExtension = file.name.split('.').pop().toLowerCase();

                if (['jpg', 'jpeg', 'png', 'gif'].includes(fileExtension)) {
                    const img = document.createElement('img');
                    img.style.maxWidth = '200px';
                    img.src = URL.createObjectURL(file);
                    preview.appendChild(img);
                } else if (fileExtension === 'pdf') {
                    const link = document.createElement('a');
                    link.href = URL.createObjectURL(file);
                    link.target = '_blank';
                    const icon = document.createElement('img');
                    icon.src = "https://img.icons8.com/color/48/000000/pdf.png";
                    icon.style.maxWidth = '48px';
                    link.appendChild(icon);
                    const text = document.createElement('p');
                    text.textContent = "Click to view PDF";
                    preview.appendChild(link);
                    preview.appendChild(text);
                } else {
                    preview.textContent = 'File type not supported.';
                }
            }
        }
    </script>

    <script>
        document.getElementById('start_date').addEventListener('change', handleDateChange);
        document.getElementById('end_date').addEventListener('change', handleDateChange);

        function handleDateChange() {
            const startDateInput = document.getElementById('start_date');
            const endDateInput = document.getElementById('end_date');

            const startDate = new Date(startDateInput.value);
            const endDate = new Date(endDateInput.value);

            // Set the min attribute of the end_date input to the selected start_date
            endDateInput.min = startDateInput.value;

            // Validate dates
            if (endDate < startDate) {
                Swal.fire({
                    title: 'Cannot Sett Date!',
                    text: 'End Date cannot be earlier than Start Date.',
                    icon: 'warning',
                    confirmButtonColor: "#9a2a27",
                    confirmButtonText: 'Ok'
                });
                endDateInput.value = "";
            }

            // Update min and max values for all dynamic perdiem date fields
            document.querySelectorAll('input[name="start_bt_perdiem[]"]').forEach(function(input) {
                input.min = startDateInput.value;
                input.max = endDateInput.value;
            });

            document.querySelectorAll('input[name="end_bt_perdiem[]"]').forEach(function(input) {
                input.min = startDateInput.value;
                input.max = endDateInput.value;
            });

            document.querySelectorAll('input[name="total_days_bt_perdiem[]"]').forEach(function(input) {
                calculateTotalDaysPerdiem(input);
            });
        }
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-beta3/js/bootstrap.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@endpush

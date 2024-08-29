@extends('layouts_.vertical', ['page_title' => 'Hotel'])

@section('css')
    <!-- Sertakan CSS Bootstrap jika diperlukan -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-beta3/css/bootstrap.min.css">
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
                            <li class="breadcrumb-item"><a href="{{ route('hotel') }}">{{ $parentLink }}</a></li>
                            <li class="breadcrumb-item active">{{ $link }}</li>
                        </ol>
                    </div>
                    <h4 class="page-title">{{ $link }}</h4>
                </div>
            </div>
        </div>
        <div class="d-sm-flex align-items-center justify-content-center">
            <div class="card col-md-8">
                <div class="card-header d-flex bg-white justify-content-between">
                    <h4 class="modal-title" id="viewFormEmployeeLabel">Add Data</h4>
                    <a href="{{ route('hotel') }}" type="button" class="btn btn-close"></a>
                </div>
                <div class="card-body" @style('overflow-y: auto;')>
                    <div class="container-fluid">
                        <form id="scheduleForm" method="post" action="{{ route('hotel.submit') }}">@csrf
                            <div class="row my-2">
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <label class="form-label" for="start">Name</label>
                                        <input type="text" name="name" id="name" value="{{ $employee_data->fullname }}" class="form-control bg-light" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row my-2">
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <label class="form-label" for="start">Unit</label>
                                        <input type="text" name="unit" id="unit" value="{{ $employee_data->unit }}" class="form-control bg-light" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row my-2">
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <label class="form-label" for="start">Grade</label>
                                        <input type="text" name="grade" id="grade" value="{{ $employee_data->job_level }}" class="form-control bg-light" readonly>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="col-md-12">
                                <div class="mb-2">
                                    <label class="form-label" for="name">Business Trip Number</label>
                                    <select class="form-control select2" id="bisnis_numb" name="bisnis_numb">
                                        <option value="">Select</option>
                                        @foreach($no_sppds as $no_sppd)
                                            <option value="{{ $no_sppd->no_sppd }}">{{ $no_sppd->no_sppd }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row my-2">
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <label class="form-label" for="start">Hotel Name</label>
                                        <input type="text" name="nama_htl" id="nama_htl" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <label class="form-label" for="start">Location</label>
                                        <input type="text" name="lokasi_htl" id="lokasi_htl" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <label class="form-label" for="start">Rooms</label>
                                        <input type="number" name="jmlkmr_htl" id="jmlkmr_htl" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <label class="form-label" for="name">Bed Type</label>
                                        <select class="form-control" name="bed_htl" required>
                                            <option value="">-</option>
                                            <option value="Singgle Bed">Singgle Bed</option>
                                            <option value="Twin Bed">Twin Bed</option>
                                            <option value="King Bed">King Bed</option>
                                            <option value="Super King Bed">Super King Bed</option>
                                            <option value="Extra Bed">Extra Bed</option>
                                            <option value="Baby Cot">Baby Cot</option>
                                            <option value="Sofa Bed">Sofa Bed</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row my-2">
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <label class="form-label" for="start">Start Date</label>
                                        <input type="date" name="tgl_masuk_htl" id="tgl_masuk_htl" class="form-control" placeholder="mm/dd/yyyy" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <label class="form-label" for="start">End Date</label>
                                        <input type="date" name="tgl_keluar_htl" id="tgl_keluar_htl" class="form-control" placeholder="mm/dd/yyyy" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <label class="form-label" for="start">Total Days</label>
                                        <div class="input-group">
                                            <input class="form-control bg-light" id="totaldays" name="totaldays" type="text" min="0" value="0" readonly>
                                            <div class="input-group-append">
                                                <span class="input-group-text">days</span>
                                            </div>
                                        </div>
                                        <input class="form-control" id="perdiem" name="perdiem" type="hidden" value="{{ $perdiem->amount }}" readonly>
                                    </div>
                                </div>
                            </div>

                            <br>
                            <div class="row">
                                <div class="col-md d-md-flex justify-content-end text-center">
                                    <input type="hidden" name="repeat_days_selected" id="repeatDaysSelected">
                                    <a href="{{ route('hotel') }}" type="button" class="btn btn-danger rounded-pill shadow px-4 me-2">Cancel</a>
                                    <button type="submit" class="btn btn-primary rounded-pill shadow px-4">Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
<!-- Tambahkan script JavaScript untuk mengumpulkan nilai repeat_days[] -->
@push('scripts')
<script>
    function toggleDivs() {
        // ca_type ca_nbt ca_e
        var ca_type = document.getElementById("ca_type");
        var ca_nbt = document.getElementById("ca_nbt");
        var ca_e = document.getElementById("ca_e");
        var div_bisnis_numb = document.getElementById("div_bisnis_numb");
        var bisnis_numb = document.getElementById("bisnis_numb");
        var div_allowance = document.getElementById("div_allowance");

        if (ca_type.value === "dns") {
            ca_nbt.style.display = "block";
            ca_e.style.display = "none";
            div_bisnis_numb.style.display = "block";
            div_allowance.style.display = "block";
        } else if (ca_type.value === "ndns"){
            ca_nbt.style.display = "block";
            ca_e.style.display = "none";
            div_bisnis_numb.style.display = "none";
            bisnis_numb.style.value = "";
            div_allowance.style.display = "none";
        } else if (ca_type.value === "entr"){
            ca_nbt.style.display = "none";
            ca_e.style.display = "block";
            div_bisnis_numb.style.display = "block";
        } else{
            ca_nbt.style.display = "none";
            ca_e.style.display = "none";
            div_bisnis_numb.style.display = "none";
            bisnis_numb.style.value = "";
        }
    }

    function validateInput(input) {
        //input.value = input.value.replace(/[^0-9,]/g, '');
        input.value = input.value.replace(/[^0-9]/g, '');
    }

    document.addEventListener('DOMContentLoaded', function() {
        const startDateInput = document.getElementById('tgl_masuk_htl');
        const endDateInput = document.getElementById('tgl_keluar_htl');
        const totalDaysInput = document.getElementById('totaldays');
        const perdiemInput = document.getElementById('perdiem');
        const allowanceInput = document.getElementById('allowance');
        const othersLocationInput = document.getElementById('others_location');
        const transportInput = document.getElementById('transport');
        const accommodationInput = document.getElementById('accommodation');
        const otherInput = document.getElementById('other');
        const totalcaInput = document.getElementById('totalca');
        const nominal_1Input = document.getElementById('nominal_1');
        const nominal_2Input = document.getElementById('nominal_2');
        const nominal_3Input = document.getElementById('nominal_3');
        const nominal_4Input = document.getElementById('nominal_4');
        const nominal_5Input = document.getElementById('nominal_5');
        const caTypeInput = document.getElementById('ca_type');

        function formatNumber(num) {
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        function parseNumber(value) {
            return parseFloat(value.replace(/\./g, '')) || 0;
        }

        function calculateTotalDays() {
            const startDate = new Date(startDateInput.value);
            const endDate = new Date(endDateInput.value);
            if (startDate && endDate && !isNaN(startDate) && !isNaN(endDate)) {
                const timeDiff = endDate - startDate;
                const daysDiff = Math.ceil(timeDiff / (1000 * 60 * 60 * 24));
                const totalDays = daysDiff > 0 ? daysDiff+1 : 0+1;
                totalDaysInput.value = totalDays;

                const perdiem = parseFloat(perdiemInput.value) || 0;
                let allowance = totalDays * perdiem;

                if (othersLocationInput.value.trim() !== '') {
                    allowance *= 1; // allowance * 50%
                }else{
                    allowance *= 0.5;
                }

                allowanceInput.value = formatNumber(Math.floor(allowance));
            } else {
                totalDaysInput.value = 0;
                allowanceInput.value = 0;
            }
            calculateTotalCA();
        }

        function formatInput(input) {
            let value = input.value.replace(/\./g, '');
            value = parseFloat(value);
            if (!isNaN(value)) {
                // input.value = formatNumber(value);
                input.value = formatNumber(Math.floor(value));
            } else {
                input.value = formatNumber(0);
            }

            calculateTotalCA();
        }

        function calculateTotalCA() {
            const allowance = parseNumber(allowanceInput.value);
            const transport = parseNumber(transportInput.value);
            const accommodation = parseNumber(accommodationInput.value);
            const other = parseNumber(otherInput.value);
            const nominal_1 = parseNumber(nominal_1Input.value);
            const nominal_2 = parseNumber(nominal_2Input.value);
            const nominal_3 = parseNumber(nominal_3Input.value);
            const nominal_4 = parseNumber(nominal_4Input.value);
            const nominal_5 = parseNumber(nominal_5Input.value);

            // Perbaiki penulisan caTypeInput.value
            const ca_type = caTypeInput.value;

            let totalca = 0;
            if (ca_type === 'dns') {
                totalca = allowance + transport + accommodation + other;
            } else if (ca_type === 'ndns') {
                totalca = transport + accommodation + other;
                allowanceInput.value = 0;
            } else if (ca_type === 'entr') {
                totalca = nominal_1 + nominal_2 + nominal_3 + nominal_4 + nominal_5;
                allowanceInput.value = 0;
            }

            // totalcaInput.value = formatNumber(totalca.toFixed(2));
            totalcaInput.value = formatNumber(Math.floor(totalca));
        }

        startDateInput.addEventListener('change', calculateTotalDays);
        endDateInput.addEventListener('change', calculateTotalDays);
        othersLocationInput.addEventListener('input', calculateTotalDays);
        caTypeInput.addEventListener('change', calculateTotalDays);
        [transportInput, accommodationInput, otherInput, allowanceInput, nominal_1, nominal_2, nominal_3, nominal_4, nominal_5].forEach(input => {
            input.addEventListener('input', () => formatInput(input));
        });
    });
</script>

<script>
    $(document).ready(function() {
        $('.select2').select2({
            theme: "bootstrap-5",

        });
    });
</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-beta3/js/bootstrap.min.js"></script>
@endpush

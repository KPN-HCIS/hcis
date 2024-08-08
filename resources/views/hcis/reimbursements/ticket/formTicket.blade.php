@extends('layouts_.vertical', ['page_title' => 'Ticket'])

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
                            <li class="breadcrumb-item"><a href="{{ route('ticket') }}">{{ $parentLink }}</a></li>
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
                    <a href="{{ route('ticket') }}" type="button" class="btn btn-close"></a>
                </div>
                <div class="card-body" @style('overflow-y: auto;')>
                    <div class="container-fluid">
                        <form id="scheduleForm" method="post" action="{{ route('ticket.submit') }}">@csrf
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
                                        <option value="-">Tanpa Bissnis Trip</option>
                                        @foreach($no_sppds as $no_sppd)
                                            <option value="{{ $no_sppd->no_ca }}">{{ $no_sppd->no_ca }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row my-2">
                                <label class="form-label" for="jk_tkt">Nama Penumpang (No KTP)</label>
                                <div class="col-md-2">
                                    <div class="mb-2 mr-0">
                                        <select class="form-control" id="jk_tkt" name="jk_tkt" required>
                                            <option value="">-</option>
                                            <option value="Mr">Mr</option>
                                            <option value="Mrs">Mrs</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <input type="text" name="np_tkt" id="np_tkt" class="form-control" placeholder="Nama Penumpang" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-2">
                                        <input type="number" name="noktp_tkt" id="noktp_tkt" class="form-control" placeholder="No KTP" required>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="mb-2">
                                        <label class="form-label" for="tlp_tkt">Nomor Telephone</label>
                                        <input type="number" name="tlp_tkt" id="tlp_tkt" class="form-control" maxlength="12" placeholder="Telp" required>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="mb-2">
                                        <label class="form-label" for="jenis_tkt">Jenis Transportasi</label>
                                        <select class="form-select" name="jenis_tkt" required>
                                            <option value="Pesawat">Pesawat</option>
                                            <option value="Kereta">Kereta</option>
                                            <option value="Bus">Bus</option>
                                            <option value="Ferry">Ferry</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="mb-2">
                                        <label class="form-label" for="dari_tkt">Kota Keberangkatan</label>
                                        <input type="text" name="dari_tkt" id="dari_tkt" class="form-control" placeholder="Kota Keberangkatan" required>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="mb-2">
                                        <label class="form-label" for="start">Kota Kedatangan</label>
                                        <input type="text" name="ke_tkt" id="ke_tkt" class="form-control" placeholder="Kota Kedatangan" required>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="mb-2">
                                        <label class="form-label" for="tgl_brkt_tkt">Tanggal Berangkat</label>
                                        <input type="date" name="tgl_brkt_tkt" id="tgl_brkt_tkt" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-2">
                                        <label class="form-label" for="jam_brkt_tkt">Jam Keberangkatan</label>
                                        <input type="time" name="jam_brkt_tkt" id="jam_brkt_tkt" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="mb-2">
                                        <label class="form-label" for="type_tkt">Type Tiket</label>
                                        <select name="type_tkt" id="type_tkt" class="form-select" onchange="toggleDivs()">
                                            <option value="One-Way">One-Way</option>
                                            <option value="Round-Trip">Round-Trip</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row my-2" id="div_ticket">
                                <div class="col-md-5">
                                    <div class="mb-2">
                                        <label class="form-label" for="tgl_plg_tkt">Tanggal Pulang</label>
                                        <input type="date" name="tgl_plg_tkt" id="tgl_plg_tkt" class="form-control" >
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-2">
                                        <label class="form-label" for="jam_plg_tkt">Jam Kepulangan</label>
                                        <input type="time" name="jam_plg_tkt" id="jam_plg_tkt" class="form-control" >
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md d-md-flex justify-content-end text-center">
                                    <input type="hidden" name="repeat_days_selected" id="repeatDaysSelected">
                                    <a href="{{ route('ticket') }}" type="button" class="btn btn-danger rounded-pill shadow px-4 me-2">Cancel</a>
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
        var typeTkt = document.getElementById("type_tkt").value;
        var divTicket = document.getElementById("div_ticket");

        if (typeTkt === "One-Way") {
            divTicket.style.display = "none";
        } else if (typeTkt === "Round-Trip") {
            divTicket.style.display = "flex";
        }
    }

    // Call the function on page load to set the initial state
    window.onload = function() {
        toggleDivs();
    };
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

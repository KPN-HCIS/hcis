@extends('layouts_.vertical', ['page_title' => 'Cash Advanced'])

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
                            <li class="breadcrumb-item"><a href="{{ route('schedules') }}">{{ $parentLink }}</a></li>
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
                    <a href="{{ route('cashadvanced') }}" type="button" class="btn btn-close"></a>
                </div>
                <div class="card-body" @style('overflow-y: auto;')>
                    <div class="container-fluid">
                        <form id="scheduleForm" method="post" action="{{ route('save-schedule') }}">@csrf
                            <div class="row my-2">
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <label class="form-label" for="name">Costing Company</label>
                                        <input type="text" class="form-control" placeholder="Enter Destination.." id="destination" name="destination" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row my-2">
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <label class="form-label" for="name">Destination</label>
                                        <select class="form-control select2" id="locationFilter" required>
                                            <option value="">Select location...</option>
                                            @foreach($locations as $location)
                                                <option value="{{ $location->area." (".$location->company_name.")" }}">{{ $location->area." (".$location->company_name.")" }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row my-2">
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <label class="form-label" for="name">CA Purposes</label>
                                        <textarea name="ca_needs" id="ca_needs" class="form-control"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row my-2">
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <label class="form-label" for="start">Start Date</label>
                                        <input type="date" name="start_date" id="start_date" class="form-control" placeholder="mm/dd/yyyy" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row my-2">
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <label class="form-label" for="start">End Date</label>
                                        <input type="date" name="end_date" id="end_date" class="form-control" placeholder="mm/dd/yyyy" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row my-2">
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <label class="form-label" for="start">CA Date Required</label>
                                        <input type="date" name="ca_required" id="ca_required" class="form-control" placeholder="mm/dd/yyyy" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row my-2">
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <label class="form-label" for="type">CA Type</label>
                                        <select name="ca_type" id="ca_type" class="form-select" onchange="toggleDivs()" required>
                                            <option value="">-</option>
                                            <option value="ca_nbt">CA Non Business Trip</option>
                                            <option value="ca_e">CA Entertainment</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row" id="ca_nbt" style="display: none;">
                                <div class="col-md-12">
                                    <div class="table-responsive-sm">
                                        <div class="d-flex flex-column gap-2">
                                            <div class="text-bg-danger p-2" style="text-align:center">Estimated Cash Advanced</div>
                                                <div class="card">
                                                    <div class="card-body">
                                                        <div class="mb-2">
                                                            <label class="form-label" for="name">Total Days</label>
                                                            <div class="input-group" style="width:50%">
                                                                <input class="form-control bg-light" name="totaldays" type="text" min="0" value="0" readonly>
                                                                <div class="input-group-append">
                                                                    <span class="input-group-text">days</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="mb-2">
                                                            <label class="form-label" for="name">Allowance</label>
                                                            <div class="input-group">
                                                                <div class="input-group-append">
                                                                    <span class="input-group-text">Rp</span>
                                                                </div>
                                                                <input class="form-control" name="allowance" type="text" min="0" value="0">
                                                            </div>
                                                        </div>
                                                        <div class="mb-2">
                                                            <label class="form-label" for="name">Transportation</label>
                                                            <div class="input-group">
                                                                <div class="input-group-append">
                                                                    <span class="input-group-text">Rp</span>
                                                                </div>
                                                                <input class="form-control" name="transport" type="text" min="0" value="0">
                                                            </div>
                                                        </div>
                                                        <div class="mb-2">
                                                            <label class="form-label" for="name">Accommodation</label>
                                                            <div class="input-group">
                                                                <div class="input-group-append">
                                                                    <span class="input-group-text">Rp</span>
                                                                </div>
                                                                <input class="form-control" name="accommodation" type="text" min="0" value="0">
                                                            </div>
                                                        </div>
                                                        <div class="mb-2">
                                                            <label class="form-label" for="name">Other</label>
                                                            <div class="input-group">
                                                                <div class="input-group-append">
                                                                    <span class="input-group-text">Rp</span>
                                                                </div>
                                                                <input class="form-control" name="other" type="text" min="0" value="0">
                                                            </div>
                                                        </div>
                                                        <div class="mb-2">
                                                            <label class="form-label" for="name">Total Cash Advanced</label>
                                                            <div class="input-group">
                                                                <div class="input-group-append">
                                                                    <span class="input-group-text">Rp</span>
                                                                </div>
                                                                <input class="form-control bg-light" name="totalca" type="text" min="0" value="0" readonly>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row" id="ca_e" style="display: none;">
                                <div class="col-md-12">
                                    <div class="table-responsive-sm">
                                        <div class="d-flex flex-column gap-2">
                                            <div class="text-bg-danger p-2" style="text-align:center">Estimated Entertainment</div>
                                                <div class="card">
                                                    <div class="card-body">
                                                        <div class="accordion" id="accordionPanelsStayOpenExample">
                                                            <div class="accordion-item">
                                                                <h2 class="accordion-header" id="panelsStayOpen-headingOne">
                                                                    <button class="accordion-button fw-medium" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseOne" aria-expanded="true" aria-controls="panelsStayOpen-collapseOne">
                                                                        Entertainment Type #1
                                                                    </button>
                                                                </h2>
                                                                <div id="panelsStayOpen-collapseOne" class="accordion-collapse collapse show" aria-labelledby="panelsStayOpen-headingOne">
                                                                    <div class="accordion-body">
                                                                        <div class="mb-2">
                                                                            <label class="form-label" for="name">Entertainment Type</label>
                                                                            <select name="enter_type_1" id="enter_type_1" class="form-select">
                                                                                <option value="">-</option>
                                                                                <option value="meal_cost">Meal Cost</option>
                                                                                <option value="accommodation">Accommodation</option>
                                                                                <option value="gift">Gift</option>
                                                                            </select>
                                                                        </div>
                                                                        <div class="mb-2">
                                                                            <label class="form-label" for="name">Entertainment Fee Detail</label>
                                                                            <textarea name="enter_fee_1" id="enter_fee_1" class="form-control"></textarea>
                                                                        </div>
                                                                        <div class="input-group">
                                                                            <div class="input-group-append">
                                                                                <span class="input-group-text">Rp</span>
                                                                            </div>
                                                                            <input class="form-control" name="nominal_1" id="nominal_1" type="text" min="0" value="0">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="accordion-item">
                                                                <h2 class="accordion-header" id="panelsStayOpen-headingTwo">
                                                                    <button class="accordion-button fw-medium collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseTwo" aria-expanded="false" aria-controls="panelsStayOpen-collapseTwo">
                                                                        Entertainment Type #2
                                                                    </button>
                                                                </h2>
                                                                <div id="panelsStayOpen-collapseTwo" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingTwo">
                                                                    <div class="accordion-body">
                                                                        <div class="mb-2">
                                                                            <label class="form-label" for="name">Entertainment Type</label>
                                                                            <select name="enter_type_2" id="enter_type_2" class="form-select">
                                                                                <option value="">-</option>
                                                                                <option value="meal_cost">Meal Cost</option>
                                                                                <option value="accommodation">Accommodation</option>
                                                                                <option value="gift">Gift</option>
                                                                            </select>
                                                                        </div>
                                                                        <div class="mb-2">
                                                                            <label class="form-label" for="name">Entertainment Fee Detail</label>
                                                                            <textarea name="enter_fee_2" id="enter_fee_2" class="form-control"></textarea>
                                                                        </div>
                                                                        <div class="input-group">
                                                                            <div class="input-group-append">
                                                                                <span class="input-group-text">Rp</span>
                                                                            </div>
                                                                            <input class="form-control" name="nominal_2" id="nominal_2" type="text" min="0" value="0">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="accordion-item">
                                                                <h2 class="accordion-header" id="panelsStayOpen-headingThree">
                                                                    <button class="accordion-button fw-medium collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseThree" aria-expanded="false" aria-controls="panelsStayOpen-collapseThree">
                                                                        Entertainment Type #3
                                                                    </button>
                                                                </h2>
                                                                <div id="panelsStayOpen-collapseThree" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingThree">
                                                                    <div class="accordion-body">
                                                                        <div class="mb-2">
                                                                            <label class="form-label" for="name">Entertainment Type</label>
                                                                            <select name="enter_type_3" id="enter_type_3" class="form-select">
                                                                                <option value="">-</option>
                                                                                <option value="meal_cost">Meal Cost</option>
                                                                                <option value="accommodation">Accommodation</option>
                                                                                <option value="gift">Gift</option>
                                                                            </select>
                                                                        </div>
                                                                        <div class="mb-2">
                                                                            <label class="form-label" for="name">Entertainment Fee Detail</label>
                                                                            <textarea name="enter_fee_3" id="enter_fee_3" class="form-control"></textarea>
                                                                        </div>
                                                                        <div class="input-group">
                                                                            <div class="input-group-append">
                                                                                <span class="input-group-text">Rp</span>
                                                                            </div>
                                                                            <input class="form-control" name="nominal_3" id="nominal_3" type="text" min="0" value="0">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="accordion-item">
                                                                <h2 class="accordion-header" id="panelsStayOpen-headingFour">
                                                                    <button class="accordion-button fw-medium collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseFour" aria-expanded="false" aria-controls="panelsStayOpen-collapseFour">
                                                                        Entertainment Type #4
                                                                    </button>
                                                                </h2>
                                                                <div id="panelsStayOpen-collapseFour" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingFour">
                                                                    <div class="accordion-body">
                                                                        <div class="mb-2">
                                                                            <label class="form-label" for="name">Entertainment Type</label>
                                                                            <select name="enter_type_4" id="enter_type_4" class="form-select">
                                                                                <option value="">-</option>
                                                                                <option value="meal_cost">Meal Cost</option>
                                                                                <option value="accommodation">Accommodation</option>
                                                                                <option value="gift">Gift</option>
                                                                            </select>
                                                                        </div>
                                                                        <div class="mb-2">
                                                                            <label class="form-label" for="name">Entertainment Fee Detail</label>
                                                                            <textarea name="enter_fee_4" id="enter_fee_4" class="form-control"></textarea>
                                                                        </div>
                                                                        <div class="input-group">
                                                                            <div class="input-group-append">
                                                                                <span class="input-group-text">Rp</span>
                                                                            </div>
                                                                            <input class="form-control" name="nominal_4" id="nominal_4" type="text" min="0" value="0">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="accordion-item">
                                                                <h2 class="accordion-header" id="panelsStayOpen-headingFive">
                                                                    <button class="accordion-button fw-medium collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseFive" aria-expanded="false" aria-controls="panelsStayOpen-collapseFive">
                                                                        Entertainment Type #5
                                                                    </button>
                                                                </h2>
                                                                <div id="panelsStayOpen-collapseFive" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingFive">
                                                                    <div class="accordion-body">
                                                                        <div class="mb-2">
                                                                            <label class="form-label" for="name">Entertainment Type</label>
                                                                            <select name="enter_type_5" id="enter_type_5" class="form-select">
                                                                                <option value="">-</option>
                                                                                <option value="meal_cost">Meal Cost</option>
                                                                                <option value="accommodation">Accommodation</option>
                                                                                <option value="gift">Gift</option>
                                                                            </select>
                                                                        </div>
                                                                        <div class="mb-2">
                                                                            <label class="form-label" for="name">Entertainment Fee Detail</label>
                                                                            <textarea name="enter_fee_5" id="enter_fee_5" class="form-control"></textarea>
                                                                        </div>
                                                                        <div class="input-group">
                                                                            <div class="input-group-append">
                                                                                <span class="input-group-text">Rp</span>
                                                                            </div>
                                                                            <input class="form-control" name="nominal_5" id="nominal_5" type="text" min="0" value="0">
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
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md d-md-flex justify-content-end text-center">
                                    <input type="hidden" name="repeat_days_selected" id="repeatDaysSelected">
                                    <a href="{{ route('schedules') }}" type="button" class="btn btn-danger rounded-pill shadow px-4 me-2">Cancel</a>
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
        
        if (ca_type.value === "ca_nbt") {
            ca_nbt.style.display = "block";
            ca_e.style.display = "none";
        } else if (ca_type.value === "ca_e"){
            ca_nbt.style.display = "none";
            ca_e.style.display = "block";
        } else{
            ca_nbt.style.display = "none";
            ca_e.style.display = "none";
        }
    }

    function validateInput(input) {
        //input.value = input.value.replace(/[^0-9,]/g, '');
        input.value = input.value.replace(/[^0-9]/g, '');
    }
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

@extends('layouts_.vertical', ['page_title' => 'Reimbursements'])

@section('css')
    {{-- <style>
    .card {
        max-width: 170px; /* Sesuaikan dengan ukuran yang diinginkan */
        margin: 0 auto; /* Center the card horizontally */
    }
    .card-body {
        display: flex;
        flex-direction: column;
        align-items: center; /* Center the content horizontally */
        justify-content: center; /* Center the content vertically */
        padding: 10px; /* Sesuaikan dengan padding yang diinginkan */
    }
    .card-body img {
        width: 75px; /* Sesuaikan dengan ukuran yang diinginkan */
        height: auto;
    }
    .card-body h4 {
        margin-top: 10px;
        font-size: 12px; /* Sesuaikan dengan ukuran font yang diinginkan */
        text-align: center;
    }
</style> --}}
@endsection

@section('content')
    <!-- Begin Page Content -->
    {{-- {{ "Hallo ".$userId." berasal dari system ".session('system') }} --}}
    <br>
    <div class="container-fluid">
        <div class="row text-center">
            <div class="col-md-3">
                <a href="{{ route('cashadvanced') }}">
                    <div class="card">
                        <div class="card-body">
                            <img src="/images/menu/cashadv.png" alt="logo">
                            <h4 class="my-3">Cash Advanced</h4>
                        </div> <!-- end card-body-->
                    </div> <!-- end card-->
                </a>
            </div> <!-- end col-->

            <div class="col-md-3">
                <a href="{{ route('medical') }}">
                    <div class="card">
                        <div class="card-body">
                            <img src="/images/menu/medical.png" alt="logo">
                            <h4 class="my-3">Medical</h4>
                        </div> <!-- end card-body-->
                    </div> <!-- end card-->
                </a>
            </div> <!-- end col-->

            <div class="col-md-3">
                <a href="{{ route('businessTrip') }}">
                    <div class="card">
                        <div class="card-body">
                            <img src="/images/menu/business-trip.png" alt="logo" style="width: 100px; height: 100px;">
                            <h4 class="my-3">Business Trip</h4>
                        </div> <!-- end card-body-->
                    </div> <!-- end card-->
                </a>
            </div> <!-- end col-->
            <div class="col-md-3">
                <a href="{{ '' }}">
                    <div class="card">
                        <div class="card-body">
                            <img src="/images/menu/business-trip.png" alt="logo" style="width: 100px; height: 100px;">
                            <h4 class="my-3">Home Trip</h4>
                        </div> <!-- end card-body-->
                    </div> <!-- end card-->
                </a>
            </div> <!-- end col-->

        </div> <!-- end row -->
    </div>
@endsection

@push('scripts')
@endpush

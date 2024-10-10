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
    <style>
        .menu-card {
            text-decoration: none;
            /* Menghapus underline dari link */
        }

        .card {
            border: none;
            /* Menghapus border default card */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            /* Menambahkan shadow */
            transition: transform 0.3s;
            /* Menambahkan animasi transisi */
        }

        .card:hover {
            transform: translateY(-10px);
            /* Efek saat di-hover */
        }

        .card-body {
            padding: 20px;
            /* Menambahkan padding pada card body */
        }

        .card-img {
            max-width: 100px;
            /* Mengatur ukuran maksimal gambar */
            margin: 0 auto;
            /* Memusatkan gambar */
            display: block;
            /* Memastikan gambar menjadi blok */
        }

        h4 {
            margin-top: 20px;
            /* Menambahkan jarak atas pada heading */
            margin-bottom: 0;
            /* Menghapus jarak bawah pada heading */
            color: #333;
            /* Mengatur warna teks */
        }
    </style>
@endsection

@section('content')
    <!-- Begin Page Content -->
    {{-- {{ "Hallo ".$userId." berasal dari system ".session('system') }} --}}
    <br>

    <div class="container-fluid">
        <div class="row row-cols-2 row-cols-md-4 row-cols-lg-6 row-cols-xxl-8 text-center">
            <div class="col">
                <a href="{{ route('cashadvanced') }}">
                    <div class="card">
                        <div class="card-body">
                            <img src="/images/menu/cashadv.png" alt="logo">
                            <h4 class="my-3">Cash Advanced</h4>
                        </div> <!-- end card-body-->
                    </div> <!-- end card-->
                </a>
            </div> <!-- end col-->
            <div class="col">
                <a href="{{ route('medical') }}">
                    <div class="card">
                        <div class="card-body">
                            <img src="/images/menu/medical.png" alt="logo">
                            <h4 class="my-3">Medical</h4>
                        </div> <!-- end card-body-->
                    </div> <!-- end card-->
                </a>
            </div> <!-- end col-->
            <div class="col">
                <a href="{{ route('businessTrip') }}">
                    <div class="card">
                        <div class="card-body">
                            <img src="/images/menu/business-trip.png" alt="logo" style="width: 100px; height: 100px;">
                            <h4 class="my-3">Business Trip</h4>
                        </div> <!-- end card-body-->
                    </div> <!-- end card-->
                </a>
            </div> <!-- end col-->
            <div class="col">
                <a href="{{ route('businessTrip.approval') }}">
                    <div class="card">
                        <div class="card-body">
                            <img src="/images/menu/business-trip.png" alt="logo" style="width: 100px; height: 100px;">
                            <h4 class="my-3">Business Trip (Approval)</h4>
                        </div> <!-- end card-body-->
                    </div> <!-- end card-->
                </a>
            </div> <!-- end col-->
            @if (auth()->check())
                @can('adminbt')
                    <div class="col">
                        <a href="{{ route('businessTrip.admin') }}">
                            <div class="card">
                                <div class="card-body">
                                    <img src="/images/menu/business-trip.png" alt="logo"
                                        style="width: 100px; height: 100px;">
                                    <h4 class="my-3">Business Trip (Admin)</h4>
                                </div> <!-- end card-body-->
                            </div> <!-- end card-->

                        </a>
                    </div> <!-- end col-->
                @endcan
            @endif
            {{-- </div> --}}

            <div class="col-md-3">
                <a href="{{ '' }}">
                    <div class="card">
                        <div class="card-body">
                            <img src="/images/menu/business-trip.png" alt="logo" style="width: 100px; height: 100px;">
                            <h4 class="my-3">Home Trip</h4>
                        </div> <!-- end card-body-->
                    </div> <!-- end card-->
                </a>
            </div><!-- end col-->

            <div class="col-md-3">
                <a href="{{ route('ticket') }}">
                    <div class="card">
                        <div class="card-body">
                            <img src="/images/menu/ticket.png" alt="logo" style="width: 100px; height: 100px;">
                            <h4 class="my-3">Tiket</h4>
                        </div> <!-- end card-body-->
                    </div> <!-- end card-->
                </a>
            </div>
            <div class="col-md-3">
                <a href="{{ route('ticket.approval') }}">
                    <div class="card">
                        <div class="card-body">
                            <img src="/images/menu/ticket.png" alt="logo" style="width: 100px; height: 100px;">
                            <h4 class="my-3">Tiket Approval</h4>
                        </div> <!-- end card-body-->
                    </div> <!-- end card-->
                </a>
            </div>

            <div class="col-md-3">
                <a href="{{ route('hotel') }}">
                    <div class="card">
                        <div class="card-body">
                            <img src="/images/menu/hotel.png" alt="logo" style="width: 100px; height: 100px;">
                            <h4 class="my-3">Hotel</h4>
                        </div> <!-- end card-body-->
                    </div> <!-- end card-->
                </a>
            </div>
            <div class="col-md-3">
                <a href="{{ route('hotel.approval') }}">
                    <div class="card">
                        <div class="card-body">
                            <img src="/images/menu/hotel.png" alt="logo" style="width: 100px; height: 100px;">
                            <h4 class="my-3">Hotel Approval</h4>
                        </div> <!-- end card-body-->
                    </div> <!-- end card-->
                </a>
            </div>

            <div class="col-md-3">
                <a href="{{ route('approval') }}">
                    <div class="card" style="height: 215px">
                        <div class="card-body">
                            <img src="/images/menu/cashadv.png" alt="logo">
                            <h4 class="my-3">Approval</h4>
                        </div> <!-- end card-body-->
                    </div> <!-- end card-->
                </a>
            </div> <!-- end col-->


        </div> <!-- end row -->
    </div>
@endsection

@push('scripts')
@endpush

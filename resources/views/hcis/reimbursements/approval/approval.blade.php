@extends('layouts_.vertical', ['page_title' => 'Cash Advanced'])

@section('css')
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
                            <li class="breadcrumb-item">{{ $parentLink }}</li>
                            <li class="breadcrumb-item active">{{ $link }}</li>
                        </ol>
                    </div>
                    <h4 class="page-title">{{ $link }}</h4>
                </div>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col">
                <a href="/reimbursements" class="btn btn-primary btn-action">
                    <i class="bi bi-caret-left-fill"></i> Back
                </a>
            </div>
        </div>

        <div class="row g-2 justify-content-center">
            <div class=" col-6 col-sm-auto">
                <div class="mb-2">
                    <a href="{{ route('approval') }}" class="btn btn-primary rounded-pill shadow w-100 position-relative">
                        Cash Advanced
                        @if ( $pendingCACount >= 1 )
                            <span class="badge bg-danger position-absolute top-0 start-100 translate-middle">{{ $pendingCACount }}</span>
                        @else

                        @endif
                    </a>
                </div>
            </div>
            <div class="col-6 col-sm-auto">
                <div class="mb-2">
                    <a href="{{ route('cashadvanced') }}" class="btn btn-outline-primary rounded-pill shadow w-100 position-relative">
                        Medical
                        @if ( $pendingHTLCount >= 1 )
                            <span class="badge bg-danger position-absolute top-0 start-100 translate-middle">{{ $pendingHTLCount }}</span>
                        @else

                        @endif
                    </a>
                </div>
            </div>
            <div class="col-6 col-sm-auto">
                <div class="mb-2">
                    <a href="{{ route('businessTrip.approval') }}" class="btn btn-outline-primary rounded-pill shadow w-100 position-relative">
                        Business Trip
                        <span class="badge bg-danger position-absolute top-0 start-100 translate-middle">99</span>
                    </a>
                </div>
            </div>
            <div class="col-6 col-sm-auto">
                <div class="mb-2">
                    <a href="{{ route('cashadvanced') }}" class="btn btn-outline-primary rounded-pill shadow w-100 position-relative">
                        Hometrip
                        <span class="badge bg-danger position-absolute top-0 start-100 translate-middle">99</span>
                    </a>
                </div>
            </div>
            <div class="col-6 col-sm-auto">
                <div class="mb-2">
                    <a href="{{ route('cashadvanced.form') }}" class="btn btn-outline-primary rounded-pill shadow w-100 position-relative">
                        Assessment
                        <span class="badge bg-danger position-absolute top-0 start-100 translate-middle"></span>
                    </a>
                </div>
            </div>
            <div class="col-6 col-sm-auto">
                <div class="mb-2">
                    <a href="{{ route('ticket.approval') }}" class="btn btn-outline-primary rounded-pill shadow w-100 position-relative">
                        Ticket
                        <span class="badge bg-danger position-absolute top-0 start-100 translate-middle"></span>
                    </a>
                </div>
            </div>
            <div class="col-6 col-sm-auto">
                <div class="mb-2">
                    <a href="{{ route('ticket.approval') }}" class="btn btn-outline-primary rounded-pill shadow w-100 position-relative">
                        Hotel
                        <span class="badge bg-danger position-absolute top-0 start-100 translate-middle"></span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Content Row -->
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h3 class="card-title">{{ $link }}</h3>
                        <div class="input-group" style="width: 30%;">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-white border-dark-subtle"><i class="ri-search-line"></i></span>
                            </div>
                            <input type="text" name="customsearch" id="customsearch" class="form-control w-  border-dark-subtle border-left-0" placeholder="search.." aria-label="search" aria-describedby="search" >
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover dt-responsive nowrap" id="scheduleTable" width="100%" cellspacing="0">
                            <thead class="thead-light">
                                <tr class="text-center">
                                    <th>No</th>
                                    <th>Type</th>
                                    <th>No CA</th>
                                    <th>Requestor</th>
                                    <th>Company</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Total CA</th>
                                    <th>Total Settlement</th>
                                    <th>Balance</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>

                                @foreach($ca_transactions->where('approval_status', 'Pending') as $ca_transaction)
                                <tr>
                                    <td>{{ $loop->index + 1 }}</td>
                                    @if($ca_transaction->type_ca == 'dns')
                                        <td>Business Trip</td>
                                    @elseif($ca_transaction->type_ca == 'ndns')
                                        <td>Non Business Trip</td>
                                    @elseif($ca_transaction->type_ca == 'entr')
                                        <td>Entertainment</td>
                                    @endif
                                    <td>{{ $ca_transaction->no_ca }}</td>
                                    <td>{{ $ca_transaction->employee->fullname }}</td>
                                    <td>{{ $ca_transaction->contribution_level_code }}</td>
                                    <td>{{ $ca_transaction->formatted_start_date }}</td>
                                    <td>{{ $ca_transaction->formatted_end_date }}</td>
                                    <td>{{ $ca_transaction->total_ca }}</td>
                                    <td>{{ $ca_transaction->total_real }}</td>
                                    <td>{{ $ca_transaction->total_cost }}</td>
                                    <td>
                                        <p type="button" class="btn btn-sm rounded-pill btn-{{ $ca_transaction->approval_status == 'Approved' ? 'success' : ($ca_transaction->approval_status == 'Rejected' ? 'danger' : 'warning') }}" style="pointer-events: none">
                                            {{ $ca_transaction->approval_status }}
                                        </p>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('approval.cashadvanced', encrypt($ca_transaction->id)) }}" class="btn btn-sm rounded-pill btn-outline-primary" title="Edit" ><i class="ri-edit-box-line"></i></a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Periksa apakah ada pesan sukses
    var successMessage = "{{ session('success') }}";

    // Jika ada pesan sukses, tampilkan sebagai alert
    if (successMessage) {
        alert(successMessage);
    }
</script>
@endpush

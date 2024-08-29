@extends('layouts_.vertical', ['page_title' => 'Cash Advanced'])

@section('css')
@endsection

@section('content')
    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="row">
            <!-- Breadcrumb Navigation -->
            <div class="col-md-6 mt-3">
                <div class="page-title-box d-flex align-items-center">
                    <ol class="breadcrumb mb-0" style="display: flex; align-items: center; padding-left: 0;">
                        <li class="breadcrumb-item" style="font-size: 25px; display: flex; align-items: center;">
                            <a href="/reimbursements" style="text-decoration: none;" class="text-primary">
                                <i class="bi bi-arrow-left"></i>
                            </a>
                        </li>
                        <li class="breadcrumb-item" style="font-size: 24px; display: flex; align-items: center; margin-left: 10px;">
                            {{ $parentLink }}
                        </li>
                        <li class="breadcrumb-item" style="font-size: 24px; display: flex; align-items: center; margin-left: 10px;">
                            {{ $link }}
                        </li>
                    </ol>
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
                    @include('hcis.reimbursements.approval.navigation.navigationApproval')
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
                                @foreach($ca_transactions as $transaction)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        @if($transaction->type_ca == 'dns')
                                            <td>Business Trip</td>
                                        @elseif($transaction->type_ca == 'ndns')
                                            <td>Non Business Trip</td>
                                        @elseif($transaction->type_ca == 'entr')
                                            <td>Entertainment</td>
                                        @endif
                                        <td>{{ $transaction->no_ca }}</td>
                                        <td>{{ $transaction->employee->fullname }}</td>
                                        <td>{{ $transaction->contribution_level_code }}</td>
                                        <td>{{ \Carbon\Carbon::parse($transaction->start_date)->format('d-M-y') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($transaction->end_date)->format('d-M-y') }}</td>
                                        <td>Rp. {{ number_format($transaction->total_ca) }}</td>
                                        <td>Rp. {{ number_format($transaction->total_real) }}</td>
                                        <td>Rp. {{ number_format($transaction->total_cost) }}</td>
                                        <td>
                                            <p class="badge text-bg-{{ $transaction->approval_sett == 'Approved' ? 'success' : ($transaction->approval_sett == 'Rejected' ? 'danger' : 'warning') }}">
                                                {{ $transaction->approval_sett }}
                                            </p>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('approval.cashadvancedFormDeklarasi', encrypt($transaction->id)) }}" class="btn btn-outline-info" title="Approve" ><i class="bi bi-card-checklist"></i></a>
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

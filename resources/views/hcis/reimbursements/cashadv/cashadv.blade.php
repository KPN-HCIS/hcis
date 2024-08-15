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
                        <li class="breadcrumb-item" style="font-size: 32px; display: flex; align-items: center;">
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

            <!-- Add Data Button -->
            <div class="col-md-6 mt-4 text-end">
                <a href="{{ $pendingCACount >= 2 ? '#' : route('cashadvanced.form') }}" class="btn btn-outline-primary rounded-pill {{ $pendingCACount >= 2 ? 'disabled' : '' }}" style="font-size: 18px">
                    <i class="bi bi-plus-circle"></i> Add Data
                </a>
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

                            @foreach($ca_transactions as $ca_transaction)
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
                                    <p type="button" class="btn btn-sm rounded-pill btn-{{ $ca_transaction->approval_status == 'Approved' ? 'success' : ($ca_transaction->approval_status == 'Declaration' ? 'info' : ($ca_transaction->approval_status == 'Pending' ? 'warning' : ($ca_transaction->approval_status == 'Rejected' ? 'danger' : ($ca_transaction->approval_status == 'Draft' ? 'secondary' : 'success')))) }}" style="pointer-events: none">
                                        {{ $ca_transaction->approval_status }}
                                    </p>
                                </td>
                                <td class="text-center">
                                    @if ($ca_transaction->approval_status == 'Approved')
                                        <a href="{{ route('cashadvanced.download', $ca_transaction->id) }}" target="_blank" class="btn btn-outline-primary" title="Print"><i class="bi bi-file-earmark-arrow-down"></i></a>
                                    @elseif ($ca_transaction->approval_status == 'Declaration')
                                        <a href="{{ route('cashadvanced.edit', encrypt($ca_transaction->id)) }}" class="btn btn-outline-warning" title="Edit" ><i class="ri-edit-box-line"></i></a>
                                    @elseif ($ca_transaction->approval_status == 'Pending')
                                        <a href="{{ route('cashadvanced.download', $ca_transaction->id) }}" target="_blank" class="btn btn-outline-primary" title="Print"><i class="bi bi-file-earmark-arrow-down"></i></a>
                                    @elseif ($ca_transaction->approval_status == 'Reject')

                                    @elseif ($ca_transaction->approval_status == 'Draft')
                                        {{-- <a href="{{ route('cashadvanced.edit', encrypt($ca_transaction->id)) }}" class="btn btn-outline-warning" title="Edit" ><i class="ri-edit-box-line"></i></a> --}}
                                        <a href="{{ route('cashadvanced.show', $ca_transaction->id) }}" class="btn btn-outline-info" title="Edit"><i class="bi bi-card-checklist"></i></a>
                                        <form action="{{ route('cashadvanced.delete', $ca_transaction->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button onclick="return confirm('Apakah ingin Menghapus?')" class="btn btn-outline-danger" title="Delete">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </form>
                                    @else

                                    @endif
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

    <!-- Modal -->
    <!-- Hanya load modal jika ID transaksi tertentu diminta -->
    @if(request()->routeIs('cashadvanced.show'))
    <div class="modal fade show d-block" id="request-ca" tabindex="-1" aria-labelledby="requestCAModal" aria-hidden="true" style="display: block;">
        <div class="modal-dialog modal-lg">
            <!-- Modal content -->
        </div>
    </div>
    @endif

    <div class="modal fade" id="request-ca" tabindex="-1" aria-labelledby="requestCAModal" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="requestCAModal">Cash Advance Request</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <!-- Employee Information -->
                        <div class="row mb-3">
                            <div class="col-md-12 text-center">
                                <h3>Nama: {{ $ca_transaction->employee->name }}</h3>
                                <h4>Unit: {{ $ca_transaction->unit }}</h4>
                            </div>
                        </div>

                        <!-- Costing and Details -->
                        <div class="row text-center">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <p><b>Costing Company:</b> {{ $ca_transaction->contribution_level_code }}</p>
                                    <p><b>Destination:</b> {{ $ca_transaction->destination }}</p>
                                    <p><b>CA Purposes:</b> {{ $ca_transaction->ca_needs }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <p><b>Start Date:</b> {{ $ca_transaction->start_date }}</p>
                                    <p><b>End Date:</b> {{ $ca_transaction->end_date }}</p>
                                    <p><b>Total Days:</b> {{ $ca_transaction->total_days }}</p>
                                    <p><b>CA Date Required:</b> {{ $ca_transaction->date_required }}</p>
                                    <p><b>Declaration Estimate:</b> {{ $ca_transaction->declare_estimate }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- CA Type and Amount -->
                        <div class="row text-center">
                            <div class="col-md-12">
                                <h3>CA Type: {{ $ca_transaction->type_ca }}</h3>
                                <h4>Business Trip Number: {{ $ca_transaction->no_sppd }}</h4>
                            </div>
                        </div>

                        <!-- Financial Details Table -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered text-center">
                                        <thead>
                                            <tr>
                                                <th>Allowance (Perdiem)</th>
                                                <th>Transportation</th>
                                                <th>Accommodation</th>
                                                <th>Other</th>
                                                <th>Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {{-- <tr>
                                                <td>{{ $ca_transaction->detail_ca['allowance'] }}</td>
                                                <td>{{ $ca_transaction->detail_ca['transport'] }}</td>
                                                <td>{{ $ca_transaction->detail_ca['accommodation'] }}</td>
                                                <td>{{ $ca_transaction->detail_ca['other'] }}</td>
                                                <td>{{ $ca_transaction->total_ca }}</td>
                                            </tr> --}}
                                        </tbody>
                                    </table>
                                </div>
                                <h4><b>Total Cash Advanced: {{ $ca_transaction->total_ca }}</b></h4>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <form action="{{ route('cashadvanced.delete', $ca_transaction->id) }}" method="POST" style="display:inline;">
                        @csrf
                        <button onclick="return confirm('Apakah ingin Menghapus?')" class="btn btn-outline-danger" title="Delete">
                            <i class="ri-delete-bin-line"></i> Delete
                        </button>
                    </form>
                    <a href="{{ route('cashadvanced.edit', $ca_transaction->id) }}" class="btn btn-outline-warning" title="Edit"><i class="ri-edit-box-line"></i></a>
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

    document.addEventListener('DOMContentLoaded', function () {
        if ({{ $pendingCACount }} > 2) {
            const actionBtn = document.getElementById('addDataBtn');
            actionBtn.removeAttribute('href'); // Menghapus atribut href
            actionBtn.classList.add('disabled');
        }
    });




</script>
@endpush

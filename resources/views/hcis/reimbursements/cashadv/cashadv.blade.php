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
                <a href="{{ route('cashadvanced.form') }}" class="btn btn-outline-primary btn-action">
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
                                    <p type="button" class="btn btn-sm rounded-pill btn-{{ $ca_transaction->approval_status == 'Approved' ? 'success' : ($ca_transaction->approval_status == 'Rejected' ? 'danger' : 'warning') }}" style="pointer-events: none">
                                        {{ $ca_transaction->approval_status }}
                                    </p>
                                </td>
                                <td class="text-center">
                                    <div style="display: {{ $ca_transaction->approval_status == 'Approved' ? 'none' : 'inline-block' }}">
                                        <a href="{{ route('cashadvanced.edit', encrypt($ca_transaction->id)) }}" class="btn btn-sm rounded-pill btn-primary" title="Edit" ><i class="ri-edit-box-line"></i></a>
                                    </div>
                                    <div style="display: {{ $ca_transaction->approval_status == 'Approved' ? 'inline-block' : 'none' }}">
                                        <a href="{{ route('cashadvanced.download', $ca_transaction->id) }}" target="_blank" class="btn btn-sm rounded-pill btn-primary" title="Print"><i class="ri-printer-line"></i></a>
                                    </div>
                                    <form action="{{ route('cashadvanced.delete', $ca_transaction->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        <button onclick="return confirm('Apakah ingin Menghapus?')" class="btn btn-sm rounded-pill btn-danger" title="Delete">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </form>
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

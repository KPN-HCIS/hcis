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
        
        <div class="row">
            <div class="col-md-auto">
              <div class="mb-3">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text bg-white border-dark-subtle"><i class="ri-search-line"></i></span>
                  </div>
                  <input type="text" name="customsearch" id="customsearch" class="form-control  border-dark-subtle border-left-0" placeholder="search.." aria-label="search" aria-describedby="search">
                </div>
              </div>
            </div>
            <div class="col">
                <div class="mb-2 text-end">
                    <a href="{{ route('cashadvanced.form') }}" class="btn btn-primary rounded-pill shadow">Create CA</a>
                </div>
            </div>
        </div>
        <!-- Content Row -->
        <div class="row">
          <div class="col-md-12">
            <div class="card shadow mb-4">
              <div class="card-body">
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
                                  <th>Actions</th>
                              </tr>
                          </thead>
                          <tbody>
                            
                            @foreach($ca_transactions as $ca_transaction)
                            <tr>
                                <td>{{ $loop->index + 1 }}</td>
                                <td>{{ $ca_transaction->type_ca }}</td>
                                <td>{{ $ca_transaction->no_ca }}</td>
                                <td>{{ $ca_transaction->user_id }}</td>
                                <td>{{ $ca_transaction->contribution_level_code }}</td>
                                <td>{{ $ca_transaction->start_date }}</td>
                                <td>{{ $ca_transaction->end_date }}</td>
                                <td>{{ $ca_transaction->total_ca }}</td>
                                <td>{{ $ca_transaction->total_real }}</td>
                                <td>{{ $ca_transaction->total_cost }}</td>
                                <td class="text-center">
                                    <a href="{{ route('cashadvanced.form', $ca_transaction->id) }}" class="btn btn-sm rounded-pill btn-primary" title="Edit" ><i class="ri-edit-box-line"></i></a> 
                                    <a class="btn btn-sm rounded-pill btn-danger" title="Delete" onclick="handleDelete(this)" data-id="{{ $ca_transaction->id }}"><i class="ri-delete-bin-line"></i></a>
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

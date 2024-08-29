@extends('layouts_.vertical', ['page_title' => 'Ticket'])

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
                        <input type="text" name="customsearch" id="customsearch"
                            class="form-control  border-dark-subtle border-left-0" placeholder="search.."
                            aria-label="search" aria-describedby="search">
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="mb-2 text-end">
                    <a href="{{ route('ticket.form') }}" class="btn btn-primary rounded-pill shadow">Add Ticket</a>
                </div>
            </div>
        </div>
        <!-- Content Row -->
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover dt-responsive nowrap" id="scheduleTable" width="100%"
                                cellspacing="0">
                                <thead class="thead-light">
                                    <tr class="text-center">
                                        <th>No</th>
                                        <th>Ticket Type</th>
                                        <th>Requestor</th>
                                        <th>Tranportation Type</th>
                                        <th>Passengers Name</th>
                                        <th>From</th>
                                        <th>Destination</th>
                                        <th>Departure</th>
                                        <th>Departure Time</th>
                                        <th>Homecoming</th>
                                        <th>Homecoming Time</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($transactions as $transaction)
                                        <tr>
                                            <td>{{ $loop->index + 1 }}</td>
                                            <td>{{ $transaction->type_tkt }}</td>
                                            <td>{{ $transaction->employee->fullname }}</td>
                                            <td>{{ $transaction->jenis_tkt }}</td>
                                            <td>{{ $transaction->np_tkt }}</td>
                                            <td>{{ $transaction->dari_tkt }}</td>
                                            <td>{{ $transaction->ke_tkt }}</td>
                                            <td>{{ \Carbon\Carbon::parse($transaction->tgl_brkt_tkt)->format('d/m/Y') }}
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($transaction->jam_brkt_tkt)->format('H:i') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($transaction->tgl_plg_tkt)->format('d/m/Y') }}
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($transaction->jam_plg_tkt)->format('H:i') }}</td>
                                            <td class="text-center">
                                                <a href="{{ route('ticket.edit', encrypt($transaction->id)) }}"
                                                    class="btn btn-sm rounded-pill btn-outline-warning" title="Edit"><i
                                                        class="ri-edit-box-line"></i></a>
                                                <form action="{{ route('ticket.delete', encrypt($transaction->id)) }}"
                                                    method="POST" style="display:inline;">
                                                    @csrf
                                                    <button onclick="return confirm('Apakah ingin Menghapus?')"
                                                        class="btn btn-sm rounded-pill btn-outline-danger" title="Delete">
                                                        <i class="ri-delete-bin-line"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                @if (session('message'))
                                    <script>
                                        alert('{{ session('message') }}');
                                    </script>
                                @endif
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

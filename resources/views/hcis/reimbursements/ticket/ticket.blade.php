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
                            class="form-control  border-dark-subtle border-left-0" placeholder="Search.."
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
                                        <th>No. Ticket</th>
                                        <th>Total Tickets</th>
                                        <th>Purposes</th>
                                        {{-- <th>Ticket Type</th> --}}
                                        {{-- <th>Requestor</th> --}}
                                        {{-- <th>Transportation Type</th> --}}
                                        {{-- <th>Passengers Name</th> --}}
                                        <th>From/To</th>
                                        {{-- <th>To</th> --}}
                                        {{-- <th>Departure</th> --}}
                                        {{-- <th>Departure Time</th> --}}
                                        {{-- <th>Homecoming</th> --}}
                                        {{-- <th>Homecoming Time</th> --}}
                                        <th>Details</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($transactions as $transaction)
                                        <tr>
                                            <td style="text-align: center">{{ $loop->index + 1 }}</td>
                                            <td>{{ $transaction->no_tkt }}</td>
                                            <td>{{ $ticketCounts[$transaction->no_tkt]['total'] ?? 1 }}</td>
                                            <td>{{ $transaction->jns_dinas_tkt }}</td>
                                            <td>{{ $transaction->dari_tkt. "/" . $transaction->ke_tkt }}</td>
                                            <td>Details</td>
                                            <td style="align-content: center">
                                                <span
                                                    class="badge rounded-pill bg-{{ $transaction->approval_status == 'Approved' ||
                                                    $transaction->approval_status == 'Declaration Approved' ||
                                                    $transaction->approval_status == 'Verified'
                                                        ? 'success'
                                                        : ($transaction->approval_status == 'Rejected' ||
                                                        $transaction->approval_status == 'Return/Refund' ||
                                                        $transaction->approval_status == 'Declaration Rejected'
                                                            ? 'danger'
                                                            : (in_array($transaction->approval_status, [
                                                                'Pending L1',
                                                                'Pending L2',
                                                                'Declaration L1',
                                                                'Declaration L2',
                                                                'Waiting Submitted',
                                                            ])
                                                                ? 'warning'
                                                                : ($transaction->approval_status == 'Draft'
                                                                    ? 'secondary'
                                                                    : (in_array($transaction->approval_status, ['Doc Accepted'])
                                                                        ? 'info'
                                                                        : 'secondary')))) }}"
                                                    style="font-size: 12px; padding: 0.5rem 1rem;"
                                                    @if ($transaction->approval_status == 'Pending L1') title="L1 Manager: {{ $managerL1Names[$transaction->manager_l1_id] ?? 'Unknown' }}"
                                                @elseif ($transaction->approval_status == 'Pending L2')
                                                    title="L2 Manager: {{ $managerL2Names[$transaction->manager_l2_id] ?? 'Unknown' }}" @elseif($transaction->approval_status == 'Declaration L1') title="L1 Manager: {{ $managerL1Names[$transaction->manager_l1_id] ?? 'Unknown' }}"
                                                    @elseif($transaction->approval_status == 'Declaration L2') title="L2 Manager: {{ $managerL2Names[$transaction->manager_l2_id] ?? 'Unknown' }}" @endif>
                                                    {{ $transaction->approval_status == 'Approved' ? 'Request Approved' : $transaction->approval_status }}
                                                </span>
                                            </td>
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
        $(document).ready(function() {
            var table = $('#yourTableId').DataTable({
                "pageLength": 10 // Set default page length
            });
            // Set to 10 entries per page
            $('#dt-length-0').val(10);

            // Trigger the change event to apply the selected value
            $('#dt-length-0').trigger('change');
        });

        // Periksa apakah ada pesan sukses
        var successMessage = "{{ session('success') }}";

        // Jika ada pesan sukses, tampilkan sebagai alert
        if (successMessage) {
            alert(successMessage);
        }
    </script>
@endpush

<div class="table-responsive">
    <table class="table table-sm dt-responsive nowrap scheduleTable" width="100%"
        cellspacing="0">
        <thead class="thead-light">
            <tr class="text-center">
                <th>No</th>
                <th class="sticky-col-header" style="background-color: white">Cash Advance No</th>
                <th>Type</th>
                <th>Requestor</th>
                <th>Company</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Extend End Date</th>
                <th>Reason</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ca_transactions_ext as $transaction)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td style="background-color: white;" class="sticky-col">{{ $transaction->no_ca }}</td>
                    @if($transaction->type_ca == 'dns')
                        <td>Business Trip</td>
                    @elseif($transaction->type_ca == 'ndns')
                        <td>Non Business Trip</td>
                    @elseif($transaction->type_ca == 'entr')
                        <td>Entertainment</td>
                    @endif
                    <td>{{ $transaction->employee->fullname }}</td>
                    <td>{{ $transaction->contribution_level_code }}</td>
                    <td>{{ \Carbon\Carbon::parse($transaction->start_date)->format('d-m-Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($transaction->end_date)->format('d-m-Y') }}</td>
                    <td>{{ $extendTime[$transaction->id]['ext_end_date'] }}</td>
                    <td>{{ $extendTime[$transaction->id]['reason_extend'] }}</td>
                    <td>
                        <p class="badge text-bg-{{ $transaction->approval_extend == 'Approved' ? 'success' : ($transaction->approval_extend == 'Declaration' ? 'info' : ($transaction->approval_extend == 'Pending' ? 'warning' : ($transaction->approval_extend == 'Rejected' ? 'danger' : ($transaction->approval_extend == 'Draft' ? 'secondary' : 'success')))) }}"
                             title="Waiting Approve by: {{ isset($fullnames[$transaction->extend_id]) ? $fullnames[$transaction->extend_id] : 'Unknown Employee' }}">
                            {{ $transaction->approval_extend }}
                        </p>
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalExtend"
                                data-no-id="{{ $transaction->id }}"
                                data-no-ca="{{ $transaction->no_ca }}"
                                data-start-date="{{ $transaction->start_date }}"
                                data-end-date="{{ $transaction->end_date }}"
                                data-total-days="{{ $transaction->total_days }}"
                                data-end-date-ext="{{ $extendTime[$transaction->id]['ext_end_date'] }}"
                                data-total-days-ext="{{ $extendTime[$transaction->id]['ext_total_days'] }}"
                                data-reason-ext="{{ $extendTime[$transaction->id]['reason_extend'] }}"
                                >
                            <i class="ri-calendar-line"></i>
                        </button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script>
    $(document).ready(function() {
        $('.scheduleTable').DataTable();
    });
</script>

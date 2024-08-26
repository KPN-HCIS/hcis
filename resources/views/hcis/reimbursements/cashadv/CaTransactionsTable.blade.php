@foreach ($ca_transactions as $ca_transaction)
    <tr>
        <td class="text-center">{{ $loop->index + 1 }}</td>
        @if ($ca_transaction->type_ca == 'dns')
            <td>Business Trip</td>
        @elseif($ca_transaction->type_ca == 'ndns')
            <td>Non Business Trip</td>
        @elseif($ca_transaction->type_ca == 'entr')
            <td>Entertainment</td>
        @endif
        <td class="text-center">{{ $ca_transaction->no_ca }}</td>
        <td>{{ $ca_transaction->employee->fullname }}</td>
        <td>{{ $ca_transaction->contribution_level_code }}</td>
        <td>{{ date('j M Y', strtotime($ca_transaction->formatted_start_date)) }}</td>
        <td>{{ date('j M Y', strtotime($ca_transaction->formatted_end_date)) }}</td>
        <td>Rp. {{ number_format($ca_transaction->total_ca) }}</td>
        <td>Rp. {{ number_format($ca_transaction->total_real) }}</td>
        <td>Rp. {{ number_format($ca_transaction->total_cost) }}</td>
        <td>
            <p class="badge text-bg-{{ $ca_transaction->approval_status == 'Approved' ? 'success' : ($ca_transaction->approval_status == 'Declaration' ? 'info' : ($ca_transaction->approval_status == 'Pending' ? 'warning' : ($ca_transaction->approval_status == 'Rejected' ? 'danger' : ($ca_transaction->approval_status == 'Draft' ? 'secondary' : 'success')))) }}" style="pointer-events: none">
                {{ $ca_transaction->approval_status }}
            </p>
        </td>
        <td class="text-center">
            <a href="{{ route('cashadvanced.download', $ca_transaction->id) }}" target="_blank" class="btn btn-outline-primary" title="Print"><i class="bi bi-file-earmark-arrow-down"></i></a>
            <form action="{{ route('cashadvanced.delete', $ca_transaction->id) }}" method="POST" style="display:inline;">
                @csrf
                <button onclick="return confirm('Apakah ingin Menghapus?')" class="btn btn-outline-danger" title="Delete">
                    <i class="ri-delete-bin-line"></i>
                </button>
            </form>
        </td>
    </tr>
@endforeach
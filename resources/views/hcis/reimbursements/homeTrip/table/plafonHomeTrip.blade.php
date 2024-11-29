<h4>Plafonds Data</h4>
<div class="table-responsive">
    <table class="table table-bordered table-sm table-striped table-hover">
        <thead class="bg-primary align-middle text-center">
            <tr>
                <th>No</th>
                <th>Name</th>
                <th>Relation</th>
                <th>Period</th>
                <th>Quota</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach ($plafonds as $year => $items)
                {{-- Loop through years --}}
                @foreach ($items as $index => $item)
                    {{-- Loop through items for each year --}}
                    <tr>
                        <td class="text-center">{{ $no++ }}</td>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->relation_type }}</td>
                        {{-- Merge Period cells for the same year --}}
                        @if ($index === 0)
                            <td rowspan="{{ count($items) }}" class="align-middle text-center">
                                {{ $year }}
                            </td>
                        @endif
                        <td class="text-center">{{ $item->quota ?? '0' }} Left</td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
</div>

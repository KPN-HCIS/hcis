<div class="row mt-2">
    {{-- Data Keluarga --}}
    <div class="card shadow-none">
        <div class="card-body">
            <h4 class="card-title">Family Data</h4>
            <div class="card-text">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm table-striped table-hover">
                        <thead class="bg-primary align-middle text-center">
                            <th>No</th>
                            <th>NIK</th>
                            <th>Name</th>
                            <th>Relation</th>
                            <th>Date of Birth</th>
                            <th>Age</th>
                            <th>Status</th>
                        </thead>
                        <tbody>
                            @foreach ($family as $item)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->gender }}</td>
                                    <td>{{ $item->relation_type }}</td>
                                    <td class="text-center">
                                        {{ \Carbon\Carbon::parse($item->date_of_birth)->format('d F Y') }}
                                    </td>
                                    <td class="text-center">
                                        {{ \Carbon\Carbon::parse($item->date_of_birth)->age }} Years Old
                                    </td>
                                    <td class="text-center">{{ $item->jobs }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

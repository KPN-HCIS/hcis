<h4>Health Coverage Limit</h4>
<div class="table-responsive">
    <table class="display nowrap dataTable dtr-inline collapsed">
        <thead class="bg-primary text-center align-middle">
            <tr>
                <th rowspan="2" class="text-center sticky"
                    style="z-index:auto !important;background-color:#AB2F2B !important;">Period</th>
                <th colspan="{{ count($dependents_fam) + 1 }}" class="text-center">Family Member</th>
            </tr>
            <tr>
                <th class="text-center">{{ $fullname }}</th>
                @foreach ($dependents_fam as $dependents_fams)
                    <th class="text-center">{{ $dependents_fams->name }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($formatted_data as $period => $quotas)
                <tr>
                    <td class="text-center sticky bg-white">{{ $period }}</td>
                    <td class="text-center">{{ $quotas['employee'] }}</td>
                    @foreach ($dependents_fam as $dependent)
                        <td class="text-center">{{ $quotas[$dependent->name] }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

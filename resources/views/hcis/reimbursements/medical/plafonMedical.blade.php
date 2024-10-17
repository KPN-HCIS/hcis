<h4>Health Coverage Limit</h4>
<div class="table-responsive">
    <table class="display nowrap dataTable dtr-inline collapsed">
        <thead class="bg-primary text-center align-middle">
            <tr>
                <th rowspan="2" class="text-center sticky"
                    style="z-index:auto !important;background-color:#AB2F2B !important;">Period</th>
                <th colspan="4" class="text-center">Type of Health Coverage</th>
            </tr>
            <tr>
                <th class="text-center">Child Birth</th>
                <th class="text-center">Inpatient</th>
                <th class="text-center">Outpatient</th>
                <th class="text-center">Glasses</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($medical_plan as $item)
                <tr>
                    <td class="text-center">{{ $item->period }}</td>
                    <td class="text-center">
                        -</td>
                    <td class="text-center">
                        -</td>
                    <td class="text-center">
                        -</td>
                    <td class="text-center">
                        -</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

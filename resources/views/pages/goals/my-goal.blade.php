<x-app-layout>
    @section('title', 'Goals')
    <x-slot name="content">
    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h1 class="h2">Your Goals</h1>
            <a href="{{ route('goals.form', Auth::user()->employee_id) }}" class="btn btn-primary px-4 shadow {{{ $goals ? '' : 'disabled' }}}">Create Goals</a>
        </div>
        <form id="formYearGoal" action="{{ route('goals') }}" method="GET">
            @php
                $filterYear = request('filterYear');
            @endphp
            <div class="d-flex align-items-end">
                <div class="form-group mr-3">
                    <label for="filterYear">Year</label>
                    <select name="filterYear" id="filterYear" onchange="yearGoal()" class="form-control border-secondary" @style('width: 120px')>
                        <option value="">select all</option>
                        @foreach ($selectYear as $year)
                            <option value="{{ $year->year }}" {{ $year->year == $filterYear ? 'selected' : '' }}>{{ $year->year }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </form>
        @foreach ($data as $row)
        @php
            // Assuming $dateTimeString is the date string '2024-04-29 06:52:40'
            $year = date('Y', strtotime($row->request->created_at));
            $formData = json_decode($row->request->goal['form_data'], true);
        @endphp
        <div class="row">
            <div class="col-md-12">
              <div class="card shadow mb-4">
                <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Goals {{ $year }}</h6>
                    @if ($row->request->status == 'Pending' && count($row->request->approval) == 0 || $row->request->sendback_to == $row->request->employee_id)
                        <a class="btn btn-primary px-3" href="{{ route('goals.edit', $row->request->goal->id) }}">Edit</a>
                    @endif
                </div>
                <div class="card-body">
                    <div class="row p-2">
                        <div class="col-lg col-sm-12 p-2">
                            <label class="font-weight-bold">Initiated By :</label>
                            <p>{{ $row->request->initiated->name.' ('.$row->request->initiated->employee_id.')' }}</p>
                        </div>
                        <div class="col-lg col-sm-12 p-2">
                            <label class="font-weight-bold">Initiated Date :</label>
                            <p>{{ $row->request->created_at }}</p>
                        </div>
                        <div class="col-lg col-sm-12 p-2">
                            <label class="font-weight-bold">Last Updated On :</label>
                            <p>{{ $row->request->updated_at }}</p>
                        </div>
                        <div class="col-lg col-sm-12 p-2">
                            <label class="font-weight-bold">Adjusted By :</label>
                            <p>{{ $row->request->updatedBy ? $row->request->updatedBy->name.' ('.$row->request->updatedBy->employee_id.')' : '-' }}</p>
                        </div>
                        <div class="col-lg col-sm-12 p-2">
                            <label class="font-weight-bold">Status :</label>
                            <div>
                                <a href="javascript:void(0)" id="{{ $row->request->goal->form_status == 'Draft' || $row->request->sendback_to == $row->request->employee_id ? '' : 'approval'}}{{ $row->request->employee_id }}" data-id="{{ $row->request->employee_id }}" class="badge {{ $row->request->goal->form_status == 'Draft' || $row->request->sendback_to == $row->request->employee_id ? 'badge-secondary' : ($row->request->status === 'Approved' ? 'badge-success' : 'badge-warning')}} badge-pill px-3 py-2">{{ $row->request->goal->form_status == 'Draft' ? 'Draft': ($row->request->status == 'Pending' ? 'Waiting For Approval' : ($row->request->sendback_to == $row->request->employee_id ? 'Waiting For Revision' : $row->request->status)) }}</a>
                            </div>
                        </div>
                    </div>
                </div>
                @if ($row->request->sendback_messages && $row->request->sendback_to == $row->request->employee_id)
                <div class="card-header" style="background-color: lightyellow">
                    <div class="row p-2">
                        <div class="col-lg col-sm-12 p-2">
                            <div class="form-group">
                                <label class="font-weight-bold">Revision Notes :</label>
                                <p>{{ $row->request->sendback_messages }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                <div class="card-body p-0">
                    <table class="table table-striped table-bordered m-0">
                        <tbody>
                        @if ($formData)
                        @foreach ($formData as $index => $data)
                            <tr>
                                <td  scope="row">
                                    <div class="row mb-3 p-2">
                                        <div class="col-lg col-sm-12 p-2">
                                            <div class="form-group">
                                                <label class="font-weight-bold">KPI {{ $index + 1 }}</label>
                                                <p>{{ $data['kpi'] }}</p>
                                            </div>
                                        </div>
                                        <div class="col-lg col-sm-12 p-2">
                                            <div class="form-group">
                                                <label class="font-weight-bold">Target</label>
                                                <p>{{ $data['target'] }}</p>
                                            </div>
                                        </div>
                                        <div class="col-lg col-sm-12 p-2">
                                            <div class="form-group">
                                                <label class="font-weight-bold">UoM</label>
                                                <p>{{ $data['uom'] }}</p>
                                                <p>{{ is_null($data['custom_uom']) ? '': $data['custom_uom'] }}</p>
                                            </div>
                                        </div>
                                        <div class="col-lg col-sm-12 p-2">
                                            <div class="form-group">
                                                <label class="font-weight-bold">Type</label>
                                                <p>{{ $data['type'] }}</p>
                                            </div>
                                        </div>
                                        <div class="col-lg col-sm-12 p-2">
                                            <div class="form-group">
                                                <label class="font-weight-bold">Weightage</label>
                                                <p>{{ $data['weightage'] }}%</p>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                            @else
                            <p>No form data available.</p>
                            @endif 
                        </tbody>
                    </table>
                </div>
            </div>
            </div>
        </div>
        @endforeach
    </div>
    
    </x-slot>
</x-app-layout>
<script src="{{ asset('js/goal-approval.js') }}"></script>
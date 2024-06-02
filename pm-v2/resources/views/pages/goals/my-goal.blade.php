@extends('layouts_.vertical', ['page_title' => 'Goals'])

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
        <div class="mandatory-field">
            <div id="alertField" class="alert alert-danger alert-dismissible {{ Session::has('error') ? '':'fade' }}" role="alert" {{ Session::has('error') ? '':'hidden' }}>
                <strong>{{ Session::get('error') }}</strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
        <form id="formYearGoal" action="{{ route('goals') }}" method="GET">
            @php
                $filterYear = request('filterYear');
            @endphp
            <div class="row align-items-end">
                <div class="col">
                    <div class="mb-3">
                        <label class="form-label" for="filterYear">Year</label>
                        <select name="filterYear" id="filterYear" onchange="yearGoal()" class="form-select border-secondary" @style('width: 120px')>
                            <option value="">select all</option>
                            @foreach ($selectYear as $year)
                                <option value="{{ $year->year }}" {{ $year->year == $filterYear ? 'selected' : '' }}>{{ $year->year }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-auto">
                    <div class="mb-3">
                        <a href="{{ route('goals.form', Auth::user()->employee_id) }}" class="btn btn-primary rounded-pill shadow {{{ $goals ? '' : 'disabled' }}}">Create Goals</a>
                    </div>
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
                    <h4 class="m-0 font-weight-bold text-primary">Goals {{ $year }}</h4>
                    @if ($row->request->status == 'Pending' && count($row->request->approval) == 0 || $row->request->sendback_to == $row->request->employee_id)
                        <a class="btn btn-info rounded-pill" href="{{ route('goals.edit', $row->request->goal->id) }}">Edit</a>
                    @endif
                </div>
                <div class="card-body">
                    <div class="row px-2">
                        <div class="col-lg col-sm-12 p-2">
                            <h5>Initiated By</h5>
                            <p class="mt-2 mb-0 text-muted">{{ $row->request->initiated->name.' ('.$row->request->initiated->employee_id.')' }}</p>
                        </div>
                        <div class="col-lg col-sm-12 p-2">
                            <h5>Initiated Date</h5>
                            <p class="mt-2 mb-0 text-muted">{{ $row->request->formatted_created_at }}</p>
                        </div>
                        <div class="col-lg col-sm-12 p-2">
                            <h5>Last Updated On</h5>
                            <p class="mt-2 mb-0 text-muted">{{ $row->request->formatted_updated_at }}</p>
                        </div>
                        <div class="col-lg col-sm-12 p-2">
                            <h5>Adjusted By</h5>
                            <p class="mt-2 mb-0 text-muted">{{ $row->request->updatedBy ? $row->request->updatedBy->name.' '.$row->request->updatedBy->employee_id : '-' }}{{ $row->request->adjustedBy && empty($adjustByManager) ? ' (Admin)': '' }}</p>
                        </div>
                        <div class="col-lg col-sm-12 p-2">
                            <h5>Status</h5>
                            <div>
                                <a href="javascript:void(0)" id="{{ $row->request->goal->form_status == 'Draft' || $row->request->sendback_to == $row->request->employee_id ? '' : 'approval'}}{{ $row->request->employee_id }}" data-id="{{ $row->request->employee_id }}" class="badge {{ $row->request->goal->form_status == 'Draft' || $row->request->sendback_to == $row->request->employee_id ? 'bg-secondary' : ($row->request->status === 'Approved' ? 'bg-success' : 'bg-warning')}} rounded-pill py-1 px-2">{{ $row->request->goal->form_status == 'Draft' ? 'Draft': ($row->request->status == 'Pending' ? 'Waiting For Approval' : ($row->request->sendback_to == $row->request->employee_id ? 'Waiting For Revision' : $row->request->status)) }}</a>
                            </div>
                        </div>
                    </div>
                </div>
                @if ($row->request->sendback_messages && $row->request->sendback_to == $row->request->employee_id)
                <div class="card-header" style="background-color: lightyellow">
                    <div class="row p-2">
                        <div class="col-lg col-sm-12 px-2">
                            <div class="form-group">
                                <h5>Revision Notes :</h5>
                                <p class="mt-1 mb-0 text-muted">{{ $row->request->sendback_messages }}</p>
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
                                    <div class="row p-2">
                                        <div class="col-lg col-sm-12 p-2">
                                            <div class="form-group">
                                                <h5>KPI {{ $index + 1 }}</h5>
                                                <p class="mt-1 mb-0 text-muted">{{ $data['kpi'] }}</p>
                                            </div>
                                        </div>
                                        <div class="col-lg col-sm-12 p-2">
                                            <div class="form-group">
                                                <h5>Target</h5>
                                                <p class="mt-1 mb-0 text-muted">{{ $data['target'] }}</p>
                                            </div>
                                        </div>
                                        <div class="col-lg col-sm-12 p-2">
                                            <div class="form-group">
                                                <h5>UoM</h5>
                                                <p class="mt-1 mb-0 text-muted">{{ $data['uom'] }}</p>
                                                <p class="mt-1 mb-0 text-muted">{{ is_null($data['custom_uom']) ? '': $data['custom_uom'] }}</p>
                                            </div>
                                        </div>
                                        <div class="col-lg col-sm-12 p-2">
                                            <div class="form-group">
                                                <h5>Type</h5>
                                                <p class="mt-1 mb-0 text-muted">{{ $data['type'] }}</p>
                                            </div>
                                        </div>
                                        <div class="col-lg col-sm-12 p-2">
                                            <div class="form-group">
                                                <h5>Weightage</h5>
                                                <p class="mt-1 mb-0 text-muted">{{ $data['weightage'] }}%</p>
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
    @endsection
    @push('scripts')
        <script src="{{ asset('js/goal-approval.js') }}"></script>
        @if(Session::has('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: "error",
                    title: "Cannot create goals",
                    text: '{{ Session::get('error') }}',
                    confirmButtonText: "OK",
                });
            });
        </script>
        @endif
    @endpush
<x-app-layout>
    @section('title', 'Goals')
    <x-slot name="content">
    <!-- Begin Page Content -->
    <div class="container-fluid">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h1 class="h2">Team Goals</h1>
        </div>
        <div class="d-sm-flex align-items-center justify-content-start mb-3">
            <button class="btn btn-outline-primary badge-pill btn-sm px-4 mb-2 mr-3 filter-btn" data-id="all">All Task</button>
            <button class="btn btn-outline-primary badge-pill btn-sm px-4 mb-2 mr-3 filter-btn" data-id="draft">Draft</button>
            <button class="btn btn-outline-primary badge-pill btn-sm px-4 mb-2 mr-3 filter-btn" data-id="waiting for revision">Waiting For Revision</button>
            <button class="btn btn-outline-primary badge-pill btn-sm px-4 mb-2 mr-3 filter-btn" data-id="waiting for approval">Waiting For Approval</button>
            <button class="btn btn-outline-primary badge-pill btn-sm px-4 mb-2 mr-3 filter-btn" data-id="approved">Approved</button>
        </div>
        <div class="d-sm-flex align-items-end mb-2">
            <div class="form-group mr-4 d-md-block d-none">
            <a href="#" class="btn btn-outline-secondary" data-toggle="modal" data-target="#modalFilter"><i class="fas fa-filter"></i></a>
            </div>
            <div class="form-group">
            <div class="input-group">
                <div class="input-group-prepend">
                <span class="input-group-text bg-white"><i class="fas fa-search"></i></span>
                </div>
                <input type="text" name="customsearch" id="customsearch" class="form-control border-left-0" placeholder="search.." aria-label="search" aria-describedby="search">
                <div class="d-sm-none input-group-append">
                <a href="#" class="input-group-text btn btn-light bg-white" data-toggle="modal" data-target="#modalFilter"><i class="fas fa-filter"></i></a>
                </div>
            </div>
            </div>
            <div class="form-group ml-md-auto d-flex justify-content-end">
            <form id="exportForm" action="{{ route('export') }}" method="POST">
                @csrf
                <input type="hidden" name="export_report_type" id="export_report_type">
                <input type="hidden" name="export_group_company" id="export_group_company">
                <input type="hidden" name="export_company" id="export_company">
                <input type="hidden" name="export_location" id="export_location">
                <a id="export" onclick="exportExcel()" class="btn btn-outline-secondary px-4 shadow disabled"><i class="fas fa-arrow-circle-down"></i> Download</a>
            </form>
            </div>
        </div>
        <!-- Content Row -->
        <div class="row">
            <div class="col-md-12">

              <div class="card shadow mb-4">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="teamGoalsTable" width="100%" cellspacing="0">
                            <thead class="thead-light">
                                <tr class="text-center">
                                    <th>Employees</th>
                                    <th>Approval Status</th>
                                    <th>Initiated On</th>
                                    <th>Initiated By</th>
                                    <th>Last Updated On</th>
                                    <th>Updated By</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $row)
                                @php
                                    $subordinates = $row->request->subordinates;
                                    $firstSubordinate = $subordinates->isNotEmpty() ? $subordinates->first() : null;
                                    $formStatus = $firstSubordinate ? $firstSubordinate->goal->form_status : null;
                                    $goalId = $firstSubordinate ? $firstSubordinate->goal->id : null;
                                    $goalData = $firstSubordinate ? $firstSubordinate->goal['form_data'] : null;
                                    $createdAt = $firstSubordinate ? $firstSubordinate->created_at : null;
                                    $updatedAt = $firstSubordinate ? $firstSubordinate->updated_at : null;
                                    $updatedBy = $firstSubordinate ? $firstSubordinate->updatedBy : null;
                                    $status = $firstSubordinate ? $firstSubordinate->status : null;
                                    $approverId = $firstSubordinate ? $firstSubordinate->current_approval_id : null;
                                    $sendbackTo = $firstSubordinate ? $firstSubordinate->sendback_to : null;
                                    $employeeId = $firstSubordinate ? $firstSubordinate->employee_id : null;
                                    $sendbackTo = $firstSubordinate ? $firstSubordinate->sendback_to : null;
                                @endphp
                                <tr>
                                    <td>{{ $row->request->employee->fullname }}</td>
                                    <td class="px-5"><a href="javascript:void(0)" id="approval{{ $employeeId }}" data-toggle="tooltip" data-id="{{ $employeeId }}" class="badge {{ $subordinates->isNotEmpty() ? ($formStatus == 'Draft' || $status == 'Sendback' ? 'badge-secondary' : ($status === 'Approved' ? 'badge-success' : 'badge-warning')) : 'badge-dark'}} badge-pill w-100">{{ $formStatus == 'Draft' ? 'Draft': ($status == 'Pending' ? 'Waiting For Approval' : ($subordinates->isNotEmpty() ? ($status == 'Sendback' ? 'Waiting For Revision' : $status) : 'No Data')) }}</a></td>
                                    <td class="text-center">{{ $createdAt }}</td>
                                    <td class="text-center">{{ $subordinates->isNotEmpty() ?$row->request->employee->fullname : '' }}</td>
                                    <td class="text-center">{{ $updatedAt }}</td>
                                    <td class="text-center">{{ $updatedBy ? $updatedBy->name.' ('.$updatedBy->employee_id.')' : '-' }}</td>
                                    <td class="text-center">
                                        @if ($row->request->employee->employee_id == Auth::user()->employee_id || !$subordinates->isNotEmpty())
                                            @if ($formStatus == 'submitted' || $formStatus == 'Approved')
                                            <a href="javascript:void(0)" class="btn btn-outline-secondary btn-sm btn-circle" data-toggle="modal" data-target="#modalDetail{{ $goalId }}"><i class="fas fa-eye"></i></a>
                                            @endif
                                            @else
                                            @if ($approverId == Auth::user()->employee_id && $status === 'Pending' || $sendbackTo == Auth::user()->employee_id && $status === 'Sendback' || !$subordinates->isNotEmpty())
                                                <a href="{{ route('team-goals.approval', $goalId) }}" class="btn btn-outline-primary btn-sm badge-pill font-weight-medium px-4">Act</a>
                                            @else
                                                <a href="javascript:void(0)" class="btn btn-outline-secondary btn-sm btn-circle" data-toggle="modal" data-target="#modalDetail{{ $goalId }}"><i class="fas fa-eye"></i></a>
                                            @endif
                                        @endif
                                    </td>
                                    @if ($data)
                                    @include('pages.goals.detail')
                                    @endif
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
              </div>
            </div>
        </div>
    </div>
    
    </x-slot>
</x-app-layout>
<script src="{{ asset('js/goal-approval.js') }}"></script>
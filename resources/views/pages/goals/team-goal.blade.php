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
                                <tr>
                                    <td>{{ $row->request->employee->fullname }}</td>
                                    <td class="px-5"><a href="javascript:void(0)" id="approval{{ $row->request->employee_id }}" data-toggle="tooltip" data-id="{{ $row->request->employee_id }}" class="badge {{ $row->request->goal->form_status == 'Draft' || $row->request->status == 'Sendback' ? 'badge-secondary' : ($row->request->status === 'Approved' ? 'badge-success' : 'badge-warning')}} badge-pill w-100">{{ $row->request->goal->form_status == 'Draft' ? 'Draft': ($row->request->status == 'Pending' ? 'Waiting For Approval' : ($row->request->status == 'Sendback' ? 'Waiting For Revision' : $row->request->status)) }}</a></td>
                                    <td class="text-center">{{ $row->request->created_at }}</td>
                                    <td class="text-center">{{ $row->request->employee->fullname }}</td>
                                    <td class="text-center">{{ $row->request->updated_at }}</td>
                                    <td class="text-center">{{ $row->request->updatedBy ? $row->request->updatedBy->name.' ('.$row->request->updatedBy->employee_id.')' : '-' }}</td>
                                    <td class="text-center">
                                        @if ($row->request->employee_id == Auth::user()->employee_id)
                                            @if ($row->request->goal->form_status == 'submitted' || $row->request->goal->form_status == 'Approved')
                                            <a href="javascript:void(0)" class="btn btn-outline-secondary btn-sm btn-circle" data-toggle="modal" data-target="#modalDetail{{ $row->request->goal->id }}"><i class="fas fa-eye"></i></a>
                                            @endif
                                            @if ($row->request->status == 'Pending' && count($row->request->approval) == 0)
                                            <a href="{{ route('goals.edit', $row->request->goal->id) }}" class="btn btn-outline-secondary btn-sm btn-circle font-weight-medium"><i class="fas fa-edit"></i></a>
                                            @endif
                                            @else
                                            @if ($row->request->current_approval_id == Auth::user()->employee_id && $row->request->status === 'Pending' || $row->request->sendback_to == Auth::user()->employee_id && $row->request->status === 'Sendback')
                                                <a href="{{ route('team-goals.approval', $row->request->form_id) }}" class="btn btn-outline-primary btn-sm badge-pill font-weight-medium px-4">Act</a>
                                            @else
                                                <a href="javascript:void(0)" class="btn btn-outline-secondary btn-sm btn-circle" data-toggle="modal" data-target="#modalDetail{{ $row->request->goal->id }}"><i class="fas fa-eye"></i></a>
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
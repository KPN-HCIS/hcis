<x-app-layout>
    @section('title', 'Goals')
    <x-slot name="content">
    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-flex align-items-center justify-content-end mb-4" >
            @if($goals == 1)
                <a href="{{ route('goals.form', Auth::user()->employee_id) }}" class="btn btn-primary px-4 shadow">Create Goal</a>
            @else
                <button type="button" class="btn btn-primary px-4 shadow" disabled>Create Goal</button>
            @endif
        </div>
        
        <div class="d-sm-flex align-items-center justify-content-start mb-4">
          <button class="btn btn-outline-primary badge-pill btn-sm px-4 mb-2 mr-3">All Task</button>
          <button class="btn btn-outline-primary badge-pill btn-sm px-4 mb-2 mr-3">Active</button>
          <button class="btn btn-outline-primary badge-pill btn-sm px-4 mb-2 mr-3">Draft</button>
          <button class="btn btn-outline-primary badge-pill btn-sm px-4 mb-2 mr-3">Completed</button>
          <button class="btn btn-outline-primary badge-pill btn-sm px-4 mb-2 mr-3">Revoked</button>
        </div>
        <!-- Content Row -->
        <div class="row">
            <div class="col-md-12">

              <div class="card shadow mb-4">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="taskTable" width="100%" cellspacing="0">
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
                                    <td class="px-5"><a href="javascript:void(0)" id="approval{{ $row->request->employee_id }}" data-toggle="tooltip" data-id="{{ $row->request->employee_id }}" class="badge {{ $row->request->goal->form_status == 'Draft' ? 'badge-secondary' : ($row->request->status === 'Approved' ? 'badge-success' : 'badge-warning')}} badge-pill w-100">{{ $row->request->goal->form_status == 'Draft' ? 'Draft': ($row->request->status == 'Pending' ? ($row->request->sendback_to ? 'Waiting For Revision' : 'Waiting For Approval' ) : $row->request->status) }}</a></td>
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
                                            @if ($row->request->current_approval_id == Auth::user()->employee_id && $row->request->status === 'Pending' && $row->request->sendback_to == Auth::user()->employee_id)
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
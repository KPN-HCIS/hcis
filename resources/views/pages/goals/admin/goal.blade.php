<div class="row">
    <div class="col-md-12">
      <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="goalTable" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr class="text-center">
                            <th>Employees</th>
                            <th>Category</th>
                            <th>Approval Status</th>
                            <th>Initiated On</th>
                            <th>Initiated By</th>
                            <th>Last Updated On</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $row)
                        <tr>
                            <td>{{ $row->employee->fullname }}</td>
                            <td>{{ $row->goal->category }}</td>
                            <td class="px-5"><a href="#" id="approval{{ $row->employee_id }}" data-toggle="tooltip" data-id="{{ $row->employee_id }}" class="badge {{ $row->goal->form_status == 'Draft' ? 'badge-secondary' : ($row->status === 'Approved' ? 'badge-success' : 'badge-warning')}} badge-pill w-100">{{ $row->goal->form_status == 'Draft' ? 'Draft':$row->status }}</a></td>
                            <td class="text-center">{{ $row->created_at }}</td>
                            <td class="text-center">{{ $row->employee->fullname }}</td>
                            <td class="text-center">{{ $row->updated_at }}</td>
                            <td class="text-center">
                              @if ( $row->status === 'Pending' && (auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('admin')))
                                  <a href="{{ route('admin.create.approval.goal', $row->form_id) }}" class="btn btn-outline-primary btn-sm badge-pill font-weight-medium px-4">Act</a>
                              @else
                                  <a href="#" class="btn btn-outline-secondary btn-sm btn-circle" data-toggle="modal" data-target="#modalDetail{{ $row->goal->id }}"><i class="fas fa-eye"></i></a>
                              @endif
                            </td>
                            @if ($data)
                            @include('pages.goals.admin.detail')
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
     
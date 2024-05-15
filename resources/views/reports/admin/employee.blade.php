<div class="row">
    <div class="col-md-12">
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="adminReportTable" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr class="text-center">
                        <th>#</th>
                        <th>NIK</th>
                        <th>Name</th>
                        <th>Gender</th>
                        <th>DOJ</th>
                        <th>Type</th>
                        <th>Unit</th>
                        <th>Job</th>
                        <th>Grade</th>
                        <th>PT</th>
                        <th>Locations</th>
                        <th>BU</th>
                        <th>Email</th>
                        <th>L1</th>
                        <th>L2</th>                                    
                        <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>

                    @foreach($data as $row)
                        <tr>
                            <td>{{ $loop->index + 1 }}</td>
                            <td>{{ $row->employee_id }}</td>
                            <td>{{ $row->fullname }}</td>
                            <td>{{ $row->gender }}</td>
                            <td>{{ $row->date_of_joining }}</td>
                            <td>{{ $row->employee_type }}</td>
                            <td>{{ $row->unit }}</td>
                            <td>{{ $row->designation }}</td>
                            <td>{{ $row->job_level }}</td>
                            <td>{{ $row->contribution_level_code }}</td>
                            <td>{{ $row->office_area." (".$row->group_company.")" }}</td>
                            <td>{{ $row->group_company }}</td>
                            <td>{{ $row->email }}</td>
                            <td>{{ $row->manager_l1_id }}</td>
                            <td>{{ $row->manager_l2_id }}</td>

                            <!--<td><span class="badge badge-success badge-pill w-100">Active</span></td>-->
                            <td class="text-center">
                                <a href="" class="btn btn-sm btn-circle btn-outline-primary" title="Edit" ><i class="fas fa-edit"></i></a>
                                
                                <a class="btn btn-sm btn-circle btn-outline-danger" title="Delete" onclick="handleDelete(this)" data-id=""><i class="fas fa-trash-alt"></i></a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    </div>
</div>
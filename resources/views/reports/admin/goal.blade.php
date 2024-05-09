<div class="row">
    <div class="col-md-12">
      <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="reportGoalsTable" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr class="text-center">
                            <th>Employees</th>
                            <th>KPI</th>
                            <th>Goal Status</th>
                            <th>Approval Status</th>
                            <th>Initiated On</th>
                            <th>Initiated By</th>
                            <th>Last Updated On</th>
                        </tr>
                    </thead>
                    <tbody>
                      @foreach ($data as $row)
                        <tr>
                          <td>{{ $row->employee->fullname }}<br>{{ $row->employee_id }}</td>
                          <td class="text-center">
                            <a href="#" class="btn btn-light btn-sm rounded-pill font-weight-medium" data-toggle="modal" data-target="#modalDetail{{ $row->goal->id }}"><i class="fas fa-search"></i></a>
                          </td>
                          <td class="text-center">
                            <span class="badge {{ $row->goal->form_status == 'Approved' ? 'badge-success' : 'badge-secondary'}} badge-pil px-3">{{ $row->goal->form_status }}</span></td>
                          <td class="text-center">
                            <a href="#" id="approval{{ $row->employee_id }}" data-toggle="tooltip" data-id="{{ $row->employee_id }}" class="approval-link badge {{ $row->status === 'Approved' ? 'badge-success' : 'badge-warning'}} badge-pil px-3">{{ $row->status }}</a>
                          </td>
                          <td class="text-center">{{ $row->created_at }}</td>
                          <td class="text-center">{{ $row->initiated->name }}<br>{{ $row->initiated->employee_id }}</td>
                          <td class="text-center">{{ $row->updated_at }}</td>
                          <div class="modal fade" id="modalDetail{{ $row->goal->id }}" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-xl mt-2" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                      <div class="form-inline text-lg mr-4">
                                          <button type="button" class="close mr-3" data-dismiss="modal" aria-label="Close">
                                              <span aria-hidden="true">&times;</span>
                                          </button>
                                          <span class="modal-title" id="viewFormEmployeeLabel">KPI's</span>
                                      </div>
                                      <div class="input-group-md">
                                          <input type="text" id="employee_name" class="form-control" placeholder="Search employee.." hidden>
                                      </div>
                                </div>
                                <div class="modal-body">
                                  <div class="container-fluid py-3">
                                      <form action="" method="post">
                                          <div class="d-sm-flex align-items-center justify-content-between mb-4">
                                                <h4>{{ $row->employee->fullname }} / <span class="font-weight-light">{{ $row->employee->employee_id }}</span></h4>
                                          </div>
                                          <!-- Content Row -->
                                          <div class="container-card">
                                            @php
                                                $formData = json_decode($row->goal['form_data'], true);
                                            @endphp
                                            @if ($formData)
                                            @foreach ($formData as $index => $data)
                                                <div class="card col-md-12 mb-4 shadow-sm">
                                                    <div class="card-header border-0 p-0 bg-white d-flex align-items-center justify-content-start">
                                                        <h1 class="rotate-n-45 text-primary"><i class="fas fa-angle-up p-0"></i></h1>
                                                    </div>
                                                    <div class="card-body p-0">
                                                        <div class="row mx-auto">
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="kpi">KPI</label>
                                                                    <textarea class="form-control bg-gray-100" disabled>{{ $data['kpi'] }}</textarea>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <div class="form-group">
                                                                    <label for="target">Target</label>
                                                                    <input type="text" value="{{ $data['target'] }}" class="form-control bg-gray-100" disabled>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <div class="form-group">
                                                                    <label for="uom">UoM</label>
                                                                    <input type="text" value="{{ $data['uom'] }}" class="form-control bg-gray-100" disabled>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <div class="form-group">
                                                                    <label for="weightage">Weightage</label>
                                                                    <div class="input-group">
                                                                        <input type="text" class="form-control bg-gray-100" value="{{ $data['weightage'] }}" disabled>
                                                                        <div class="input-group-append">
                                                                            <span class="input-group-text">%</span>
                                                                        </div>
                                                                    </div>
                                                                    <!-- Tambahkan kode untuk menampilkan error weightage jika ada -->
                                                                    @if ($errors->has("weightage"))
                                                                        <span class="text-danger">{{ $errors->first("weightage") }}</span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <div class="form-group">
                                                                    <label for="type">Type</label>
                                                                    <input type="text" value="{{ $data['type'] }}" class="form-control bg-gray-100" disabled>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                            @else
                                                <p>No form data available.</p>
                                            @endif                
                                </div>
                                      </form>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </tr>
                      @endforeach
                    </tbody>
                </table>
            </div>
        </div>
      </div>
    </div>
</div>
<div class="modal fade" id="modalApproval{{ $row->request->goal->id }}" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
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
              <form id="goalApprovalForm" action="{{ route('approval.goal') }}" method="post">
                @csrf
                <input type="hidden" name="id" value="{{ $row->request->goal->id }}">
                <input type="hidden" name="employee_id" value="{{ $row->request->employee_id }}">
                <input type="hidden" name="current_approver_id" value="{{ $row->request->current_approval_layer_id }}">
                  <div class="d-sm-flex align-items-center mb-4">
                        <h4>{{ $row->request->employee->fullname }} / <span class="font-weight-light">{{ $row->request->employee->employee_id }}</span></h4>
                  </div>
                  <!-- Content Row -->
                  <div class="container-card">
                    @php
                        $formData = json_decode($row->request->goal['form_data'], true);
                    @endphp
                    @if ($formData)
                    @foreach ($formData as $index => $data)
                        <div class="card col-md-12 mb-4 shadow-sm">
                            <div class="card-header border-0 p-0 bg-white d-flex align-items-center justify-content-start">
                                {{-- <span>#{{ $index + 1 }}</span> --}}
                                <h1 class="rotate-n-45 text-primary"><i class="fa fa-angle-up p-0"></i></h1>
                            </div>
                            <div class="card-body pt-0">
                                <div class="row mx-auto">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="kpi">KPI</label>
                                            <textarea name="kpi[]" class="form-control" required>{{ $data['kpi'] }}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="target">Target</label>
                                            <input type="text" name="target[]" value="{{ $data['target'] }}" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="uom">UoM</label>
                                            <select class="form-control" name="uom[]" id="uom" title="Unit of Measure" required>
                                                <option value="">- Select -</option>
                                                @foreach ($uomOption as $label => $options)
                                                <optgroup label="{{ $label }}">
                                                    @foreach ($options as $option)
                                                        <option value="{{ $option }}" {{ $data['uom'] === $option ? 'selected' : '' }}>
                                                            {{ $option }}
                                                        </option>
                                                    @endforeach
                                                </optgroup>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="weightage">Weightage</label>
                                            <div class="input-group">
                                                <input type="number" min="5" max="100" name="weightage[]" class="form-control" value="{{ $data['weightage'] }}" required>
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
                                            <select class="form-control" name="type[]" id="type" required>
                                                <option value="">Select</option>
                                                @foreach ($typeOption as $label => $options)
                                                    @foreach ($options as $option)
                                                        <option value="{{ $option }}"
                                                            {{ $data['type'] === $option ? 'selected' : '' }}>
                                                            {{ $option }}
                                                        </option>
                                                    @endforeach
                                                @endforeach
                                            </select>
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
            <form id="goalSendbackForm" action="{{ route('sendback.goal') }}" method="post">
                @csrf
                <input type="hidden" name="request_id" id="request_id">
                <input type="hidden" name="sendto" id="sendto">
                <input type="hidden" name="employee_id" value="{{ $row->request->employee_id }}">
                <div class="d-flex align-items-center mt-4">
                    <div class="form-group w-100">
                        <label for="messages">Messages</label>
                        <textarea name="messages" id="messages{{ $row->request->id }}" class="form-control" placeholder="Enter messages..">{{ $row->request->messages }}</textarea>
                    </div>
                </div>
                @if ($row->request->sendback_messages)
                <div class="d-flex align-items-center mt-4">
                    <div class="form-group w-100">
                        <label>Sendback Messages</label>
                        <textarea class="form-control" @disabled(true)>{{ $row->request->sendback_messages }}</textarea>
                    </div>
                </div>
                @endif
                <div class="d-sm-flex align-items-center justify-content-end mb-4">
                    <div class="align-item-center justify-content-between">
                        <div class="btn dropleft">
                            <a class="btn btn-outline-secondary px-4 badge-pill" href="#" role="button" data-toggle="dropdown" aria-expanded="false">Send back</a>
                              <div class="dropdown-menu shadow-sm">
                                <a class="dropdown-item" href="#" onclick="sendBack('{{ $row->request->id }}','{{ $row->request->employee->employee_id }}','{{ $row->request->employee->fullname }}')">{{ $row->request->employee->fullname .' '.$row->request->employee->employee_id }}</a>
                                @foreach ($row->request->approval as $item)
                                    <a class="dropdown-item" href="#" onclick="sendBack('{{ $item->request_id }}','{{ $item->approver_id }}','{{ $row->approver_name }}')">{{ $row->approver_name.' '.$item->approver_id }}</a>
                                @endforeach
                              </div> 
                            </div>           
                            <a href="#" onclick="confirmAprroval()" class="btn btn-primary badge-pill px-4">Approve</a>
                    </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
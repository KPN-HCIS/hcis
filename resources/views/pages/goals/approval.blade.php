<x-app-layout>
    @section('title', 'Goals')
    <x-slot name="content">
    <!-- Begin Page Content -->
    <div class="container-fluid">
        
    @if ($errors->any())
    <div class="alert alert-danger">
            @foreach ($errors->all() as $error)
                {{ $error }}
            @endforeach
    </div>
    @endif

    <div class="mandatory-field"></div>

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-start mb-4">
            <h1 class="h3">Approval Goals</h1>
        </div>
        @foreach ($data as $index => $row)
        <form id="goalApprovalForm" action="{{ route('approval.goal') }}" method="post">
            @csrf
            <input type="hidden" name="id" value="{{ $row->request->goal->id }}">
            <input type="hidden" name="employee_id" value="{{ $row->request->employee_id }}">
            <input type="hidden" name="current_approver_id" value="{{ $row->request->current_approval_id }}">
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
                                        <input type="text" name="uom[]" id="uom" value="{{ $data['uom'] }}" class="form-control" readonly>
                                        <input 
                                            type="text" 
                                            name="custom_uom[]" 
                                            id="custom_uom{{ $index }}" 
                                            class="form-control mt-2" 
                                            value="{{ $data['custom_uom'] }}" 
                                            placeholder=" UoM" 
                                            @if ($data['uom'] !== 'Other') 
                                                style="display: none;" 
                                            @endif 
                                            readonly
                                        >
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="type">Type</label>
                                        <input type="text" name="type[]" id="type" value="{{ $data['type'] }}" class="form-control" readonly>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="weightage">Weightage</label>
                                        <div class="input-group">
                                            <input name="weightage[]" class="form-control" value="{{ $data['weightage'] }}" required>
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
                            </div>
                        </div>
                    </div>
                @endforeach
                <div class="d-flex align-items-center mt-4 mb-2">
                    <div class="form-group w-100">
                        <label for="messages">Messages*</label>
                        <textarea name="messages" id="messages{{ $row->request->id }}" class="form-control" placeholder="Enter messages..">{{ $row->request->messages }}</textarea>
                    </div>
                </div>
                @else
                    <p>No form data available.</p>
                @endif                
            </div>
        </form>
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <div class="d-flex align-items-center justify-content-center mb-3"><h5>Total Weightage : <span class="font-weight-bold text-success" id="totalWeightage">100%</span></h5></div>
        </div>
        <form id="goalSendbackForm" action="{{ route('sendback.goal') }}" method="post">
            @csrf
            <input type="hidden" name="request_id" id="request_id">
            <input type="hidden" name="sendto" id="sendto">
            <input type="hidden" name="sendback" id="sendback" value="Sendback">
            <textarea @style('display: none') name="sendback_message" id="sendback_message"></textarea>
            <input type="hidden" name="form_id" value="{{ $row->request->form_id }}">
            
            <input type="hidden" name="approver" id="approver" value="{{ $row->request->manager->fullname.' ('.$row->request->manager->employee_id.')' }}">
            
            <input type="hidden" name="employee_id" value="{{ $row->request->employee_id }}">
            @if ($row->request->sendback_messages)
            <div class="d-flex align-items-center mt-4">
                <div class="form-group w-100">
                    <label>Sendback Messages</label>
                    <textarea class="form-control" @disabled(true)>{{ $row->request->sendback_messages }}</textarea>
                </div>
            </div>
            @endif
            <div class="d-sm-flex align-items-center justify-content-end mb-4">
                <div class="align-item-center justify-content-between text-center mb-4">
                    <a class="btn btn-dark px-4 mr-3 rounded-pill" href="#" role="button" data-toggle="dropdown" aria-expanded="false">Send back</a>
                        <div class="dropdown-menu shadow-sm">
                        <h6 class="dropdown-header dark">Select person below :</h6>
                        <a class="dropdown-item" href="#" onclick="sendBack('{{ $row->request->id }}','{{ $row->request->employee->employee_id }}','{{ $row->request->employee->fullname }}')">{{ $row->request->employee->fullname .' '.$row->request->employee->employee_id }}</a>
                        @foreach ($row->request->approval as $item)
                            <a class="dropdown-item" href="#" onclick="sendBack('{{ $item->request_id }}','{{ $item->approver_id }}','{{ $item->approverName->fullname }}')">{{ $item->approverName->fullname.' '.$item->approver_id }}</a>
                        @endforeach
                        </div> 
                    <a href="{{ url()->previous() }}" class="btn btn-danger px-4 mr-3 rounded-pill">Cancel</a>
                    <a href="javascript:void(0)" onclick="confirmAprroval()" class="btn btn-primary rounded-pill px-4">Approve</a>
                </div>
          </div>
        </form>
        @endforeach
    </div>
    </x-slot>
</x-app-layout>

{{-- <script src="{{ asset('js/goal-form.js') }}"></script> --}}
<script src="{{ asset('js/goal-approval.js') }}"></script>
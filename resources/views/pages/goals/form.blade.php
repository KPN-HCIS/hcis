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

        <!-- Page Heading -->
        <div class="d-flex align-items-center justify-content-start mb-4">
        </div>
        <form id="goalForm" action="{{ route('goals.submit') }}" method="POST">
        <div class="d-flex align-items-center justify-content-between mb-4">
            @csrf
            <input type="hidden" name="submit_type" id="submitType" value=""> <!-- Hidden input to store the button clicked -->
            <button type="submit" name="save_draft" class="btn btn-outline-secondary btn-sm badge-pill px-4 mr-3 save-draft" onclick="return setSubmitType('save_draft')"><i class="fas fa-save d-sm-none"></i><span class="d-sm-block d-none">Save as Draft</span></button>
            <div class="d-flex align-items-center">
                <a href="{{ url()->previous() }}" class="btn btn-outline-secondary px-4 mr-3">Cancel</a>
                <button type="submit" name="submit_form" class="btn btn-primary px-4 shadow" onclick="return setSubmitType('submit_form')">Submit</button>
            </div>
          </div>
          @foreach ($layer as $index => $data)
          <input type="hidden" class="form-control" name="users_id" value="{{ Auth::user()->id }}">
          <input type="hidden" class="form-control" name="approver_id" value="{{ $data->approver_id }}">
          <input type="hidden" class="form-control" name="employee_id" value="{{ $data->employee_id }}">
          <input type="hidden" class="form-control" name="category" value="Goals Setting">
          @endforeach
          <!-- Content Row -->
          <div class="container-card">
            <div class="card col-md-12 mb-4 border-top shadow-sm">
                <div class="card-header border-0 p-0 bg-white d-flex align-items-center justify-content-start">
                    {{-- <span>#{{ $index + 1 }}</span> --}}
                    <h1 class="rotate-n-45 text-primary"><i class="fas fa-angle-up p-0"></i></h1>
                </div>
                <div class="card-body p-0">
                    <div class="row mx-auto">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="kpi">KPI</label>
                                <textarea name="kpi[]" id="kpi" class="form-control" required>{{ old('kpi.0') }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="target">Target</label>
                                <input type="text" name="target[]" value="{{ old('target.0') }}" id="target" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="uom">UoM</label>
                                <select class="form-control select2 max-w-full" name="uom[]" id="uom{{ $index }}" onchange="otherUom('{{ $index }}')" title="Unit of Measure" required>
                                    <option value="">- Select -</option>
                                    @foreach ($uomOption as $label => $options)
                                    <optgroup label="{{ $label }}">
                                        @foreach ($options as $option)
                                            <option value="{{ $option }}">
                                                {{ $option }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                    @endforeach
                                </select>
                                <input type="text" class="form-control mt-2" name="custom_uom[]" id="custom_uom{{ $index }}" @style('display: none') placeholder="Enter UoM">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="weightage">Weightage</label>
                                <div class="input-group">
                                    <input type="number" min="5" max="100" class="form-control" name="weightage[]" value="{{ old('weightage.0') }}" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>                                  
                            </div>
                            {{ $errors->first("weightage") }}
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="type">Type</label>
                                <select class="form-control" name="type[]" id="type" required>
                                    <option value="">- Select -</option>
                                    <option value="Higher is better">Higher is better</option>
                                    <option value="Lower is better">Lower is better</option>
                                    <option value="Exact value">Exact value</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <a class="btn btn-outline-primary badge-pill px-4 mt-2 mb-4" onclick="addField('input')"><i class="fas fa-plus"></i> Add KPI</a>
        </div>
        </form>
    </div>
    </x-slot>
</x-app-layout>
<script src="{{ asset('js/goal-form.js') }}"></script>
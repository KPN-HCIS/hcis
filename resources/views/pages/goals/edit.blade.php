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

    <div class="alert alert-danger mandatory-field" hidden='false'>
        All fields is mandatory.
    </div>

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-start mb-4">
            <h1 class="h3">Edit Goals</h1>
        </div>
        <form id="goalForm" action="{{ route('goals.update') }}" method="POST">
        @csrf
          <input type="hidden" class="form-control" name="id" value="{{ $goal->id }}">
          <input type="hidden" class="form-control" name="employee_id" value="{{ $goal->employee_id }}">
          <input type="hidden" class="form-control" name="category" value="Goals">
          <!-- Content Row -->
        <div class="container-card">
        @foreach ($data as $index => $row)
            <div class="card col-md-12 mb-4 shadow-sm">
                <div class="card-header border-0 p-0 bg-white d-flex align-items-center justify-content-start">
                    <h1 class="rotate-n-45 text-primary"><i class="fas fa-angle-up p-0"></i></h1>
                </div>
                <div class="card-body p-0">
                    <div class="row mx-auto">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="kpi">KPI {{ $index + 1 }}</label>
                                <textarea name="kpi[]" id="kpi" class="form-control">{{ $row['kpi'] }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="target">Target</label>
                                <input type="text" name="target[]" value="{{ $row['target'] }}" id="target" class="form-control">
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
                                            <option value="{{ $option }}"
                                                {{ $selectedUoM[$index] === $option ? 'selected' : '' }}>
                                                {{ $option }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                    @endforeach
                                </select>
                                <input 
                                    type="text" 
                                    name="custom_uom[]" 
                                    id="custom_uom{{ $index }}" 
                                    class="form-control mt-2" 
                                    value="{{ $row['custom_uom'] }}" 
                                    placeholder="Enter UoM" 
                                    @if ($selectedUoM[$index] !== 'Other') 
                                        style="display: none;" 
                                    @endif 
                                >
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
                                                {{ $selectedType[$index] === $option ? 'selected' : '' }}>
                                                {{ $option }}
                                            </option>
                                        @endforeach
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="weightage">Weightage</label>
                                <div class="input-group">
                                    <input type="number" min="5" max="100" class="form-control" name="weightage[]" value="{{ $row['weightage'] }}" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>                                  
                            </div>
                            {{ $errors->first("weightage") }}
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <input type="hidden" id="count" value="{{ $formCount }}">
        <div class="col-md-2">
            <a class="btn btn-outline-primary badge-pill px-4 mt-2 mb-4" onclick="addField('edit')"><i class="fas fa-plus"></i> Add KPI</a>
        </div>
        <div class="d-sm-flex align-items-end justify-content-between mb-4">
            <input type="hidden" name="submit_type" id="submitType" value=""> <!-- Hidden input to store the button clicked -->
            <div class="d-flex inline align-items-center justify-content-center mb-3">
                <h5 class="text-center">Total Weightage : <span class="font-weight-bold text-success" id="totalWeightage">{{ $goal->form_status == "Draft" ? "" : "100%" }}</span></h5>
            </div>
            <div class="d-flex align-items-center justify-content-center mb-3">
                @if ($goal->form_status=='Draft')
                <button type="submit" name="save_draft" class="btn btn-info save-draft mr-3" onclick="return setSubmitType('save_draft')"><i class="fas fa-save d-sm-none"></i><span class="d-sm-block d-none">Save as Draft</span></button>  
                @endif
                <a href="{{ url()->previous() }}" class="btn btn-danger px-4 mr-3">Cancel</a>
                <button type="submit" name="submit_form" class="btn btn-primary px-4 shadow" onclick="return setSubmitType('submit_form')">Submit</button>
            </div>
        </div>
        </form>
    </div>
    </x-slot>
</x-app-layout>

<script src="{{ asset('js/goal-form.js') }}"></script>
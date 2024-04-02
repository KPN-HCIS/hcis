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
        <div class="d-sm-flex align-items-center justify-content-start mb-4">
        </div>
        <form action="{{ route('goals-submit') }}" method="POST">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
          <a class="btn btn-outline-secondary btn-sm badge-pill px-4 save-draft">Save as Draft</a>
            @csrf
            <button type="submit" class="btn btn-primary px-4 shadow">Submit</button>
          </div>
          <input type="hidden" class="form-control" name="id" value="1">
          <!-- Content Row -->
          <div class="container-card">
            <div class="card col-md-12 mb-3 border-left-primary shadow-sm">
                <div class="card-body pt-5">
                    <div class="row mx-auto">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="kpi">KPI</label>
                                <textarea name="kpi[]" id="kpi" class="form-control">{{ old('kpi.0') }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="target">Target</label>
                                <input type="text" name="target[]" value="{{ old('target.0') }}" id="target" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="uom">UoM</label>
                                <select class="form-control" name="uom[]" id="uom" title="Unit of Measure" required>
                                    <option value="">Select</option>
                                    <option value="Piece">Piece</option>
                                    <option value="Kilogram">Kilogram</option>
                                    <option value="Hectare">Hectare</option>
                                    <option value="Other">Others</option>
                                </select>
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
                                    <option value="">Select</option>
                                    <option value="Higher is Better">Higher is Better</option>
                                    <option value="Lower is Better">Lower is Better</option>
                                    <option value="Exact Value">Exact Value</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <a class="btn btn-outline-primary badge-pill px-4 mt-2 mb-4 add_field_button"><i class="fas fa-plus"></i> Add KPI</a>
        </div>
        </form>
    </div>
    </x-slot>
</x-app-layout>
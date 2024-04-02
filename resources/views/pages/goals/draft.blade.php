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
            @csrf
            <button type="submit" name="save_draft" class="btn btn-outline-secondary btn-sm badge-pill px-4 save-draft">Save as Draft</button>
            <button type="submit" name="submit" class="btn btn-primary px-4 shadow">Submit</button>
        </div>
          <input type="hidden" class="form-control" name="id" value="1">
          <!-- Content Row -->
          @for($i = 0; $i <= 9; $i++)
            <div class="card col-md-12 mb-3 border-left-primary shadow-sm">
                <div class="card-header border-0 px-0 bg-white d-sm-flex align-items-center justify-content-start">#{{ $i+1 }}</div>
                <div class="card-body pt-0">
                    <div class="row mx-auto">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="kpi">KPI</label>
                                <textarea name="kpi[]" id="kpi" class="form-control">{{ old('kpi.'.$i) }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="target">Target</label>
                                <input type="text" name="target[]" value="{{ old('target.'.$i) }}" id="target" class="form-control">
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
                                    <input type="number" min="5" max="100" class="form-control" name="weightage[]" value="{{ old('weightage.'.$i) }}" required>
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
            @endfor
        </form>
    </div>
    </x-slot>
</x-app-layout>
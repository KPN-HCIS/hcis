<x-app-layout>
    @section('title', 'Goals')
    <x-slot name="content">
    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-start mb-4">
        </div>
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
          <button class="btn btn-outline-secondary btn-sm badge-pill px-4">Save as Draft</button>
        <form action="{{ route('goals-approve') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-primary px-4">Submit</button>
          </div>
          <input type="hidden" class="form-control" name="id" value="3">
          <!-- Content Row -->
          <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table bg-white" id="tableInitiate" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>KPI</th>
                                        <th>Target</th>
                                        <th>UoM</th>
                                        <th>Weightage (%)</th>
                                        <th>Type</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td><textarea class="form-control" name="kpi_1" placeholder="Enter your KPI.." required>{{ old('kpi_1') }}</textarea></td>
                                        <td><input type="number" class="form-control" name="target_1" placeholder="Enter target.." required value="{{ old('target_1') }}"></td>
                                        <td>
                                            <select class="form-control" name="uom_1" id="uom_1" title="Unit of Measure" required>
                                                <option value="">Select</option>
                                                <option value="Piece">Piece</option>
                                                <option value="Kilogram">Kilogram</option>
                                                <option value="Hectare">Hectare</option>
                                                <option value="Other">Others</option>
                                            </select>
                                            <input type="text" name="uom_other_1" id="uom_other_1" class="form-control" placeholder="Enter UoM.." hidden>
                                        </td>
                                        <td><input type="number" min="5" max="100" class="form-control" name="weightage_1" placeholder="Enter persentage.." value="{{ old('weightage_1') }}" required></td>
                                        <td>
                                            <select class="form-control w-75" name="type_1" id="type_1" required>
                                                <option value="">Select</option>
                                                <option value="Higher is Better">Higher is Better</option>
                                                <option value="Lower is Better">Lower is Better</option>
                                                <option value="Exact Value">Exact Value</option>
                                            </select>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
          </div>
        </form>
    </div>
    </x-slot>
</x-app-layout>
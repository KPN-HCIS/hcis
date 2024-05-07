<x-app-layout>
@section('title', 'Goals')
<x-slot name="content">
    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-start mb-4">
          <button class="btn btn-outline-primary rounded-pill btn-sm px-4 mr-3">Create Role</button>
          <button class="btn btn-outline-primary rounded-pill btn-sm px-4 mr-3">Manage Role</button>
          <button class="btn btn-outline-primary rounded-pill btn-sm px-4 mr-3">Assign Users</button>
        </div>
        <form class="d-sm-flex align-items-center justify-content-between mb-4">
            <div class="form-group">
                <label for="role_name">Role Name</label>
                <input class="form-control" type="text" name="role_name" placeholder="Enter role name..">
            </div>
            <button class="btn btn-primary px-4">Create Role</button>
        </form>
        <!-- Content Row -->
        <div class="row">
            <div class="col-md-3">
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <ul class="navbar-nav">
                            <li>
                                <a class="nav-link" href="{{ route('home') }}">
                                <span>Goals</span></a>
                            </li>
                            <hr class="sidebar-divider mt-2 mb-3 w-100">
                            <li>
                                <a class="nav-link" href="{{ route('home') }}">
                                <span>Appraisal</span></a>
                            </li>
                            <hr class="sidebar-divider mt-2 mb-3 w-100">
                            <li>
                                <a class="nav-link" href="{{ route('home') }}">
                                <span>Calibration</span></a>
                            </li>
                            <hr class="sidebar-divider mt-2 mb-3 w-100">
                            <li>
                                <a class="nav-link" href="{{ route('home') }}">
                                <span>Reports</span></a>
                            </li>
                            <hr class="sidebar-divider mt-2 mb-3 w-100">
                            <li>
                                <a class="nav-link" href="{{ route('home') }}">
                                <span>Layer</span></a>
                            </li>
                            <hr class="sidebar-divider mt-2 mb-3 w-100">
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-9">
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <div class="d-sm-flex align-items-center justify-content-end mb-4">
                            <div class="form-group">
                                <input type="text" class="form-control rounded-pill px-3" placeholder="Search..">
                            </div>
                        </div>
                        <div>
                            <nav>
                                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                  <button class="nav-link border-left-0 border-top-0 border-right-0 border-bottom bg-white active" id="nav-edit-tab" data-toggle="tab" data-target="#nav-edit" type="button" role="tab" aria-controls="nav-edit" aria-selected="true">Edit / Actions</button>
                                  <button class="nav-link border-left-0 border-top-0 border-right-0 border-bottom bg-white" id="nav-view-tab" data-toggle="tab" data-target="#nav-view" type="button" role="tab" aria-controls="nav-view" aria-selected="false">View</button>
                                  <button class="nav-link border-left-0 border-top-0 border-right-0 border-bottom bg-white" id="nav-reports-tab" data-toggle="tab" data-target="#nav-reports" type="button" role="tab" aria-controls="nav-reports" aria-selected="false">Reports</button>
                                  <button class="nav-link border-left-0 border-top-0 border-right-0 border-bottom bg-white" id="nav-imports-tab" data-toggle="tab" data-target="#nav-imports" type="button" role="tab" aria-controls="nav-imports" aria-selected="false">Imports</button>
                                </div>
                              </nav>
                              <div class="tab-content p-3" id="nav-tabContent">
                                <div class="tab-pane fade show active pt-2" id="nav-edit" role="tabpanel" aria-labelledby="nav-edit-tab">
                                    <div class="form-group">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="gridCheck">
                                            <label class="form-check-label" for="gridCheck">
                                                Edit item
                                            </label>
                                        </div>
                                    </div>                                    
                                    <div class="form-group">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="gridCheck">
                                            <label class="form-check-label" for="gridCheck">
                                                Edit item
                                            </label>
                                        </div>
                                    </div>                                    
                                </div>
                                <div class="tab-pane fade pt-2" id="nav-view" role="tabpanel" aria-labelledby="nav-view-tab">
                                    <div class="form-group">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="gridCheck">
                                            <label class="form-check-label" for="gridCheck">
                                                View item
                                            </label>
                                        </div>
                                    </div>                                    
                                    <div class="form-group">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="gridCheck">
                                            <label class="form-check-label" for="gridCheck">
                                                View item
                                            </label>
                                        </div>
                                    </div>     
                                </div>
                                <div class="tab-pane fade pt-2" id="nav-reports" role="tabpanel" aria-labelledby="nav-reports-tab">
                                    <div class="form-group">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="gridCheck">
                                            <label class="form-check-label" for="gridCheck">
                                                Reports item
                                            </label>
                                        </div>
                                    </div>                                    
                                    <div class="form-group">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="gridCheck">
                                            <label class="form-check-label" for="gridCheck">
                                                Reports item
                                            </label>
                                        </div>
                                    </div>     
                                </div>
                                <div class="tab-pane fade pt-2" id="nav-imports" role="tabpanel" aria-labelledby="nav-imports-tab">
                                    <div class="form-group">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="gridCheck">
                                            <label class="form-check-label" for="gridCheck">
                                                Imports item
                                            </label>
                                        </div>
                                    </div>                                    
                                    <div class="form-group">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="gridCheck">
                                            <label class="form-check-label" for="gridCheck">
                                                Imports item
                                            </label>
                                        </div>
                                    </div>     
                                </div>
                              </div>                              
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Initiate Modal-->
    <div class="modal fade p-0" id="formInitiateEmployee" tabindex="-1" role="dialog" aria-labelledby="formInitiateEmployee" aria-hidden="true">
      <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content" @style('height:100vh; border-radius:0; border:0')>
          <div class="modal-header justify-content-start">
                <div class="form-inline text-lg mr-4">
                    <button type="button" class="close mr-3" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <span class="modal-title" id="formInitiateEmployeeLabel">Approval Form</span>
                </div>
                <div class="input-group-md">
                    <input type="text" id="employee_name" class="form-control" placeholder="Search employee.." hidden>
                </div>
          </div>
          <div class="modal-body" @style('overflow-y: auto;')>
            <div class="container-fluid py-3">
                <form action="" method="post">
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                          <h4>Tiger Nixon <span class="font-weight-light">01112131411</span></h4>
                          <button type="submit" class="btn btn-primary px-5" aria-label="Submit">Submit</button>
                    </div>
                    <!-- Content Row -->
                    <div class="row">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="tableInitiate" width="100%" cellspacing="0">
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
                                        <td><textarea class="form-control" name="kpi_1" placeholder="Enter your KPI.." required>On-time Delivery Rate</textarea></td>
                                        <td><input type="number" class="form-control" name="target_1" placeholder="Enter target.." required value="Achieve a 95% on-time delivery rate for all shipments."></td>
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
                                        <td><input type="number" min="5" max="100" class="form-control" name="weightage" placeholder="Enter persentage.." value="20" required></td>
                                        <td>
                                            <select class="form-control w-75" name="type_1" id="type_1" required>
                                                <option value="">Select</option>
                                                <option value="Higher is better" selected>Higher is better</option>
                                                <option value="Lower is better">Lower is better</option>
                                                <option value="Exact value">Exact value</option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td><textarea class="form-control" name="kpi_1" placeholder="Enter your KPI.." required>On-time Delivery Rate</textarea></td>
                                        <td><input type="number" class="form-control" name="target_1" placeholder="Enter target.." required value="Achieve a 95% on-time delivery rate for all shipments."></td>
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
                                        <td><input type="number" min="5" max="100" class="form-control" name="weightage" placeholder="Enter persentage.." value="20" required></td>
                                        <td>
                                            <select class="form-control w-75" name="type_1" id="type_1" required>
                                                <option value="">Select</option>
                                                <option value="Higher is better" selected>Higher is better</option>
                                                <option value="Lower is better">Lower is better</option>
                                                <option value="Exact value">Exact value</option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>3</td>
                                        <td><textarea class="form-control" name="kpi_1" placeholder="Enter your KPI.." required>On-time Delivery Rate</textarea></td>
                                        <td><input type="number" class="form-control" name="target_1" placeholder="Enter target.." required value="Achieve a 95% on-time delivery rate for all shipments."></td>
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
                                        <td><input type="number" min="5" max="100" class="form-control" name="weightage" placeholder="Enter persentage.." value="20" required></td>
                                        <td>
                                            <select class="form-control w-75" name="type_1" id="type_1" required>
                                                <option value="">Select</option>
                                                <option value="Higher is better" selected>Higher is better</option>
                                                <option value="Lower is better">Lower is better</option>
                                                <option value="Exact value">Exact value</option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>4</td>
                                        <td><textarea class="form-control" name="kpi_1" placeholder="Enter your KPI.." required>On-time Delivery Rate</textarea></td>
                                        <td><input type="number" class="form-control" name="target_1" placeholder="Enter target.." required value="Achieve a 95% on-time delivery rate for all shipments."></td>
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
                                        <td><input type="number" min="5" max="100" class="form-control" name="weightage" placeholder="Enter persentage.." value="20" required></td>
                                        <td>
                                            <select class="form-control w-75" name="type_1" id="type_1" required>
                                                <option value="">Select</option>
                                                <option value="Higher is better" selected>Higher is better</option>
                                                <option value="Lower is better">Lower is better</option>
                                                <option value="Exact value">Exact value</option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>5</td>
                                        <td><textarea class="form-control" name="kpi_1" placeholder="Enter your KPI.." required>On-time Delivery Rate</textarea></td>
                                        <td><input type="number" class="form-control" name="target_1" placeholder="Enter target.." required value="Achieve a 95% on-time delivery rate for all shipments."></td>
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
                                        <td><input type="number" min="5" max="100" class="form-control" name="weightage" placeholder="Enter persentage.." value="20" required></td>
                                        <td>
                                            <select class="form-control w-75" name="type_1" id="type_1" required>
                                                <option value="">Select</option>
                                                <option value="Higher is better" selected>Higher is better</option>
                                                <option value="Lower is better">Lower is better</option>
                                                <option value="Exact value">Exact value</option>
                                            </select>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </form>
            </div>
          </div>
        </div>
      </div>
    </div>
</x-slot>
</x-app-layout>

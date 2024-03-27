<x-app-layout>
    @section('title', 'Goals')
    <x-slot name="content">
    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-end mb-4">
          <a href="{{ route('goals-form') }}" class="btn btn-primary px-4 shadow">Create Goal</a>
        </div>
        <div class="d-sm-flex align-items-center justify-content-start mb-4">
          <button class="btn btn-outline-primary badge-pill btn-sm px-4 mr-3">All Task</button>
          <button class="btn btn-outline-primary badge-pill btn-sm px-4 mr-3">Active</button>
          <button class="btn btn-outline-primary badge-pill btn-sm px-4 mr-3">Draft</button>
          <button class="btn btn-outline-primary badge-pill btn-sm px-4 mr-3">Completed</button>
          <button class="btn btn-outline-primary badge-pill btn-sm px-4 mr-3">Revoked</button>
        </div>
        <!-- Content Row -->
        <div class="row">
            <div class="col-md-12">

              <div class="card shadow mb-4">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="taskTable" width="100%" cellspacing="0">
                            <thead class="thead-light">
                                <tr class="text-center">
                                    <th>Employees</th>
                                    <th>Category</th>
                                    <th>Approval Status</th>
                                    <th>Initiated On</th>
                                    <th>Initiated By</th>
                                    <th>Last Updated On</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Tiger Nixon</td>
                                    <td>KPI Setting</td>
                                    <td class="px-5"><span class="badge badge-success badge-pill w-100">Complete</span></td>
                                    <td class="text-center">20/12/2024</td>
                                    <td class="text-center">Tiger Nixon</td>
                                    <td class="text-center">30/12/2024</td>
                                    <td><button class="btn btn-outline-primary btn-sm w-100 badge-pill font-weight-medium" data-toggle="modal" data-target="#viewFormEmployee">View form</button></td>
                                </tr>
                                <tr>
                                  <td>Garrett Winters</td>
                                  <td>KPI Setting</td>
                                  <td class="px-5"><span class="badge badge-success badge-pill w-100">Complete</span></td>
                                  <td class="text-center">20/12/2024</td>
                                  <td class="text-center">Garrett Winters</td>
                                  <td class="text-center">30/12/2024</td>
                                  <td><button class="btn btn-outline-primary btn-sm w-100 badge-pill font-weight-medium" data-toggle="modal" data-target="#viewFormEmployee">View form</button></td>
                                </tr>
                                <tr>
                                  <td>Ashton Cox</td>
                                  <td>KPI Setting</td>
                                  <td class="px-5"><span id="approval" class="badge badge-warning badge-pill w-100">Pending</span></td>
                                  <td class="text-center">20/12/2024</td>
                                  <td class="text-center">Ashton Cox</td>
                                  <td class="text-center">30/12/2024</td>
                                  <td><a href="{{ route('goals-approval','3') }}" class="btn btn-outline-primary btn-sm w-100 badge-pill font-weight-medium">Act</a></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
              </div>
            </div>
        </div>
    </div>
    <!-- Initiate Modal-->
    <div class="modal fade p-0" id="viewFormEmployee" tabindex="-1" role="dialog" aria-labelledby="viewFormEmployee" aria-hidden="true">
      <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content" @style('height:100vh; border-radius:0; border:0')>
          <div class="modal-header justify-content-start">
                <div class="form-inline text-lg mr-4">
                    <button type="button" class="close mr-3" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <span class="modal-title" id="viewFormEmployeeLabel">Approval Form</span>
                </div>
                <div class="input-group-md">
                    <input type="text" id="employee_name" class="form-control" placeholder="Search employee.." hidden>
                </div>
          </div>
          <div class="modal-body" @style('overflow-y: auto;')>
            <div class="container-fluid py-3">
                <form action="" method="post">
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                          <h4>Employee Name / <span class="font-weight-light">Employee ID</span></h4>
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
                                        <td><textarea class="form-control">On-time Delivery Rate</textarea></td>
                                        <td><input type="text" class="form-control" value="95%"></td>
                                        <td>
                                            <input type="text" class="form-control" value="Percent">
                                            <input type="text" name="uom_other_1" id="uom_other_1" class="form-control" placeholder="Enter UoM.." hidden>
                                        </td>
                                        <td><input type="text" min="5" max="100" class="form-control" name="weightage" placeholder="Enter persentage.." value="20" required></td>
                                        <td>
                                            <input type="text" class="form-control" value="Higher is Better">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td><textarea class="form-control">On-time Delivery Rate</textarea></td>
                                        <td><input type="text" class="form-control" value="95%"></td>
                                        <td>
                                            <input type="text" class="form-control" value="Percent">
                                            <input type="text" name="uom_other_1" id="uom_other_1" class="form-control" placeholder="Enter UoM.." hidden>
                                        </td>
                                        <td><input type="text" min="5" max="100" class="form-control" name="weightage" placeholder="Enter persentage.." value="20" required></td>
                                        <td>
                                            <input type="text" class="form-control" value="Higher is Better">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>3</td>
                                        <td><textarea class="form-control">On-time Delivery Rate</textarea></td>
                                        <td><input type="text" class="form-control" value="95%"></td>
                                        <td>
                                            <input type="text" class="form-control" value="Percent">
                                            <input type="text" name="uom_other_1" id="uom_other_1" class="form-control" placeholder="Enter UoM.." hidden>
                                        </td>
                                        <td><input type="text" min="5" max="100" class="form-control" name="weightage" placeholder="Enter persentage.." value="20" required></td>
                                        <td>
                                            <input type="text" class="form-control" value="Higher is Better">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>4</td>
                                        <td><textarea class="form-control">On-time Delivery Rate</textarea></td>
                                        <td><input type="text" class="form-control" value="95%"></td>
                                        <td>
                                            <input type="text" class="form-control" value="Percent">
                                            <input type="text" name="uom_other_1" id="uom_other_1" class="form-control" placeholder="Enter UoM.." hidden>
                                        </td>
                                        <td><input type="text" min="5" max="100" class="form-control" name="weightage" placeholder="Enter persentage.." value="20" required></td>
                                        <td>
                                            <input type="text" class="form-control" value="Higher is Better">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>5</td>
                                        <td><textarea class="form-control">On-time Delivery Rate</textarea></td>
                                        <td><input type="text" class="form-control" value="95%"></td>
                                        <td>
                                            <input type="text" class="form-control" value="Percent">
                                            <input type="text" name="uom_other_1" id="uom_other_1" class="form-control" placeholder="Enter UoM.." hidden>
                                        </td>
                                        <td><input type="text" min="5" max="100" class="form-control" name="weightage" placeholder="Enter persentage.." value="20" required></td>
                                        <td>
                                            <input type="text" class="form-control" value="Higher is Better">
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
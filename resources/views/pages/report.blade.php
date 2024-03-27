<x-app-layout>
  @section('title', 'Reports')
  <x-slot name="content">
    <!-- Begin Page Content -->
    <div class="container-fluid">
      <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div class="form-group">
          <label for="report_type">Report Name:</label>
          <select class="form-control" name="report_type" id="report_type">
            <option value="">select report</option>
            <option value="KPI">KPI</option>
            <option value="Appraisal">Appraisal</option>
          </select>
        </div>     
        <button class="btn btn-primary px-4 shadow">Generate</button>   
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
                              </tr>
                          </thead>
                          <tbody>
                              <tr>
                                  <td>Tiger Nixon</td>
                                  <td>KPI Setting</td>
                                  <td>Complete</td>
                                  <td class="text-center">20/12/2024</td>
                                  <td class="text-center">Tiger Nixon</td>
                                  <td class="text-center">30/12/2024</td>
                              </tr>
                              <tr>
                                <td>Garrett Winters</td>
                                <td>KPI Setting</td>
                                <td>Complete</td>
                                <td class="text-center">20/12/2024</td>
                                <td class="text-center">Garrett Winters</td>
                                <td class="text-center">30/12/2024</td>
                            </tr>
                            <tr>
                              <td>Ashton Cox</td>
                              <td>KPI Setting</td>
                              <td>Pending: L1 Manager - Douglas McGee</td>
                              <td class="text-center">20/12/2024</td>
                              <td class="text-center">Ashton Cox</td>
                              <td class="text-center">30/12/2024</td>
                            </tr>
                          </tbody>
                      </table>
                  </div>
              </div>
            </div>
          </div>
      </div>
  </div>
  </x-slot>
</x-app-layout>
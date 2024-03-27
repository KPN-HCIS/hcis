<x-app-layout>
    @section('title', 'Schedule')
    <x-slot name="content">
    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-end mb-4">
            <a href="{{ route('schedules-form') }}" class="btn btn-primary px-4 shadow">Create Schedule</a>
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
                                  <th>#</th>
                                  <th>Name</th>
                                  <th>From</th>
                                  <th>To</th>
                                  <th>Initiated By</th>
                                  <th>Trigger date</th>
                                  <th>Status</th>
                                  <th>Actions</th>
                              </tr>
                          </thead>
                          <tbody>
                              <tr>
                                    <td>1</td>
                                    <td>KPI Setting Period</td>
                                    <td>01/06/2024</td>
                                    <td>30/06/2024</td>
                                    <td>Admin 01</td>
                                    <td>31/05/2024</td>
                                    <td><span class="badge badge-success badge-pill w-100">Active</span></td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-circle btn-outline-primary" title="Edit" ><i class="fas fa-edit"></i></button>
                                        <button class="btn btn-sm btn-circle btn-outline-secondary" title="Archive"><i class="fas fa-inbox"></i></button>
                                        <button class="btn btn-sm btn-circle btn-outline-danger" title="Delete"><i class="fas fa-trash-alt"></i></button>
                                    </td>
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
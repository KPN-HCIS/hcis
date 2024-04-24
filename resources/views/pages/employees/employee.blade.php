<x-app-layout>
    @section('title', 'Employee')
    <x-slot name="content">
    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-end mb-4">
            <a href="{{ route('schedules-form') }}" class="btn btn-primary px-4 shadow">Create Employee</a>
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
                                <th>NIK</th>
                                <th>Name</th>
                                <th>Gender</th>
                                <th>DOJ</th>
                                <th>Type</th>
                                <th>Unit</th>
                                <th>Job</th>
                                <th>Grade</th>
                                <th>PT</th>
                                <th>Locations</th>
                                <th>BU</th>
                                <th>Email</th>
                                <th>L1</th>
                                <th>L2</th>                                    
                                <th>Actions</th>
                              </tr>
                          </thead>
                          <tbody>

                            @foreach($employees as $employee)
                              <tr>
                                    <td>{{ $loop->index + 1 }}</td>
                                    <td>{{ $employee->employee_id }}</td>
                                    <td>{{ $employee->fullname }}</td>
                                    <td>{{ $employee->gender }}</td>
                                    <td>{{ $employee->date_of_joining }}</td>
                                    <td>{{ $employee->employee_type }}</td>
                                    <td>{{ $employee->unit }}</td>
                                    <td>{{ $employee->designation }}</td>
                                    <td>{{ $employee->job_level }}</td>
                                    <td>{{ $employee->contribution_level_code }}</td>
                                    <td>{{ $employee->office_area }}</td>
                                    <td>{{ $employee->group_company }}</td>
                                    <td>{{ $employee->email }}</td>
                                    <td>{{ $employee->manager_l1_id }}</td>
                                    <td>{{ $employee->manager_l2_id }}</td>

                                    <!--<td><span class="badge badge-success badge-pill w-100">Active</span></td>-->
                                    <td class="text-center">
                                        <a href="" class="btn btn-sm btn-circle btn-outline-primary" title="Edit" ><i class="fas fa-edit"></i></a>
                                        
                                        <a class="btn btn-sm btn-circle btn-outline-danger" title="Delete" onclick="handleDelete(this)" data-id=""><i class="fas fa-trash-alt"></i></a>
                                    </td>
                              </tr>
                              @endforeach
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
<script>
    // Periksa apakah ada pesan sukses
    var successMessage = "{{ session('success') }}";

    // Jika ada pesan sukses, tampilkan sebagai alert
    if (successMessage) {
        alert(successMessage);
    }
</script>
<script>
        function handleDelete(element) {
            if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
                var scheduleId = element.getAttribute('data-id');

                fetch('/schedule/' + scheduleId, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Terjadi kesalahan saat menghapus data.');
                    }
                    location.reload();
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat menghapus data.');
                });
            }
        }
    
</script>

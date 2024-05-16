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
                                  <th>Type</th>
                                  <th>From</th>
                                  <th>To</th>
                                  <th>Reminder</th>
                                  <th>Days</th>
                                  <th>Messages</th>
                                  <th>Actions</th>
                              </tr>
                          </thead>
                          <tbody>

                            @foreach($schedules as $schedule)
                              <tr>
                                    <td>{{ $loop->index + 1 }}</td>
                                    <td>{{ $schedule->schedule_name }}</td>
                                    <td>{{ $schedule->employee_type }}</td>
                                    <td>{{ $schedule->start_date }}</td>
                                    <td>{{ $schedule->end_date }}</td>
                                    <td>@if($schedule->checkbox_reminder == '1') Yes @else No @endif</td>
                                    <td>@if($schedule->repeat_days<>'') {{ $schedule->repeat_days }} @else {{ $schedule->before_end_date.' Days Before End Date' }} @endif</td>
                                    <td>{{ $schedule->messages }}</td>
                                    <!--<td><span class="badge badge-success badge-pill w-100">Active</span></td>-->
                                    <td class="text-center">
                                        <a href="{{ route('edit-schedule', $schedule->id) }}" class="btn btn-sm btn-circle btn-outline-primary" title="Edit" ><i class="fas fa-edit"></i></a>
                                        
                                        <a class="btn btn-sm btn-circle btn-outline-danger" title="Delete" onclick="handleDelete(this)" data-id="{{ $schedule->id }}"><i class="fas fa-trash-alt"></i></a>
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

<x-app-layout>
    @section('title', 'Layer')
    <x-slot name="content">
    <!-- Begin Page Content -->
    <div class="container-fluid">       
        
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
                                <th>PT</th>
                                <th>BU</th>
                                <th>Layer</th>
                                <th>Superior</th>
                                <th>L1</th>
                                <th>L2</th>
                                <th>L3</th>                                   
                                <th>Actions</th>
                              </tr>
                          </thead>
                          <tbody>

                            @foreach($approvalLayers as $approvalLayer)
                              <tr>
                                    <td>{{ $loop->index + 1 }}</td>
                                    <td>{{ $approvalLayer->employee_id }}</td>
                                    <td>{{ $approvalLayer->fullname }}</td>
                                    <td>{{ $approvalLayer->contribution_level_code }}</td>
                                    <td>{{ $approvalLayer->group_company }}</td>
                                    <td>{{ $approvalLayer->layer }}</td>
                                    <td>{{ $approvalLayer->directname }}</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
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
<script>
$(document).ready(function() {
    // Initialize DataTable
    var table = $('#taskTable').DataTable();

    // Apply filter when location dropdown value changes
    $('#locationFilter').on('change', function() {
        applyLocationFilter(table);
    });

    // Apply filter when table is redrawn (e.g., when navigating to next page)
    table.on('draw.dt', function() {
        applyLocationFilter(table);
    });
});

function applyLocationFilter(table) {
    var locationId = $('#locationFilter').val().toUpperCase();

    // Filter table based on location
    table.column(10).search(locationId).draw(); // Adjust index based on your table structure
}

</script>

<x-app-layout>
    @section('title', 'Layer')
    <x-slot name="content">
    <!-- Begin Page Content -->
    <div class="container-fluid">       
        <div class="d-sm-flex align-items-center justify-content-end mb-4">
            <button type="button" class="btn btn-primary open-import-modal" title="Edit">Import Layer</button>
        </div>
        <!-- Content Row -->
        <div class="row">
          <div class="col-md-12">

            <div class="card shadow mb-4">
              <div class="card-body">
                  <div class="table-responsive">
                      <table class="table table-hover" id="layerTable" width="100%" cellspacing="0">
                          <thead class="thead-light">
                              <tr class="text-center">
                                <th>#</th>
                                <th>NIK</th>
                                <th>Name</th>
                                <th>PT</th>
                                <th>Area</th>
                                <th>BU</th>
                                <th>Superior</th>                                 
                                <th>Actions</th>
                              </tr>
                          </thead>
                          <tbody>
                            @php 
                                $no=1;
                            @endphp
                            @foreach($approvalLayers as $approvalLayer)
                                <tr>
                                    <td>
                                        {{ $no++ }}
                                    </td>
                                    <td>{{ $approvalLayer->employee_id }}</td>
                                    <td>{{ $approvalLayer->fullname }}</td>
                                    <td>{{ $approvalLayer->contribution_level_code }}</td>
                                    <td>{{ $approvalLayer->office_area }}</td>
                                    <td>{{ $approvalLayer->group_company }}</td>
                                    <td>
                                        @php
                                            $layersArray = explode('|', $approvalLayer->layers);
                                            $approverNamesArray = explode('|', $approvalLayer->approver_names);
                                        @endphp
                                        @foreach($layersArray as $index => $layer)
                                            {{ "L".$layer }} : {{ $approverNamesArray[$index] }}<br>
                                        @endforeach
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-circle btn-outline-primary open-edit-modal"
                                        data-employee-id="{{ $approvalLayer->employee_id }}"
                                        data-fullname="{{ $approvalLayer->fullname }}"
                                        data-app="{{ $approvalLayer->approver_ids }}"
                                        data-layer="{{ $approvalLayer->layers }}"
                                        data-app-name="{{ $approvalLayer->approver_names }}"
                                        title="Edit"><i class="fas fa-edit"></i></button>
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

<!-- Modal -->
<div class="modal fade" id="editModal" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Update Superior</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Form for editing employee details -->
                <form id="editForm" action="{{ route('update-layer') }}" method="POST">
                    @csrf
                    <input type="hidden" name="employee_id" id="employee_id">
                    <div class="form-group" style="display: none;">
                        <label for="employee_id">Employee ID:</label>
                        <input type="text" class="form-control" id="employeeId" name="employee_id" readonly>
                    </div>
                    <div class="form-group">
                        <label for="fullname">Full Name:</label>
                        <input type="text" class="form-control" id="fullname" name="fullname" readonly>
                    </div>
                    <hr>
                    <div class="input-group margin" id="viewlayer">
                        
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary" form="editForm">Save changes</button>
            </div>
        </div>
    </div>
</div>

<!-- importModal -->
<div class="modal fade" id="importModal" role="dialog" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importModalLabel">Import Superior</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="importForm" action="{{ route('import-layer') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="excelFile">Upload Excel File</label>
                        <input type="file" class="form-control-file" id="excelFile" name="excelFile" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary" form="importForm">Import Data</button>
            </div>
        </div>
    </div>
</div>

</x-slot>
</x-app-layout>

<script>
    $(document).ready(function() {
        $('.open-edit-modal').on('click', function() {
            var employeeId = $(this).data('employee-id');
            $('#employeeId').text(employeeId);
            $('#editModal').modal('show');
        });
    });
    $(document).ready(function() {
        $('.open-import-modal').on('click', function() {
            // var employeeId = $(this).data('employee-id');
            // $('#employeeId').text(employeeId);
            $('#importModal').modal('show');
        });
    });
</script>
<script>
    // Periksa apakah ada pesan sukses
    var successMessage = "{{ session('success') }}";

    // Jika ada pesan sukses, tampilkan sebagai alert
    if (successMessage) {
        alert(successMessage);
    }
</script>

<script>
$(document).ready(function() {

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
<script>
    $(document).ready(function() {
        $('.open-edit-modal').on('click', function() {
            var employeeId = $(this).data('employee-id');
            var fullname = $(this).data('fullname');
            var app = $(this).data('app');
            var layer = $(this).data('layer');
            var appname = $(this).data('app-name');

            // populateModal(employeeId, fullname, app, layer, appname);
            populateModal(employeeId, fullname, app, layer, appname, {!! json_encode($employees) !!});
        });
    });

    function populateModal(employeeId, fullName, app, layer, appName, employees) {
        $('#employee_id').val(employeeId);
        $('#employeeId').val(employeeId);
        $('#fullname').val(fullName);

        if (app.includes('|')) {
            // Jika nilai app mengandung karakter '|', lakukan pemisahan
            apps = app.split('|');
            layers = layer.split('|');
            appNames = appName.split('|');
        } else {
            // Jika tidak mengandung karakter '|', gunakan nilai langsung
            apps = [app]; // Ubah ke array untuk konsistensi
            layers = [layer];
            appNames = [appName];
        }

        $('#viewlayer').empty();
        $('#nikAppInputs').empty();
        var layerIndex = 1;

        for (var i = 0; i < (apps.length + 3); i++) {
            var selectOptions = '<option value="">- Select -</option>';
            for (var j = 0; j < employees.length; j++) {
                var selected = (employees[j].employee_id == apps[i]) ? 'selected' : '';
                selectOptions += '<option value="' + employees[j].employee_id + '" ' + selected + '>' + employees[j].fullname + ' - ' + employees[j].employee_id + '</option>';
            }

            var disabled = (i > apps.length) ? 'disabled' : ''; // Disable additional layers initially
            $('#viewlayer').append('<div class="input-group mb-2"><div class="input-group-prepend"><span class="input-group-text">Layer ' + layerIndex + '</span></div><select name="nik_app[]" class="form-control select2" style="width:380px" ' + disabled + '>' + selectOptions + '</select></div>');
            layerIndex++;
        }

        // Initialize Select2
        $('.select2').select2({
            dropdownParent: $('#editModal')
        });

        // Add change event listener to enable the next layer only if the current one is selected
        $('#viewlayer .select2').each(function (index) {
            $(this).on('change', function () {
                if ($(this).val() !== '') {
                    // Enable the next select element if current selection is not empty
                    for (var i = index + 2; i < $('#viewlayer .select2').length; i++) {
                        if(i === index + 2){
                            $('#viewlayer .select2').eq(i).val('').prop('disabled', false).trigger('change');
                        }
                    }
                } else {
                    // Disable the subsequent select elements if the current one is cleared
                    for (var i = index + 2; i < $('#viewlayer .select2').length; i++) {
                        $('#viewlayer .select2').eq(i).val('').prop('disabled', true).trigger('change');
                    }
                }
            });
        });

        $('#editModal').modal('show');
    }
</script>
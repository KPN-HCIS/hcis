<div class="modal fade" id="modalFilter" data-backdrop="static" data-keyboard="false" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog mt-3" role="document">
        <div class="modal-content">
            <div class="modal-header">
              <div class="form-inline text-lg mr-4">
                  <button type="button" class="close mr-3" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                  </button>
                  <span class="modal-title" id="viewFormEmployeeLabel">Filters</span>
              </div>
              <div class="input-group-md">
                  <input type="text" id="employee_name" class="form-control" placeholder="Search employee.." hidden>
              </div>
            </div>
            <form id="filter_form" action="" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="container-fluid py-3">
                        <!-- Content Row -->
                        <div class="container-card">
                            <div class="d-sm-flex">
                                <div class="form-group">
                                    <label for="report_type">Report Type:</label>
                                    <select class="form-control" name="report_type" id="report_type">
                                    <option value="">select report</option>
                                    <option value="Goal">Goal</option>
                                    </select>
                                </div> 
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="d-sm-flex justify-content-end">
                        <a class="btn btn-outline-secondary mr-3" data-dismiss="modal">Cancel</a>
                        <button type="submit" class="btn btn-primary">Apply</button>
                    </div>
                </div>
            </form>
            </div>
        </div>
  </div>
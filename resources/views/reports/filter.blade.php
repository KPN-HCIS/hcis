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
                            <div class="d-sm-flex">
                                <div class="form-group">
                                    <label for="group_company">Group Company</label>
                                    <select class="form-control" name="group_company" id="group_company">
                                        <option value="">- select group company -</option>
                                        @foreach ($groupCompanies as $groupCompany)
                                        <option value="{{ $groupCompany }}">{{ $groupCompany }}</option>
                                        @endforeach
                                    </select>
                                </div> 
                            </div>
                            <div class="flex">
                                <div class="form-group">
                                    <label for="company">Company</label>
                                    <select class="form-control select2" name="company" id="company">
                                        <option value="">- select company -</option>
                                        @foreach ($companies as $company)
                                        <option value="{{ $company->contribution_level_code }}">{{ $company->contribution_level }}</option>
                                        @endforeach
                                    </select>
                                </div> 
                            </div>
                            <div class="flex">
                                <div class="form-group">
                                    <label for="location">Location</label>
                                    <select class="form-control select2" name="location" id="location">
                                        <option value="">- select location -</option>
                                        @foreach ($locations as $location)
                                        <option value="{{ $location->work_area }}">{{ $location->area.' ('.$location->company_name.')' }}</option>
                                        @endforeach
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
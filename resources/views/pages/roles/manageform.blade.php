@if ($roleId)
<form action="{{ route('roles.update') }}" method="POST">
  @csrf

  <input type="hidden" name="roleId" value="{{ $roleId }}">
  <div class="d-sm-flex align-items-right justify-content-end mb-4">
    <button type="submit" class="btn btn-primary px-4">Update</button>
  </div>
  <div class="row mb-4">
    <div class="col-md-3 mb-3">
      <div class="list-group" id="list-tab" role="tablist">
        <a class="list-group-item list-group-item-action active" id="list-goal-list" data-toggle="list" href="#list-goal" role="tab" aria-controls="goal">Goals</a>
        <a class="list-group-item list-group-item-action" id="list-report-list" data-toggle="list" href="#list-report" role="tab" aria-controls="report">Reports</a>
        <a class="list-group-item list-group-item-action" id="list-setting-list" data-toggle="list" href="#list-setting" role="tab" aria-controls="setting">Settings</a>
      </div>
    </div>
    <div class="col-md-9">
      <div class="card">
        <div class="card-body">
          <div class="row">
            <div class="tab-content" id="nav-tabContent">
              <div class="tab-pane fade active show" id="list-goal" role="tabpanel" aria-labelledby="list-goal-list">
                <ul class="nav">
                  <li class="nav-item">
                    <a class="nav-link" id="goal-accessibility" data-toggle="list" href="#list-goal-accessibility" role="tab" aria-controls="goal">Accessibility</a>
                  </li>
                </ul>
                <div class="tab-pane fade p-3 active show" id="list-goal-accessibility" role="tabpanel" aria-labelledby="goal-accessibility">
                  <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" value="{{ $permissions[0]->id }}" name="goalView" {{ $permissions[0]->role_permission_id ? 'checked' : '' }}>
                    <label class="form-check-label" for="goalView">
                      View
                    </label>
                  </div>
                  <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" value="{{ $permissions[1]->id }}" name="goalApproval" {{ $permissions[1]->role_permission_id ? 'checked' : '' }}>
                    <label class="form-check-label" for="goalApproval">
                      Initiate Approval
                    </label>
                  </div>                                                
                  <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" value="{{ $permissions[2]->id }}" name="goalSendback" {{ $permissions[2]->role_permission_id ? 'checked' : '' }}>
                    <label class="form-check-label" for="goalSendback">
                      Sendback Approval
                    </label>
                  </div>
                </div>
              </div>
              <div class="tab-pane fade" id="list-report" role="tabpanel" aria-labelledby="list-report-list">
                <ul class="nav">
                  <li class="nav-item">
                    <a class="nav-link" id="report-accessibility" data-toggle="list" href="#list-report-accessibility" role="tab" aria-controls="report">Accessibility</a>
                  </li>
                </ul>
                <div class="tab-pane fade p-3 active show" id="list-report-accessibility" role="tabpanel" aria-labelledby="report-accessibility">
                  <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" value="{{ $permissions[3]->id }}" name="reportView" {{ $permissions[3]->role_permission_id ? 'checked' : '' }}>
                    <label class="form-check-label" for="reportView">
                      View
                    </label>
                  </div>
                </div>
              </div>
              <div class="tab-pane fade" id="list-setting" role="tabpanel" aria-labelledby="list-setting-list">
                <ul class="nav">
                  <li class="nav-item">
                    <a class="nav-link" id="setting-accessibility" data-toggle="list" href="#list-setting-accessibility" role="tab" aria-controls="setting">Accessibility</a>
                  </li>
                </ul>
                <div class="tab-pane fade p-3 active show" id="list-setting-accessibility" role="tabpanel" aria-labelledby="setting-accessibility">
                  <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" value="{{ $permissions[4]->id }}" name="settingView" {{ $permissions[4]->role_permission_id ? 'checked' : '' }}>
                    <label class="form-check-label" for="settingView">
                      View
                    </label>
                  </div>
                  <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" value="{{ $permissions[5]->id }}" name="scheduleView" {{ $permissions[5]->role_permission_id ? 'checked' : '' }}>
                    <label class="form-check-label" for="scheduleView">
                      Schedule Settings
                    </label>
                  </div>                                                
                  <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" value="{{ $permissions[6]->id }}" name="layerView" {{ $permissions[6]->role_permission_id ? 'checked' : '' }}>
                    <label class="form-check-label" for="layerView">
                      Layer Settings
                    </label>
                  </div>
                  <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" value="{{ $permissions[7]->id }}" name="roleView" {{ $permissions[7]->role_permission_id ? 'checked' : '' }}>
                    <label class="form-check-label" for="roleView">
                      Role Permission Settings
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
</form>
@endif

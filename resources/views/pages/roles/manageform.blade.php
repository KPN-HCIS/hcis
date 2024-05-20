@if ($roleId)
<form action="{{ route('roles.update') }}" method="POST">
  @csrf

  <input type="hidden" name="roleId" value="{{ $roleId }}">
  <div class="row">
    <div class="col-md-8">
      <div class="d-sm-flex align-items-right justify-content-end mb-4">
        <button type="submit" class="btn btn-primary px-4">Update</button>
      </div>
    </div>
  </div>
  {{-- @foreach ($restriction as $key => $value)
  <input type="text" value="{{ $value }}">
  @endforeach --}}
  @foreach ($roles as $role)
  @php
      // Decode the JSON string
      $restriction = json_decode($role->restriction, true);
  @endphp
  @endforeach
      
  <div class="row mb-3">
    <div class="col-md-8">
      <div class="form-group">
        <label for="roleName">Restrict Group Company (Keeping blank means no restrictions)</label>
        @if(is_array($restriction) && isset($restriction['group_company']))
            <select class="form-control select2" name="group_company[]" multiple="multiple">
              @foreach ($groupCompanies as $groupCompany)
                  <option value="{{ $groupCompany }}" {{ isset($restriction['group_company']) && in_array($groupCompany, $restriction['group_company']) ? 'selected' : '' }}>{{ $groupCompany }}</option>
              @endforeach
            </select>
        @endif
      </div>
    </div>
  </div>
  <div class="row mb-3">
    <div class="col-md-8">
      <div class="form-group">
        <label for="roleName">Restrict Company (Keeping blank means no restrictions)</label>
        <select class="form-control select2" name="contribution_level_code[]" multiple="multiple">
            @foreach ($companies as $company)
                <option value="{{ $company->contribution_level_code }}" {{ isset($restriction['contribution_level_code']) && in_array($company->contribution_level_code, $restriction['contribution_level_code']) ? 'selected' : '' }}>{{ $company->contribution_level.' ('.$company->contribution_level_code.')' }}</option>
            @endforeach
        </select>
      </div>
    </div>
  </div>
  <div class="row mb-3">
    <div class="col-md-8">
      <div class="form-group">
        <label for="roleName">Restrict Location (Keeping blank means no restrictions)</label>
          <select class="form-control select2" name="work_area_code[]" multiple="multiple">
            @foreach ($locations as $location)
                <option value="{{ $location->work_area }}" {{ isset($restriction['work_area_code']) && in_array($location->work_area, $restriction['work_area_code']) ? 'selected' : '' }}>{{ $location->area.' ('.$location->company_name.')' }}</option>
            @endforeach
          </select>
      </div>
    </div>
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
                    <input class="form-check-input" type="checkbox" value="{{ $permissions[0] }}" name="goalView" {{ isset($permissionNames[0]) ? 'checked' : '' }}>
                    <label class="form-check-label" for="goalView">
                      View
                    </label>
                  </div>
                  <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" value="{{ $permissions[1] }}" name="goalApproval" {{ isset($permissionNames[1]) ? 'checked' : '' }}>
                    <label class="form-check-label" for="goalApproval">
                      Initiate Approval
                    </label>
                  </div>                                                
                  <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" value="{{ $permissions[2] }}" name="goalSendback" {{ isset($permissionNames[2]) ? 'checked' : '' }}>
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
                    <input class="form-check-input" type="checkbox" value="{{ $permissions[3] }}" name="reportView" {{ isset($permissionNames[3]) ? 'checked' : '' }}>
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
                    <input class="form-check-input" type="checkbox" value="{{ $permissions[4] }}" name="settingView" {{ isset($permissionNames[4]) ? 'checked' : '' }}>
                    <label class="form-check-label" for="settingView">
                      View
                    </label>
                  </div>
                  <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" value="{{ $permissions[5] }}" name="scheduleView" {{ isset($permissionNames[5]) ? 'checked' : '' }}>
                    <label class="form-check-label" for="scheduleView">
                      Schedule Settings
                    </label>
                  </div>                                                
                  <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" value="{{ $permissions[6] }}" name="layerView" {{ isset($permissionNames[6]) ? 'checked' : '' }}>
                    <label class="form-check-label" for="layerView">
                      Layer Settings
                    </label>
                  </div>
                  <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" value="{{ $permissions[7] }}" name="roleView" {{ $permissionNames[7] ? 'checked' : '' }}>
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

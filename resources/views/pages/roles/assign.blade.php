@extends('pages.roles.app') <!-- Extend the main layout -->

@section('content')
  <div class="d-sm-flex align-items-end mb-2">
    <div class="d-sm-flex">
      <div class="form-group">
          <label for="report_type">Permission Group:</label>
          <select class="form-control" name="permission_name" onchange="getPermissionData(this.value)">
          <option value="">Select Permission Group</option>
          @foreach ($roles as $role)
            <option value="{{ $role->id }}">{{ $role->name }}</option>
          @endforeach
          </select>
      </div> 
    </div>
  </div>
@endsection
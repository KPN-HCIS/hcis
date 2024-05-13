@extends('pages.roles.app') <!-- Extend the main layout -->

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-2">
  <div class="form-group">
      <label for="role_name">Select Permission Role</label>
      <select name="" id="roleName" onchange="getPermissionData(this.value)" class="form-control">
        <option value="">Select</option>
        @foreach ($roles as $role)
          <option value="{{ $role->id }}">{{ $role->name }}</option>
        @endforeach
      </select>
  </div>
</div>
@endsection
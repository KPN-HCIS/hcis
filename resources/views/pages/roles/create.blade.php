@extends('pages.roles.app') <!-- Extend the main layout -->

@section('content')
  <form class="d-sm-flex align-items-center justify-content-between mb-4">
    <div class="form-group">
        <label for="role_name">Role Name</label>
        <input class="form-control" type="text" name="role_name" placeholder="Enter role name..">
    </div>
    <button class="btn btn-primary px-4">Create Role</button>
  </form>
@endsection
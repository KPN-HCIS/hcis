@if ($roleId)
<form action="{{ route('assign.user') }}" method="POST">
  @csrf
  <input type="hidden" name="role_id" value="{{ $roleId }}">
  <div class="row mb-2">
    <div class="col">Granted To:</div>
  </div>
  <div class="row mb-3">
    <div class="col">
      <select class="form-control select2" name="users_id[]" multiple="multiple">
        @foreach ($users as $user)
          @php
            $isSelected = false;
            foreach ($roles as $role) {
                if ($role->model_id == $user->id) {
                    $isSelected = true;
                    break;
                }
            }
          @endphp
          <option value="{{ $user->id }}" {{ $isSelected ? 'selected' : '' }}>{{ $user->name }}</option>
        @endforeach
      </select>
    </div>
  </div>
  <div class="row">
    <div class="col">
      <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3">Save</button>
    </div>
  </div>
</form>
@endif

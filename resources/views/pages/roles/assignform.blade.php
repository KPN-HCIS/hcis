@if ($roleId)
<div class="card">
  <div class="card-body">

    <form action="{{ route('assign.user') }}" method="POST">
      @csrf
      <input type="hidden" name="role_id" value="{{ $roleId }}">
      <div class="row mb-3">
        <div class="col">
          <label class="form-label" for="granted">Granted To:</label>
          <select class="form-control select2" id="granted" name="users_id[]" multiple="multiple">
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
        <div class="col-lg-auto text-end">
          <button type="submit" class="btn btn-primary rounded-pill px-3">Save</button>
        </div>
      </div>
    </form>
  </div>
</div>
@endif

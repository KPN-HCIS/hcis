@foreach ($roles->modelHasRole as $row)
<div class="row">
  <div class="col">
    <select class="form-control select2" name="location[]" multiple="multiple">
      <option value="AL">Alabama</option>
    </select>
  </div>
</div>
@endforeach

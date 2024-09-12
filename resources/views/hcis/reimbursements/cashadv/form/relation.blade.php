<script>
    var formCount = 1;

    function addMoreFormRelation(event) {
        event.preventDefault();
        if (formCount < 20) { // Assuming a maximum of 100 forms
            formCount++;
            document.getElementById(`form-container-e-relation-${formCount}`).style.display = 'block';
        }
    }

    function removeFormRelation(index, event) {
        event.preventDefault();
        if (formCount > 1) {
            let formContainer = document.getElementById(`form-container-e-relation-${index}`);

            formContainer.querySelectorAll('input[type="text"], input[type="date"]').forEach(input => {
                input.value = '';
            });

            formContainer.querySelectorAll('input[type="number"]').forEach(input => {
                input.value = 0;
            });

            formContainer.querySelectorAll('select').forEach(select => {
                select.selectedIndex = 0;
            });

            formContainer.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
                checkbox.checked = false;
            });

            document.querySelector(`#nominal_bt_penginapan_${formCount}`).value = 0;

            document.getElementById(`form-container-e-relation-${formCount}`).style.display = 'none';
            formCount--;
        }
    }

    function clearFormRelation(index, event) {
        event.preventDefault();
        if (formCount > 0) {
            let formContainer = document.getElementById(`form-container-e-relation-${index}`);

            formContainer.querySelectorAll('input[type="text"], input[type="date"]').forEach(input => {
                input.value = '';
            });

            formContainer.querySelectorAll('input[type="number"]').forEach(input => {
                input.value = 0;
            });

            formContainer.querySelectorAll('select').forEach(select => {
                select.selectedIndex = 0;
            });

            formContainer.querySelectorAll('textarea').forEach(textarea => {
                textarea.value = '';
            });

            formContainer.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
                checkbox.checked = false;
            });

            document.querySelector(`#nominal_bt_penginapan_${formCount}`).value = 0;
        }
    }
</script>

@for ($i = 1; $i <= 20; $i++)
    <div id="form-container-e-relation-{{ $i }}" class="card-body bg-light p-2 mb-3" style="{{ $i > 1 ? 'display: none;' : '' }} border-radius: 1%;">
        <div class="row">
            <!-- Penginapan Date -->
            <div class="col-md-12 mb-2">
                <label class="form-label">Relation Type</label>
                <div class="form-check">
                    <input class="form-check-input"
                        type="checkbox"
                        name="accommodation_e_relation[]"
                        id="accommodation_e_relation_{{$i}}"
                        value="accommodation">
                    <label class="form-check-label"
                        for="accommodation_e_relation_{{$i}}">Accommodation</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input"
                        name="transport_e_relation[]"
                        type="checkbox"
                        id="transport_e_relation_{{$i}}"
                        value="transport">
                    <label class="form-check-label"
                        for="transport_e_relation_{{$i}}">Transport</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input"
                        name="gift_e_relation[]" type="checkbox"
                        id="gift_e_relation_{{$i}}" value="gift">
                    <label class="form-check-label"
                        for="gift_e_relation_{{$i}}">Gift</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input"
                        name="fund_e_relation[]" type="checkbox"
                        id="fund_e_relation_{{$i}}" value="fund">
                    <label class="form-check-label"
                        for="fund_e_relation_{{$i}}">Fund</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input"
                        name="food_e_relation[]" type="checkbox"
                        id="food_e_relation_{{$i}}" value="food">
                    <label class="form-check-label"
                        for="food_e_relation_{{$i}}">Food/Beverages/Souvenir</label>
                </div>
            </div>
            <div class="col-md-4 mb-2">
                <label class="form-label"
                    for="name">Name</label>
                <input type="text" name="rname_e_relation[]"
                    id="rname_e_relation_{{$i}}" class="form-control">
            </div>
            <div class="col-md-4 mb-2">
                <label class="form-label"
                    for="position">Position</label>
                <input type="text"
                    name="rposition_e_relation[]"
                    id="rposition_e_relation_{{$i}}"
                    class="form-control">
            </div>
            <div class="col-md-4 mb-2">
                <label class="form-label"
                    for="company">Company</label>
                <input type="text" name="rcompany_e_relation[]"
                    id="rcompany_e_relation_{{$i}}"
                    class="form-control">
            </div>
            <div class="col-md-12">
                <label class="form-label"
                    for="purpose">Purpose</label>
                <textarea name="rpurpose_e_relation[]"
                    id="rpurpose_e_relation_{{$i}}"
                    class="form-control"></textarea>
            </div>
        </div>
        <br>
        <div class="row mt-3">
            <div class="d-flex justify-start w-100">
                @if ($i > 0)
                    <button class="btn btn-danger mr-2" style="margin-right: 10px" id="form-container-e-relation-{{ $i }}-cl" name="form-container-e-relation-{{ $i }}" value="Clear" onclick="clearFormRelation({{ $i }}, event)">Clear</button>
                @endif
                @if ($i > 1)
                    <button class="btn btn-warning mr-2" id="form-container-e-relation-{{ $i }}-no" name="form-container-e-relation-{{ $i }}" value="Tidak" onclick="removeFormRelation({{ $i }}, event)">Remove</button>
                @endif
            </div>
        </div>
    </div>
@endfor

<div class="mt-3">
    <button class="btn btn-primary" id="form-container-e-relation-{{ $i }}-yes" name="form-container-e-relation-{{ $i }}" value="Ya" onclick="addMoreFormRelation(event)">Add More</button>
</div>

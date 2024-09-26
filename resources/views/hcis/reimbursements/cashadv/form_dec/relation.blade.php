<script>
    var formCountRelation = 0;

    window.addEventListener('DOMContentLoaded', function() {
        formCountRelation = document.querySelectorAll('#form-container-relation > div').length;
        console.log("Form ada",formCountRelation);
    });

    function addMoreFormRelationDec(event) {
        event.preventDefault();
        formCountRelation++;

        const newForm = document.createElement("div");
        newForm.id = `form-container-e-relation-${formCountRelation}`;
        newForm.className = "card-body p-2 mb-3";
        newForm.style.backgroundColor = "#f8f8f8";
        newForm.innerHTML = `
            <p class="fs-4 text-primary" style="font-weight: bold; ">Relation Entertainment ${formCountRelation}</p>
            <div class="card-body bg-light p-2 mb-3">
                <p class="fs-5 text-primary" style="font-weight: bold;">Relation Declaration</p>
                <div class="row">
                    <!-- Relation Date -->
                    <div class="col-md-12 mb-2">
                        <label class="form-label">Relation Type</label>
                        <div class="form-check">
                            <input class="form-check-input"
                                type="checkbox"
                                name="accommodation_e_relation[]"
                                id="accommodation_e_relation_${formCountRelation}"
                                value="accommodation">
                            <label class="form-check-label"
                                for="accommodation_e_relation_${formCountRelation}">Accommodation</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input"
                                name="transport_e_relation[]"
                                type="checkbox"
                                id="transport_e_relation_${formCountRelation}"
                                value="transport">
                            <label class="form-check-label"
                                for="transport_e_relation_${formCountRelation}">Transport</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input"
                                name="gift_e_relation[]" type="checkbox"
                                id="gift_e_relation_${formCountRelation}" value="gift">
                            <label class="form-check-label"
                                for="gift_e_relation_${formCountRelation}">Gift</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input"
                                name="fund_e_relation[]" type="checkbox"
                                id="fund_e_relation_${formCountRelation}" value="fund">
                            <label class="form-check-label"
                                for="fund_e_relation_${formCountRelation}">Fund</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input"
                                name="food_e_relation[]" type="checkbox"
                                id="food_e_relation_${formCountRelation}" value="food">
                            <label class="form-check-label"
                                for="food_e_relation_${formCountRelation}">Food/Beverages/Souvenir</label>
                        </div>
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label"
                            for="name">Name</label>
                        <input type="text" name="rname_e_relation[]"
                            id="rname_e_relation_${formCountRelation}" class="form-control">
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label"
                            for="position">Position</label>
                        <input type="text"
                            name="rposition_e_relation[]"
                            id="rposition_e_relation_${formCountRelation}"
                            class="form-control">
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label"
                            for="company">Company</label>
                        <input type="text" name="rcompany_e_relation[]"
                            id="rcompany_e_relation_${formCountRelation}"
                            class="form-control">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label"
                            for="purpose">Purpose</label>
                        <textarea name="rpurpose_e_relation[]"
                            id="rpurpose_e_relation_${formCountRelation}"
                            class="form-control"></textarea>
                    </div>
                </div>
                <br>
                <div class="row mt-3">
                    <div class="d-flex justify-start w-100">
                        <button class="btn btn-danger mr-2" style="margin-right: 10px" onclick="clearFormRelation(${formCountRelation}, event)">Reset</button>
                        <button class="btn btn-warning mr-2" onclick="removeFormRelation(${formCountRelation}, event)">Delete</button>
                    </div>
                </div>
            </div>
        `;
        document.getElementById("form-container-relation").appendChild(newForm);
        updateCheckboxVisibility();
    }

    function addMoreFormRelationReq(event) {
        event.preventDefault();
        formCountRelation++;

        const newForm = document.createElement("div");
        newForm.id = `form-container-e-relation-${formCountRelation}`;
        newForm.className = "card-body p-2 mb-3";
        newForm.style.backgroundColor = "#f8f8f8";
        newForm.innerHTML = `
            <p class="fs-4 text-primary" style="font-weight: bold; ">Relation Entertainment ${formCountRelation}</p>
            <div class="card-body bg-light p-2 mb-3">
                <p class="fs-5 text-primary" style="font-weight: bold;">Relation Request</p>
                <div class="row">
                    <!-- Relation Date -->
                    <div class="col-md-12 mb-2">
                        <label class="form-label">Relation Type</label>
                        <div class="form-check">
                            <input class="form-check-input"
                                type="checkbox"
                                name="accommodation_e_relation[]"
                                id="accommodation_e_relation_${formCountRelation}"
                                value="accommodation">
                            <label class="form-check-label"
                                for="accommodation_e_relation_${formCountRelation}">Accommodation</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input"
                                name="transport_e_relation[]"
                                type="checkbox"
                                id="transport_e_relation_${formCountRelation}"
                                value="transport">
                            <label class="form-check-label"
                                for="transport_e_relation_${formCountRelation}">Transport</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input"
                                name="gift_e_relation[]" type="checkbox"
                                id="gift_e_relation_${formCountRelation}" value="gift">
                            <label class="form-check-label"
                                for="gift_e_relation_${formCountRelation}">Gift</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input"
                                name="fund_e_relation[]" type="checkbox"
                                id="fund_e_relation_${formCountRelation}" value="fund">
                            <label class="form-check-label"
                                for="fund_e_relation_${formCountRelation}">Fund</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input"
                                name="food_e_relation[]" type="checkbox"
                                id="food_e_relation_${formCountRelation}" value="food">
                            <label class="form-check-label"
                                for="food_e_relation_${formCountRelation}">Food/Beverages/Souvenir</label>
                        </div>
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label"
                            for="name">Name</label>
                        <input type="text" name="rname_e_relation[]"
                            id="rname_e_relation_${formCountRelation}" class="form-control">
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label"
                            for="position">Position</label>
                        <input type="text"
                            name="rposition_e_relation[]"
                            id="rposition_e_relation_${formCountRelation}"
                            class="form-control">
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label"
                            for="company">Company</label>
                        <input type="text" name="rcompany_e_relation[]"
                            id="rcompany_e_relation_${formCountRelation}"
                            class="form-control">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label"
                            for="purpose">Purpose</label>
                        <textarea name="rpurpose_e_relation[]"
                            id="rpurpose_e_relation_${formCountRelation}"
                            class="form-control"></textarea>
                    </div>
                </div>
                <br>
                <div class="row mt-3">
                    <div class="d-flex justify-start w-100">
                        <button class="btn btn-danger mr-2" style="margin-right: 10px" onclick="clearFormRelation(${formCountRelation}, event)">Reset</button>
                        <button class="btn btn-warning mr-2" onclick="removeFormRelation(${formCountRelation}, event)">Delete</button>
                    </div>
                </div>
            </div>
        `;
        document.getElementById("form-container-relation").appendChild(newForm);
        updateCheckboxVisibility();
    }

    $('.btn-warning').click(function(event) {
        event.preventDefault();
        var index = $(this).closest('.card-body').index() + 1;
        removeFormRelation(index, event);
    });

    function removeFormRelation(index, event) {
        event.preventDefault();
        if (formCountRelation > 0) {
            const formContainer = document.getElementById(`form-container-e-relation-${index}`);
            if (formContainer) {
                $(`#form-container-e-relation-${index}`).remove();
                formCountRelation--;
            }
        }
    }

    function clearFormRelation(index, event) {
        event.preventDefault();
        let form = document.getElementById(`form-container-e-relation-${index}`);

        form.querySelectorAll('input[type="text"], textarea').forEach(input => {
            input.value = '';
        });

        form.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
            checkbox.checked = false;
        });
    }
</script>

@if (!empty($detailCA['relation_e']) && $detailCA['relation_e'][0]['name'] !== null)
    <div id="form-container-relation">
        @foreach($detailCA['relation_e'] as $index => $relation)
            <div id="form-container-e-relation-{{ $loop->index + 1 }}" class="p-2 mb-4 rounded-3" style="background-color: #f8f8f8">
                <p class="fs-4 text-primary" style="font-weight: bold; ">Relation Entertainment {{ $loop->index + 1 }}</p>
                <div id="form-container-e-relation-req-{{ $loop->index + 1 }}" class="card-body bg-light p-2 mb-3" style="border-radius: 1%;">
                    <div class="row">
                        <p class="fs-5 text-primary" style="font-weight: bold;">Relation Entertainment Request</p>
                        <div class="col-md-6">
                            <table width="100%">
                                <tr>
                                    <th width="40%">Relation Type</th>
                                    <td class="block">:</td>
                                    <td>
                                        @php
                                            $relationTypes = [];
                                            $typeMap = [
                                                'Food' => 'Food/Beverages/Souvenir',
                                                'Gift' => 'Gift',
                                                'Transport' => 'Transport',
                                                'Accommodation' => 'Accommodation',
                                                'Fund' => 'Fund',
                                            ];

                                            // Mengumpulkan semua tipe relasi yang berstatus true
                                            foreach($relation['relation_type'] as $type => $status) {
                                                if ($status && isset($typeMap[$type])) {
                                                    $relationTypes[] = $typeMap[$type]; // Menggunakan pemetaan untuk mendapatkan deskripsi
                                                }
                                            }
                                        @endphp

                                        {{ implode(', ', $relationTypes) }} {{-- Menggabungkan tipe relasi yang relevan menjadi string --}}
                                    </td>
                                </tr>
                                <tr>
                                    <th>Name</th>
                                    <td class="block">:</td>
                                    <td>{{$relation['name']}}</td>
                                </tr>
                                <tr>
                                    <th>Position</th>
                                    <td class="block">:</td>
                                    <td>{{$relation['position']}}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table width="100%">
                                <tr>
                                    <th>Company</th>
                                    <td class="block">:</td>
                                    <td>{{$relation['company']}}</td>
                                </tr>
                                <tr>
                                    <th>Purpose</th>
                                    <td class="block">:</td>
                                    <td>{{$relation['purpose']}}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div id="form-container-e-relation-dec-{{ $loop->index + 1 }}" class="card-body bg-light p-2 mb-3" style="border-radius: 1%;">
                    <p class="fs-5 text-primary" style="font-weight: bold;">Relation Entertainment Declaration</p>
                    @if (isset($declareCA['relation_e'][$index]))
                        @php
                            $relation_dec = $declareCA['relation_e'][$index];
                        @endphp
                        <div class="row">
                            <!-- Relation Date -->
                            <div class="col-md-12 mb-2">
                                <label class="form-label">Relation Type</label>
                                <div class="form-check">
                                    <input class="form-check-input"
                                        type="checkbox"
                                        name="accommodation_e_relation[]"
                                        id="accommodation_e_relation_{{ $loop->index + 1 }}"
                                        value="accommodation" {{ isset($relation_dec['relation_type']['Accommodation']) && $relation_dec['relation_type']['Accommodation'] ? 'checked' : '' }}>
                                    <label class="form-check-label"
                                        for="accommodation_e_relation_{{ $loop->index + 1 }}">Accommodation</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input"
                                        name="transport_e_relation[]"
                                        type="checkbox"
                                        id="transport_e_relation_{{ $loop->index + 1 }}"
                                        value="transport" {{ isset($relation_dec['relation_type']['Transport']) && $relation_dec['relation_type']['Transport'] ? 'checked' : '' }}>
                                    <label class="form-check-label"
                                        for="transport_e_relation_{{ $loop->index + 1 }}">Transport</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input"
                                        name="gift_e_relation[]" type="checkbox"
                                        id="gift_e_relation_{{ $loop->index + 1 }}"
                                        value="gift" {{ isset($relation_dec['relation_type']['Gift']) && $relation_dec['relation_type']['Gift'] ? 'checked' : '' }}>
                                    <label class="form-check-label"
                                        for="gift_e_relation_{{ $loop->index + 1 }}">Gift</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input"
                                        name="fund_e_relation[]" type="checkbox"
                                        id="fund_e_relation_{{ $loop->index + 1 }}"
                                        value="fund" {{ isset($relation_dec['relation_type']['Fund']) && $relation_dec['relation_type']['Fund'] ? 'checked' : '' }}>
                                    <label class="form-check-label"
                                        for="fund_e_relation_{{ $loop->index + 1 }}">Fund</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input"
                                        name="food_e_relation[]" type="checkbox"
                                        id="food_e_relation_{{ $loop->index + 1 }}"
                                        value="food" {{ isset($relation_dec['relation_type']['Food']) && $relation_dec['relation_type']['Food'] ? 'checked' : '' }}>
                                    <label class="form-check-label"
                                        for="food_e_relation_{{ $loop->index + 1 }}">Food/Beverages/Souvenir</label>
                                </div>
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="form-label"
                                    for="name">Name</label>
                                <input type="text"
                                    name="rname_e_relation[]"
                                    id="rname_e_relation_{{ $loop->index + 1 }}"
                                    value="{{ $relation_dec['name'] }}"
                                    class="form-control">
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="form-label"
                                    for="position">Position</label>
                                <input type="text"
                                    name="rposition_e_relation[]"
                                    id="rposition_e_relation_{{ $loop->index + 1 }}"
                                    value="{{ $relation_dec['position'] }}"
                                    class="form-control">
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="form-label"
                                    for="company">Company</label>
                                <input type="text"
                                    name="rcompany_e_relation[]"
                                    id="rcompany_e_relation_{{ $loop->index + 1 }}"
                                    value="{{ $relation_dec['company'] }}"
                                    class="form-control">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label"
                                    for="purpose">Purpose</label>
                                <textarea name="rpurpose_e_relation[]"
                                    id="rpurpose_e_relation_{{ $loop->index + 1 }}"
                                    class="form-control">{{ $relation_dec['purpose'] }}</textarea>
                            </div>
                        </div>
                        <br>
                        <div class="row mt-3">
                            <div class="d-flex justify-start w-100">
                                <button class="btn btn-danger mr-2" style="margin-right: 10px" onclick="clearFormRelation({{ $loop->index + 1 }}, event)">Reset</button>
                                {{-- <button class="btn btn-warning mr-2" onclick="removeFormRelation({{ $loop->index + 1 }}, event)">Delete</button> --}}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
        @foreach ($declareCA['relation_e'] as $index => $relation_dec)
            @if (!isset($detailCA['relation_e'][$index]))
                <div id="form-container-e-relation-{{ $loop->index + 1 }}" class="card-body bg-light p-2 mb-3" style="border-radius: 1%;">
                    <p class="fs-4 text-primary" style="font-weight: bold; ">Relation Entertainment {{ $loop->index + 1 }}</p>
                    <div class="row">
                        <!-- Relation Date -->
                        <div class="col-md-12 mb-2">
                            <label class="form-label">Relation Type</label>
                            <div class="form-check">
                                <input class="form-check-input"
                                    type="checkbox"
                                    name="accommodation_e_relation[]"
                                    id="accommodation_e_relation_1"
                                    value="accommodation" {{ isset($relation_dec['relation_type']['Accommodation']) && $relation_dec['relation_type']['Accommodation'] ? 'checked' : '' }}>
                                <label class="form-check-label"
                                    for="accommodation_e_relation_1">Accommodation</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input"
                                    name="transport_e_relation[]"
                                    type="checkbox"
                                    id="transport_e_relation_1"
                                    value="transport" {{ isset($relation_dec['relation_type']['Transport']) && $relation_dec['relation_type']['Transport'] ? 'checked' : '' }}>
                                <label class="form-check-label"
                                    for="transport_e_relation_1">Transport</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input"
                                    name="gift_e_relation[]" type="checkbox"
                                    id="gift_e_relation_1"
                                    value="gift" {{ isset($relation_dec['relation_type']['Gift']) && $relation_dec['relation_type']['Gift'] ? 'checked' : '' }}>
                                <label class="form-check-label"
                                    for="gift_e_relation_1">Gift</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input"
                                    name="fund_e_relation[]" type="checkbox"
                                    id="fund_e_relation_1"
                                    value="fund" {{ isset($relation_dec['relation_type']['Fund']) && $relation_dec['relation_type']['Fund'] ? 'checked' : '' }}>
                                <label class="form-check-label"
                                    for="fund_e_relation_1">Fund</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input"
                                    name="food_e_relation[]" type="checkbox"
                                    id="food_e_relation_1"
                                    value="food" {{ isset($relation_dec['relation_type']['Food']) && $relation_dec['relation_type']['Food'] ? 'checked' : '' }}>
                                <label class="form-check-label"
                                    for="food_e_relation_1">Food/Beverages/Souvenir</label>
                            </div>
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="form-label"
                                for="name">Name</label>
                            <input type="text"
                                name="rname_e_relation[]"
                                id="rname_e_relation_1"
                                value="{{ $relation_dec['name'] }}"
                                class="form-control">
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="form-label"
                                for="position">Position</label>
                            <input type="text"
                                name="rposition_e_relation[]"
                                id="rposition_e_relation_1"
                                value="{{ $relation_dec['position'] }}"
                                class="form-control">
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="form-label"
                                for="company">Company</label>
                            <input type="text"
                                name="rcompany_e_relation[]"
                                id="rcompany_e_relation_1"
                                value="{{ $relation_dec['company'] }}"
                                class="form-control">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label"
                                for="purpose">Purpose</label>
                            <textarea name="rpurpose_e_relation[]"
                                id="rpurpose_e_relation_1"
                                class="form-control">{{ $relation_dec['purpose'] }}</textarea>
                        </div>
                    </div>
                    <br>
                    <div class="row mt-3">
                        <div class="d-flex justify-start w-100">
                            <button class="btn btn-danger mr-2" style="margin-right: 10px" onclick="clearFormRelation({{ $loop->index + 1 }}, event)">Reset</button>
                            <button class="btn btn-warning mr-2" onclick="removeFormRelation({{ $loop->index + 1 }}, event)">Delete</button>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    </div>

    <div class="mt-3">
        <button class="btn btn-primary" id="addMoreButtonRelation" onclick="addMoreFormRelationDec(event)">Add More</button>
    </div>
@elseif (!empty($declareCA['relation_e']) && $declareCA['relation_e'][0]['name'] !== null)
    <div id="form-container-relation">
        @foreach ($declareCA['relation_e'] as $index => $relation_dec)
            @if (!isset($detailCA['relation_e'][$index]))
                <div id="form-container-e-relation-{{ $loop->index + 1 }}" class="card-body bg-light p-2 mb-3" style="border-radius: 1%;">
                    <p class="fs-4 text-primary" style="font-weight: bold; ">Relation Entertainment {{ $loop->index + 1 }}</p>
                    <div class="row">
                        <!-- Relation Date -->
                        <div class="col-md-12 mb-2">
                            <label class="form-label">Relation Type</label>
                            <div class="form-check">
                                <input class="form-check-input"
                                    type="checkbox"
                                    name="accommodation_e_relation[]"
                                    id="accommodation_e_relation_1"
                                    value="accommodation" {{ isset($relation_dec['relation_type']['Accommodation']) && $relation_dec['relation_type']['Accommodation'] ? 'checked' : '' }}>
                                <label class="form-check-label"
                                    for="accommodation_e_relation_1">Accommodation</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input"
                                    name="transport_e_relation[]"
                                    type="checkbox"
                                    id="transport_e_relation_1"
                                    value="transport" {{ isset($relation_dec['relation_type']['Transport']) && $relation_dec['relation_type']['Transport'] ? 'checked' : '' }}>
                                <label class="form-check-label"
                                    for="transport_e_relation_1">Transport</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input"
                                    name="gift_e_relation[]" type="checkbox"
                                    id="gift_e_relation_1"
                                    value="gift" {{ isset($relation_dec['relation_type']['Gift']) && $relation_dec['relation_type']['Gift'] ? 'checked' : '' }}>
                                <label class="form-check-label"
                                    for="gift_e_relation_1">Gift</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input"
                                    name="fund_e_relation[]" type="checkbox"
                                    id="fund_e_relation_1"
                                    value="fund" {{ isset($relation_dec['relation_type']['Fund']) && $relation_dec['relation_type']['Fund'] ? 'checked' : '' }}>
                                <label class="form-check-label"
                                    for="fund_e_relation_1">Fund</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input"
                                    name="food_e_relation[]" type="checkbox"
                                    id="food_e_relation_1"
                                    value="food" {{ isset($relation_dec['relation_type']['Food']) && $relation_dec['relation_type']['Food'] ? 'checked' : '' }}>
                                <label class="form-check-label"
                                    for="food_e_relation_1">Food/Beverages/Souvenir</label>
                            </div>
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="form-label"
                                for="name">Name</label>
                            <input type="text"
                                name="rname_e_relation[]"
                                id="rname_e_relation_1"
                                value="{{ $relation_dec['name'] }}"
                                class="form-control">
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="form-label"
                                for="position">Position</label>
                            <input type="text"
                                name="rposition_e_relation[]"
                                id="rposition_e_relation_1"
                                value="{{ $relation_dec['position'] }}"
                                class="form-control">
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="form-label"
                                for="company">Company</label>
                            <input type="text"
                                name="rcompany_e_relation[]"
                                id="rcompany_e_relation_1"
                                value="{{ $relation_dec['company'] }}"
                                class="form-control">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label"
                                for="purpose">Purpose</label>
                            <textarea name="rpurpose_e_relation[]"
                                id="rpurpose_e_relation_1"
                                class="form-control">{{ $relation_dec['purpose'] }}</textarea>
                        </div>
                    </div>
                    <br>
                    <div class="row mt-3">
                        <div class="d-flex justify-start w-100">
                            <button class="btn btn-danger mr-2" style="margin-right: 10px" onclick="clearFormRelation({{ $loop->index + 1 }}, event)">Reset</button>
                            <button class="btn btn-warning mr-2" onclick="removeFormRelation({{ $loop->index + 1 }}, event)">Delete</button>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    </div>

    <div class="mt-3">
        <button class="btn btn-primary" id="addMoreButtonRelation" onclick="addMoreFormRelationDec(event)">Add More</button>
    </div>
@else
    <div id="form-container-relation">
        <div id="form-container-e-relation-1" class="card-body p-2 mb-3" style="background-color: #f8f8f8">
            <p class="fs-4 text-primary" style="font-weight: bold; ">Relation Entertainment 1</p>
            <div class="card-body bg-light p-2 mb-3">
                <p class="fs-5 text-primary" style="font-weight: bold;">Relation Entertainment Declaration</p>
                <div class="row">
                    <!-- Relation Date -->
                    <div class="col-md-12 mb-2">
                        <label class="form-label">Relation Type</label>
                        <div class="form-check">
                            <input class="form-check-input"
                                type="checkbox"
                                name="accommodation_e_relation[]"
                                id="accommodation_e_relation_1"
                                value="accommodation">
                            <label class="form-check-label"
                                for="accommodation_e_relation_1">Accommodation</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input"
                                name="transport_e_relation[]"
                                type="checkbox"
                                id="transport_e_relation_1"
                                value="transport">
                            <label class="form-check-label"
                                for="transport_e_relation_1">Transport</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input"
                                name="gift_e_relation[]" type="checkbox"
                                id="gift_e_relation_1" value="gift">
                            <label class="form-check-label"
                                for="gift_e_relation_1">Gift</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input"
                                name="fund_e_relation[]" type="checkbox"
                                id="fund_e_relation_1" value="fund">
                            <label class="form-check-label"
                                for="fund_e_relation_1">Fund</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input"
                                name="food_e_relation[]" type="checkbox"
                                id="food_e_relation_1" value="food">
                            <label class="form-check-label"
                                for="food_e_relation_1">Food/Beverages/Souvenir</label>
                        </div>
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label"
                            for="name">Name</label>
                        <input type="text" name="rname_e_relation[]"
                            id="rname_e_relation_1" class="form-control">
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label"
                            for="position">Position</label>
                        <input type="text"
                            name="rposition_e_relation[]"
                            id="rposition_e_relation_1"
                            class="form-control">
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label"
                            for="company">Company</label>
                        <input type="text" name="rcompany_e_relation[]"
                            id="rcompany_e_relation_1"
                            class="form-control">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label"
                            for="purpose">Purpose</label>
                        <textarea name="rpurpose_e_relation[]"
                            id="rpurpose_e_relation_1"
                            class="form-control"></textarea>
                    </div>
                </div>
                <br>
                <div class="row mt-3">
                    <div class="d-flex justify-start w-100">
                        <button class="btn btn-danger mr-2" style="margin-right: 10px" onclick="clearFormRelation(1, event)">Reset</button>
                        <button class="btn btn-warning mr-2" onclick="removeFormRelation(1, event)">Delete</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-3">
        <button class="btn btn-primary" id="addMoreButtonRelation" onclick="addMoreFormRelationDec(event)">Add More</button>
    </div>
@endif

<script>
    var formCount = 1;

    function addMoreFormRelation(event) {
        event.preventDefault();
        formCount++;

        const newForm = document.createElement("div");
        newForm.id = `form-container-e-relation-${formCount}`;
        newForm.className = "card-body bg-light p-2 mb-3";
        newForm.innerHTML = `
            <div class="row">
                <!-- Relation Date -->
                <div class="col-md-12 mb-2">
                    <label class="form-label">Relation Type</label>
                    <div class="form-check">
                        <input class="form-check-input"
                            type="checkbox"
                            name="accommodation_e_relation[]"
                            id="accommodation_e_relation_${formCount}"
                            value="accommodation">
                        <label class="form-check-label"
                            for="accommodation_e_relation_${formCount}">Accommodation</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input"
                            name="transport_e_relation[]"
                            type="checkbox"
                            id="transport_e_relation_${formCount}"
                            value="transport">
                        <label class="form-check-label"
                            for="transport_e_relation_${formCount}">Transport</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input"
                            name="gift_e_relation[]" type="checkbox"
                            id="gift_e_relation_${formCount}" value="gift">
                        <label class="form-check-label"
                            for="gift_e_relation_${formCount}">Gift</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input"
                            name="fund_e_relation[]" type="checkbox"
                            id="fund_e_relation_${formCount}" value="fund">
                        <label class="form-check-label"
                            for="fund_e_relation_${formCount}">Fund</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input"
                            name="food_e_relation[]" type="checkbox"
                            id="food_e_relation_${formCount}" value="food">
                        <label class="form-check-label"
                            for="food_e_relation_${formCount}">Food/Beverages/Souvenir</label>
                    </div>
                </div>
                <div class="col-md-4 mb-2">
                    <label class="form-label"
                        for="name">Name</label>
                    <input type="text" name="rname_e_relation[]"
                        id="rname_e_relation_${formCount}" class="form-control">
                </div>
                <div class="col-md-4 mb-2">
                    <label class="form-label"
                        for="position">Position</label>
                    <input type="text"
                        name="rposition_e_relation[]"
                        id="rposition_e_relation_${formCount}"
                        class="form-control">
                </div>
                <div class="col-md-4 mb-2">
                    <label class="form-label"
                        for="company">Company</label>
                    <input type="text" name="rcompany_e_relation[]"
                        id="rcompany_e_relation_${formCount}"
                        class="form-control">
                </div>
                <div class="col-md-12">
                    <label class="form-label"
                        for="purpose">Purpose</label>
                    <textarea name="rpurpose_e_relation[]"
                        id="rpurpose_e_relation_${formCount}"
                        class="form-control"></textarea>
                </div>
            </div>
            <br>
            <div class="row mt-3">
                <div class="d-flex justify-start w-100">
                    <button class="btn btn-danger mr-2" style="margin-right: 10px" onclick="clearFormRelation(${formCount}, event)">Clear</button>
                    <button class="btn btn-warning mr-2" onclick="removeFormRelation(${formCount}, event)">Remove</button>
                </div>
            </div>
        `;
        document.getElementById("form-container-relation").appendChild(newForm);
        // Call updateCheckboxVisibility after adding form
        updateCheckboxVisibility();
    }

    function removeFormRelation(index, event) {
        event.preventDefault();
        if (formCount > 0) {
            let formContainer = document.getElementById(`form-container-e-relation-${index}`);
            formContainer.remove();
            formCount--;
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

<div id="form-container-relation">
    <div id="form-container-e-relation-1" class="card-body bg-light p-2 mb-3" style="border-radius: 1%;">
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
                <button class="btn btn-danger mr-2" style="margin-right: 10px" onclick="clearFormRelation(1, event)">Clear</button>
                <button class="btn btn-warning mr-2" onclick="removeFormRelation(1, event)">Remove</button>
            </div>
        </div>
    </div>
</div>

<div class="mt-3">
    <button class="btn btn-primary" id="addMoreButtonRelation" onclick="addMoreFormRelation(event)">Add More</button>
</div>

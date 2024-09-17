<script>
    var formCount = 1;

    function addMoreFormDetail(event) {
        event.preventDefault();
        formCount++;

        const newForm = document.createElement("div");
        newForm.id = `form-container-e-detail-${formCount}`;
        newForm.className = "card-body bg-light p-2 mb-3";
        newForm.innerHTML = `
            <div class="row">
                <div class="col-md-6 mb-2">
                    <label class="form-label">Entertainment Type</label>
                    <select name="enter_type_e_detail[]" id="enter_type_e_detail_${formCount}" class="form-select">
                        <option value="">-</option>
                        <option value="food">Food/Beverages/Souvenir</option>
                        <option value="transport">Transport</option>
                        <option value="accommodation">Accommodation</option>
                        <option value="gift">Gift</option>
                        <option value="fund">Fund</option>
                    </select>
                </div>
                <div class="col-md-6 mb-2">
                    <label class="form-label">Amount</label>
                    <div class="input-group">
                        <div class="input-group-append">
                            <span class="input-group-text">Rp</span>
                        </div>
                        <input class="form-control" name="nominal_e_detail[]"
                            id="nominal_e_detail_${formCount}"
                            type="text" min="0" value="0"
                            onfocus="this.value = this.value === '0' ? '' : this.value;"
                            oninput="formatInput(this)"
                            onblur="formatOnBlur(this)">
                    </div>
                </div>
                <div class="col-md-12 mb-2">
                    <label class="form-label">Entertainment Fee Detail</label>
                    <textarea name="enter_fee_e_detail[]" class="form-control"></textarea>
                </div>
            </div>
            <br>
            <div class="row mt-3">
                <div class="d-flex justify-start w-100">
                    <button class="btn btn-danger mr-2" style="margin-right: 10px" onclick="clearFormDetail(${formCount}, event)">Clear</button>
                    <button class="btn btn-warning mr-2" onclick="removeFormDetail(${formCount}, event)">Remove</button>
                </div>
            </div>
        `;

        document.getElementById("form-container-detail").appendChild(newForm);

        newForm.querySelector('input[name="nominal_e_detail[]"]').addEventListener('input', function() {
            formatInput(this);
            calculateTotalNominalEDetail();
        });

        calculateTotalNominalEDetail();
    }

    function removeFormDetail(index, event) {
        event.preventDefault();
        if (formCount > 0) {
            let nominalValue = cleanNumber(document.querySelector(`#nominal_e_detail_${index}`).value);
            let totalCA = cleanNumber(document.querySelector('input[name="totalca"]').value) || 0;
            totalCA -= nominalValue;
            document.querySelector('input[name="total_e_detail"]').value = formatNumber(totalCA);
            document.querySelector('input[name="totalca"]').value = formatNumber(totalCA);

            // Hide the form to be removed
            let formContainer = document.getElementById(`form-container-e-detail-${index}`);
            formContainer.remove();
            formCount--;

            updateCheckboxVisibility();
        }
    }

    function clearFormDetail(index, event) {
        event.preventDefault();
        if (formCount > 0) {
            let nominalValue = cleanNumber(document.querySelector(`#nominal_e_detail_${index}`).value);
            let totalCA = cleanNumber(document.querySelector('input[name="totalca"]').value) || 0;
            totalCA -= nominalValue;
            document.querySelector('input[name="total_e_detail"]').value = formatNumber(totalCA);
            document.querySelector('input[name="totalca"]').value = formatNumber(totalCA);

            let formContainer = document.getElementById(`form-container-e-detail-${index}`);

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

            document.querySelector(`#nominal_e_detail_${index}`).value = 0;

            // Call this function to update visibility of relation fields
            updateCheckboxVisibility();
        }
    }

    function updateCheckboxVisibility() {
        const selectedOptions = Array.from(document.querySelectorAll('select[name="enter_type_e_detail[]"]'))
            .map(select => select.value)
            .filter(value => value !== "");

        for (let i = 1; i <= formCount; i++) {
            const formContainerERelation = document.getElementById(`form-container-e-relation-${i}`);
            if (!formContainerERelation) continue; // Skip if the form does not exist

            formContainerERelation.querySelectorAll('.form-check').forEach(checkDiv => {
                const checkbox = checkDiv.querySelector('input.form-check-input');
                const checkboxValue = checkbox.value.toLowerCase().replace(/\s/g, "_");
                checkDiv.style.display = selectedOptions.includes(checkboxValue) ? 'block' : 'none';
            });
        }
    }


    document.addEventListener('DOMContentLoaded', function() {
        function formatInput(input) {
            let value = input.value.replace(/\./g, '');
            value = parseFloat(value);
            if (!isNaN(value)) {
                input.value = formatNumber(Math.floor(value));
            } else {
                input.value = formatNumber(0);
            }
            calculateTotalNominalEDetail();
        }

        // Function to calculate the total nominal value for EDetail
        function calculateTotalNominalEDetail() {
            let total = 0;
            document.querySelectorAll('input[name="nominal_e_detail[]"]').forEach(input => {
                total += parseNumber(input.value);
            });
            document.querySelector('input[name="total_e_detail"]').value = formatNumber(total);
            document.getElementById('totalca').value = formatNumber(total);
        }

        document.querySelectorAll('input[name="nominal_e_detail[]"]').forEach(input => {
            input.addEventListener('input', function() {
                formatInput(this);
                calculateTotalNominalEDetail(); // Pastikan kita menghitung total di sini
            });
        });

        // Call the function after the existing select elements are processed
        document.querySelectorAll('select[name="enter_type_e_detail[]"]').forEach(select => {
            select.addEventListener('change', updateCheckboxVisibility);
        });  

        calculateTotalNominalEDetail();
        updateCheckboxVisibility();
    });
</script>

<div id="form-container-detail">
    <div id="form-container-e-detail-1" class="card-body bg-light p-2 mb-3" style="border-radius: 1%;">
        <div class="row">
            <div class="col-md-6 mb-2">
                <label class="form-label">Entertainment Type</label>
                <select name="enter_type_e_detail[]" id="enter_type_e_detail_1" class="form-select">
                    <option value="">-</option>
                    <option value="food">Food/Beverages/Souvenir</option>
                    <option value="transport">Transport</option>
                    <option value="accommodation">Accommodation</option>
                    <option value="gift">Gift</option>
                    <option value="fund">Fund</option>
                </select>
            </div>
            <div class="col-md-6 mb-2">
                <label class="form-label">Amount</label>
                <div class="input-group">
                    <div class="input-group-append">
                        <span class="input-group-text">Rp</span>
                    </div>
                    <input class="form-control" name="nominal_e_detail[]"
                        id="nominal_e_detail_1"
                        type="text" min="0" value="0"
                        onfocus="this.value = this.value === '0' ? '' : this.value;"
                        oninput="formatInput(this)"
                        onblur="formatOnBlur(this)">
                </div>
            </div>
            <div class="col-md-12">
                <label class="form-label">Entertainment Fee Detail</label>
                <textarea name="enter_fee_e_detail[]" class="form-control"></textarea>
            </div>
        </div>
        <br>
        <div class="row mt-3">
            <div class="d-flex justify-start w-100">
                <button class="btn btn-danger mr-2" style="margin-right: 10px" onclick="clearFormDetail(1, event)">Clear</button>
                <button class="btn btn-warning mr-2" onclick="removeFormDetail(1, event)">Remove</button>
            </div>
        </div>
    </div>
</div>

<div class="mt-3">
    <button class="btn btn-primary" id="addMoreButtonDetail" onclick="addMoreFormDetail(event)">Add More</button>
</div>

<div class="mt-2">
    <label class="form-label">Total Entertain</label>
    <div class="input-group">
        <div class="input-group-append">
            <span class="input-group-text">Rp</span>
        </div>
        <input class="form-control bg-light"
            name="total_e_detail" id="total_e_detail"
            type="text" min="0" value="0"
            readonly>
    </div>
</div>

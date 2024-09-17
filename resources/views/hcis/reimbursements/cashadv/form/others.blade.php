<script>
    var formCount = 1;

    function addMoreFormLainnya(event) {
        event.preventDefault();
        formCount++;
        const newForm = document.createElement("div");
        newForm.id = `form-container-bt-lainnya-${formCount}`;
        newForm.className = "card-body bg-light p-2 mb-3";
        newForm.innerHTML = `
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <label class="form-label">Date</label>
                        <input type="date" name="tanggal_bt_lainnya[]" class="form-control" placeholder="mm/dd/yyyy">
                    </div>
                    <div class="col-md-6 mb-2">
                        <label class="form-label">Amount</label>
                        <div class="input-group mb-3">
                            <div class="input-group-append">
                                <span class="input-group-text">Rp</span>
                            </div>
                            <input class="form-control" name="nominal_bt_lainnya[]" id="nominal_bt_lainnya_${formCount}" type="text" min="0" value="0" onfocus="this.value = this.value === '0' ? '' : this.value;" oninput="formatInput(this)" onblur="formatOnBlur(this)">
                        </div>
                    </div>
                    <div class="col-md-12 mb-2">
                        <div class="mb-2">
                            <label class="form-label">Information</label>
                            <textarea name="keterangan_bt_lainnya[]" class="form-control"></textarea>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="d-flex justify-start w-100">
                        <button class="btn btn-danger mr-2" style="margin-right: 10px" onclick="clearFormLainnya(${formCount}, event)">Clear</button>
                        <button class="btn btn-warning mr-2" onclick="removeFormLainnya(${formCount}, event)">Remove</button>
                    </div>
                </div>
            `;
        document.getElementById("form-container-lainnya").appendChild(newForm);
    }

    // function removeFormLainnya(index, event) {
    //     event.preventDefault();
    //     if (formCount > 0) {
    //         let nominalValue = cleanNumber(document.querySelector(`#nominal_bt_lainnya_${formCount}`).value);

    //         let total = cleanNumber(document.querySelector('input[name="total_bt_lainnya"]').value);
    //         total -= nominalValue;
    //         document.querySelector('input[name="total_bt_lainnya"]').value = formatNumber(total);

    //         let formContainer = document.getElementById(`form-container-bt-lainnya-${index}`);
    //         formContainer.remove();
    //         formCount--;

    //         document.querySelector(`#nominal_bt_lainnya_${formCount}`).value = 0;
    //         calculateTotalNominalBTTotal();
    //     }
    // }

    function removeFormLainnya(index, event) {
        event.preventDefault();
        let nominalValue = cleanNumber(
            document.querySelector(`#nominal_bt_lainnya_${index}`).value
        );
        let total = cleanNumber(
            document.querySelector('input[name="total_bt_lainnya"]').value
        );
        total -= nominalValue;
        document.querySelector('input[name="total_bt_lainnya"]').value =
            formatNumber(total);

        // Clear the inputs
        const formContainer = document.getElementById(
            `form-container-bt-lainnya-${index}`
        );
        formContainer
            .querySelectorAll('input[type="text"], input[type="date"]')
            .forEach((input) => {
                input.value = "";
            });
        formContainer.querySelector("textarea").value = "";

        formContainer.remove();
        formCount--;

        // Reset nilai untuk nominal BT Lainnya
        document.querySelector(`#nominal_bt_lainnya_${index}`).value = 0;
        calculateTotalNominalBTTotal();
    }

    function clearFormLainnya(index, event) {
        event.preventDefault();
        let nominalValue = cleanNumber(
            document.querySelector(`#nominal_bt_lainnya_${index}`).value
        );
        let total = cleanNumber(
            document.querySelector('input[name="total_bt_lainnya"]').value
        );
        total -= nominalValue;
        document.querySelector('input[name="total_bt_lainnya"]').value =
            formatNumber(total);

        // Clear the inputs
        const formContainer = document.getElementById(
            `form-container-bt-lainnya-${index}`
        );
        formContainer
            .querySelectorAll('input[type="text"], input[type="date"]')
            .forEach((input) => {
                input.value = "";
            });
        formContainer.querySelector("textarea").value = "";

        // Reset nilai untuk nominal BT Lainnya
        document.querySelector(`#nominal_bt_lainnya_${index}`).value = 0;
        calculateTotalNominalBTTotal();
    }

    function calculateTotalNominalBTLainnya() {
        let total = 0;
        document.querySelectorAll('input[name="nominal_bt_lainnya[]"]').forEach(input => {
            total += cleanNumber(input.value);
        });
        document.getElementById("total_bt_lainnya").value = formatNumber(total);
    }

    function onNominalChange() {
        calculateTotalNominalBTLainnya();
    }

</script>

<div id="form-container-lainnya">
    <div id="form-container-bt-lainnya-1" class="card-body bg-light p-2 mb-3">
        <div class="row">
            <!-- Lainnya Date -->
            <div class="col-md-6 mb-2">
                <label class="form-label">Date</label>
                <input type="date" name="tanggal_bt_lainnya[]" class="form-control" placeholder="mm/dd/yyyy">
            </div>
            <div class="col-md-6 mb-2">
                <label class="form-label">Amount</label>
                <div class="input-group mb-3">
                    <div class="input-group-append">
                        <span class="input-group-text">Rp</span>
                    </div>
                    <input class="form-control" name="nominal_bt_lainnya[]" id="nominal_bt_lainnya_1" type="text" min="0" value="0" onfocus="this.value = this.value === '0' ? '' : this.value;" oninput="formatInput(this)" onblur="formatOnBlur(this)">
                </div>
            </div>

            <!-- Information -->
            <div class="col-md-12 mb-2">
                <div class="mb-2">
                    <label class="form-label">Information</label>
                    <textarea name="keterangan_bt_lainnya[]" class="form-control"></textarea>
                </div>
            </div>
        </div>
        <div class="row mt-3">
            <div class="d-flex justify-start w-100">
                <button class="btn btn-danger mr-2" style="margin-right: 10px" onclick="clearFormLainnya(1, event)">Clear</button>
                <button class="btn btn-warning mr-2" onclick="removeFormLainnya(1, event)">Remove</button>
            </div>
        </div>
    </div>
</div>

<div class="mt-3">
    <button class="btn btn-primary" id="addMoreButton" onclick="addMoreFormLainnya(event)">Add More</button>
</div>

<div class="mt-2">
    <label class="form-label">Total Others</label>
    <div class="input-group">
        <div class="input-group-append">
            <span class="input-group-text">Rp</span>
        </div>
        <input class="form-control bg-light" name="total_bt_lainnya" id="total_bt_lainnya" type="text" min="0" value="0" readonly>
    </div>
</div>

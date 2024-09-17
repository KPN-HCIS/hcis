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
                    ${
                        formCount > 1
                            ? `<button class="btn btn-warning mr-2" onclick="removeFormLainnya(${formCount}, event)">Remove</button>`
                            : ""
                    }
                </div>
            </div>
        `;
    document.getElementById("form-container").appendChild(newForm);
}

function removeFormLainnya(index, event) {
    event.preventDefault();
    if (formCount > 1) {
        let nominalValue = cleanNumber(
            document.querySelector(`#nominal_bt_lainnya_${index}`).value
        );
        let total = cleanNumber(
            document.querySelector('input[name="total_bt_lainnya"]').value
        );
        total -= nominalValue;
        document.querySelector('input[name="total_bt_lainnya"]').value =
            formatNumber(total);
        // Clear the form inputs for cleanliness
        let formContainer = document.getElementById(
            `form-container-bt-lainnya-${index}`
        );
        formContainer
            .querySelectorAll('input[type="text"], input[type="date"]')
            .forEach((input) => {
                input.value = "";
            });
        formContainer
            .querySelectorAll('input[type="number"]')
            .forEach((input) => {
                input.value = 0;
            });
        formContainer.querySelectorAll("select").forEach((select) => {
            select.selectedIndex = 0;
        });
        formContainer.querySelectorAll("textarea").forEach((textarea) => {
            textarea.value = "";
        });

        // Remove the form container from the DOM
        formContainer.remove();
        formCount--;

        // Reset nilai nominal di form yang disembunyikan (optional)
        document.querySelector(`#nominal_bt_lainnya_${index}`).value = 0;
        calculateTotalNominalBTLainnya();
    }
}

function clearFormLainnya(index, event) {
    event.preventDefault();
    if (formCount > 0) {
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
    }
}

function calculateTotalNominalBTLainnya() {
    let total = 0;
    const nominalInputs = document.querySelectorAll(
        'input[name="nominal_bt_lainnya[]"]'
    );
    nominalInputs.forEach((input) => {
        total += cleanNumber(input.value);
    });
    document.getElementById("total_bt_lainnya").value = formatNumber(total);
}

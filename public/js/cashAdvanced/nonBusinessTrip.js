var formCount = 1;

function addMoreFormNBT(event) {
    event.preventDefault();

    // We can set a maximum number of forms
    if (formCount < 3) {
        formCount++;
        // Create a new form div
        const newForm = document.createElement("div");
        newForm.id = `form-container-nbt-${formCount}`;
        newForm.className = "card-body bg-light p-2 mb-3";
        newForm.styleName = "border-radius: 1%;";
        newForm.innerHTML = `
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <label class="form-label">Date</label>
                        <input type="date" name="tanggal_nbt[]" class="form-control" placeholder="mm/dd/yyyy">
                    </div>
                    <div class="col-md-6 mb-2">
                        <label class="form-label">Amount</label>
                        <div class="input-group mb-3">
                            <div class="input-group-append">
                                <span class="input-group-text">Rp</span>
                            </div>
                            <input class="form-control" name="nominal_nbt[]" id="nominal_nbt_${formCount}" type="text" min="0" value="0" onfocus="this.value = this.value === '0' ? '' : this.value;" oninput="formatInput(this)" onblur="formatOnBlur(this)">
                        </div>
                    </div>
                    <div class="col-md-12 mb-2">
                        <div class="mb-2">
                            <label class="form-label">Information</label>
                            <textarea name="keterangan_nbt[]" class="form-control"></textarea>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="d-flex justify-start w-100">
                        <button class="btn btn-danger mr-2" style="margin-right: 10px" onclick="clearFormNBT(${formCount}, event)">Clear</button>
                        ${
                            formCount > 1
                                ? `<button class="btn btn-warning mr-2" onclick="removeFormNBT(${formCount}, event)">Remove</button>`
                                : ""
                        }
                    </div>
                </div>
            `;
        document.getElementById("form-container").appendChild(newForm);
    }
}

function removeFormNBT(index, event) {
    event.preventDefault();
    if (formCount > 1) {
        adjustTotalCA(index);
        clearFormInputs(index);
        document.getElementById(`form-container-nbt-${index}`).remove();
        formCount--;
    }
}

function clearFormNBT(index, event) {
    event.preventDefault();
    if (formCount > 0) {
        adjustTotalCA(index);
        clearFormInputs(index);
        document.querySelector(`#nominal_nbt_${index}`).value = 0;
    }
}

function adjustTotalCA(index) {
    let nominalValue = cleanNumber(
        document.querySelector(`#nominal_nbt_${index}`).value
    );
    let totalCA =
        cleanNumber(document.querySelector('input[name="totalca"]').value) || 0;
    totalCA -= nominalValue;
    document.querySelector('input[name="totalca"]').value =
        formatNumber(totalCA);
}

function clearFormInputs(index) {
    let formContainer = document.getElementById(`form-container-nbt-${index}`);

    formContainer
        .querySelectorAll(
            'input[type="text"], input[type="date"], input[type="number"]'
        )
        .forEach((input) => {
            input.value = input.type === "number" ? 0 : "";
        });

    formContainer.querySelectorAll("select").forEach((select) => {
        select.selectedIndex = 0;
    });

    formContainer.querySelectorAll("textarea").forEach((textarea) => {
        textarea.value = "";
    });
}

document.addEventListener("DOMContentLoaded", function () {
    function formatInput(input) {
        let value = input.value.replace(/\./g, "");
        let formattedValue = formatNumber(
            isNaN(parseFloat(value)) ? 0 : Math.floor(parseFloat(value))
        );
        input.value = formattedValue;
        calculateTotalNominal();
    }

    function calculateTotalNominal() {
        let total = Array.from(
            document.querySelectorAll('input[name="nominal_nbt[]"]')
        ).reduce((acc, input) => acc + parseNumber(input.value), 0);
        document.getElementById("totalca").value = formatNumber(total);
    }

    // Attach input event to the existing nominal fields
    document
        .querySelectorAll('input[name="nominal_nbt[]"]')
        .forEach((input) => {
            input.addEventListener("input", function () {
                formatInput(this);
            });
        });

    calculateTotalNominal();
});

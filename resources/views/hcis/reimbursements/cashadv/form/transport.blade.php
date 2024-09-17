<script>
    var formCount = 1;

    function addMoreFormTransport(event) {
        event.preventDefault();
        formCount++;

        const newForm = document.createElement("div");
        newForm.id = `form-container-bt-transport-${formCount}`;
        newForm.className = "card-body bg-light p-2 mb-3";
        newForm.innerHTML = `
            <div class="row">
                <!-- Transport Date -->
                <div class="col-md-4 mb-2">
                    <label class="form-label">Transport Date</label>
                    <input type="date" name="tanggal_bt_transport[]" class="form-control" placeholder="mm/dd/yyyy">
                </div>
                <div class="col-md-4 mb-2">
                    <label class="form-label" for="name">Company Code</label>
                    <select class="form-control select2" id="company_bt_transport_${formCount}" name="company_bt_transport[]">
                        <option value="">Select Company...</option>
                        @foreach ($companies as $company)
                            <option value="{{ $company->contribution_level_code }}">
                                {{ $company->contribution_level . ' (' . $company->contribution_level_code . ')' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-2">
                    <label class="form-label">Amount</label>
                    <div class="input-group mb-3">
                        <div class="input-group-append">
                            <span class="input-group-text">Rp</span>
                        </div>
                        <input class="form-control"
                                name="nominal_bt_transport[]"
                                id="nominal_bt_transport_${formCount}"
                                type="text"
                                min="0"
                                value="0"
                                onfocus="this.value = this.value === '0' ? '' : this.value;"
                                oninput="formatInput(this)"
                                onblur="formatOnBlur(this)" onchange="calculateTotalNominalBTTransport()">
                    </div>
                </div>

                <!-- Information -->
                <div class="col-md-12 mb-2">
                    <div class="mb-2">
                        <label class="form-label">Information</label>
                        <textarea name="keterangan_bt_transport[]" class="form-control"></textarea>
                    </div>
                </div>
            </div>
            <br>
            <div class="row mt-3">
                <div class="d-flex justify-start w-100">
                    <button class="btn btn-danger mr-2" style="margin-right: 10px" onclick="clearFormTransport(${formCount}, event)">Clear</button>
                    <button class="btn btn-warning mr-2" onclick="removeFormTransport(${formCount}, event)">Remove</button>
                </div>
            </div>
        `;
        document.getElementById("form-container-transport").appendChild(newForm);
    }

    function removeFormTransport(index, event) {
        event.preventDefault();
        if (formCount > 0) {
            let nominalValue = cleanNumber(document.querySelector(`#nominal_bt_transport_${formCount}`).value);

            let total = cleanNumber(document.querySelector('input[name="total_bt_transport"]').value);
            total -= nominalValue;
            document.querySelector('input[name="total_bt_transport"]').value = formatNumber(total);

            let formContainer = document.getElementById(`form-container-bt-transport-${index}`);

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

            document.querySelector(`#nominal_bt_transport_${index}`).value = 0;

            formContainer.remove();
            formCount--;
            calculateTotalNominalBTTotal();
        }
    }

    function clearFormTransport(index, event) {
        event.preventDefault();
        if (formCount > 0) {
            let nominalValue = cleanNumber(document.querySelector(`#nominal_bt_transport_${index}`).value);

            let total = cleanNumber(document.querySelector('input[name="total_bt_transport"]').value);
            total -= nominalValue;
            document.querySelector('input[name="total_bt_transport"]').value = formatNumber(total);

            let formContainer = document.getElementById(`form-container-bt-transport-${index}`);

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

            document.querySelector(`#nominal_bt_transport_${index}`).value = 0;
            calculateTotalNominalBTTotal();
        }
    }

    function calculateTotalNominalBTTransport() {
        let total = 0;
        document.querySelectorAll('input[name="nominal_bt_transport[]"]').forEach(input => {
            total += cleanNumber(input.value); // Gunakan cleanNumber untuk parsing
        });
        document.querySelector('input[name="total_bt_transport"]').value = formatNumber(total); // Tampilkan dengan format
    }

    function onNominalChange() {
        calculateTotalNominalBTTransport();
    }
</script>

<div id="form-container-transport">
    <div id="form-container-bt-transport-1" class="card-body bg-light p-2 mb-3" style="border-radius: 1%;">
        <div class="row">
            <!-- Transport Date -->
            <div class="col-md-4 mb-2">
                <label class="form-label">Transport Date</label>
                <input type="date" name="tanggal_bt_transport[]" class="form-control" placeholder="mm/dd/yyyy">
            </div>
            <div class="col-md-4 mb-2">
                <label class="form-label" for="name">Company Code</label>
                <select class="form-control select2" id="company_bt_transport_1" name="company_bt_transport[]">
                    <option value="">Select Company...</option>
                    @foreach ($companies as $company)
                        <option value="{{ $company->contribution_level_code }}">
                            {{ $company->contribution_level . ' (' . $company->contribution_level_code . ')' }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 mb-2">
                <label class="form-label">Amount</label>
                <div class="input-group mb-3">
                    <div class="input-group-append">
                        <span class="input-group-text">Rp</span>
                    </div>
                    <input class="form-control"
                            name="nominal_bt_transport[]"
                            id="nominal_bt_transport_1"
                            type="text"
                            min="0"
                            value="0"
                            onfocus="this.value = this.value === '0' ? '' : this.value;"
                            oninput="formatInput(this)"
                            onblur="formatOnBlur(this)">
                </div>
            </div>

            <!-- Information -->
            <div class="col-md-12 mb-2">
                <div class="mb-2">
                    <label class="form-label">Information</label>
                    <textarea name="keterangan_bt_transport[]" class="form-control"></textarea>
                </div>
            </div>
        </div>
        <br>
        <div class="row mt-3">
            <div class="d-flex justify-start w-100">
                <button class="btn btn-danger mr-2" style="margin-right: 10px" onclick="clearFormTransport(1, event)">Clear</button>
                <button class="btn btn-warning mr-2" onclick="removeFormTransport(1, event)">Remove</button>
            </div>
        </div>
    </div>
</div>

<div class="mt-3">
    <button class="btn btn-primary" id="addMoreButtonTransport" onclick="addMoreFormTransport(event)">Add More</button>
</div>

<div class="mt-2">
    <label class="form-label">Total Transport</label>
    <div class="input-group">
        <div class="input-group-append">
            <span class="input-group-text">Rp</span>
        </div>
        <input class="form-control bg-light"
            name="total_bt_transport"
            id="total_bt_transport" type="text"
            min="0" value="0" readonly>
    </div>
</div>

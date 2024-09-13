<script>
    var formCount = 1;

    function addMoreFormPenginapan(event) {
        event.preventDefault();
        if (formCount < 3) { // Assuming a maximum of 100 forms
            formCount++;
            document.getElementById(`form-container-bt-penginapan-${formCount}`).style.display = 'block';
        }
    }

    function removeFormPenginapan(index, event) {
        event.preventDefault();
        if (formCount > 1) {
            let nominalValue = cleanNumber(document.querySelector(`#nominal_bt_penginapan_${formCount}`).value);

            let total = cleanNumber(document.querySelector('input[name="total_bt_penginapan"]').value);
            total -= nominalValue;
            document.querySelector('input[name="total_bt_penginapan"]').value = formatNumber(total);

            let formContainer = document.getElementById(`form-container-bt-penginapan-${index}`);

            formContainer.querySelectorAll('input[type="text"], input[type="date"]').forEach(input => {
                input.value = '';
            });

            formContainer.querySelectorAll('input[type="number"]').forEach(input => {
                input.value = 0;
            });

            formContainer.querySelectorAll('select').forEach(select => {
                select.selectedIndex = 0;
            });

            document.querySelector(`#nominal_bt_penginapan_${formCount}`).value = 0;

            document.getElementById(`form-container-bt-penginapan-${formCount}`).style.display = 'none';
            formCount--;
            calculateTotalNominalBTTotal();
        }
    }

    function clearFormPenginapan(index, event) {
        event.preventDefault();
        if (formCount > 0) {
            let nominalValue = cleanNumber(document.querySelector(`#nominal_bt_penginapan_${formCount}`).value);

            let total = cleanNumber(document.querySelector('input[name="total_bt_penginapan"]').value);
            total -= nominalValue;
            document.querySelector('input[name="total_bt_penginapan"]').value = formatNumber(total);

            let formContainer = document.getElementById(`form-container-bt-penginapan-${index}`);

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

            document.querySelector(`#nominal_bt_penginapan_${formCount}`).value = 0;
            calculateTotalNominalBTTotal();
        }
    }

    function calculateTotalNominalBTPenginapan() {
        let total = 0;
        document.querySelectorAll('input[name="nominal_bt_penginapan[]"]').forEach(input => {
            total += cleanNumber(input.value);
        });
        document.querySelector('input[name="total_bt_penginapan"]').value = formatNumber(total);
    }

    function onNominalChange() {
        calculateTotalNominalBTPenginapan();
    }

    function calculateTotalDays(startInput, endInput, totalDaysInput) {
        const startDate = new Date(startInput.value);
        const endDate = new Date(endInput.value);
        if(startDate && endDate && endDate >= startDate) {
            const timeDiff = endDate - startDate;
            const daysDiff = Math.ceil(timeDiff / (1000 * 3600 * 24)); // Convert time to days
            totalDaysInput.value = daysDiff > 0 ? daysDiff : 0; // Ensure non-negative
        } else {
            totalDaysInput.value = 0; // Set to 0 if invalid dates
        }
    }
</script>

@for ($i = 1; $i <= 3; $i++)
    <div id="form-container-bt-penginapan-{{ $i }}" class="card-body bg-light p-2 mb-3" style="{{ $i > 1 ? 'display: none;' : '' }} border-radius: 1%;">
        <div class="row">
            <!-- Penginapan Date -->
            <div class="col-md-4 mb-2">
                <label class="form-label">Accommodation Start Plan</label>
                <input type="date" name="start_bt_penginapan[]"
                    id="start_bt_penginapan_{{ $i }}"
                    class="form-control start-penginapan"
                    placeholder="mm/dd/yyyy" onchange="calculateTotalDays(this, document.getElementById('end_bt_penginapan_{{ $i }}'), document.querySelector('#total_days_bt_penginapan_{{ $i }}'))">
            </div>
            <div class="col-md-4 mb-2">
                <label class="form-label">Accommodation End Plan</label>
                <input type="date" name="end_bt_penginapan[]"
                    id="end_bt_penginapan_{{ $i }}"
                    class="form-control end-penginapan"
                    placeholder="mm/dd/yyyy" onchange="calculateTotalDays(document.getElementById('start_bt_penginapan_{{ $i }}'), this, document.querySelector('#total_days_bt_penginapan_{{ $i }}'))">
            </div>
            <div class="col-md-4 mb-2">
                <label class="form-label">Total Days</label>
                <div class="input-group">
                    <input
                        class="form-control bg-light total-days-penginapan"
                        id="total_days_bt_penginapan_{{ $i }}"
                        name="total_days_bt_penginapan[]"
                        type="number" min="0"
                        value="0" readonly>
                    <div class="input-group-append">
                        <span class="input-group-text">days</span>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-2">
                <label class="form-label">Hotel Name</label>
                <input type="text"
                    name="hotel_name_bt_penginapan[]"
                    class="form-control" placeholder="Hotel">
            </div>
            <div class="col-md-4 mb-2">
                <label class="form-label">Company Code</label>
                <select class="form-control select2"
                    id="company_bt_penginapan_{{ $i }}"
                    name="company_bt_penginapan[]">
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
                        name="nominal_bt_penginapan[]"
                        id="nominal_bt_penginapan_{{ $i }}" type="text"
                        min="0" value="0"
                        onfocus="this.value = this.value === '0' ? '' : this.value;"
                        oninput="formatInput(this)"
                        onblur="formatOnBlur(this)">>
                </div>
            </div>
        </div>
        <br>
        <div class="row mt-3">
            <div class="d-flex justify-start w-100">
                @if ($i > 0)
                    <button class="btn btn-danger mr-2" style="margin-right: 10px" id="form-container-bt-penginapan-{{ $i }}-cl" name="form-container-bt-penginapan-{{ $i }}" value="Clear" onclick="clearFormPenginapan({{ $i }}, event)">Clear</button>
                @endif
                @if ($i > 1)
                    <button class="btn btn-warning mr-2" id="form-container-bt-penginapan-{{ $i }}-no" name="form-container-bt-penginapan-{{ $i }}" value="Tidak" onclick="removeFormPenginapan({{ $i }}, event)">Remove</button>
                @endif
            </div>
        </div>
    </div>
@endfor

<div class="mt-3">
    <button class="btn btn-primary" id="form-container-bt-penginapan-{{ $i }}-yes" name="form-container-bt-penginapan-{{ $i }}" value="Ya" onclick="addMoreFormPenginapan(event)">Add More</button>
</div>

<div class="mt-2">
    <label class="form-label">Total Accommodation</label>
    <div class="input-group">
        <div class="input-group-append">
            <span class="input-group-text">Rp</span>
        </div>
        <input class="form-control bg-light"
            name="total_bt_penginapan"
            id="total_bt_penginapan" type="text"
            min="0" value="0" readonly>
    </div>
</div>

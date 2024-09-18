<script>
    var formCount = 0;

    window.addEventListener('DOMContentLoaded', function() {
        formCount = document.querySelectorAll('#form-container-penginapan > div').length;
    });

    function addMoreFormPenginapan(event) {
        event.preventDefault();
        formCount++;

        const newForm = document.createElement("div");
        newForm.id = `form-container-bt-penginapan-${formCount}`;
        newForm.className = "card-body bg-light p-2 mb-3";
        newForm.innerHTML = `
            <div class="row">
                <!-- Penginapan Date -->
                <div class="col-md-4 mb-2">
                    <label class="form-label">Accommodation Start Plan</label>
                    <input type="date" name="start_bt_penginapan[]"
                        id="start_bt_penginapan_${formCount}"
                        class="form-control start-penginapan"
                        placeholder="mm/dd/yyyy" onchange="calculateTotalDaysPenginapan(this, document.getElementById('end_bt_penginapan_${formCount}'), document.querySelector('#total_days_bt_penginapan_${formCount}'))">
                </div>
                <div class="col-md-4 mb-2">
                    <label class="form-label">Accommodation End Plan</label>
                    <input type="date" name="end_bt_penginapan[]"
                        id="end_bt_penginapan_${formCount}"
                        class="form-control end-penginapan"
                        placeholder="mm/dd/yyyy" onchange="calculateTotalDaysPenginapan(document.getElementById('start_bt_penginapan_${formCount}'), this, document.querySelector('#total_days_bt_penginapan_${formCount}'))">
                </div>
                <div class="col-md-4 mb-2">
                    <label class="form-label">Total Days</label>
                    <div class="input-group">
                        <input
                            class="form-control bg-light total-days-penginapan"
                            id="total_days_bt_penginapan_${formCount}"
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
                        id="company_bt_penginapan_${formCount}"
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
                            id="nominal_bt_penginapan_${formCount}" type="text"
                            min="0" value="0"
                            onfocus="this.value = this.value === '0' ? '' : this.value;"
                            oninput="formatInput(this)"
                            onblur="formatOnBlur(this)">
                    </div>
                </div>
            </div>
            <br>
            <div class="row mt-3">
                <div class="d-flex justify-start w-100">
                    <button class="btn btn-danger mr-2" style="margin-right: 10px" onclick="clearFormPenginapan(${formCount}, event)">Clear</button>
                    <button class="btn btn-warning mr-2" onclick="removeFormPenginapan(${formCount}, event)">Remove</button>
                </div>
            </div>
        `;
        document.getElementById("form-container-penginapan").appendChild(newForm);
    }

    $('.btn-warning').click(function(event) {
        event.preventDefault();
        var index = $(this).closest('.card-body').index() + 1;
        removeFormPenginapan(index, event);
    });

    function removeFormPenginapan(index, event) {
        event.preventDefault();
        if (formCount > 0) {
            const formContainer = document.getElementById(`form-container-bt-penginapan-${index}`);
            if (formContainer) {
                const nominalInput = formContainer.querySelector(`#nominal_bt_penginapan_${index}`);
                if (nominalInput) {
                    let nominalValue = cleanNumber(nominalInput.value);
                    let total = cleanNumber(document.querySelector('input[name="total_bt_penginapan"]').value);
                    total -= nominalValue;
                    document.querySelector('input[name="total_bt_penginapan"]').value = formatNumber(total);
                    calculateTotalNominalBTTotal();
                }
                $(`#form-container-bt-penginapan-${index}`).remove();
                formCount--;
            }
        }
    }

    function clearFormPenginapan(index, event) {
        event.preventDefault();
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

    function calculateTotalDaysPenginapan(startInput, endInput, totalDaysInput) {
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

@if (!empty($detailCA['detail_penginapan']) && $detailCA['detail_penginapan'][0]['start_date'] !== null)
    <div id="form-container-penginapan">
        @foreach($detailCA['detail_penginapan'] as $penginapan)
            <div id="form-container-bt-penginapan-{{ $loop->index + 1 }}" class="card-body bg-light p-2 mb-3">
                <div class="row">
                    <!-- Penginapan Date -->
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Accommodation Start Plan</label>
                        <input type="date" name="start_bt_penginapan[]"
                            id="start_bt_penginapan_{{ $loop->index + 1 }}"
                            class="form-control start-penginapan"
                            value="{{$penginapan['start_date']}}"
                            placeholder="mm/dd/yyyy"
                            onchange="calculateTotalDaysPenginapan(this, document.getElementById('end_bt_penginapan_1'), document.querySelector('#total_days_bt_penginapan_1'))">
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Accommodation End Plan</label>
                        <input type="date" name="end_bt_penginapan[]"
                            id="end_bt_penginapan_{{ $loop->index + 1 }}"
                            class="form-control end-penginapan"
                            value="{{$penginapan['end_date']}}"
                            placeholder="mm/dd/yyyy"
                            onchange="calculateTotalDaysPenginapan(document.getElementById('start_bt_penginapan_{{ $loop->index + 1 }}'), this, document.querySelector('#total_days_bt_penginapan_1'))">
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Total Days</label>
                        <div class="input-group">
                            <input
                                class="form-control bg-light total-days-penginapan"
                                id="total_days_bt_penginapan_{{ $loop->index + 1 }}"
                                name="total_days_bt_penginapan[]"
                                type="number" min="0"
                                value="{{$penginapan['total_days']}}"
                                readonly>
                            <div class="input-group-append">
                                <span class="input-group-text">days</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 mb-2">
                        <label class="form-label">Hotel Name</label>
                        <input type="text"
                            name="hotel_name_bt_penginapan[]"
                            class="form-control" placeholder="Hotel"
                            id="hotel_name_bt_penginapan_{{ $loop->index + 1 }}"
                            value="{{$penginapan['hotel_name']}}">
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Company Code</label>
                        <select class="form-control select2"
                            id="company_bt_penginapan_{{ $loop->index + 1 }}"
                            name="company_bt_penginapan[]">
                            <option value="">Select Company...</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->contribution_level_code }}"
                                    @if($company->contribution_level_code == $penginapan['company_code']) selected @endif>
                                    {{ $company->contribution_level." (".$company->contribution_level_code.")" }}
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
                                id="nominal_bt_penginapan_{{ $loop->index + 1 }}" type="text"
                                min="0"
                                value="{{ number_format($penginapan['nominal'], 0, ',', '.') }}"
                                onfocus="this.value = this.value === '0' ? '' : this.value;"
                                oninput="formatInput(this)"
                                onblur="formatOnBlur(this)">
                        </div>
                    </div>
                </div>
                <br>
                <div class="row mt-3">
                    <div class="d-flex justify-start w-100">
                        <button class="btn btn-danger mr-2" style="margin-right: 10px" onclick="clearFormPenginapan({{ $loop->index + 1 }}, event)">Clear</button>
                        <button class="btn btn-warning mr-2" onclick="removeFormPenginapan({{ $loop->index + 1 }}, event)">Remove</button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-3">
        <button class="btn btn-primary" id="addMoreButton" onclick="addMoreFormPenginapan(event)">Add More</button>
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
                min="0" value="{{ number_format(array_sum(array_column($detailCA['detail_penginapan'], 'nominal')), 0, ',', '.') }}" readonly>
        </div>
    </div>
@else
    <div id="form-container-penginapan">
        <div id="form-container-bt-penginapan-1" class="card-body bg-light p-2 mb-3">
            <div class="row">
                <!-- Penginapan Date -->
                <div class="col-md-4 mb-2">
                    <label class="form-label">Accommodation Start Plan</label>
                    <input type="date" name="start_bt_penginapan[]"
                        id="start_bt_penginapan_1"
                        class="form-control start-penginapan"
                        placeholder="mm/dd/yyyy" onchange="calculateTotalDaysPenginapan(this, document.getElementById('end_bt_penginapan_1'), document.querySelector('#total_days_bt_penginapan_1'))">
                </div>
                <div class="col-md-4 mb-2">
                    <label class="form-label">Accommodation End Plan</label>
                    <input type="date" name="end_bt_penginapan[]"
                        id="end_bt_penginapan_1"
                        class="form-control end-penginapan"
                        placeholder="mm/dd/yyyy" onchange="calculateTotalDaysPenginapan(document.getElementById('start_bt_penginapan_1'), this, document.querySelector('#total_days_bt_penginapan_1'))">
                </div>
                <div class="col-md-4 mb-2">
                    <label class="form-label">Total Days</label>
                    <div class="input-group">
                        <input
                            class="form-control bg-light total-days-penginapan"
                            id="total_days_bt_penginapan_1"
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
                        class="form-control" placeholder="Hotel" id="hotel_name_bt_penginapan_1">
                </div>
                <div class="col-md-4 mb-2">
                    <label class="form-label">Company Code</label>
                    <select class="form-control select2"
                        id="company_bt_penginapan_1"
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
                            id="nominal_bt_penginapan_1" type="text"
                            min="0" value="0"
                            onfocus="this.value = this.value === '0' ? '' : this.value;"
                            oninput="formatInput(this)"
                            onblur="formatOnBlur(this)">
                    </div>
                </div>
            </div>
            <br>
            <div class="row mt-3">
                <div class="d-flex justify-start w-100">
                    <button class="btn btn-danger mr-2" style="margin-right: 10px" onclick="clearFormPenginapan(1, event)">Clear</button>
                    <button class="btn btn-warning mr-2" onclick="removeFormPenginapan(1, event)">Remove</button>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-3">
        <button class="btn btn-primary" id="addMoreButton" onclick="addMoreFormPenginapan(event)">Add More</button>
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
@endif

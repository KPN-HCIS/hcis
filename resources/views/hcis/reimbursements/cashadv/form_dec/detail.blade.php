<script>
    var formCountDetail = 0;

    window.addEventListener('DOMContentLoaded', function() {
        formCountDetail = document.querySelectorAll('#form-container-detail > div').length;
        updateCheckboxVisibility();
    });

    function addMoreFormDetailDec(event) {
        event.preventDefault();
        formCountDetail++;

        const newForm = document.createElement("div");
        newForm.id = `form-container-e-detail-${formCountDetail}`;
        newForm.className = "card-body p-2 mb-3";
        newForm.style.backgroundColor = "#f8f8f8";
        newForm.innerHTML = `
            <p class="fs-4 text-primary" style="font-weight: bold; ">Detail Entertainment ${formCountDetail}</p>
            <div class="card-body bg-light p-2 mb-3">
                <p class="fs-5 text-primary" style="font-weight: bold;">Penginapan Declaration</p>
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <label class="form-label">Entertainment Type</label>
                        <select name="enter_type_e_detail[]" id="enter_type_e_detail_${formCountDetail}" class="form-select">
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
                                id="nominal_e_detail_${formCountDetail}"
                                type="text" min="0" value="0"
                                onfocus="this.value = this.value === '0' ? '' : this.value;"
                                oninput="formatInputENT(this)">
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
                        <button class="btn btn-danger mr-2" style="margin-right: 10px"
                            onclick="clearFormDetail(${formCountDetail}, event)">Reset</button>
                        <button class="btn btn-warning mr-2"
                            onclick="removeFormDetail(${formCountDetail}, event)">Delete</button>
                    </div>
                </div>
            </div>
        `;

        document.getElementById("form-container-detail").appendChild(newForm);

        // Menambahkan listener untuk select dan input baru
        newForm.querySelector('select[name="enter_type_e_detail[]"]').addEventListener('change', updateCheckboxVisibility);

        newForm.querySelector('input[name="nominal_e_detail[]"]').addEventListener('input', function() {
            formatInputENT(this);
            calculateTotalNominalEDetail();
        });

        calculateTotalNominalEDetail(); // Hitung total secara otomatis.
        updateCheckboxVisibility(); // Memperbarui visibilitas checkbox.
    }

    function addMoreFormDetailReq(event) {
        event.preventDefault();
        formCountDetail++;

        const newForm = document.createElement("div");
        newForm.id = `form-container-e-detail-${formCountDetail}`;
        newForm.className = "card-body p-2 mb-3";
        newForm.style.backgroundColor = "#f8f8f8";
        newForm.innerHTML = `
            <p class="fs-4 text-primary" style="font-weight: bold; ">Detail Entertainment ${formCountDetail}</p>
            <div class="card-body bg-light p-2 mb-3">
                <p class="fs-5 text-primary" style="font-weight: bold;">Penginapan Request</p>
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <label class="form-label">Entertainment Type</label>
                        <select name="enter_type_e_detail[]" id="enter_type_e_detail_${formCountDetail}" class="form-select">
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
                                id="nominal_e_detail_${formCountDetail}"
                                type="text" min="0" value="0"
                                onfocus="this.value = this.value === '0' ? '' : this.value;"
                                oninput="formatInputENT(this)">
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
                        <button class="btn btn-danger mr-2" style="margin-right: 10px"
                            onclick="clearFormDetail(${formCountDetail}, event)">Reset</button>
                        <button class="btn btn-warning mr-2"
                            onclick="removeFormDetail(${formCountDetail}, event)">Delete</button>
                    </div>
                </div>
            </div>
        `;

        document.getElementById("form-container-detail").appendChild(newForm);

        // Menambahkan listener untuk select dan input baru
        newForm.querySelector('select[name="enter_type_e_detail[]"]').addEventListener('change', updateCheckboxVisibility);

        newForm.querySelector('input[name="nominal_e_detail[]"]').addEventListener('input', function() {
            formatInputENT(this);
            calculateTotalNominalEDetail();
        });

        calculateTotalNominalEDetail(); // Hitung total secara otomatis.
        updateCheckboxVisibility(); // Memperbarui visibilitas checkbox.
    }

    $('.btn-warning').click(function(event) {
        event.preventDefault();
        var index = $(this).closest('.card-body').index() + 1;
        removeFormDetail(index, event);
    });

    function removeFormDetail(index, event) {
        event.preventDefault();
        if (formCountDetail > 0) {
            let nominalValue = cleanNumber(document.querySelector(`#nominal_e_detail_${index}`).value);
            let totalCA = cleanNumber(document.querySelector('input[name="totalca"]').value) || 0;
            totalCA -= nominalValue;
            document.querySelector('input[name="total_e_detail"]').value = formatNumber(totalCA);
            document.querySelector('input[name="totalca"]').value = formatNumber(totalCA);

            // Hide the form to be removed
            let formContainer = document.getElementById(`form-container-e-detail-${index}`);
            formContainer.remove();
            formCountDetail--;

            updateCheckboxVisibility();
        }
    }

    function removeFormDetailDec(index, event) {
        event.preventDefault();
        if (formCountDetail > 0) {
            let nominalValue = cleanNumber(document.querySelector(`#nominal_e_detail_${index}`).value);
            let totalCA = cleanNumber(document.querySelector('input[name="totalca"]').value) || 0;
            totalCA -= nominalValue;
            document.querySelector('input[name="total_e_detail"]').value = formatNumber(totalCA);
            document.querySelector('input[name="totalca"]').value = formatNumber(totalCA);

            // Hide the form to be removed
            let formContainer = document.getElementById(`form-container-e-detail-dec-${index}`);
            formContainer.remove();
            formCountDetail--;

            updateCheckboxVisibility();
        }
    }

    function clearFormDetail(index, event) {
        event.preventDefault();
        if (formCountDetail > 0) {
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

    function calculateTotalNominalEDetail() {
        let total = 0;
        document.querySelectorAll('input[name="nominal_e_detail[]"]').forEach(input => {
            total += parseNumber(input.value);
        });
        document.querySelector('input[name="total_e_detail"]').value = formatNumber(total);
        document.getElementById('totalca').value = formatNumber(total);
    }

    function updateCheckboxVisibility() {
        const selectedOptions = Array.from(document.querySelectorAll('select[name="enter_type_e_detail[]"]'))
            .map(select => select.value)
            .filter(value => value !== "");

        const formContainerERelation = document.querySelectorAll('[id^="form-container-e-relation-"]');
        const formContainerERelationDec = document.querySelectorAll('[id^="form-container-e-relation-dec-"]');

        formContainerERelation.forEach(container => {
            container.querySelectorAll('.form-check').forEach(checkDiv => {
                const checkbox = checkDiv.querySelector('input.form-check-input');
                const checkboxValue = checkbox.value.toLowerCase().replace(/\s/g, "_");
                if (selectedOptions.includes(checkboxValue)) {
                    checkDiv.style.display = 'block'; // Show the checkbox
                } else {
                    checkDiv.style.display = 'none';  // Hide the checkbox
                    checkbox.checked = false;         // Uncheck the hidden checkbox
                }
            });
        });

        formContainerERelationDec.forEach(container => {
            container.querySelectorAll('.form-check').forEach(checkDiv => {
                const checkbox = checkDiv.querySelector('input.form-check-input');
                const checkboxValue = checkbox.value.toLowerCase().replace(/\s/g, "_");
                if (selectedOptions.includes(checkboxValue)) {
                    checkDiv.style.display = 'block'; // Show the checkbox
                } else {
                    checkDiv.style.display = 'none';  // Hide the checkbox
                    checkbox.checked = false;         // Uncheck the hidden checkbox
                }
            });
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('input[name="nominal_e_detail[]"]').forEach(input => {
            input.addEventListener('input', function() {
                formatInputENT(this);
                calculateTotalNominalEDetail(); // Ensure we calculate total here
            });
        });

        // Call the function after the existing select elements are processed
        document.querySelectorAll('select[name="enter_type_e_detail[]"]').forEach(select => {
            select.addEventListener('change', updateCheckboxVisibility);
        });

        calculateTotalNominalEDetail();
        updateCheckboxVisibility();
    });

    function formatInputENT(input) {
        let value = input.value.replace(/\./g, '');
        value = parseFloat(value);
        if (!isNaN(value)) {
            input.value = formatNumber(Math.floor(value));
        } else {
            input.value = formatNumber(0);
        }
        calculateTotalNominalEDetail();
    }
</script>

@if (!empty($detailCA['detail_e']) && $detailCA['detail_e'][0]['type'] !== null)
    <div id="form-container-detail">
        @foreach ($detailCA['detail_e'] as $index => $detail)
            <div id="form-container-e-detail-{{ $loop->index + 1 }}" class="p-2 mb-4 rounded-3" style="background-color: #f8f8f8">
                <p class="fs-4 text-primary" style="font-weight: bold; ">Detail Entertainment {{ $loop->index + 1 }}</p>
                <div id="form-container-e-detail-req-{{ $loop->index + 1 }}" class="card-body bg-light p-2 mb-3" style="border-radius: 1%;">
                    <p class="fs-5 text-primary" style="font-weight: bold;">Detail Entertainment Request</p>
                    <div class="row">
                        <!-- Company Code -->
                        <div class="col-md-6">
                            <table class="table" style="border: none; border-collapse: collapse; padding: 1%;">
                                <tr>
                                    <th class="label" style="border: none; width:40%;">Entertainment Type</th>
                                    <td class="colon" style="border: none; width:1%;">:</td>
                                    <td class="value" style="border: none;">
                                        @php
                                            $typeMap = [
                                                'food' => 'Food/Beverages/Souvenir',
                                                'transport' => 'Transport',
                                                'accommodation' => 'Accommodation',
                                                'gift' => 'Gift',
                                                'fund' => 'Fund',
                                            ];
                                        @endphp
                                        {{ $typeMap[$detail['type']] ?? $detail['type'] }}
                                    </td>
                                </tr>
                                <tr>
                                    <th class="label" style="border: none;">Amount</th>
                                    <td class="colon" style="border: none;">:</td>
                                    <td class="value" style="border: none;">{{ number_format($detail['nominal'], 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <th class="label" style="border: none;">Entertainment Fee Detail</th>
                                    <td class="colon" style="border: none;">:</td>
                                    <td class="value" style="border: none;">{{ $detail['fee_detail'] }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div id="form-container-e-detail-dec-{{ $loop->index + 1 }}" class="card-body bg-light p-2 mb-3" style="border-radius: 1%;">
                    <p class="fs-5 text-primary" style="font-weight: bold;">Detail Entertainment Declaration</p>
                    @if (isset($declareCA['detail_e'][$index]))
                        @php
                            $detail_dec = $declareCA['detail_e'][$index];
                        @endphp
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Entertainment Type</label>
                                <select name="enter_type_e_detail[]" id="enter_type_e_detail[]" class="form-select">
                                    <option value="">-</option>
                                    <option value="food" {{ $detail_dec['type'] == 'food' ? 'selected' : '' }}>Food/Beverages/Souvenir</option>
                                    <option value="transport" {{ $detail_dec['type'] == 'transport' ? 'selected' : '' }}>Transport</option>
                                    <option value="accommodation" {{ $detail_dec['type'] == 'accommodation' ? 'selected' : '' }}>Accommodation</option>
                                    <option value="gift" {{ $detail_dec['type'] == 'gift' ? 'selected' : '' }}>Gift</option>
                                    <option value="fund" {{ $detail_dec['type'] == 'fund' ? 'selected' : '' }}>Fund</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Amount</label>
                                <div class="input-group">
                                    <div class="input-group-append">
                                        <span class="input-group-text">Rp</span>
                                    </div>
                                    <input class="form-control" name="nominal_e_detail[]"
                                        id="nominal_e_detail_{{ $loop->index + 1 }}"
                                        type="text" min="0" value="{{ number_format($detail_dec['nominal'], 0, ',', '.') }}"
                                        onfocus="this.value = this.value === '0' ? '' : this.value;"
                                        oninput="formatInputENT(this)">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Entertainment Fee Detail</label>
                                <textarea name="enter_fee_e_detail[]" class="form-control">{{ $detail_dec['fee_detail'] }}</textarea>
                            </div>
                        </div>
                    @endif
                    <br>
                    <div class="row mt-3">
                        <div class="d-flex justify-start w-100">
                            <button class="btn btn-danger mr-2" style="margin-right: 10px" onclick="clearFormDetail({{ $loop->index + 1 }}, event)">Reset</button>
                            <button class="btn btn-warning mr-2" onclick="removeFormDetailDec({{ $loop->index + 1 }}, event)">Delete</button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
        @foreach ($declareCA['detail_e'] as $index => $detail_dec)
            @if (!isset($detailCA['detail_e'][$index]))
                <div id="form-container-e-detail-{{ $loop->index + 1 }}" class="p-2 mb-4 rounded-3" style="background-color: #f8f8f8">
                    <p class="fs-4 text-primary" style="font-weight: bold; ">Detail Entertainment {{ $loop->index + 1 }}</p>
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Entertainment Type</label>
                            <select name="enter_type_e_detail[]" id="enter_type_e_detail_{{ $loop->index + 1 }}" class="form-select">
                                <option value="">-</option>
                                <option value="food" {{ $detail_dec['type'] == 'food' ? 'selected' : '' }}>Food/Beverages/Souvenir</option>
                                <option value="transport" {{ $detail_dec['type'] == 'transport' ? 'selected' : '' }}>Transport</option>
                                <option value="accommodation" {{ $detail_dec['type'] == 'accommodation' ? 'selected' : '' }}>Accommodation</option>
                                <option value="gift" {{ $detail_dec['type'] == 'gift' ? 'selected' : '' }}>Gift</option>
                                <option value="fund" {{ $detail_dec['type'] == 'fund' ? 'selected' : '' }}>Fund</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Amount</label>
                            <div class="input-group">
                                <div class="input-group-append">
                                    <span class="input-group-text">Rp</span>
                                </div>
                                <input class="form-control" name="nominal_e_detail[]"
                                    id="nominal_e_detail_{{ $loop->index + 1 }}"
                                    type="text" min="0" value="{{ number_format($detail_dec['nominal'], 0, ',', '.') }}"
                                    onfocus="this.value = this.value === '0' ? '' : this.value;"
                                    oninput="formatInputENT(this)">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Entertainment Fee Detail</label>
                            <textarea name="enter_fee_e_detail[]" class="form-control">{{ $detail_dec['fee_detail'] }}</textarea>
                        </div>
                    </div>
                    <br>
                    <div class="row mt-3">
                        <div class="d-flex justify-start w-100">
                            <button class="btn btn-danger mr-2" style="margin-right: 10px" onclick="clearFormDetail({{ $loop->index + 1 }}, event)">Reset</button>
                            <button class="btn btn-warning mr-2" onclick="removeFormDetail({{ $loop->index + 1 }}, event)">Delete</button>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    </div>

    <div class="mt-3">
        <button class="btn btn-primary" id="addMoreButtonDetail" onclick="addMoreFormDetailDec(event)">Add More</button>
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
@elseif (!empty($declareCA['detail_e']) && $declareCA['detail_e'][0]['nominal'] !== null)
    <div id="form-container-detail">
        @foreach ($declareCA['detail_e'] as $index => $detail_dec)
            @if (!isset($detailCA['detail_e'][$index]))
                <div id="form-container-e-detail-{{ $loop->index + 1 }}" class="p-2 mb-4 rounded-3" style="background-color: #f8f8f8">
                    <p class="fs-4 text-primary" style="font-weight: bold; ">Detail Entertainment {{ $loop->index + 1 }}</p>
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Entertainment Type</label>
                            <select name="enter_type_e_detail[]" id="enter_type_e_detail_{{ $loop->index + 1 }}" class="form-select">
                                <option value="">-</option>
                                <option value="food" {{ $detail_dec['type'] == 'food' ? 'selected' : '' }}>Food/Beverages/Souvenir</option>
                                <option value="transport" {{ $detail_dec['type'] == 'transport' ? 'selected' : '' }}>Transport</option>
                                <option value="accommodation" {{ $detail_dec['type'] == 'accommodation' ? 'selected' : '' }}>Accommodation</option>
                                <option value="gift" {{ $detail_dec['type'] == 'gift' ? 'selected' : '' }}>Gift</option>
                                <option value="fund" {{ $detail_dec['type'] == 'fund' ? 'selected' : '' }}>Fund</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Amount</label>
                            <div class="input-group">
                                <div class="input-group-append">
                                    <span class="input-group-text">Rp</span>
                                </div>
                                <input class="form-control" name="nominal_e_detail[]"
                                    id="nominal_e_detail_{{ $loop->index + 1 }}"
                                    type="text" min="0" value="{{ number_format($detail_dec['nominal'], 0, ',', '.') }}"
                                    onfocus="this.value = this.value === '0' ? '' : this.value;"
                                    oninput="formatInputENT(this)">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Entertainment Fee Detail</label>
                            <textarea name="enter_fee_e_detail[]" class="form-control">{{ $detail_dec['fee_detail'] }}</textarea>
                        </div>
                    </div>
                    <br>
                    <div class="row mt-3">
                        <div class="d-flex justify-start w-100">
                            <button class="btn btn-danger mr-2" style="margin-right: 10px" onclick="clearFormDetail({{ $loop->index + 1 }}, event)">Reset</button>
                            <button class="btn btn-warning mr-2" onclick="removeFormDetail({{ $loop->index + 1 }}, event)">Delete</button>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    </div>

    <div class="mt-3">
        <button class="btn btn-primary" id="addMoreButtonDetail" onclick="addMoreFormDetailDec(event)">Add More</button>
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
@else
    <div id="form-container-detail">
        <div id="form-container-e-detail-1" class="card-body p-2 mb-3" style="background-color: #f8f8f8">
            <p class="fs-4 text-primary" style="font-weight: bold; ">Detail Entertainment 1</p>
            <div class="card-body bg-light p-2 mb-3">
                <p class="fs-5 text-primary" style="font-weight: bold;">Detail Entertainment Declaration</p>
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
                                oninput="formatInputENT(this)">
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
                        <button class="btn btn-danger mr-2" style="margin-right: 10px" onclick="clearFormDetail(1, event)">Reset</button>
                        <button class="btn btn-warning mr-2" onclick="removeFormDetail(1, event)">Delete</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-3">
        <button class="btn btn-primary" id="addMoreButtonDetail" onclick="addMoreFormDetailDec(event)">Add More</button>
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
@endif

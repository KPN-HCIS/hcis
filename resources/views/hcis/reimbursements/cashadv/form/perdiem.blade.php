<script>
    var formCount = 1;

    function addMoreFormPerdiem(event) {
        event.preventDefault();
        formCount++;

        const newForm = document.createElement("div");
        newForm.id = `form-container-bt-perdiem-${formCount}`;
        newForm.className = "card-body bg-light p-2 mb-3";
        newForm.innerHTML = `
            <div class="row">
                <!-- Company Code -->
                <div class="col-md-6 mb-2">
                    <label class="form-label" for="company_bt_perdiem${formCount}">Company Code</label>
                    <select class="form-control select2" id="company_bt_perdiem_${formCount}" name="company_bt_perdiem[]">
                        <option value="">Select Company...</option>
                        @foreach ($companies as $company)
                            <option value="{{ $company->contribution_level_code }}">
                                {{ $company->contribution_level . ' (' . $company->contribution_level_code . ')' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Location Agency -->
                <div class="col-md-6 mb-2">
                    <label class="form-label" for="locationFilter">Location Agency</label>
                    <select class="form-control location-select" name="location_bt_perdiem[]" id="location_bt_perdiem_${formCount}">
                        <option value="">Select Location...</option>
                        @foreach ($locations as $location)
                            <option value="{{ $location->area }}">
                                {{ $location->area . ' (' . $location->company_name . ')' }}
                            </option>
                        @endforeach
                        <option value="Others">Others</option>
                    </select>
                    <br>
                    <input type="text" name="other_location_bt_perdiem[]" class="form-control other-location" placeholder="Other Location" value="" style="display: none;">
                </div>
            </div>
            <div class="row">
                <!-- Start Perdiem -->
                <div class="col-md-4 mb-2">
                    <label class="form-label">Start Perdiem</label>
                    <input type="date" name="start_bt_perdiem[]" class="form-control start-perdiem" placeholder="mm/dd/yyyy" onchange="calculateTotalDaysPerdiem(this)">
                </div>

                <!-- End Perdiem -->
                <div class="col-md-4 mb-2">
                    <label class="form-label">End Perdiem</label>
                    <input type="date" name="end_bt_perdiem[]" class="form-control end-perdiem" placeholder="mm/dd/yyyy" onchange="calculateTotalDaysPerdiem(this)">
                </div>

                <!-- Total Days -->
                <div class="col-md-4 mb-2">
                    <label class="form-label">Total Days</label>
                    <div class="input-group">
                        <input class="form-control bg-light total-days-perdiem" name="total_days_bt_perdiem[]" type="number" value="0" readonly>
                        <div class="input-group-append">
                            <span class="input-group-text">days</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Amount -->
            <div class="mb-2">
                <label class="form-label">Amount</label>
            </div>
            <div class="input-group">
                <div class="input-group-append">
                    <span class="input-group-text">Rp</span>
                </div>
                <input class="form-control bg-light" name="nominal_bt_perdiem[]" id="nominal_bt_perdiem_${formCount}" type="text" value="0" onchange="onNominalChange()">
            </div>
            <br>

            <!-- Action Buttons -->
            <div class="row mt-3">
                <div class="d-flex justify-start w-100">
                    <button class="btn btn-danger mr-2" style="margin-right: 10px" onclick="clearFormPerdiem(${formCount}, event)">Clear</button>
                    <button class="btn btn-warning mr-2" onclick="removeFormPerdiem(${formCount}, event)">Remove</button>
                </div>
            </div>
        `;
        document.getElementById("form-container-perdiem").appendChild(newForm);
    }

    function removeFormPerdiem(index, event) {
        event.preventDefault();
        if (formCount > 0) {
            const nominalInput = document.querySelector(`#nominal_bt_perdiem_${index}`);
            if (nominalInput) {
                let nominalValue = cleanNumber(nominalInput.value);
                let total = cleanNumber(document.querySelector('input[name="total_bt_perdiem"]').value);
                total -= nominalValue;
                document.querySelector('input[name="total_bt_perdiem"]').value = formatNumber(total);
            }

            const formContainer = document.getElementById(`form-container-bt-perdiem-${index}`);
            if (formContainer) {
                formContainer.remove();
                formCount--;
                calculateTotalNominalBTTotal();
            }
        }
    }


    function clearFormPerdiem(index, event) {
        event.preventDefault();
        if (formCount > 0) {
            let nominalValue = cleanNumber(document.querySelector(`#nominal_bt_perdiem_${index}`).value);
            let total = cleanNumber(document.querySelector('input[name="total_bt_perdiem"]').value);
            total -= nominalValue;
            document.querySelector('input[name="total_bt_perdiem"]').value = formatNumber(total);

            let formContainer = document.getElementById(`form-container-bt-perdiem-${index}`);
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

            document.querySelector(`#nominal_bt_perdiem_${index}`).value = 0;
            calculateTotalNominalBTTotal();
        }
    }

    function calculateTotalNominalBTPerdiem() {
        let total = 0;
        document.querySelectorAll('input[name="nominal_bt_perdiem[]"]').forEach(input => {
            total += cleanNumber(input.value);
        });
        document.querySelector('input[name="total_bt_perdiem"]').value = formatNumber(total);
        calculateTotalNominalBTTotal();
    }

    function onNominalChange() {
        calculateTotalNominalBTPerdiem();
    }

    function calculateTotalDaysPerdiem(input) {
        const formGroup = input.closest('.row').parentElement;
        const startDateInput = formGroup.querySelector('input.start-perdiem');
        const endDateInput = formGroup.querySelector('input.end-perdiem');
        const totalDaysInput = formGroup.querySelector('input.total-days-perdiem');
        const perdiemInput = document.getElementById('perdiem');
        const allowanceInput = formGroup.querySelector('input[name="nominal_bt_perdiem[]"]');

        if (startDateInput.value && endDateInput.value) {
            const startDate = new Date(startDateInput.value);
            const endDate = new Date(endDateInput.value);

            if (!isNaN(startDate) && !isNaN(endDate) && startDate <= endDate) {
                const diffTime = Math.abs(endDate - startDate);
                const totalDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
                totalDaysInput.value = totalDays;

                const perdiem = parseFloat(perdiemInput.value) || 0;
                let allowance = totalDays * perdiem;

                const locationSelect = formGroup.querySelector('select[name="location_bt_perdiem[]"]');
                const otherLocationInput = formGroup.querySelector('input[name="other_location_bt_perdiem[]"]');

                if (locationSelect.value === "Others" || otherLocationInput.value.trim() !== '') {
                    allowance *= 1;
                } else {
                    allowance *= 0.5;
                }

                allowanceInput.value = formatNumberPerdiem(allowance);
                calculateTotalNominalBTPerdiem();
            } else {
                totalDaysInput.value = 0;
                allowanceInput.value = 0;
            }
        } else {
            totalDaysInput.value = 0;
            allowanceInput.value = 0;
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Use event delegation to handle changes on dynamically added select elements
        document.getElementById('form-container-perdiem').addEventListener('change', function(event) {
            if (event.target && event.target.classList.contains('location-select')) {
                toggleOthersBT(event.target);
            }
        });

        // Function to toggle the visibility of the 'Others' input field
        function toggleOthersBT(selectElement) {
            const formGroup = selectElement.closest('.mb-2').parentElement;
            const othersInput = formGroup.querySelector('input[name="other_location_bt_perdiem[]"]');
            if (selectElement.value === "Others") {
                othersInput.style.display = 'block';
                othersInput.required = true;
            } else {
                othersInput.style.display = 'none';
                othersInput.required = false;
                othersInput.value = "";
            }
        }

        // Add event listener to the existing select elements on page load
        document.querySelectorAll('.location-select').forEach(function(selectElement) {
            selectElement.addEventListener('change', function() {
                toggleOthersBT(this);
            });
        });
    });


</script>

@if (!empty($detailCA['detail_perdiem']) && $detailCA['detail_perdiem'][0]['start_date'] !== null)
    <div id="form-container-perdiem">
        @foreach ($detailCA['detail_perdiem'] as $perdiem)
            <div id="form-container-bt-perdiem-1" class="card-body bg-light p-2 mb-3" style="border-radius: 1%;">
                <div class="row">
                    <!-- Company Code -->
                    <div class="col-md-6 mb-2">
                        <label class="form-label" for="company_bt_perdiem1">Company Code</label>
                        <select class="form-control select2" id="company_bt_perdiem_1" name="company_bt_perdiem[]">
                            <option value="">Select Company...</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->contribution_level_code }}"
                                    @if($company->contribution_level_code == $perdiem['company_code']) selected @endif>
                                    {{ $company->contribution_level." (".$company->contribution_level_code.")" }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Location Agency -->
                    <div class="col-md-6 mb-2">
                        <label class="form-label" for="locationFilter">Location Agency</label>
                        <select class="form-control location-select" name="location_bt_perdiem[]" id="location_bt_perdiem[]">
                            <option value="">Select location...</option>
                            @foreach($locations as $location)
                                <option value="{{ $location->area }}"
                                    @if($location->area == $perdiem['location']) selected @endif>
                                    {{ $location->area." (".$location->company_name.")" }}
                                </option>
                            @endforeach
                            <option value="Others" @if('Others' == $perdiem['location']) selected @endif>Others</option>
                        </select>
                        @if($perdiem['location'] == 'Others')
                            <input type="text" name="other_location_bt_perdiem[]" class="form-control mt-3 other-location" placeholder="Other Location" value="{{ $perdiem['other_location'] }}">
                        @endif
                        <br>
                        <input type="text" name="other_location_bt_perdiem[]" class="form-control other-location" placeholder="Other Location" value="" style="display: none;">
                    </div>
                </div>
                <div class="row">
                    <!-- Start Perdiem -->
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Start Perdiem</label>
                        <input type="date" name="start_bt_perdiem[]" class="form-control start-perdiem" value="{{$perdiem['start_date']}}" placeholder="mm/dd/yyyy"
                            onchange="calculateTotalDaysPerdiem(this)">
                    </div>

                    <!-- End Perdiem -->
                    <div class="col-md-4 mb-2">
                        <label class="form-label">End Perdiem</label>
                        <input type="date" name="end_bt_perdiem[]" class="form-control end-perdiem" value="{{$perdiem['end_date']}}" placeholder="mm/dd/yyyy"
                            onchange="calculateTotalDaysPerdiem(this)">
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Total Days</label>
                        <div class="input-group">
                            <input class="form-control bg-light total-days-perdiem" name="total_days_bt_perdiem[]" type="number" value="0" readonly>
                            <div class="input-group-append">
                                <span class="input-group-text">days</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mb-2">
                    <label class="form-label">Amount</label>
                </div>
                <div class="input-group">
                    <div class="input-group-append">
                        <span class="input-group-text">Rp</span>
                    </div>
                    <input class="form-control bg-light" name="nominal_bt_perdiem[]" id="nominal_bt_perdiem_1   " type="text" value="{{$perdiem['total_days']}}" onchange="onNominalChange()">
                </div>
                <br>
                <div class="row mt-3">
                    <div class="d-flex justify-start w-100">
                        <button class="btn btn-danger mr-2" style="margin-right: 10px" onclick="clearFormPerdiem(1, event)">Clear</button>
                        <button class="btn btn-warning mr-2" onclick="removeFormPerdiem(1, event)">Remove</button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-3">
        <button class="btn btn-primary" onclick="addMoreFormPerdiem(event)">Add More</button>
    </div>

    <div class="mt-2">
        <label class="form-label">Total Perdiem</label>
        <div class="input-group">
            <div class="input-group-append">
                <span class="input-group-text">Rp</span>
            </div>
            <input class="form-control bg-light" name="total_bt_perdiem" id="total_bt_perdiem" type="text" value="0" readonly>
        </div>
    </div>
@else
    <div id="form-container-perdiem">
        <div id="form-container-bt-perdiem-1" class="card-body bg-light p-2 mb-3" style="border-radius: 1%;">
            <div class="row">
                <!-- Company Code -->
                <div class="col-md-6 mb-2">
                    <label class="form-label" for="company_bt_perdiem1">Company Code</label>
                    <select class="form-control select2" id="company_bt_perdiem_1" name="company_bt_perdiem[]">
                        <option value="">Select Company...</option>
                        @foreach ($companies as $company)
                            <option value="{{ $company->contribution_level_code }}">
                                {{ $company->contribution_level . ' (' . $company->contribution_level_code . ')' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Location Agency -->
                <div class="col-md-6 mb-2">
                    <label class="form-label" for="locationFilter">Location Agency</label>
                    <select class="form-control location-select" name="location_bt_perdiem[]" id="location_bt_perdiem_${formCount}">
                        <option value="">Select Location...</option>
                        @foreach ($locations as $location)
                            <option value="{{ $location->area }}">
                                {{ $location->area . ' (' . $location->company_name . ')' }}
                            </option>
                        @endforeach
                        <option value="Others">Others</option>
                    </select>
                    <br>
                    <input type="text" name="other_location_bt_perdiem[]" class="form-control other-location" placeholder="Other Location" value="" style="display: none;">
                </div>
            </div>
            <div class="row">
                <!-- Start Perdiem -->
                <div class="col-md-4 mb-2">
                    <label class="form-label">Start Perdiem</label>
                    <input type="date" name="start_bt_perdiem[]" class="form-control start-perdiem" placeholder="mm/dd/yyyy"
                        onchange="calculateTotalDaysPerdiem(this)">
                </div>

                <!-- End Perdiem -->
                <div class="col-md-4 mb-2">
                    <label class="form-label">End Perdiem</label>
                    <input type="date" name="end_bt_perdiem[]" class="form-control end-perdiem" placeholder="mm/dd/yyyy"
                        onchange="calculateTotalDaysPerdiem(this)">
                </div>
                <div class="col-md-4 mb-2">
                    <label class="form-label">Total Days</label>
                    <div class="input-group">
                        <input class="form-control bg-light total-days-perdiem" name="total_days_bt_perdiem[]" type="number" value="0" readonly>
                        <div class="input-group-append">
                            <span class="input-group-text">days</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mb-2">
                <label class="form-label">Amount</label>
            </div>
            <div class="input-group">
                <div class="input-group-append">
                    <span class="input-group-text">Rp</span>
                </div>
                <input class="form-control bg-light" name="nominal_bt_perdiem[]" id="nominal_bt_perdiem_1   " type="text" value="0" onchange="onNominalChange()">
            </div>
            <br>
            <div class="row mt-3">
                <div class="d-flex justify-start w-100">
                    <button class="btn btn-danger mr-2" style="margin-right: 10px" onclick="clearFormPerdiem(1, event)">Clear</button>
                    <button class="btn btn-warning mr-2" onclick="removeFormPerdiem(1, event)">Remove</button>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-3">
        <button class="btn btn-primary" onclick="addMoreFormPerdiem(event)">Add More</button>
    </div>

    <div class="mt-2">
        <label class="form-label">Total Perdiem</label>
        <div class="input-group">
            <div class="input-group-append">
                <span class="input-group-text">Rp</span>
            </div>
            <input class="form-control bg-light" name="total_bt_perdiem" id="total_bt_perdiem" type="text" value="0" readonly>
        </div>
    </div>
@endif

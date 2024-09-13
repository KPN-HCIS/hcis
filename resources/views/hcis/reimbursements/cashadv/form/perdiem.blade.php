<script>
    var formCount = 1;

    function addMoreFormPerdiem(event) {
        event.preventDefault();
        if (formCount < 3) {
            formCount++;
            document.getElementById(`form-container-bt-perdiem-${formCount}`).style.display = 'block';
        }
    }

    function removeFormPerdiem(index, event) {
        event.preventDefault();
        if (formCount > 1) {
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
            $(`#company_bt_perdiem_${index}`).val(null).trigger('change');

            document.querySelector(`#nominal_bt_perdiem_${index}`).value = 0;

            formContainer.style.display = 'none';
            formCount--;
            calculateTotalNominalBTTotal();
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
            $(`#company_bt_perdiem_${index}`).val(null).trigger('change');
            document.querySelector(`#nominal_bt_perdiem_${index}`).value = 0;
            calculateTotalNominalBTTotal();
        }
    }


    function calculateTotalNominalBTPerdiem() {
        let total = 0;
        document.querySelectorAll('input[name="nominal_bt_perdiem[]"]').forEach(input => {
            total += cleanNumber(input.value); // Gunakan cleanNumber untuk parsing
        });
        document.querySelector('input[name="total_bt_perdiem"]').value = formatNumber(total); // Tampilkan dengan format
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

        // Check if start and end dates are provided
        if (startDateInput.value && endDateInput.value) {
            const startDate = new Date(startDateInput.value);
            const endDate = new Date(endDateInput.value);

            if (!isNaN(startDate) && !isNaN(endDate) && startDate <= endDate) {
                const diffTime = Math.abs(endDate - startDate);
                const totalDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
                totalDaysInput.value = totalDays;

                const perdiem = parseFloat(perdiemInput.value) || 0;
                let allowance = totalDays * perdiem;

                // Check location for allowance percentage
                const locationSelect = formGroup.querySelector('select[name="location_bt_perdiem[]"]');
                const otherLocationInput = formGroup.querySelector('input[name="other_location_bt_perdiem[]"]');

                if (locationSelect.value === "Others" || otherLocationInput.value.trim() !== '') {
                    allowance *= 1; // allowance * 100%
                } else {
                    allowance *= 0.5; // allowance * 50%
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
        const formContainerBTPerdiem = document.getElementById(`form-container-bt-perdiem-${formCount}`);

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

        document.querySelectorAll('.location-select').forEach(function(selectElement) {
            selectElement.addEventListener('change', function() {
                toggleOthersBT(this);
            });
        });
    });

</script>

@for ($i = 1; $i <= 3; $i++)
    <div id="form-container-bt-perdiem-{{ $i }}" class="card-body bg-light p-2 mb-3" style="{{ $i > 1 ? 'display: none;' : '' }} border-radius: 1%;">
        <div class="row">
            <!-- Company Code -->
            <div class="col-md-6 mb-2">
                <label class="form-label" for="company_bt_perdiem{{$i}}">Company Code</label>
                <select class="form-control select2" id="company_bt_perdiem_{{$i}}" name="company_bt_perdiem[]">
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
                <select class="form-control location-select" name="location_bt_perdiem[]" id="location_bt_perdiem_{{$i}}">
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
            <input class="form-control bg-light" name="nominal_bt_perdiem[]" id="nominal_bt_perdiem_{{ $i }}" type="text" value="0" onchange="onNominalChange()">
        </div>
        <br>
        <div class="row mt-3">
            <div class="d-flex justify-start w-100">
                @if ($i > 0)
                    <button class="btn btn-danger mr-2" style="margin-right: 10px" id="form-container-bt-perdiem-{{ $i }}-cl" name="form-container-bt-perdiem-{{ $i }}" value="Clear" onclick="clearFormPerdiem({{ $i }}, event)">Clear</button>
                @endif
                @if ($i > 1)
                    <button class="btn btn-warning" id="form-container-bt-perdiem-{{ $i }}-no" name="form-container-bt-perdiem-{{ $i }}" value="Tidak" onclick="removeFormPerdiem({{ $i }}, event)">Remove</button>
                @endif
            </div>
        </div>
    </div>
@endfor

<div class="mt-3">
    <button class="btn btn-primary" id="form-container-bt-perdiem-{{ $i }}-yes" name="form-container-bt-perdiem-{{ $i }}" value="Ya" onclick="addMoreFormPerdiem(event)">Add More</button>
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

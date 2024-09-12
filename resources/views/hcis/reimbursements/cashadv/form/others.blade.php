<script>
    var formCount = 1;

    function addMoreFormLainnya(event) {
        event.preventDefault();
        if (formCount < 100) {
            formCount++;
            document.getElementById(`form-container-bt-lainnya-${formCount}`).style.display = 'block';
        }
    }

    function removeFormLainnya(index, event) {
        event.preventDefault();
        if (formCount > 1) {
            let nominalValue = cleanNumber(document.querySelector(`#nominal_bt_lainnya_${index}`).value);
            let total = cleanNumber(document.querySelector('input[name="total_bt_lainnya"]').value);
            total -= nominalValue;
            document.querySelector('input[name="total_bt_lainnya"]').value = formatNumber(total);

            // Clear the form inputs for cleanliness
            let formContainer = document.getElementById(`form-container-bt-lainnya-${index}`);
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

            // Remove the form container from the DOM
            formContainer.style.display = 'none';
            formCount--;

            // Reset nilai nominal di form yang disembunyikan (optional)
            document.querySelector(`#nominal_bt_lainnya_${index}`).value = 0;
            calculateTotalNominalBTTotal();
        }
    }

    function clearFormLainnya(index, event) {
        event.preventDefault();
        if (formCount > 0) {
            let nominalValue = cleanNumber(document.querySelector(`#nominal_bt_lainnya_${index}`).value);
            let total = cleanNumber(document.querySelector('input[name="total_bt_lainnya"]').value);
            total -= nominalValue;
            document.querySelector('input[name="total_bt_lainnya"]').value = formatNumber(total);

            // Clear the form inputs for cleanliness
            let formContainer = document.getElementById(`form-container-bt-lainnya-${index}`);
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

            document.querySelector(`#nominal_bt_lainnya_${index}`).value = 0;
            calculateTotalNominalBTTotal();
        }
    }

    function calculateTotalNominalBTLainnya() {
        let total = 0;
        document.querySelectorAll('input[name="nominal_bt_lainnya[]"]').forEach(input => {
            total += cleanNumber(input.value);
        });
        document.querySelector('input[name="total_bt_lainnya"]').value = formatNumber(total);
    }

    function onNominalChange() {
        calculateTotalNominalBTLainnya();
    }
</script>

@for ($i = 1; $i <= 100; $i++)
    <div id="form-container-bt-lainnya-{{ $i }}" class="card-body bg-light p-2 mb-3" style="{{ $i > 1 ? 'display: none;' : '' }} border-radius: 1%;">
        <div class="row">
            <!-- Lainnya Date -->
            <div class="col-md-6 mb-2">
                <label class="form-label">Date</label>
                <input type="date" name="tanggal_bt_lainnya[]"
                    class="form-control" placeholder="mm/dd/yyyy">
            </div>
            <div class="col-md-6 mb-2">
                <label class="form-label">Amount</label>
                <div class="input-group mb-3">
                    <div class="input-group-append">
                        <span class="input-group-text">Rp</span>
                    </div>
                    <input class="form-control"
                        name="nominal_bt_lainnya[]"
                        id="nominal_bt_lainnya_{{ $i }}" type="text"
                        min="0" value="0"
                        onfocus="this.value = this.value === '0' ? '' : this.value;"
                        oninput="formatInput(this)"
                        onblur="formatOnBlur(this)">
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
        <br>
        <div class="row mt-3">
            <div class="d-flex justify-start w-100">
                @if ($i > 0)
                    <button class="btn btn-danger mr-2" style="margin-right: 10px" id="form-container-bt-lainnya-{{ $i }}-cl" name="form-container-bt-lainnya-{{ $i }}" value="Clear" onclick="clearFormLainnya({{ $i }}, event)">Clear</button>
                @endif
                @if ($i > 1)
                    <button class="btn btn-warning mr-2" id="form-container-bt-lainnya-{{ $i }}-no" name="form-container-bt-lainnya-{{ $i }}" value="Tidak" onclick="removeFormLainnya({{ $i }}, event)">Remove</button>
                @endif
            </div>
        </div>
    </div>
@endfor

<div class="mt-3">
    <button class="btn btn-primary" id="form-container-bt-lainnya-{{ $i }}-yes" name="form-container-bt-lainnya-{{ $i }}" value="Ya" onclick="addMoreFormLainnya(event)">Add More</button>
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

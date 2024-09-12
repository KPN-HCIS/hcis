<script>
    var formCount = 1;

    function addMoreFormNBT(event) {
        event.preventDefault();
        if (formCount < 20) {
            formCount++;
            document.getElementById(`form-container-nbt-${formCount}`).style.display = 'block';
        }
    }

    function removeFormNBT(index, event) {
        event.preventDefault();
        if (formCount > 1) {
            adjustTotalCA(index);
            clearFormInputs(index);
            document.getElementById(`form-container-nbt-${index}`).style.display = 'none';
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
        let nominalValue = cleanNumber(document.querySelector(`#nominal_nbt_${index}`).value);
        let totalCA = cleanNumber(document.querySelector('input[name="totalca"]').value) || 0;
        totalCA -= nominalValue;
        document.querySelector('input[name="totalca"]').value = formatNumber(totalCA);
    }

    function clearFormInputs(index) {
        let formContainer = document.getElementById(`form-container-nbt-${index}`);

        formContainer.querySelectorAll('input[type="text"], input[type="date"], input[type="number"]').forEach(input => {
            input.value = (input.type === 'number') ? 0 : '';
        });

        formContainer.querySelectorAll('select').forEach(select => {
            select.selectedIndex = 0;
        });

        formContainer.querySelectorAll('textarea').forEach(textarea => {
            textarea.value = '';
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        function formatInput(input) {
            let value = input.value.replace(/\./g, '');
            let formattedValue = formatNumber(isNaN(parseFloat(value)) ? 0 : Math.floor(parseFloat(value)));
            input.value = formattedValue;
            calculateTotalNominal();
        }

        function calculateTotalNominal() {
            let total = Array.from(document.querySelectorAll('input[name="nominal_nbt[]"]'))
                .reduce((acc, input) => acc + parseNumber(input.value), 0);
            document.getElementById('totalca').value = formatNumber(total);
        }

        // Attach input event to the existing nominal fields
        document.querySelectorAll('input[name="nominal_nbt[]"]').forEach(input => {
            input.addEventListener('input', function() {
                formatInput(this);
            });
        });

        calculateTotalNominal();
    });

</script>

@for ($i = 1; $i <= 100; $i++)
    <div id="form-container-nbt-{{ $i }}" class="card-body bg-light p-2 mb-3" style="{{ $i > 1 ? 'display: none;' : '' }} border-radius: 1%;">
        <div class="row">
            <div class="col-md-6 mb-2">
                <label class="form-label">Date</label>
                <input type="date" name="tanggal_nbt[]"
                    class="form-control" placeholder="mm/dd/yyyy">
            </div>
            <div class="col-md-6 mb-2">
                <label class="form-label">Amount</label>
                <div class="input-group mb-3">
                    <div class="input-group-append">
                        <span class="input-group-text">Rp</span>
                    </div>
                    <input class="form-control" name="nominal_nbt[]"
                        id="nominal_nbt_{{ $i }}" type="text"
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
                    <textarea name="keterangan_nbt[]" class="form-control"></textarea>
                </div>
            </div>
        </div>
        <br>
        <div class="row mt-3">
            <div class="d-flex justify-start w-100">
                @if ($i > 0)
                    <button class="btn btn-danger mr-2" style="margin-right: 10px" id="form-container-nbt-{{ $i }}-cl" name="form-container-nbt-{{ $i }}" value="Clear" onclick="clearFormNBT({{ $i }}, event)">Clear</button>
                @endif
                @if ($i > 1)
                    <button class="btn btn-warning mr-2" id="form-container-nbt-{{ $i }}-no" name="form-container-nbt-{{ $i }}" value="Tidak" onclick="removeFormNBT({{ $i }}, event)">Remove</button>
                @endif
            </div>
        </div>
    </div>
@endfor

<div class="mt-3">
    <button class="btn btn-primary" id="form-container-nbt-{{ $i }}-yes" name="form-container-nbt-{{ $i }}" value="Ya" onclick="addMoreFormNBT(event)">Add More</button>
</div>

<script>
    var formCount = 1;

    function addMoreFormLainnya(event) {
        event.preventDefault();
        if (formCount < 5) {
            formCount++;
            document.getElementById(`form-container-bt-lainnya-${formCount}`).style.display = 'block';
        }
    }

    function removeFormLainnya(index, event) {
        event.preventDefault();
        if (formCount > 1) {
            // Ambil nilai nominal dari form yang akan dihapus
            let nominalValue = cleanNumber(document.querySelector(`#nominal_bt_lainnya_${index}`).value);

            // Kurangi nilai nominal dari total
            let total = cleanNumber(document.querySelector('input[name="total_bt_lainnya"]').value);
            total -= nominalValue;
            document.querySelector('input[name="total_bt_lainnya"]').value = formatNumber(total);

            // Sembunyikan form
            document.getElementById(`form-container-bt-lainnya-${index}`).style.display = 'none';
            formCount--;

            // Reset nilai nominal di form yang disembunyikan (optional)
            document.querySelector(`#nominal_bt_lainnya_${index}`).value = 0;
        }
    }

    function calculateTotalNominalBTLainnya() {
        let total = 0;
        document.querySelectorAll('input[name="nominal_bt_lainnya[]"]').forEach(input => {
            total += cleanNumber(input.value); // Gunakan cleanNumber untuk parsing
        });
        document.querySelector('input[name="total_bt_lainnya"]').value = formatNumber(total); // Tampilkan dengan format
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
                        id="nominal_bt_lainnya" type="text"
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
        @if ($i > 1)
            <div class="mt-3">
                <div class="d-flex justify-content-end">
                    <button class="btn btn-warning mr-2" id="form-container-bt-lainnya-{{ $i }}-no" name="form-container-bt-lainnya-{{ $i }}" value="Tidak" onclick="removeFormLainnya({{ $i }}, event)">Remove</button>
                </div>
            </div>
        @endif
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
        <input class="form-control bg-light"
            name="total_bt_lainnya"
            id="total_bt_lainnya" type="text"
            min="0" value="0" readonly>
    </div>
</div>

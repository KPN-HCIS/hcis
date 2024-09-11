<script>
    var formCount = 1;

    function addMoreFormTransport(event) {
        event.preventDefault();
        if (formCount < 5) {
            formCount++;
            document.getElementById(`form-container-bt-transport-${formCount}`).style.display = 'block';
        }
    }

    function removeFormTransport(index, event) {
        event.preventDefault();
        if (formCount > 1) {
            // Ambil nilai nominal dari form yang akan dihapus
            let nominalValue = cleanNumber(document.querySelector(`#nominal_bt_transport_${index}`).value);

            // Kurangi nilai nominal dari total
            let total = cleanNumber(document.querySelector('input[name="total_bt_transport"]').value);
            total -= nominalValue;
            document.querySelector('input[name="total_bt_transport"]').value = formatNumber(total);

            // Sembunyikan form
            document.getElementById(`form-container-bt-transport-${index}`).style.display = 'none';
            formCount--;

            // Reset nilai nominal di form yang disembunyikan (optional)
            document.querySelector(`#nominal_bt_transport_${index}`).value = 0;
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

@for ($i = 1; $i <= 100; $i++)
    <div id="form-container-bt-transport-{{ $i }}" class="card-body bg-light p-2 mb-3" style="{{ $i > 1 ? 'display: none;' : '' }} border-radius: 1%;">
        <div class="row">
            <!-- Transport Date -->
            <div class="col-md-4 mb-2">
                <label class="form-label">Transport Date</label>
                <input type="date" name="tanggal_bt_transport[]" class="form-control" placeholder="mm/dd/yyyy">
            </div>
            <div class="col-md-4 mb-2">
                <label class="form-label" for="name">Company Code</label>
                <select class="form-control select2" id="company_bt_transport_{{ $i }}" name="company_bt_transport[]">
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
                           id="nominal_bt_transport_{{ $i }}"
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
        @if ($i > 1)
            <div class="mt-3">
                <div class="d-flex justify-content-end">
                    <button class="btn btn-warning mr-2" id="form-container-bt-transport-{{ $i }}-no" name="form-container-bt-transport-{{ $i }}" value="Tidak" onclick="removeFormTransport({{ $i }}, event)">Remove</button>
                </div>
            </div>
        @endif
    </div>
@endfor

<div class="mt-3">
    <button class="btn btn-primary" id="form-container-bt-transport-{{ $i }}-yes" name="form-container-bt-transport-{{ $i }}" value="Ya" onclick="addMoreFormTransport(event)">Add More</button>
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

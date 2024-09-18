<script>
    var formCount = 0;

    window.addEventListener('DOMContentLoaded', function() {
        formCount = document.querySelectorAll('#form-container-transport > div').length;
    });

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
                        <textarea name="keterangan_bt_transport[]" class="form-control" placeholder="Write your information ..."></textarea>
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

    $('.btn-warning').click(function(event) {
        event.preventDefault();
        var index = $(this).closest('.card-body').index() + 1;
        removeFormTransport(index, event);
    });

    function removeFormTransport(index, event) {
        event.preventDefault();
        if (formCount > 0) {
            const formContainer = document.getElementById(`form-container-bt-transport-${index}`);
            if (formContainer) {
                const nominalInput = formContainer.querySelector(`#nominal_bt_transport_${index}`);
                if (nominalInput) {
                    let nominalValue = cleanNumber(nominalInput.value);
                    let total = cleanNumber(document.querySelector('input[name="total_bt_transport"]').value);
                    total -= nominalValue;
                    document.querySelector('input[name="total_bt_transport"]').value = formatNumber(total);
                    calculateTotalNominalBTTotal();
                }
                $(`#form-container-bt-transport-${index}`).remove();
                formCount--;
            }
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

@if (!empty($detailCA['detail_transport']) && $detailCA['detail_transport'][0]['tanggal'] !== null)
    <div id="form-container-transport">
        @foreach ($detailCA['detail_transport'] as $transport)
            <div id="form-container-bt-transport-{{ $loop->index + 1 }}" class="card-body bg-light p-2 mb-3" style="border-radius: 1%;">
                <div class="row">
                    <!-- Transport Date -->
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Transport Date</label>
                        <input type="date" name="tanggal_bt_transport[]" class="form-control" value="{{$transport['tanggal']}}" placeholder="mm/dd/yyyy">
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label" for="name">Company Code</label>
                        <select class="form-control select2" id="company_bt_transport_{{ $loop->index + 1 }}" name="company_bt_transport[]">
                            <option value="">Select Company...</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->contribution_level_code }}"
                                    @if($company->contribution_level_code == $transport['company_code']) selected @endif>
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
                                    name="nominal_bt_transport[]"
                                    id="nominal_bt_transport_{{ $loop->index + 1 }}"
                                    type="text"
                                    min="0"
                                    value="{{number_format($transport['nominal'], 0, ',', '.') }}"
                                    onfocus="this.value = this.value === '0' ? '' : this.value;"
                                    oninput="formatInput(this)"
                                    onblur="formatOnBlur(this)">
                        </div>
                    </div>

                    <!-- Information -->
                    <div class="col-md-12 mb-2">
                        <div class="mb-2">
                            <label class="form-label">Information</label>
                            <textarea name="keterangan_bt_transport[]" class="form-control" placeholder="Write your information ...">{{$transport['keterangan']}}</textarea>
                        </div>
                    </div>
                </div>
                <br>
                <div class="row mt-3">
                    <div class="d-flex justify-start w-100">
                        <button class="btn btn-danger mr-2" style="margin-right: 10px" onclick="clearFormTransport({{ $loop->index + 1 }}, event)">Clear</button>
                        <button class="btn btn-warning mr-2" onclick="removeFormTransport({{ $loop->index + 1 }}, event)">Remove</button>
                    </div>
                </div>
            </div>
        @endforeach
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
                min="0" value="{{ number_format(array_sum(array_column($detailCA['detail_transport'], 'nominal')), 0, ',', '.') }}" readonly>
        </div>
    </div>
@else
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
                        <textarea name="keterangan_bt_transport[]" placeholder="Write your information ..." class="form-control"></textarea>
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
@endif


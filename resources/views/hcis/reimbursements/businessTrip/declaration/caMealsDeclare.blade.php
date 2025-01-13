<script src="{{ asset('/js/btCashAdvanced/meals.js') }}"></script>
<script>
    var formCountMeals = 0;

    window.addEventListener('DOMContentLoaded', function() {
        formCountMeals = document.querySelectorAll('#form-container-meals > div').length;
    });

    function addMoreFormMealsDec(event) {
        event.preventDefault();
        formCountMeals++;
        const newForm = document.createElement("div");
        newForm.id = `form-container-bt-meals-${formCountMeals}`;
        newForm.className = "card-body p-2 mb-3";
        newForm.style.backgroundColor = "#f8f8f8";
        newForm.innerHTML = `
                <p class="fs-4 text-primary" style="font-weight: bold; ">Meals ${formCountMeals}</p>
                <div class="card-body bg-light p-2 mb-3">
                    <p class="fs-5 text-primary" style="font-weight: bold;">Meals Declaration</p>
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Date</label>
                            <input type="date" name="tanggal_bt_meals[]" class="form-control" placeholder="mm/dd/yyyy">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Amount</label>
                            <div class="input-group mb-3">
                                <div class="input-group-append">
                                    <span class="input-group-text">Rp</span>
                                </div>
                                <input class="form-control" name="nominal_bt_meals[]" id="nominal_bt_meals_${formCountMeals}" type="text" min="0" value="0" onfocus="this.value = this.value === '0' ? '' : this.value;" oninput="formatInput(this)" onblur="formatOnBlur(this)">
                            </div>
                        </div>
                        <div class="col-md-12 mb-2">
                            <div class="mb-2">
                                <label class="form-label">Information</label>
                                <textarea name="keterangan_bt_meals[]" class="form-control" placeholder="Write your information ..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="d-flex justify-start w-100">
                            <button class="btn btn-outline-warning btn-sm" style="margin-right: 10px" onclick="clearFormMeals(${formCountMeals}, event)">Reset</button>
                            <button class="btn btn-outline-primary btn-sm" onclick="removeFormMeals(${formCountMeals}, event)">Delete</button>
                        </div>
                    </div>
                </div>
            `;
        document.getElementById("form-container-meals").appendChild(newForm);
    }

    function addMoreFormMealsReq(event) {
        event.preventDefault();
        formCountMeals++;
        const newForm = document.createElement("div");
        newForm.id = `form-container-bt-meals-${formCountMeals}`;
        newForm.className = "card-body p-2 mb-3";
        newForm.style.backgroundColor = "#f8f8f8";
        newForm.innerHTML = `
                <p class="fs-4 text-primary" style="font-weight: bold; ">Meals ${formCountMeals}</p>
                <div class="card-body bg-light p-2 mb-3">
                    <p class="fs-5 text-primary" style="font-weight: bold;">Meals Declaration</p>
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Date</label>
                            <input type="date" name="tanggal_bt_meals[]" class="form-control" placeholder="mm/dd/yyyy">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Amount</label>
                            <div class="input-group mb-3">
                                <div class="input-group-append">
                                    <span class="input-group-text">Rp</span>
                                </div>
                                <input class="form-control" name="nominal_bt_meals[]" id="nominal_bt_meals_${formCountMeals}" type="text" min="0" value="0" onfocus="this.value = this.value === '0' ? '' : this.value;" oninput="formatInput(this)" onblur="formatOnBlur(this)">
                            </div>
                        </div>
                        <div class="col-md-12 mb-2">
                            <div class="mb-2">
                                <label class="form-label">Information</label>
                                <textarea name="keterangan_bt_meals[]" class="form-control" placeholder="Write your information ..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="d-flex justify-start w-100">
                            <button class="btn btn-outline-warning btn-sm" style="margin-right: 10px" onclick="clearFormMeals(${formCountMeals}, event)">Reset</button>
                            <button class="btn btn-outline-primary btn-sm" onclick="removeFormMeals(${formCountMeals}, event)">Delete</button>
                        </div>
                    </div>
                </div>
            `;
        document.getElementById("form-container-meals").appendChild(newForm);
    }

    $('.btn-warning').click(function(event) {
        event.preventDefault();
        var index = $(this).closest('.card-body').index() + 1;
        removeFormMeals(index, event);
    });

    function removeFormMeals(index, event) {
        event.preventDefault();
        if (formCountMeals > 0) {
            const formContainer = document.getElementById(`form-container-bt-meals-${index}`);
            if (formContainer) {
                const nominalInput = formContainer.querySelector(`#nominal_bt_meals_${index}`);
                if (nominalInput) {
                    let nominalValue = cleanNumber(nominalInput.value);
                    let total = cleanNumber(document.querySelector('input[name="total_bt_meals"]').value);
                    total -= nominalValue;
                    document.querySelector('input[name="total_bt_meals"]').value = formatNumber(total);
                    calculateTotalNominalBTTotal();
                }
                $(`#form-container-bt-meals-${index}`).remove();
                formCountMeals--;
            }
        }
    }

    function clearFormMeals(index, event) {
        event.preventDefault();
        let nominalValue = cleanNumber(document.querySelector(`#nominal_bt_meals_${index}`).value);
        let total = cleanNumber(document.querySelector('input[name="total_bt_meals"]').value);
        total -= nominalValue;
        document.querySelector('input[name="total_bt_meals"]').value = formatNumber(total);

        // Clear the inputs
        const formContainer = document.getElementById(`form-container-bt-meals-${index}`);
        formContainer.querySelectorAll('input[type="text"], input[type="date"]').forEach((input) => {input.value = "";});
        formContainer.querySelector("textarea").value = "";

        // Reset nilai untuk nominal BT meals
        document.querySelector(`#nominal_bt_meals_${index}`).value = 0;
        calculateTotalNominalBTTotal();
    }

    function calculateTotalNominalBTMeals() {
        let total = 0;
        document.querySelectorAll('input[name="nominal_bt_meals[]"]').forEach(input => {
            total += cleanNumber(input.value);
        });
        document.getElementById("total_bt_meals").value = formatNumber(total);
    }

    function onNominalChange() {
        calculateTotalNominalBTMeals();
    }

</script>

@if (!empty($detailCA['detail_meals']) && $detailCA['detail_meals'][0]['tanggal'] !== null)
    <div id="form-container-meals">
        @foreach ($detailCA['detail_meals'] as $index => $meals)
            <div id="form-container-bt-meals-{{ $loop->index + 1 }}" class="p-2 mb-3 rounded-3" style="background-color: #f8f8f8">
                <p class="fs-4 text-primary" style="font-weight: bold; ">Meals {{ $loop->index + 1 }}</p>
                <div id="form-container-bt-meals-req-{{ $loop->index + 1 }}" class="card-body bg-light p-2 mb-3" style="border-radius: 1%;">
                    <p class="fs-5 text-primary" style="font-weight: bold;">Meals Request</p>
                    <div class="row">
                        <div class="col-md-6">
                            <table width="100%">
                                <tr>
                                    <th width="40%">Date</th>
                                    <td class="block">:</td>
                                    <td>{{ $meals['tanggal'] }}</td>
                                </tr>
                                <tr>
                                    <th>Amount</th>
                                    <td class="block">:</td>
                                    <td> Rp {{ number_format($meals['nominal'], 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <th>Information</th>
                                    <td class="block">:</td>
                                    <td>{{ $meals['keterangan'] }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div id="form-container-bt-meals-dec-{{ $loop->index + 1 }}" class="card-body bg-light p-2 mb-3">
                    <p class="fs-5 text-primary" style="font-weight: bold; ">Meals Declaration</p>
                    @if (isset($declareCA['detail_meals'][$index]))
                        @php
                            $meals_dec = $declareCA['detail_meals'][$index];
                        @endphp
                        <div class="row">
                            <!-- meals Date -->
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Date</label>
                                <input type="date" name="tanggal_bt_meals[]" class="form-control" value="{{$meals_dec['tanggal']}}" placeholder="mm/dd/yyyy">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Amount</label>
                                <div class="input-group mb-3">
                                    <div class="input-group-append">
                                        <span class="input-group-text">Rp</span>
                                    </div>
                                    <input class="form-control" name="nominal_bt_meals[]" id="nominal_bt_meals_{{ $loop->index + 1 }}" type="text" min="0" value="{{ number_format($meals_dec['nominal'], 0, ',', '.') }}" onfocus="this.value = this.value === '0' ? '' : this.value;" oninput="formatInput(this)" onblur="formatOnBlur(this)">
                                </div>
                            </div>

                            <!-- Information -->
                            <div class="col-md-12 mb-2">
                                <div class="mb-2">
                                    <label class="form-label">Information</label>
                                    <textarea name="keterangan_bt_meals[]" class="form-control" placeholder="Write your information ...">{{ $meals_dec['keterangan'] }}</textarea>
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="row mt-3">
                        <div class="d-flex justify-start w-100">
                            <button class="btn btn-outline-warning btn-sm" style="margin-right: 10px" onclick="clearFormMeals({{ $loop->index + 1 }}, event)">Reset</button>
                            {{-- <button class="btn btn-warning mr-2" onclick="removeFormMeals({{ $loop->index + 1 }}, event)">Delete</button> --}}
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
        @foreach ($declareCA['detail_meals'] as $index => $meals_dec)
            @if (!isset($detailCA['detail_meals'][$index]))
                <div id="form-container-bt-meals-{{ $loop->index + 1 }}" class="p-2 mb-3 rounded-3" style="background-color: #f8f8f8">
                    <p class="fs-4 text-primary" style="font-weight: bold; ">Meals {{ $loop->index + 1 }}</p>
                    <div class="fs-5 bg-light text-primary p-2">
                        <p class="fs-5 text-primary" style="font-weight: bold; ">Meals Declaration</p>
                        <div class="row">
                            <!-- meals Date -->
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Date</label>
                                <input type="date" name="tanggal_bt_meals[]" class="form-control" value="{{$meals_dec['tanggal']}}" placeholder="mm/dd/yyyy">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Amount</label>
                                <div class="input-group mb-3">
                                    <div class="input-group-append">
                                        <span class="input-group-text">Rp</span>
                                    </div>
                                    <input class="form-control" name="nominal_bt_meals[]" id="nominal_bt_meals_{{ $loop->index + 1 }}" type="text" min="0" value="{{ number_format($meals_dec['nominal'], 0, ',', '.') }}" onfocus="this.value = this.value === '0' ? '' : this.value;" oninput="formatInput(this)" onblur="formatOnBlur(this)">
                                </div>
                            </div>

                            <!-- Information -->
                            <div class="col-md-12 mb-2">
                                <div class="mb-2">
                                    <label class="form-label">Information</label>
                                    <textarea name="keterangan_bt_meals[]" class="form-control">{{ $meals_dec['keterangan'] }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="d-flex justify-start w-100">
                                <button class="btn btn-outline-warning btn-sm" style="margin-right: 10px" onclick="clearFormMeals({{ $loop->index + 1 }}, event)">Reset</button>
                                <button class="btn btn-outline-primary btn-sm" onclick="removeFormMeals({{ $loop->index + 1 }}, event)">Delete</button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    </div>

    <div class="mt-1">
        <button class="btn btn-primary btn-sm" id="addMoreButtonLainnya" onclick="addMoreFormMealsDec(event)">Add More</button>
    </div>

    <div class="mt-2 mb-2">
        <label class="form-label">Total Meals</label>
        <div class="input-group">
            <div class="input-group-append">
                <span class="input-group-text">Rp</span>
            </div>
            <input class="form-control bg-light" name="total_bt_meals" id="total_bt_meals" type="text" min="0" value="{{ number_format(array_sum(array_column($declareCA['detail_meals'], 'nominal')), 0, ',', '.') }}" readonly>
        </div>
    </div>
@elseif (!empty($declareCA['detail_meals']) && $declareCA['detail_meals'][0]['nominal'] !== null)
    <div id="form-container-meals">
        @foreach ($declareCA['detail_meals'] as $index => $meals_dec)
            @if (!isset($detailCA['detail_meals'][$index]))
                <div id="form-container-bt-meals-{{ $loop->index + 1 }}" class="card-body p-2 mb-3" style="background-color: #f8f8f8">
                    <p class="fs-4 text-primary" style="font-weight: bold; ">Meals {{ $loop->index + 1 }}</p>
                    <div class="card-body bg-light p-2 mb-3">
                        <p class="fs-5 text-primary" style="font-weight: bold;">Meals Declaration</p>
                        <div class="row">
                            <!-- meals Date -->
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Date</label>
                                <input type="date" name="tanggal_bt_meals[]" class="form-control" value="{{$meals_dec['tanggal']}}" placeholder="mm/dd/yyyy">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Amount</label>
                                <div class="input-group mb-3">
                                    <div class="input-group-append">
                                        <span class="input-group-text">Rp</span>
                                    </div>
                                    <input class="form-control" name="nominal_bt_meals[]" id="nominal_bt_meals_{{ $loop->index + 1 }}" type="text" min="0" value="{{ number_format($meals_dec['nominal'], 0, ',', '.') }}" onfocus="this.value = this.value === '0' ? '' : this.value;" oninput="formatInput(this)" onblur="formatOnBlur(this)">
                                </div>
                            </div>

                            <!-- Information -->
                            <div class="col-md-12 mb-2">
                                <div class="mb-2">
                                    <label class="form-label">Information</label>
                                    <textarea name="keterangan_bt_meals[]" class="form-control">{{ $meals_dec['keterangan'] }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="d-flex justify-start w-100">
                                <button class="btn btn-outline-warning btn-sm" style="margin-right: 10px" onclick="clearFormMeals({{ $loop->index + 1 }}, event)">Reset</button>
                                <button class="btn btn-outline-primary btn-sm" onclick="removeFormMeals({{ $loop->index + 1 }}, event)">Delete</button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    </div>

    <div class="mt-3">
        <button class="btn btn-primary" id="addMoreButtonLainnya" onclick="addMoreFormMealsDec(event)">Add More</button>
    </div>

    <div class="mt-2 mb-2">
        <label class="form-label">Total Meals</label>
        <div class="input-group">
            <div class="input-group-append">
                <span class="input-group-text">Rp</span>
            </div>
            <input class="form-control bg-light" name="total_bt_meals" id="total_bt_meals" type="text" min="0" value="{{ number_format(array_sum(array_column($declareCA['detail_meals'], 'nominal')), 0, ',', '.') }}" readonly>
        </div>
    </div>
@else
    <div id="form-container-meals">
        <div id="form-container-bt-meals-1" class="card-body p-2 mb-3" style="background-color: #f8f8f8">
            <p class="fs-4 text-primary" style="font-weight: bold; ">Meals 1</p>
            <div class="card-body bg-light p-2 mb-3">
                <p class="fs-5 text-primary" style="font-weight: bold;">Meals Declaration</p>
                <div class="row">
                    <!-- meals Date -->
                    <div class="col-md-6 mb-2">
                        <label class="form-label">Date</label>
                        <input type="date" name="tanggal_bt_meals[]" class="form-control" placeholder="mm/dd/yyyy">
                    </div>
                    <div class="col-md-6 mb-2">
                        <label class="form-label">Amount</label>
                        <div class="input-group mb-3">
                            <div class="input-group-append">
                                <span class="input-group-text">Rp</span>
                            </div>
                            <input class="form-control" name="nominal_bt_meals[]" id="nominal_bt_meals_1" type="text" min="0" value="0" onfocus="this.value = this.value === '0' ? '' : this.value;" oninput="formatInput(this)" onblur="formatOnBlur(this)">
                        </div>
                    </div>

                    <!-- Information -->
                    <div class="col-md-12 mb-2">
                        <div class="mb-2">
                            <label class="form-label">Information</label>
                            <textarea name="keterangan_bt_meals[]" class="form-control" placeholder="Write your information ..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="d-flex justify-start w-100">
                        <button class="btn btn-outline-warning btn-sm" style="margin-right: 10px" onclick="clearFormMeals(1, event)">Reset</button>
                        <button class="btn btn-outline-primary btn-sm" onclick="removeFormMeals(1, event)">Delete</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-3">
        <button class="btn btn-primary" id="addMoreButton" onclick="addMoreFormMealsDec(event)">Add More</button>
    </div>

    <div class="mt-2 mb-2">
        <label class="form-label">Total Meals</label>
        <div class="input-group">
            <div class="input-group-append">
                <span class="input-group-text">Rp</span>
            </div>
            <input class="form-control bg-light" name="total_bt_meals" id="total_bt_meals" type="text" min="0" value="0" readonly>
        </div>
    </div>
@endif


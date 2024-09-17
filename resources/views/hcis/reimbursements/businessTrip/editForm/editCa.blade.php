<div class="col-md-12">
    <div class="table-responsive-sm">
        <div class="d-flex flex-column gap-2">
            <div class="card">

                <div id="perdiem-card" class="card-body" style="display:">
                    <div class="accordion" id="accordionPerdiem">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="enter-headingOne">
                                <button
                                    @if (count($detailPerdiem) > 0) class="accordion-button @if ($detailPerdiem[0]['start_date'] === null) collapsed @endif
                                    fw-medium" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#enter-collapseOne"
                                    aria-expanded="@if ($detailPerdiem[0]['start_date'] === null) true @else false @endif"
                                aria-controls="enter-collapseOne" @else class="accordion-button collapsed fw-medium"
                                    type="button" data-bs-toggle="collapse" data-bs-target="#enter-collapseOne"
                                    aria-expanded="false" aria-controls="enter-collapseOne" @endif
                                    >
                                    Perdiem Plan
                                </button>
                            </h2>
                            <div id="enter-collapseOne"
                                @if (count($detailPerdiem) > 0) class="accordion-collapse @if ($detailPerdiem[0]['start_date'] === null) collapse @else show @endif"
                            @else class="accordion-collapse collapse" @endif
                                aria-labelledby="enter-headingOne">
                                <div class="accordion-body">
                                    <div id="form-container-bt-perdiem">
                                        @if (!empty($detailPerdiem))
                                            @foreach ($detailPerdiem as $index => $perdiem)
                                                <div class="mb-2">
                                                    <label class="form-label">Start
                                                        Perdiem</label>
                                                    <input type="date" name="start_bt_perdiem[]"
                                                        class="form-control start-perdiem"
                                                        value="{{ old('start_bt_perdiem.' . $index, $perdiem['start_date'] ?? '') }}">
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label">End
                                                        Perdiem</label>
                                                    <input type="date" name="end_bt_perdiem[]"
                                                        class="form-control end-perdiem"
                                                        value="{{ old('end_bt_perdiem.' . $index, $perdiem['end_date'] ?? '') }}">
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label" for="start">Total
                                                        Days</label>
                                                    <div class="input-group">
                                                        <input class="form-control bg-light total-days-perdiem"
                                                            id="total_days_bt_perdiem_{{ $index }}"
                                                            name="total_days_bt_perdiem[]" type="text" min="0"
                                                            value="{{ old('total_days_bt_perdiem.' . $index, $perdiem['total_days'] ?? '') }}"
                                                            readonly>
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">days</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label" for="name">Location
                                                        Agency</label>
                                                    <select class="form-control location-select"
                                                        name="location_bt_perdiem[]">
                                                        <option value="">
                                                            Select
                                                            location...
                                                        </option>
                                                        @foreach ($locations as $location)
                                                            <option value="{{ $location->area }}"
                                                                {{ ($perdiem['location'] ?? '') == $location->area ? 'selected' : '' }}>
                                                                {{ $location->area . ' (' . $location->company_name . ')' }}
                                                            </option>
                                                        @endforeach
                                                        <option value="Others"
                                                            {{ ($perdiem['location'] ?? '') == 'Others' ? 'selected' : '' }}>
                                                            Others
                                                        </option>
                                                    </select>
                                                    <br>
                                                    <input type="text" name="other_location_bt_perdiem[]"
                                                        class="form-control other-location" placeholder="Other Location"
                                                        value="{{ old('other_location_bt_perdiem.' . $index, $perdiem['other_location'] ?? '') }}"
                                                        style="{{ ($perdiem['location'] ?? '') == 'Others' ? 'display:block;' : 'display:none;' }}">
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label" for="name">Company
                                                        Code</label>
                                                    <select class="form-control select2"
                                                        id="companyFilter_{{ $index }}"
                                                        name="company_bt_perdiem[]">
                                                        <option value="">
                                                            ---
                                                            Select
                                                            Company
                                                            ---
                                                        </option>
                                                        @foreach ($companies as $company)
                                                            <option value="{{ $company->contribution_level_code }}"
                                                                {{ ($perdiem['company_code'] ?? '') == $company->contribution_level_code ? 'selected' : '' }}>
                                                                {{ $company->contribution_level . ' (' . $company->contribution_level_code . ')' }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label">Amount</label>
                                                </div>
                                                <div class="input-group mb-3">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">Rp</span>
                                                    </div>
                                                    <input class="form-control bg-light" name="nominal_bt_perdiem[]"
                                                        id="nominal_bt_perdiem_{{ $index }}" type="text"
                                                        min="0"
                                                        value="{{ old('nominal_bt_perdiem.' . $index, $perdiem['nominal'] ?? '') }}"
                                                        readonly>
                                                </div>
                                                <hr class="border border-primary border-1 opacity-50">
                                            @endforeach
                                        @else
                                            <!-- Default empty fields if no data is available -->
                                            <div class="mb-2">
                                                <label class="form-label">Start
                                                    Perdiem</label>
                                                <input type="date" name="start_bt_perdiem[]"
                                                    class="form-control start-perdiem">
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label">End
                                                    Perdiem</label>
                                                <input type="date" name="end_bt_perdiem[]"
                                                    class="form-control end-perdiem">
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label" for="start">Total
                                                    Days</label>
                                                <div class="input-group">
                                                    <input class="form-control bg-light total-days-perdiem"
                                                        id="total_days_bt_perdiem_0" name="total_days_bt_perdiem[]"
                                                        type="text" min="0" readonly>
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">days</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label" for="name">Location
                                                    Agency</label>
                                                <select class="form-control select2 location-select"
                                                    name="location_bt_perdiem[]">
                                                    <option value="">
                                                        Select
                                                        location...
                                                    </option>
                                                    @foreach ($locations as $location)
                                                        <option value="{{ $location->area }}">
                                                            {{ $location->area . ' (' . $location->company_name . ')' }}
                                                        </option>
                                                    @endforeach
                                                    <option value="Others">
                                                        Others
                                                    </option>
                                                </select>
                                                <br>
                                                <input type="text" name="other_location_bt_perdiem[]"
                                                    class="form-control other-location" placeholder="Other Location"
                                                    style="display:none;">
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label" for="name">Company
                                                    Code</label>
                                                <select class="form-control select2" id="companyFilter_0"
                                                    name="company_bt_perdiem[]">
                                                    <option value="">
                                                        --- Select
                                                        Company ---
                                                    </option>
                                                    @foreach ($companies as $company)
                                                        <option value="{{ $company->contribution_level_code }}">
                                                            {{ $company->contribution_level . ' (' . $company->contribution_level_code . ')' }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label">Amount</label>
                                            </div>
                                            <div class="input-group mb-3">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">Rp</span>
                                                </div>
                                                <input class="form-control bg-light" name="nominal_bt_perdiem[]"
                                                    id="nominal_bt_perdiem_0" type="text" min="0" readonly>
                                            </div>
                                            <hr class="border border-primary border-1 opacity-50">
                                        @endif
                                    </div>

                                    <div class="mb-2">
                                        <label class="form-label">Total
                                            Perdiem</label>
                                        <div class="input-group">
                                            <div class="input-group-append">
                                                <span class="input-group-text">Rp</span>
                                            </div>
                                            <input class="form-control bg-light" name="total_bt_perdiem[]"
                                                id="total_bt_perdiem[]" type="text" min="0"
                                                value="{{ $totalPerdiem ?? 0 }}" readonly>
                                        </div>
                                    </div>
                                    <button type="button" id="add-more-bt-perdiem" class="btn btn-primary mt-3">Add
                                        More</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="transport-card" class="card-body" style="display:">
                    <div class="accordion" id="accordionTransport">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingTransport">
                                <button
                                    @if (count($detailTransport) > 0) class="accordion-button @if ($detailTransport[0]['tanggal'] === null) collapsed @endif
                                    fw-medium" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapseTransport"
                                    aria-expanded="@if ($detailTransport[0]['tanggal'] === null) false @else true @endif"
                                aria-controls="collapseTransport" @else
                                    class="accordion-button collapsed fw-medium" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#collapseTransport"
                                    aria-expanded="false" aria-controls="collapseTransport" @endif
                                    >
                                    Transport Plan
                                </button>
                            </h2>
                            <div id="collapseTransport"
                                @if (count($detailTransport) > 0) class="accordion-collapse @if ($detailTransport[0]['tanggal'] === null) collapse @else show @endif"
                            @else class="accordion-collapse collapse" @endif
                                aria-labelledby="headingTransport">
                                <div class="accordion-body">
                                    <div id="form-container-bt-transport">
                                        @php
                                            // Provide default empty array if detail_transport is not set
                                            $detailTransport = $caDetail['detail_transport'] ?? [];

                                            // Calculate total transport cost with default values
                                            $totalTransport = array_reduce(
                                                $detailTransport,
                                                function ($carry, $item) {
                                                    return $carry + (int) ($item['nominal'] ?? 0);
                                                },
                                                0,
                                            );
                                        @endphp
                                        @if (!empty($detailTransport))
                                            @foreach ($detailTransport as $index => $transport)
                                                <div class="mb-2">
                                                    <label class="form-label">
                                                        Transport Date</label>
                                                    <input type="date" name="tanggal_bt_transport[]"
                                                        class="form-control" placeholder="mm/dd/yyyy"
                                                        value="{{ old('tanggal_bt_transport.' . $index, $transport['tanggal'] ?? '') }}">
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label" for="name">Company
                                                        Code</label>
                                                    <select class="form-control select2"
                                                        id="companyFilter_{{ $index }}"
                                                        name="company_bt_transport[]">
                                                        <option value="">
                                                            Select
                                                            Company...
                                                        </option>
                                                        @foreach ($companies as $company)
                                                            <option value="{{ $company->contribution_level_code }}"
                                                                {{ ($transport['company_code'] ?? '') == $company->contribution_level_code ? 'selected' : '' }}>
                                                                {{ $company->contribution_level . ' (' . $company->contribution_level_code . ')' }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label">Information</label>
                                                    <textarea name="keterangan_bt_transport[]" class="form-control" placeholder="Write your information here...">{{ old('keterangan_bt_transport.' . $index, $transport['keterangan'] ?? '') }}</textarea>
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label">Amount</label>
                                                </div>
                                                <div class="input-group mb-3">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">Rp</span>
                                                    </div>
                                                    <input class="form-control" name="nominal_bt_transport[]"
                                                        id="nominal_bt_transport_{{ $index }}" type="text"
                                                        min="0"
                                                        value="{{ old('nominal_bt_transport.' . $index, $transport['nominal'] ?? '0') }}">
                                                </div>

                                                <hr class="border border-primary border-1 opacity-50">
                                            @endforeach
                                        @else
                                            <!-- Default empty fields if no data is available -->
                                            <div class="mb-2">
                                                <label class="form-label">
                                                    Transport Date</label>
                                                <input type="date" name="tanggal_bt_transport[]"
                                                    class="form-control" placeholder="mm/dd/yyyy">
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label" for="name">Company
                                                    Code</label>
                                                <select class="form-control select2" id="companyFilter_0"
                                                    name="company_bt_transport[]">
                                                    <option value="">
                                                        Select
                                                        Company...
                                                    </option>
                                                    @foreach ($companies as $company)
                                                        <option value="{{ $company->contribution_level_code }}">
                                                            {{ $company->contribution_level . ' (' . $company->contribution_level_code . ')' }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label">Information</label>
                                                <textarea name="keterangan_bt_transport[]" class="form-control" placeholder="Write your information here..."></textarea>
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label">Amount</label>
                                            </div>
                                            <div class="input-group mb-3">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">Rp</span>
                                                </div>
                                                <input class="form-control" name="nominal_bt_transport[]"
                                                    id="nominal_bt_transport_0" type="text" min="0">
                                            </div>

                                            <hr class="border border-primary border-1 opacity-50">
                                        @endif
                                    </div>

                                    <div class="mb-2">
                                        <label class="form-label">Total
                                            Transport</label>
                                        <div class="input-group">
                                            <div class="input-group-append">
                                                <span class="input-group-text">Rp</span>
                                            </div>
                                            <input class="form-control bg-light" name="total_bt_transport[]"
                                                id="total_bt_transport[]" type="text" min="0"
                                                value="{{ $totalTransport ?? 0 }}" readonly>
                                        </div>
                                    </div>
                                    <button type="button" id="add-more-bt-transport"
                                        class="btn btn-primary mt-3">Add
                                        More</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="penginapan-card" class="card-body" style="display:">
                    <div class="accordion" id="accordionPenginapan">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingPenginapan">
                                <button
                                    @if (count($detailPenginapan) > 0) class="accordion-button @if ($detailPenginapan[0]['start_date'] === null) collapsed @endif
                                    fw-medium" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapsePenginapan"
                                    aria-expanded="@if ($detailPenginapan[0]['start_date'] === null) false @else true @endif"
                                aria-controls="collapsePenginapan" @else
                                    class="accordion-button collapsed fw-medium" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#collapsePenginapan"
                                    aria-expanded="false" aria-controls="collapsePenginapan" @endif
                                    >
                                    Accommodation Plan
                                </button>
                            </h2>
                            <div id="collapsePenginapan"
                                @if (count($detailPenginapan) > 0) class="accordion-collapse @if ($detailPenginapan[0]['start_date'] === null) collapse @else show @endif"
                            @else class="accordion-collapse collapse" @endif
                                aria-labelledby="headingPenginapan">
                                <div class="accordion-body">
                                    <div id="form-container-bt-penginapan">
                                        @php
                                            // Default empty array if 'detail_penginapan' is not set
                                            $penginapan = $caDetail['detail_penginapan'] ?? [];

                                            // Calculate total penginapan cost
                                            $totalPenginapanCost = array_reduce(
                                                $penginapan,
                                                function ($carry, $item) {
                                                    return $carry + (int) ($item['nominal'] ?? 0);
                                                },
                                                0,
                                            );
                                        @endphp

                                        <!-- Form for Penginapan Details -->
                                        <div id="form-container-bt-penginapan">
                                            @if (!empty($penginapan))
                                                @foreach ($penginapan as $index => $item)
                                                    <div class="mb-2">
                                                        <label class="form-label">Accommodation Start
                                                            Date</label>
                                                        <input type="date" name="start_bt_penginapan[]"
                                                            class="form-control start-penginapan"
                                                            placeholder="mm/dd/yyyy"
                                                            value="{{ old('start_bt_penginapan.' . $index, $item['start_date'] ?? '') }}">
                                                    </div>
                                                    <div class="mb-2">
                                                        <label class="form-label">Accommodation End
                                                            Date</label>
                                                        <input type="date" name="end_bt_penginapan[]"
                                                            class="form-control end-penginapan"
                                                            placeholder="mm/dd/yyyy"
                                                            value="{{ old('end_bt_penginapan.' . $index, $item['end_date'] ?? '') }}">
                                                    </div>
                                                    <div class="mb-2">
                                                        <label class="form-label" for="start">Total
                                                            Days</label>
                                                        <div class="input-group">
                                                            <input class="form-control bg-light total-days-penginapan"
                                                                id="total_days_bt_penginapan_{{ $index }}"
                                                                name="total_days_bt_penginapan[]" type="text"
                                                                min="0"
                                                                value="{{ old('total_days_bt_penginapan.' . $index, $item['total_days'] ?? '0') }}"
                                                                readonly>
                                                            <div class="input-group-append">
                                                                <span class="input-group-text">days</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="mb-2">
                                                        <label class="form-label" for="name">Hotel
                                                            Name</label>
                                                        <input type="text" name="hotel_name_bt_penginapan[]"
                                                            class="form-control" placeholder="ex: Westin"
                                                            value="{{ old('hotel_name_bt_penginapan.' . $index, $item['hotel_name'] ?? '') }}">
                                                    </div>
                                                    <div class="mb-2">
                                                        <label class="form-label" for="name">Company
                                                            Code</label>
                                                        <select class="form-control select2"
                                                            id="companyFilter_{{ $index }}"
                                                            name="company_bt_penginapan[]">
                                                            <option value="">
                                                                Select
                                                                Company...
                                                            </option>
                                                            @foreach ($companies as $company)
                                                                <option
                                                                    value="{{ $company->contribution_level_code }}"
                                                                    {{ ($item['company_code'] ?? '') == $company->contribution_level_code ? 'selected' : '' }}>
                                                                    {{ $company->contribution_level . ' (' . $company->contribution_level_code . ')' }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="mb-2">
                                                        <label class="form-label">Amount</label>
                                                    </div>
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">Rp</span>
                                                        </div>
                                                        <input class="form-control" name="nominal_bt_penginapan[]"
                                                            id="nominal_bt_penginapan_{{ $index }}"
                                                            type="text" min="0"
                                                            value="{{ old('nominal_bt_penginapan.' . $index, $item['nominal'] ?? '0') }}">
                                                    </div>

                                                    <hr class="border border-primary border-1 opacity-50">
                                                @endforeach
                                            @else
                                                <!-- Default empty fields if no data is available -->
                                                <div class="mb-2">
                                                    <label class="form-label">Accommodation
                                                        Start Date</label>
                                                    <input type="date" name="start_bt_penginapan[]"
                                                        class="form-control start-penginapan"
                                                        placeholder="mm/dd/yyyy">
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label">Accommodation End Date</label>
                                                    <input type="date" name="end_bt_penginapan[]"
                                                        class="form-control end-penginapan" placeholder="mm/dd/yyyy">
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label" for="start">Total
                                                        Days</label>
                                                    <div class="input-group">
                                                        <input class="form-control bg-light total-days-penginapan"
                                                            id="total_days_bt_penginapan_0"
                                                            name="total_days_bt_penginapan[]" type="text"
                                                            min="0" readonly>
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">days</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label" for="name">Hotel
                                                        Name</label>
                                                    <input type="text" name="hotel_name_bt_penginapan[]"
                                                        class="form-control" placeholder="ex: Westin">
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label" for="name">Company
                                                        Code</label>
                                                    <select class="form-control select2" id="companyFilter_0"
                                                        name="company_bt_penginapan[]">
                                                        <option value="">
                                                            Select
                                                            Company...
                                                        </option>
                                                        @foreach ($companies as $company)
                                                            <option value="{{ $company->contribution_level_code }}">
                                                                {{ $company->contribution_level . ' (' . $company->contribution_level_code . ')' }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label">Amount</label>
                                                </div>
                                                <div class="input-group mb-3">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">Rp</span>
                                                    </div>
                                                    <input class="form-control" name="nominal_bt_penginapan[]"
                                                        id="nominal_bt_penginapan_0" type="text" min="0">
                                                </div>

                                                <hr class="border border-primary border-1 opacity-50">
                                            @endif
                                        </div>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label">Accommodation Total</label>
                                        <div class="input-group">
                                            <div class="input-group-append">
                                                <span class="input-group-text">Rp</span>
                                            </div>
                                            <input class="form-control bg-light" name="total_bt_penginapan[]"
                                                id="total_bt_penginapan" type="text" min="0"
                                                value="{{ $totalPenginapanCost }}" readonly>
                                        </div>
                                    </div>

                                    <button type="button" id="add-more-bt-penginapan"
                                        class="btn btn-primary mt-3">Add
                                        More</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="lainnya-card" class="card-body" style="display:">
                    <div class="accordion" id="accordionLainnya">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingLainnya">
                                <button
                                    @if (count($detailLainnya) > 0) class="accordion-button @if ($detailLainnya[0]['tanggal'] === null) collapsed @endif
                                    fw-medium" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapseLainnya"
                                    aria-expanded="@if ($detailLainnya[0]['tanggal'] === null) false @else true @endif"
                                aria-controls="collapseLainnya" @else class="accordion-button collapsed fw-medium"
                                    type="button" data-bs-toggle="collapse" data-bs-target="#collapseLainnya"
                                    aria-expanded="false" aria-controls="collapseLainnya" @endif
                                    >
                                    Others Plan
                                </button>
                            </h2>
                            <div id="collapseLainnya"
                                @if (count($detailLainnya) > 0) class="accordion-collapse @if ($detailLainnya[0]['tanggal'] === null) collapse @else show @endif"
                            @else class="accordion-collapse collapse" @endif
                                aria-labelledby="headingLainnya">
                                <div class="accordion-body">
                                    <div id="form-container-bt-lainnya">
                                        @php
                                            // Default empty array if 'detail_lainnya' is not set
                                            $lainnya = $caDetail['detail_lainnya'] ?? [];

                                            // Calculate total lainnya cost
                                            $totalLainnyaCost = array_reduce(
                                                $lainnya,
                                                function ($carry, $item) {
                                                    return $carry + (int) ($item['nominal'] ?? 0);
                                                },
                                                0,
                                            );
                                        @endphp

                                        <div id="form-container-bt-lainnya">
                                            @if (!empty($lainnya))
                                                @foreach ($lainnya as $index => $lainnyaItem)
                                                    <div class="lainnya-item">
                                                        <div class="mb-2">
                                                            <label class="form-label">Date</label>
                                                            <input type="date" name="tanggal_bt_lainnya[]"
                                                                class="form-control"
                                                                value="{{ old('tanggal_bt_lainnya.' . $index, $lainnyaItem['tanggal'] ?? '') }}"
                                                                placeholder="mm/dd/yyyy">
                                                        </div>
                                                        <div class="mb-2">
                                                            <label class="form-label">Information</label>
                                                            <textarea name="keterangan_bt_lainnya[]" class="form-control" placeholder="Write your other purposes ...">{{ old('keterangan_bt_lainnya.' . $index, $lainnyaItem['keterangan'] ?? '') }}</textarea>
                                                        </div>
                                                        <div class="mb-2">
                                                            <label class="form-label">Amount</label>
                                                            <div class="input-group mb-3">
                                                                <div class="input-group-append">
                                                                    <span class="input-group-text">Rp</span>
                                                                </div>
                                                                <input class="form-control nominal-lainnya"
                                                                    name="nominal_bt_lainnya[]" type="text"
                                                                    min="0"
                                                                    value="{{ old('nominal_bt_lainnya.' . $index, $lainnyaItem['nominal'] ?? '0') }}">
                                                            </div>
                                                        </div>
                                                        <hr class="border border-primary border-1 opacity-50">
                                                    </div>
                                                @endforeach
                                            @else
                                                <div class="lainnya-item">
                                                    <div class="mb-2">
                                                        <label class="form-label">Date</label>
                                                        <input type="date" name="tanggal_bt_lainnya[]"
                                                            class="form-control" placeholder="mm/dd/yyyy">
                                                    </div>
                                                    <div class="mb-2">
                                                        <label class="form-label">Information</label>
                                                        <textarea name="keterangan_bt_lainnya[]" class="form-control" placeholder="Write your other purposes ..."></textarea>
                                                    </div>
                                                    <div class="mb-2">
                                                        <label class="form-label">Amount</label>
                                                        <div class="input-group mb-3">
                                                            <div class="input-group-append">
                                                                <span class="input-group-text">Rp</span>
                                                            </div>
                                                            <input class="form-control nominal-lainnya"
                                                                name="nominal_bt_lainnya[]" type="text"
                                                                min="0" value="0">
                                                        </div>
                                                    </div>
                                                    <hr class="border border-primary border-1 opacity-50">
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label">Total
                                            Lainnya</label>
                                        <div class="input-group">
                                            <div class="input-group-append">
                                                <span class="input-group-text">Rp</span>
                                            </div>
                                            @php
                                                $index = $index ?? 0;
                                                $totalLainnya = $totalLainnya ?? 0; // Default to 0 if $totalLainnya is not set
                                                $formattedTotalLainnya = number_format($totalLainnya, 0, ',', '.');
                                            @endphp
                                            <input class="form-control bg-light" name="total_bt_lainnya[]"
                                                id="total_bt_lainnya" type="text" min="0"
                                                value="{{ old('total_bt_lainnya.' . $index, $formattedTotalLainnya) }}"
                                                readonly>
                                        </div>
                                    </div>

                                    <button type="button" id="add-more-bt-lainnya" class="btn btn-primary mt-3">Add
                                        More</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- </div> --}}
                {{-- </div> --}}
            </div>
        </div>
        <br>
        @php
            // Provide default empty arrays if any section is not set
            $detailPerdiem = $caDetail['detail_perdiem'] ?? [];
            $detailTransport = $caDetail['detail_transport'] ?? [];
            $detailPenginapan = $caDetail['detail_penginapan'] ?? [];
            $detailLainnya = $caDetail['detail_lainnya'] ?? [];

            // Calculate total costs for each section
            $totalPerdiem = array_reduce(
                $detailPerdiem,
                function ($carry, $item) {
                    return $carry + (int) ($item['nominal'] ?? 0);
                },
                0,
            );

            $totalTransport = array_reduce(
                $detailTransport,
                function ($carry, $item) {
                    return $carry + (int) ($item['nominal'] ?? 0);
                },
                0,
            );

            $totalPenginapan = array_reduce(
                $detailPenginapan,
                function ($carry, $item) {
                    return $carry + (int) ($item['nominal'] ?? 0);
                },
                0,
            );

            $totalLainnya = array_reduce(
                $detailLainnya,
                function ($carry, $item) {
                    return $carry + (int) ($item['nominal'] ?? 0);
                },
                0,
            );

            // Total Cash Advanced
            $totalCashAdvanced = $totalPerdiem + $totalTransport + $totalPenginapan + $totalLainnya;
        @endphp
        <div class="col-md-12 mb-2">
            <label class="form-label">Total Cash Advanced</label>
            <div class="input-group">
                <div class="input-group-append">
                    <span class="input-group-text">Rp</span>
                </div>
                <input class="form-control bg-light" name="totalca" id="totalca" type="text" min="0"
                    value="{{ $totalCashAdvanced }}" readonly>
            </div>
        </div>
    </div>
</div>
</div>

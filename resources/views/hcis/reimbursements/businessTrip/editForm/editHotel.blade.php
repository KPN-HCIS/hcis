<div class="row mt-2" id="hotel_div"
style="display: {{ count($hotelData) > 0 ? 'block' : 'none' }};">
<div class="col-md-12">
    <div class="table-responsive-sm">
        <div class="d-flex flex-column gap-2" id="hotel_forms_container">
            @for ($i = 1; $i <= 5; $i++)
                @php
                    $hotel = $hotelData[$i - 1] ?? null;
                @endphp
                <div class="hotel-form" id="hotel-form-{{ $i }}"
                    style="display: {{ $i === 1 || ($hotel && $i <= count($hotelData)) ? 'block' : 'none' }};">
                    <div class="text-bg-primary p-2"
                        style="text-align:center; border-radius:4px;">
                        Hotel {{ $i }}
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Hotel Name</label>
                                <div class="input-group">
                                    <input class="form-control bg-white"
                                        name="nama_htl[]" type="text"
                                        value="{{ $hotel['nama_htl'] ?? '' }}"
                                        placeholder="ex: Westin">
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Hotel
                                    Location</label>
                                <div class="input-group">
                                    <input class="form-control bg-white"
                                        name="lokasi_htl[]" type="text"
                                        value="{{ $hotel['lokasi_htl'] ?? '' }}"
                                        placeholder="ex: Jakarta">
                                </div>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-9 mb-2">
                                <label class="form-label">Bed Size</label>
                                <select class="form-select" name="bed_htl[]"
                                    required>
                                    <option value="Single Bed"
                                        {{ ($hotel['bed_htl'] ?? '') == 'Single Bed' ? 'selected' : '' }}>
                                        Single Bed
                                    </option>
                                    <option value="Twin Bed"
                                        {{ ($hotel['bed_htl'] ?? '') == 'Twin Bed' ? 'selected' : '' }}>
                                        Twin Bed
                                    </option>
                                    <option value="King Bed"
                                        {{ ($hotel['bed_htl'] ?? '') == 'King Bed' ? 'selected' : '' }}>
                                        King Bed
                                    </option>
                                    <option value="Super King Bed"
                                        {{ ($hotel['bed_htl'] ?? '') == 'Super King Bed' ? 'selected' : '' }}>
                                        Super King Bed
                                    </option>
                                    <option value="Extra Bed"
                                        {{ ($hotel['bed_htl'] ?? '') == 'Extra Bed' ? 'selected' : '' }}>
                                        Extra Bed
                                    </option>
                                    <option value="Baby Cot"
                                        {{ ($hotel['bed_htl'] ?? '') == 'Baby Cot' ? 'selected' : '' }}>
                                        Baby Cot
                                    </option>
                                    <option value="Sofa Bed"
                                        {{ ($hotel['bed_htl'] ?? '') == 'Sofa Bed' ? 'selected' : '' }}>
                                        Sofa Bed
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="form-label">Total Room</label>
                                <div class="input-group">
                                    <input class="form-control bg-white"
                                        name="jmlkmr_htl[]" type="number"
                                        min="1"
                                        value="{{ $hotel['jmlkmr_htl'] ?? '' }}"
                                        placeholder="ex: 1">
                                </div>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-5 mb-2">
                                <label class="form-label">Check In Date</label>
                                <input type="date"
                                    class="form-control datepicker check-in-date"
                                    name="tgl_masuk_htl[]"
                                    value="{{ $hotel['tgl_masuk_htl'] ?? '' }}"
                                    data-index="{{ $i }}"
                                    onchange="calculateTotalDays(this)">
                            </div>
                            <div class="col-md-5 mb-2">
                                <label class="form-label">Check Out
                                    Date</label>
                                <input type="date"
                                    class="form-control datepicker check-out-date"
                                    name="tgl_keluar_htl[]"
                                    value="{{ $hotel['tgl_keluar_htl'] ?? '' }}"
                                    data-index="{{ $i }}"
                                    onchange="calculateTotalDays(this)">
                            </div>
                            <div class="col-md-2 mb-2">
                                <label class="form-label">Total Days</label>
                                <input type="number"
                                    class="form-control datepicker bg-light total-days"
                                    name="total_hari[]"
                                    value="{{ $hotel['total_hari'] ?? '' }}"
                                    readonly>
                            </div>
                        </div>
                            @if ($i < 5)
                                <div class="mt-3">
                                    <label class="form-label">Add more
                                        hotel</label>
                                    <div class="form-check">
                                        <input class="form-check-input"
                                            type="radio"
                                            id="more_htl_no_{{ $i }}"
                                            name="more_htl_{{ $i }}"
                                            value="Tidak"
                                            {{ ($hotel['more_htl'] ?? 'Tidak') == 'Tidak' ? 'checked' : '' }}>
                                        <label class="form-check-label"
                                            for="more_htl_no_{{ $i }}">Tidak</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input"
                                            type="radio"
                                            id="more_htl_yes_{{ $i }}"
                                            name="more_htl_{{ $i }}"
                                            value="Ya"
                                            {{ ($hotel['more_htl'] ?? 'Tidak') == 'Ya' ? 'checked' : '' }}>
                                        <label class="form-check-label"
                                            for="more_htl_yes_{{ $i }}">Ya</label>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endfor
        </div>
    </div>
    {{-- </div> --}}

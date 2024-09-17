<div class="row mt-2" id="tiket_div" style="display: {{ count($ticketData) > 0 ? 'block' : 'none' }};">
    <div class="col-md-12">
        <div class="table-responsive-sm">
            <div class="d-flex flex-column gap-2" id="ticket_forms_container">
                @for ($i = 1; $i <= 5; $i++)
                    @php
                        $ticket = $ticketData[$i - 1] ?? null;
                    @endphp
                    <div class="ticket-form" id="ticket-form-{{ $i }}"
                        style="display: {{ $i === 1 || ($ticket && $i <= count($ticketData)) ? 'block' : 'none' }};">
                        <div class="text-bg-primary p-2" style="text-align:center; border-radius:4px;">
                            Ticket {{ $i }}
                        </div>
                        <div class="card">
                            <div class="card-body">
                                <div class="row mb-2">
                                    <div class="col-md-4">
                                        <label class="form-label">NIK</label>
                                        <div class="input-group">
                                            <input class="form-control" name="noktp_tkt[]" type="number"
                                                value="{{ $ticket['noktp_tkt'] ?? '' }}"
                                                placeholder="ex: 3521XXXXXXXXXXXX">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">From</label>
                                        <div class="input-group">
                                            <input class="form-control bg-white" name="dari_tkt[]" type="text"
                                                placeholder="ex. Yogyakarta (YIA)"
                                                value="{{ $ticket['dari_tkt'] ?? '' }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">To</label>
                                        <div class="input-group">
                                            <input class="form-control bg-white" name="ke_tkt[]" type="text"
                                                placeholder="ex. Jakarta (CGK)" value="{{ $ticket['ke_tkt'] ?? '' }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-8">
                                        <label class="form-label">Date</label>
                                        <div class="input-group">
                                            <input class="form-control bg-white" id="tgl_brkt_tkt_{{ $i }}"
                                                name="tgl_brkt_tkt[]" type="date"
                                                value="{{ $ticket['tgl_brkt_tkt'] ?? '' }}"
                                                onchange="validateDates({{ $i }})">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Time</label>
                                        <div class="input-group">
                                            <input class="form-control bg-white" id="jam_brkt_tkt_{{ $i }}"
                                                name="jam_brkt_tkt[]" type="time"
                                                value="{{ $ticket['jam_brkt_tkt'] ?? '' }}"
                                                onchange="validateDates({{ $i }})">
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <label for="ket_tkt_{{ $i }}" class="form-label">Information</label>
                                    <textarea class="form-control" id="ket_tkt_{{ $i }}" name="ket_tkt[]" rows="3"
                                        placeholder="This field is for editing ticket details, e.g., Citilink, Garuda Indonesia, etc.">{{ $ticket['ket_tkt'] ?? '' }}</textarea>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-8 mb-2">
                                        <label class="form-label" for="jenis_tkt_{{ $i }}">Transportation
                                            Type</label>
                                        <div class="input-group">
                                            <select class="form-select" name="jenis_tkt[]"
                                                id="jenis_tkt_{{ $i }}">
                                                <option value="">Select
                                                    Transportation Type</option>
                                                <option value="Train"
                                                    {{ ($ticket['jenis_tkt'] ?? '') == 'Train' ? 'selected' : '' }}>
                                                    Train</option>
                                                <option value="Bus"
                                                    {{ ($ticket['jenis_tkt'] ?? '') == 'Bus' ? 'selected' : '' }}>
                                                    Bus</option>
                                                <option value="Airplane"
                                                    {{ ($ticket['jenis_tkt'] ?? '') == 'Airplane' ? 'selected' : '' }}>
                                                    Airplane</option>
                                                <option value="Car"
                                                    {{ ($ticket['jenis_tkt'] ?? '') == 'Car' ? 'selected' : '' }}>
                                                    Car</option>
                                                <option value="Ferry"
                                                    {{ ($ticket['jenis_tkt'] ?? '') == 'Ferry' ? 'selected' : '' }}>
                                                    Ferry</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <label for="type_tkt_{{ $i }}" class="form-label">Ticket
                                            Type</label>
                                        <select class="form-select" name="type_tkt[]" required>
                                            <option value="One Way"
                                                {{ ($ticket['type_tkt'] ?? '') == 'One Way' ? 'selected' : '' }}>
                                                One Way</option>
                                            <option value="Round Trip"
                                                {{ ($ticket['type_tkt'] ?? '') == 'Round Trip' ? 'selected' : '' }}>
                                                Round Trip</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="round-trip-options"
                                    style="display: {{ ($ticket['type_tkt'] ?? '') == 'Round Trip' ? 'block' : 'none' }};">
                                    <div class="row mb-2">
                                        <div class="col-md-8 mb-2">
                                            <label class="form-label">Return
                                                Date</label>
                                            <div class="input-group">
                                                <input class="form-control bg-white" name="tgl_plg_tkt[]" type="date"
                                                    id="tgl_plg_tkt_{{ $i }}"
                                                    value="{{ $ticket['tgl_plg_tkt'] ?? '' }}"
                                                    onchange="validateDates({{ $i }})">
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <label class="form-label">Return
                                                Time</label>
                                            <div class="input-group">
                                                <input class="form-control bg-white"
                                                    id="jam_plg_tkt_{{ $i }}" name="jam_plg_tkt[]"
                                                    type="time" value="{{ $ticket['jam_plg_tkt'] ?? '' }}"
                                                    onchange="validateDates({{ $i }})">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @if ($i < 5)
                                    <div class="mt-3">
                                        <label class="form-label">Add more
                                            ticket</label>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio"
                                                id="more_tkt_no_{{ $i }}"
                                                name="more_tkt_{{ $i }}" value="Tidak"
                                                {{ ($ticket['more_tkt'] ?? 'Tidak') == 'Tidak' ? 'checked' : '' }}>
                                            <label class="form-check-label"
                                                for="more_tkt_no_{{ $i }}">Tidak</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio"
                                                id="more_tkt_yes_{{ $i }}"
                                                name="more_tkt_{{ $i }}" value="Ya"
                                                {{ ($ticket['more_tkt'] ?? 'Tidak') == 'Ya' ? 'checked' : '' }}>
                                            <label class="form-check-label"
                                                for="more_tkt_yes_{{ $i }}">Ya</label>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endfor
            </div>
        </div>

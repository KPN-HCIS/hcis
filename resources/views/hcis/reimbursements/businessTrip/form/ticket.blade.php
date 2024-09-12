<div class="container-fluid">
    <div id="tiket_div">
        <div class="d-flex flex-column gap-1" id="ticket_forms_container">
            <?php for ($i = 1; $i <= 5; $i++) : ?>
            <div class="card bg-light" id="ticket-form-<?php echo $i; ?>" style="display: <?php echo $i === 1 ? 'block' : 'none'; ?>;">
                <div class="card-body">
                    <div class="h5 text-uppercase">
                        <b>TICKET <?php echo $i; ?></b>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <label class="form-label">Employee Name</label>
                            <select class="form-select" id="noktp_tkt_<?php echo $i; ?>" name="noktp_tkt[]" required>
                                <option value="" disabled selected>--- Choose Passengers ---</option>
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->ktp }}">
                                        {{ $employee->ktp . ' - ' . $employee->fullname }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="form-label">From</label>
                            <div class="input-group">
                                <input class="form-control bg-white" name="dari_tkt[]" type="text"
                                    placeholder="ex. Yogyakarta (YIA)">
                            </div>
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="form-label">To</label>
                            <div class="input-group">
                                <input class="form-control bg-white" name="ke_tkt[]" type="text"
                                    placeholder="ex. Jakarta (CGK)">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Date</label>
                            <div class="input-group">
                                <input class="form-control bg-white" id="tgl_brkt_tkt_<?php echo $i; ?>"
                                    name="tgl_brkt_tkt[]" type="date" onchange="validateDates(<?php echo $i; ?>)">
                            </div>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Time</label>
                            <div class="input-group">
                                <input class="form-control bg-white" id="jam_brkt_tkt_<?php echo $i; ?>"
                                    name="jam_brkt_tkt[]" type="time" onchange="validateDates(<?php echo $i; ?>)">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label class="form-label" for="jenis_tkt_<?php echo $i; ?>">Transportation Type</label>
                            <div class="input-group">
                                <select class="form-select" name="jenis_tkt[]" id="jenis_tkt_<?php echo $i; ?>">
                                    <option value="">Select Transportation Type</option>
                                    <option value="Train">Train</option>
                                    <option value="Bus">Bus</option>
                                    <option value="Airplane">Airplane</option>
                                    <option value="Car">Car</option>
                                    <option value="Ferry">Ferry</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label for="type_tkt_<?php echo $i; ?>" class="form-label">Ticket Type</label>
                            <select class="form-select" name="type_tkt[]" required>
                                <option value="One Way">One Way</option>
                                <option value="Round Trip">Round Trip</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-2">
                            <label for="ket_tkt_<?php echo $i; ?>" class="form-label">Information</label>
                            <textarea class="form-control" id="ket_tkt_<?php echo $i; ?>" name="ket_tkt[]" rows="3"
                                placeholder="This field is for adding ticket details, e.g., Citilink, Garuda Indonesia, etc."></textarea>
                        </div>
                    </div>
                    <div class="round-trip-options" style="display: none;">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Return Date</label>
                                <div class="input-group">
                                    <input class="form-control bg-white" name="tgl_plg_tkt[]" type="date"
                                        id="tgl_plg_tkt_<?php echo $i; ?>"
                                        onchange="validateDates(<?php echo $i; ?>)">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Return Time</label>
                                <div class="input-group">
                                    <input class="form-control bg-white" id="jam_plg_tkt_<?php echo $i; ?>"
                                        name="jam_plg_tkt[]" type="time" onchange="validateDates(<?php echo $i; ?>)">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-2">
                        <button type="button" class="btn btn-sm btn-outline-primary add-ticket-btn">Add Ticket Data</button>
                        <button type="button" class="btn btn-sm btn-outline-danger remove-ticket-btn">Remove Ticket</button>
                    </div>
                </div>
            </div>
            <?php endfor; ?>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="card-body p-2 bg-light" id="hotel_div">
        <div class="d-flex flex-column gap-2" id="hotel_forms_container">
            <?php for ($i = 1; $i <= 5; $i++) : ?>
            <div class="hotel-form" id="hotel-form-<?php echo $i; ?>" style="display: <?php echo $i === 1 ? 'block' : 'none'; ?>;">
                <div class="h5 text-uppercase">
                    <b>Hotel <?php echo $i; ?></b>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Hotel
                            Name</label>
                        <div class="input-group">
                            <input class="form-control bg-white" name="nama_htl[]" type="text"
                                placeholder="ex: Hyatt">
                        </div>
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Hotel
                            Location</label>
                        <div class="input-group">
                            <input class="form-control bg-white" name="lokasi_htl[]" type="text"
                                placeholder="ex: Jakarta">
                        </div>
                    </div>
                    <div class="col-md-2 mb-2">
                        <label class="form-label">Bed Size</label>
                        <select class="form-select" name="bed_htl[]" required>
                            <option value="Single Bed">Single Bed
                            </option>
                            <option value="Twin Bed">Twin Bed
                            </option>
                            <option value="King Bed">King Bed
                            </option>
                            <option value="Super King Bed">Super
                                King
                                Bed
                            </option>
                            <option value="Extra Bed">Extra Bed
                            </option>
                            <option value="Baby Cot">Baby Cot
                            </option>
                            <option value="Sofa Bed">Sofa Bed
                            </option>
                        </select>
                    </div>
                    <div class="col-md-2 mb-2">
                        <label class="form-label">Total
                            Room</label>
                        <div class="input-group">
                            <input class="form-control bg-white" name="jmlkmr_htl[]" type="number" min="1"
                                placeholder="ex: 1">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-5 mb-2">
                        <label class="form-label">Check In
                            Date</label>
                        <input type="date" class="form-control datepicker" name="tgl_masuk_htl[]"
                            onchange="calculateTotalDays(<?php echo $i; ?>)">
                    </div>
                    <div class="col-md-5 mb-2">
                        <label class="form-label">Check Out
                            Date</label>
                        <input type="date" class="form-control datepicker" name="tgl_keluar_htl[]"
                            onchange="calculateTotalDays(<?php echo $i; ?>)">
                    </div>
                    <div class="col-md-2 mb-2">
                        <label class="form-label">Total
                            Days</label>
                        <input type="number" class="form-control datepicker bg-light" name="total_hari[]" readonly>
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-outline-primary add-hotel-btn">Add Hotel Data</button>
                <button type="button" class="btn btn-sm btn-outline-danger remove-hotel-btn">Remove Hotel</button>
                {{-- <div class="mt-1">
                    <button class="btn btn-sm btn-outline-primary">Add Hotel Data</button>
                </div> --}}
            </div>
            <?php endfor; ?>
        </div>
    </div>
</div>

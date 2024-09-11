
<div id="perdiem-card" class="card-body" style="display: ">
    <div class="accordion" id="accordionPerdiem">
        <div class="accordion-item">
            <h2 class="accordion-header" id="enter-headingOne">
                <button class="accordion-button collapsed fw-medium" type="button" data-bs-toggle="collapse"
                    data-bs-target="#enter-collapseOne" aria-expanded="false" aria-controls="enter-collapseOne">
                    Perdiem Plan
                </button>
            </h2>
            <div id="enter-collapseOne" class="accordion-collapse collapse" aria-labelledby="enter-headingOne">
                <div class="accordion-body">
                    <div id="form-container-bt-perdiem">
                        <div class="mb-2">
                            <label class="form-label">Start
                                Perdiem</label>
                            <input type="date" name="start_bt_perdiem[]" class="form-control start-perdiem"
                                placeholder="mm/dd/yyyy">
                        </div>
                        <div class="mb-2">
                            <label class="form-label">End
                                Perdiem</label>
                            <input type="date" name="end_bt_perdiem[]" class="form-control end-perdiem"
                                placeholder="mm/dd/yyyy">
                        </div>
                        <div class="mb-2">
                            <label class="form-label" for="start">Total
                                Days</label>
                            <div class="input-group">
                                <input class="form-control bg-light total-days-perdiem" id="total_days_bt_perdiem[]"
                                    name="total_days_bt_perdiem[]" type="text" min="0" value="0"
                                    readonly>
                                <div class="input-group-append">
                                    <span class="input-group-text">days</span>
                                </div>
                            </div>
                        </div>
                        <!-- HTML -->
                        <div class="mb-2">
                            <label class="form-label" for="name">Location
                                Agency</label>
                            <select class="form-control location-select" name="location_bt_perdiem[]">
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
                            <input type="text" name="other_location_bt_perdiem[]" class="form-control other-location"
                                placeholder="Other Location" value="" style="display: none;">
                        </div>
                        <div class="mb-2">
                            <label class="form-label" for="name">Company
                                Code</label>
                            <select class="form-control" id="companyFilter" name="company_bt_perdiem[]">
                                <option value="">
                                    ---
                                    Select
                                    Company
                                    ---
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
                            <input class="form-control bg-light" name="nominal_bt_perdiem[]" id="nominal_bt_perdiem"
                                type="text" min="0" value="0" readonly>
                        </div>
                        <hr class="border border-primary border-1 opacity-50">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Total
                            Perdiem</label>
                        <div class="input-group">
                            <div class="input-group-append">
                                <span class="input-group-text">Rp</span>
                            </div>
                            <input class="form-control bg-light" name="total_bt_perdiem[]" id="total_bt_perdiem[]"
                                type="text" min="0" value="0" readonly>
                        </div>
                    </div>
                    <button type="button" id="add-more-bt-perdiem" class="btn btn-primary mt-3">Add
                        More</button>
                </div>
            </div>
        </div>
    </div>
</div>

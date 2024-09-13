{{-- <div id="penginapan-card" class="card-body"
                                                                            style="display: ">
                                                                            <div class="accordion"
                                                                                id="accordionPenginapan">
                                                                                <div class="accordion-item">
                                                                                    <h2 class="accordion-header"
                                                                                        id="headingPenginapan">
                                                                                        <button
                                                                                            class="accordion-button collapsed fw-medium"
                                                                                            type="button"
                                                                                            data-bs-toggle="collapse"
                                                                                            data-bs-target="#collapsePenginapan"
                                                                                            aria-expanded="false"
                                                                                            aria-controls="collapsePenginapan">
                                                                                            Accommodation Plan
                                                                                        </button>
                                                                                    </h2>
                                                                                    <div id="collapsePenginapan"
                                                                                        class="accordion-collapse collapse"
                                                                                        aria-labelledby="headingPenginapan">
                                                                                        <div class="accordion-body">
                                                                                            <div
                                                                                                id="form-container-bt-penginapan">
                                                                                                <div class="mb-2">
                                                                                                    <label
                                                                                                        class="form-label">Accommodation
                                                                                                        Start</label>
                                                                                                    <input type="date"
                                                                                                        name="start_bt_penginapan[]"
                                                                                                        class="form-control start-penginapan"
                                                                                                        placeholder="mm/dd/yyyy">
                                                                                                </div>
                                                                                                <div class="mb-2">
                                                                                                    <label
                                                                                                        class="form-label">Accommodation
                                                                                                        End</label>
                                                                                                    <input type="date"
                                                                                                        name="end_bt_penginapan[]"
                                                                                                        class="form-control end-penginapan"
                                                                                                        placeholder="mm/dd/yyyy">
                                                                                                </div>
                                                                                                <div class="mb-2">
                                                                                                    <label
                                                                                                        class="form-label"
                                                                                                        for="start">Total
                                                                                                        Days</label>
                                                                                                    <div
                                                                                                        class="input-group">
                                                                                                        <input
                                                                                                            class="form-control bg-light total-days-penginapan"
                                                                                                            id="total_days_bt_penginapan[]"
                                                                                                            name="total_days_bt_penginapan[]"
                                                                                                            type="text"
                                                                                                            min="0"
                                                                                                            value="0"
                                                                                                            readonly>
                                                                                                        <div
                                                                                                            class="input-group-append">
                                                                                                            <span
                                                                                                                class="input-group-text">days</span>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="mb-2">
                                                                                                    <label
                                                                                                        class="form-label"
                                                                                                        for="name">Hotel
                                                                                                        Name</label>
                                                                                                    <input type="text"
                                                                                                        name="hotel_name_bt_penginapan[]"
                                                                                                        class="form-control"
                                                                                                        placeholder="ex: Westin">
                                                                                                </div>
                                                                                                <div class="mb-2">
                                                                                                    <label
                                                                                                        class="form-label"
                                                                                                        for="name">Company
                                                                                                        Code</label>
                                                                                                    <select
                                                                                                        class="form-control select2"
                                                                                                        id="companyFilter"
                                                                                                        name="company_bt_penginapan[]">
                                                                                                        <option
                                                                                                            value="">
                                                                                                            Select
                                                                                                            Company...
                                                                                                        </option>
                                                                                                        @foreach ($companies as $company)
                                                                                                            <option
                                                                                                                value="{{ $company->contribution_level_code }}">
                                                                                                                {{ $company->contribution_level . ' (' . $company->contribution_level_code . ')' }}
                                                                                                            </option>
                                                                                                        @endforeach
                                                                                                    </select>
                                                                                                </div>
                                                                                                <div class="mb-2">
                                                                                                    <label
                                                                                                        class="form-label">Amount</label>
                                                                                                </div>
                                                                                                <div
                                                                                                    class="input-group mb-3">
                                                                                                    <div
                                                                                                        class="input-group-append">
                                                                                                        <span
                                                                                                            class="input-group-text">Rp</span>
                                                                                                    </div>
                                                                                                    <input
                                                                                                        class="form-control"
                                                                                                        name="nominal_bt_penginapan[]"
                                                                                                        id="nominal_bt_penginapan[]"
                                                                                                        type="text"
                                                                                                        min="0"
                                                                                                        value="0">
                                                                                                </div>
                                                                                                <hr
                                                                                                    class="border border-primary border-1 opacity-50">
                                                                                            </div>
                                                                                            <div class="mb-2">
                                                                                                <label
                                                                                                    class="form-label">Total
                                                                                                    Accommodation
                                                                                                </label>
                                                                                                <div class="input-group">
                                                                                                    <div
                                                                                                        class="input-group-append">
                                                                                                        <span
                                                                                                            class="input-group-text">Rp</span>
                                                                                                    </div>
                                                                                                    <input
                                                                                                        class="form-control bg-light"
                                                                                                        name="total_bt_penginapan[]"
                                                                                                        id="total_bt_penginapan"
                                                                                                        type="text"
                                                                                                        min="0"
                                                                                                        value="0"
                                                                                                        readonly>
                                                                                                </div>
                                                                                            </div>
                                                                                            <button type="button"
                                                                                                id="add-more-bt-penginapan"
                                                                                                class="btn btn-primary mt-3">Add
                                                                                                More</button>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div> --}}

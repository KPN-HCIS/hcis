<div id="transport-card" class="card-body"
                                                                            style="display:">
                                                                            <div class="accordion"
                                                                                id="accordionTransport">
                                                                                <div class="accordion-item">
                                                                                    <h2 class="accordion-header"
                                                                                        id="headingTransport">
                                                                                        <button
                                                                                            class="accordion-button collapsed fw-medium"
                                                                                            type="button"
                                                                                            data-bs-toggle="collapse"
                                                                                            data-bs-target="#collapseTransport"
                                                                                            aria-expanded="false"
                                                                                            aria-controls="collapseTransport">
                                                                                            Transport Plan
                                                                                        </button>
                                                                                    </h2>
                                                                                    <div id="collapseTransport"
                                                                                        class="accordion-collapse collapse"
                                                                                        aria-labelledby="headingTransport">
                                                                                        <div class="accordion-body">
                                                                                            <div
                                                                                                id="form-container-bt-transport">
                                                                                                <div class="mb-2">
                                                                                                    <label
                                                                                                        class="form-label">
                                                                                                        Transport
                                                                                                        Date</label>
                                                                                                    <input type="date"
                                                                                                        name="tanggal_bt_transport[]"
                                                                                                        class="form-control"
                                                                                                        placeholder="mm/dd/yyyy">
                                                                                                </div>
                                                                                                <div class="mb-2">
                                                                                                    <label
                                                                                                        class="form-label"
                                                                                                        for="name">Company
                                                                                                        Code</label>
                                                                                                    <select
                                                                                                        class="form-control select2"
                                                                                                        id="companyFilter"
                                                                                                        name="company_bt_transport[]">
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
                                                                                                        class="form-label">Information</label>
                                                                                                    <textarea name="keterangan_bt_transport[]" class="form-control" placeholder="Write your information here..."></textarea>
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
                                                                                                        name="nominal_bt_transport[]"
                                                                                                        id="nominal_bt_transport[]"
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
                                                                                                    Transport</label>
                                                                                                <div class="input-group">
                                                                                                    <div
                                                                                                        class="input-group-append">
                                                                                                        <span
                                                                                                            class="input-group-text">Rp</span>
                                                                                                    </div>
                                                                                                    <input
                                                                                                        class="form-control bg-light"
                                                                                                        name="total_bt_transport[]"
                                                                                                        id="total_bt_transport[]"
                                                                                                        type="text"
                                                                                                        min="0"
                                                                                                        value="0"
                                                                                                        readonly>
                                                                                                </div>
                                                                                            </div>
                                                                                            <button type="button"
                                                                                                id="add-more-bt-transport"
                                                                                                class="btn btn-primary mt-3">Add
                                                                                                More</button>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>

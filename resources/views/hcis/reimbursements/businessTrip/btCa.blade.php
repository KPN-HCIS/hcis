<div class="row mt-2" id="ca_div" style="display: none;">
    <div class="col-md-12">
        <div class="table-responsive-sm">
            <div class="d-flex flex-column gap-2">
                <div class="text-bg-primary p-2"
                    style="text-align:center; border-radius:4px;">Cash Advanced</div>
                {{-- <div class="card"> --}}
                {{-- <div class="card-body"> --}}

                {{-- <div class="row"> --}}
                <div class="row" id="ca_bt" style="">
                    <div class="col-md-12">
                        <div class="table-responsive-sm">
                            <div class="d-flex flex-column gap-2">
                                <ul class="nav nav-pills mb-3" id="pills-tab"
                                    role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active"
                                            id="pills-perdiem-tab"
                                            data-bs-toggle="pill"
                                            data-bs-target="#pills-perdiem"
                                            type="button" role="tab"
                                            aria-controls="pills-perdiem"
                                            aria-selected="true">Perdiem Plan</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link"
                                            id="pills-transport-tab"
                                            data-bs-toggle="pill"
                                            data-bs-target="#pills-transport"
                                            type="button" role="tab"
                                            aria-controls="pills-transport"
                                            aria-selected="false">Transport
                                            Plan</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link"
                                            id="pills-accomodation-tab"
                                            data-bs-toggle="pill"
                                            data-bs-target="#pills-accomodation"
                                            type="button" role="tab"
                                            aria-controls="pills-accomodation"
                                            aria-selected="false">Accomodation
                                            Plan</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="pills-other-tab"
                                            data-bs-toggle="pill"
                                            data-bs-target="#pills-other"
                                            type="button" role="tab"
                                            aria-controls="pills-other"
                                            aria-selected="false">Other Plan</button>
                                    </li>

                                </ul>
                                <div class="card">
                                    <div class="tab-content" id="pills-tabContent">
                                        <div class="tab-pane fade show active"
                                            id="pills-perdiem" role="tabpanel"
                                            aria-labelledby="pills-perdiem-tab">
                                            {{-- ca perdiem content --}}
                                            @include('hcis.reimbursements.businessTrip.caPerdiem')
                                        </div>
                                        <div class="tab-pane fade"
                                            id="pills-transport" role="tabpanel"
                                            aria-labelledby="pills-transport-tab">
                                            {{-- ca transport content --}}
                                            @include('hcis.reimbursements.businessTrip.caTransport')
                                        </div>
                                        <div class="tab-pane fade"
                                            id="pills-accomodation" role="tabpanel"
                                            aria-labelledby="pills-accomodation-tab">
                                            {{-- ca accommodatioon content --}}
                                            @include('hcis.reimbursements.businessTrip.caAccommodation')</div>
                                        <div class="tab-pane fade" id="pills-other"
                                            role="tabpanel"
                                            aria-labelledby="pills-other-tab">
                                            {{-- ca others content --}}
                                            @include('hcis.reimbursements.businessTrip.caOther')</div>
                                    </div>

                                    <br>
                                    <div class="col-md-12 mb-2">
                                        <label class="form-label">Total Cash
                                            Advanced</label>
                                        <div class="input-group">
                                            <div class="input-group-append">
                                                <span
                                                    class="input-group-text">Rp</span>
                                            </div>
                                            <input class="form-control bg-light"
                                                name="totalca" id="totalca"
                                                type="text" min="0"
                                                value="0" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

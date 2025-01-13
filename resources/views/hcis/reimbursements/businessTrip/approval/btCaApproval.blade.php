<div class="row" id="ca_bt" style="">
    <div class="col-md-12">
        <div class="table-responsive-sm">
            <div class="row mb-2">
                <div class="col-md-6 mb-2">
                    <label for="date_required" class="form-label">Date Required</label>
                    <input type="date" class="form-control form-control-sm bg-light" id="date_required_2"
                        name="date_required" placeholder="Date Required" onchange="syncDateRequired(this)"
                        value="{{ $ca->date_required ?? 0 }}" readonly>
                </div>
                <div class="col-md-6 mb-2">
                    <label class="form-label" for="ca_decla">Declaration Estimate</label>
                    <input type="date" name="ca_decla" id="ca_decla_2" class="form-control form-control-sm bg-light"
                        placeholder="mm/dd/yyyy" value="{{ $ca->declare_estimate ?? 0 }}" readonly>
                </div>
            </div>
            <div class="d-flex flex-column">
                <ul class="nav mb-2" id="pills-tab" role="tablist">
                    @if ($group_company != 'KPN Plantations')
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="pills-meals-tab" data-bs-toggle="pill"
                                data-bs-target="#pills-meals" type="button" role="tab" aria-controls="pills-meals"
                                aria-selected="true">Meals</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="pills-transport-tab" data-bs-toggle="pill"
                                data-bs-target="#pills-transport" type="button" role="tab"
                                aria-controls="pills-transport" aria-selected="true">Transport</button>
                        </li>
                    @else
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="pills-transport-tab" data-bs-toggle="pill"
                                data-bs-target="#pills-transport" type="button" role="tab"
                                aria-controls="pills-transport" aria-selected="true">Transport</button>
                        </li>
                    @endif
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="pills-accomodation-tab" data-bs-toggle="pill"
                            data-bs-target="#pills-accomodation" type="button" role="tab"
                            aria-controls="pills-accomodation" aria-selected="false">Accomodation</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="pills-other-tab" data-bs-toggle="pill"
                            data-bs-target="#pills-other" type="button" role="tab" aria-controls="pills-other"
                            aria-selected="false">Others</button>
                    </li>

                </ul>
                {{-- <div class="card"> --}}
                <div class="tab-content" id="pills-tabContent">
                    @if ($group_company != 'KPN Plantations')
                        <div class="tab-pane fade show active" id="pills-meals" role="tabpanel"
                            aria-labelledby="pills-meals-tab">
                            {{-- ca meals content --}}
                            @include('hcis.reimbursements.businessTrip.approval.caMealsApproval')
                        </div>
                        <div class="tab-pane fade show" id="pills-transport" role="tabpanel"
                            aria-labelledby="pills-transport-tab">
                            {{-- ca transport content --}}
                            @include('hcis.reimbursements.businessTrip.approval.caTransportApproval')
                        </div>
                    @else
                        <div class="tab-pane fade show active" id="pills-transport" role="tabpanel"
                            aria-labelledby="pills-transport-tab">
                            {{-- ca transport content --}}
                            @include('hcis.reimbursements.businessTrip.approval.caTransportApproval')
                        </div>
                    @endif
                    <div class="tab-pane fade" id="pills-accomodation" role="tabpanel"
                        aria-labelledby="pills-accomodation-tab">
                        {{-- ca accommodatioon content --}}
                        @include('hcis.reimbursements.businessTrip.approval.caAccommodationApproval')</div>
                    <div class="tab-pane fade" id="pills-other" role="tabpanel" aria-labelledby="pills-other-tab">
                        {{-- ca others content --}}
                        @include('hcis.reimbursements.businessTrip.approval.caOtherApproval')
                    </div>
                </div>

                <br>
                <div class="col-md-12 mb-2">
                    <label class="form-label">Total Cash
                        Advanced</label>
                    <div class="input-group">
                        <div class="input-group-append">
                            <span class="input-group-text">Rp</span>
                        </div>
                        <input class="form-control bg-light" name="totalca" id="totalca" type="text"
                            min="0"
                            value="{{ isset($ca) && $ca->total_cost ? number_format($ca->total_cost, 0, ',', '.') : '0' }}"
                            readonly>
                    </div>
                </div>
                {{-- </div> --}}
            </div>
        </div>
    </div>
</div>

<div class="col-md-12">
    <ul class="nav nav-pills nav-tabs" id="pills-tab" role="tablist">
        <li class="nav-item" role="presentation" id="nav-perdiem">
            <a href="{{ route('approval.cashadvanced') }}" style="border-radius: 10px 10px 0px 0px"
                class="nav-link {{ request()->routeIs('approval.cashadvanced') ? 'active' : '' }}" id="pills-perdiem-tab" role="tab"
                aria-controls="pills-perdiem" aria-selected="false">Cash Advanced Approval</a>
        </li>
        <li class="nav-item" role="presentation" id="nav-cashAdvanced">
            <a href="{{ route('businessTrip.approval') }}" style="border-radius: 10px 10px 0px 0px"
                class="nav-link {{ request()->routeIs('businessTrip.approval') ? 'active' : '' }}" id="pills-cashAdvanced-tab" role="tab"
                aria-controls="pills-cashAdvanced" aria-selected="false">Bussiness Trip Approval</a>
        </li>
        <li class="nav-item" role="presentation" id="nav-ticket">
            <a href="{{ route('ticket.approval') }}" style="border-radius: 10px 10px 0px 0px"
                class="nav-link {{ request()->routeIs('ticket.approval') ? 'active' : '' }}" id="pills-ticket-tab" role="tab"
                aria-controls="pills-ticket" aria-selected="false">Ticket Approval</a>
        </li>
        <li class="nav-item" role="presentation" id="nav-hotel">
            <a href="{{ route('hotel.approval') }}" style="border-radius: 10px 10px 0px 0px"
                class="nav-link {{ request()->routeIs('hotel.approval') ? 'active' : '' }}" id="pills-hotel-tab" role="tab"
                aria-controls="pills-hotel" aria-selected="false">Hotel Approval</a>
        </li>
        <li class="nav-item" role="presentation" id="nav-medical">
            <a href="{{ route('medical.approval') }}" style="border-radius: 10px 10px 0px 0px"
                class="nav-link {{ request()->routeIs('medical.approval') ? 'active' : '' }}" id="pills-medical-tab" role="tab"
                aria-controls="pills-medical" aria-selected="false">Medical Approval</a>
        </li>
    </ul>
</div>

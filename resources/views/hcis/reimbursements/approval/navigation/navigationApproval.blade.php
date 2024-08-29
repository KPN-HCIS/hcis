<div class="row g-2 mb-3 justify-content-start">
    <div class=" col-6 col-sm-auto">
        <div class="mb-2">
            <a href="{{ route('approval.cashadvanced') }}"
                class="btn {{ request()->routeIs('approval.cashadvanced') ? 'btn-primary' : 'btn-outline-primary' }} rounded-pill shadow w-100 position-relative">
                Cash Advanced
                @if ( $pendingCACount >= 1 )
                    <span class="badge bg-danger position-absolute top-0 start-100 translate-middle">{{ $pendingCACount }}</span>
                @endif
            </a>
        </div>
    </div>
    <div class="col-6 col-sm-auto">
        <div class="mb-2">
            <a href="{{ route('approval.cashadvancedDeklarasi') }}"
                class="btn {{ request()->routeIs('approval.cashadvancedDeklarasi') ? 'btn-primary' : 'btn-outline-primary' }} rounded-pill shadow w-100 position-relative">
                Deklarasi Cash Advanced
                @if ( $pendingDECCount >= 1 )
                    <span class="badge bg-danger position-absolute top-0 start-100 translate-middle">{{ $pendingDECCount }}</span>
                @endif
            </a>
        </div>
    </div>
    <div class="col-6 col-sm-auto">
        <div class="mb-2">
            <a href="{{ route('cashadvanced.form') }}" class="btn btn-outline-primary rounded-pill shadow w-100 position-relative">
                Medical
                @if ( $pendingHTLCount >= 1 )
                    <span class="badge bg-danger position-absolute top-0 start-100 translate-middle">{{ $pendingHTLCount }}</span>
                @else

                @endif
            </a>
        </div>
    </div>
    <div class="col-6 col-sm-auto">
        <div class="mb-2">
            <a href="{{ route('cashadvanced.form') }}" class="btn btn-outline-primary rounded-pill shadow w-100 position-relative">
                Hometrip
                <span class="badge bg-danger position-absolute top-0 start-100 translate-middle">99</span>
            </a>
        </div>
    </div>
    <div class="col-6 col-sm-auto">
        <div class="mb-2">
            <a href="{{ route('cashadvanced.form') }}" class="btn btn-outline-primary rounded-pill shadow w-100 position-relative">
                Assessment
                <span class="badge bg-danger position-absolute top-0 start-100 translate-middle"></span>
            </a>
        </div>
    </div>
</div>

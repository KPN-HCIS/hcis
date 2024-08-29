<div class="row g-2 mt-1 mb-2 justify-content-start">
    <div class="col-6 col-sm-auto">
        <div class="mb-2">
            <a href="{{ route('cashadvanced') }}"
            class="btn {{ request()->routeIs('cashadvanced') ? 'btn-primary' : 'btn-outline-primary' }} rounded-pill shadow w-100 position-relative">
                Request
            </a>
        </div>
    </div>
    <div class="col-6 col-sm-auto">
        <div class="mb-2">
            <a href="{{ route('cashadvancedDeklarasi') }}"
            class="btn {{ request()->routeIs('cashadvancedDeklarasi') ? 'btn-primary' : 'btn-outline-primary' }} rounded-pill shadow w-100 position-relative">
                Declaration
                @if ( $deklarasiCACount >= 1 )
                    <span class="badge bg-danger position-absolute top-0 start-100 translate-middle">{{ $deklarasiCACount }}</span>
                @endif
            </a>
        </div>
    </div>
    <div class="col-6 col-sm-auto">
        <div class="mb-2">
            <a href="{{ route('cashadvancedDone') }}"
            class="btn {{ request()->routeIs('cashadvancedDone') ? 'btn-primary' : 'btn-outline-primary' }} rounded-pill shadow w-100 position-relative">
                Done
                {{-- @if ( $deklarasiCACount >= 1 ) --}}
                    {{-- <span class="badge bg-danger position-absolute top-0 start-100 translate-middle">{{ $deklarasiCACount }}</span> --}}
                {{-- @endif --}}
            </a>
        </div>
    </div>
</div>

{{-- <a href="{{ route('dashboard') }}" class="logo text-center logo-light">
    <span class="logo-lg">
        <img src="{{ asset('assets/images/logo/logo.png') }}" alt="" height="16" />
    </span>
    <span class="logo-sm">
        <img src="{{ asset('assets/images/logo/logo_sm.png') }}" alt="" height="16" />
    </span>
</a>

<a href="{{ route('dashboard') }}" class="logo text-center logo-dark">
    <span class="logo-lg">
        <img src="{{ asset('assets/images/logo/logo-dark.png') }}" alt="" height="16" />
    </span>
    <span class="logo-sm">
        <img src="{{ asset('assets/images/logo/logo_sm_dark.png') }}" alt="" height="16" />
    </span>
</a> --}}


{{-- logo light --}}
<a href="{{ route('dashboard') }}" class="logo text-center text-white px-2">
    <span class="logo-lg logo-text">
        <i class="fas fa-coins mr-2 text-info"></i>
        {{ env('APP_NAME', 'Laravel') }}
    </span>
    <span class="logo-sm logo-text">
        <i class="fas fa-coins text-info"></i>
    </span>
</a>

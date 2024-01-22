@php
    $layoutData['cssClasses'] =  'navbar navbar-expand-md navbar-overlap d-print-none bg-yellow-lt';
@endphp
<div class="page">
    <!-- Top Navbar -->
    @include('tenant.partials.navbar.overlap-topbar')
    <div class="page-wrapper">
        <!-- Page Content -->
        @yield('content')
        @include('tenant.partials.footer.bottom')
    </div>
</div>

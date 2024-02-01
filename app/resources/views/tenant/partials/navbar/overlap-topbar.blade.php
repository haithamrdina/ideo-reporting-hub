<header class="navbar navbar-expand-md navbar-overlap d-print-none bg-dark">
    <div class="container-xl">
        <button class="navbar-toggler bg-red rounded-4" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu"
                aria-controls="navbar-menu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <h1 class="navbar-brand navbar-brand-autodark d-none-navbar-horizontal pe-0 pe-md-3">
            @include('tenant.partials.common.logo')
        </h1>

        <div class="navbar-nav flex-row order-md-last">
            @include('tenant.partials.header.top-right')
        </div>

        <div class="collapse navbar-collapse" id="navbar-menu">
            <div class="d-flex flex-column flex-md-row flex-fill align-items-stretch align-items-md-center">
                <ul class="navbar-nav">
                    @include('tenant.partials.navbar.menu')
                </ul>
            </div>
        </div>
    </div>
</header>


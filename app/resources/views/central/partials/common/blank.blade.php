<div class="page-body">
    <div class="container-xl d-flex flex-column justify-content-center">
        <div class="empty">
            <div class="empty-img">
                <img src="{{ asset('static/illustrations/no-data-found.svg') }}" class="w-100" style="min-height: 20rem;" alt="">
            </div>
            <p class="empty-title">No results found</p>
            <p class="empty-subtitle text-muted">
                Try adjusting your search or filter to find what you're looking for.
            </p>
            <div class="empty-action">
                <a href="{{ route($routeName) }}" class="btn btn-red">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                        stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M12 5l0 14" />
                        <path d="M5 12l14 0" />
                    </svg>
                {{$title}}
                </a>
            </div>
        </div>
    </div>
</div>

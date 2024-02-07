<div>

    <div class="page">
        <!-- Top Navbar -->
        <livewire:tenant.partials.navbar.overlap-topbar />
        <div class="page-wrapper">
            <!-- Page Content -->
            <livewire:tenant.partials.pages.page-header :groupes="$groupes" />
            <div class="page-body">
                <div class="container-xl">
                    <div class="row row-deck row-cards">
                        <livewire:tenant.partials.contents.stat-inscrit-per-date
                            :statDateConfig="$contract_start_date_conf"
                            :date="$statDate"
                            :statsTimingPerDate="$statsTimingPerDate"
                            :statsInscriptionsPerDate="$statsInscriptionsPerDate"
                            :enrollfields="$enrollfields"
                        />
                    </div>
                </div>
            </div>
        </div>
        <livewire:tenant.partials.footer.bottom/>
    </div>

</div>

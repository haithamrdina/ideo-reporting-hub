<footer class="footer footer-transparent d-print-none">
    <div class="container-xl">
        <div class="row text-center align-items-center flex-row-reverse">
            <div class="col-12 col-lg-auto mt-3 mt-lg-0">
                <ul class="list-inline list-inline-dots mb-0">
                    <li class="list-inline-item">
                        Copyright &copy; {{ now()->year }} {{config('ideoreport.title', 'Ideo Reporting')}} by
                        <a href="https://ideo.ma"
                           class="link-secondary text-red">{{config('ideoreport.bottom_title', 'Ideo Factory')}}</a>.
                        All rights reserved -
                        <a href="javascript:void(0)" class="link-secondary text-red" rel="noopener">
                            {{config('ideoreport.current_version', '1.0')}}
                        </a> -
                    </li>
                </ul>
            </div>
        </div>
    </div>
</footer>

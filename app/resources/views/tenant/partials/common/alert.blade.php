@if (Session::has('message'))
    <div class="alert alert-info alert-dismissible bg-white" role="alert">
        <div class="d-flex">
            <div>
                <i class="ti ti-info-circle"></i>
            </div>
            <div>
                &nbsp;{{ Session('message') }}
            </div>
        </div>
        <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
    </div>
@elseif(Session::has('success'))
    <div class="alert alert-success alert-dismissible bg-white" role="alert">
        <div class="d-flex">
            <div>
                <i class="ti ti-circle-check"></i>
            </div>
            <div>
                &nbsp;{{ Session('success') }}
            </div>
        </div>
        <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
    </div>
@elseif(Session::has('error'))
    <div class="alert alert-danger alert-dismissible bg-white" role="alert">
        <div class="d-flex">
            <div>
                <i class="ti ti-exclamation-circle"></i>
            </div>
            <div>
                &nbsp;{{ Session('error') }}
            </div>
        </div>
        <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
    </div>
@endif

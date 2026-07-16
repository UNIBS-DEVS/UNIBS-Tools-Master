@if (Session::has('error'))
    <div class="alert alert-danger alert-dismissible fade show py-2 px-3 mb-2 small rounded-3 shadow-sm border-0"
        role="alert">

        <div class="d-flex align-items-center">

            <i class="fa-solid fa-circle-xmark me-2"></i>

            <span>
                {{ Session::get('error') }}
            </span>

        </div>
        <button type="button" class="btn-close shadow-none ms-auto" data-bs-dismiss="alert" style="padding: 0.8rem 1rem;">
        </button>

    </div>
@endif

@if (Session::has('success'))
    <div class="alert alert-success alert-dismissible fade show py-2 px-3 mb-2 small rounded-3 shadow-sm border-0"
        role="alert">

        <div class="d-flex align-items-center">

            <i class="fa-solid fa-circle-check me-2"></i>

            <span>
                {{ Session::get('success') }}
            </span>

        </div>

        <button type="button" class="btn-close shadow-none ms-auto" data-bs-dismiss="alert"
            style="padding: 0.8rem 1rem;">
        </button>

    </div>
@endif

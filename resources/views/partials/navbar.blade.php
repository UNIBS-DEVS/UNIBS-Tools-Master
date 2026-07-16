<nav class="navbar navbar-expand-lg px-4">
    <div class="container-fluid">

        <button class="btn btn-outline-secondary" id="toggleSidebar">
            <i class="fa-solid fa-bars" id="toggleIcon"></i>
        </button>

        <div class="dropdown ms-auto">
            <a href="#" class="d-flex align-items-center dropdown-toggle text-decoration-none"
                data-bs-toggle="dropdown">
                <i class="fa fa-user-circle fs-5 me-2"></i>
                <span class="fw-semibold">
                    {{ Auth::user()->name }}
                    ({{ implode(', ', Auth::user()->roles ?? []) }})
                </span>
            </a>

            <ul class="dropdown-menu dropdown-menu-end shadow">
                <li>
                    <a class="dropdown-item" href="{{ route('profile') }}">
                        <i class="fa fa-user me-2"></i> Profile
                    </a>
                </li>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="dropdown-item text-danger">
                            <i class="fa fa-right-from-bracket me-2"></i> Logout
                        </button>
                    </form>
                </li>
            </ul>
        </div>

    </div>
</nav>

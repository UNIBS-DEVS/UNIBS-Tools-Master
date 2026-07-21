<aside class="sidebar" id="sidebar">

    <!-- Logo -->
    <div class="sidebar-header p-3 border-bottom text-center">
        <img src="{{ asset('assets/images/company-logo.png') }}" class="sidebar-logo mb-2">
    </div>

    {{-- Dashboard --}}
    <a href="{{ route('dashboard.index') }}" class="{{ request()->routeIs('dashboard.index') ? 'active' : '' }}">
        <i class="fa fa-gauge"></i>
        <span>Dashboard</span>
    </a>

    {{-- User --}}
    @if (auth()->user()->hasRole(['admin', 'employee']))
        <a href="{{ route('users.index') }}" class="sidebar-submenu">
            <i class="fa fa-user"></i>
            <span>User</span>
        </a>
    @endif


    {{-- Accounts --}}
    @if (auth()->user()->hasRole(['admin', 'hr', 'accounts']))
        {{-- Accounts --}}
        <a data-bs-toggle="collapse" href="#accountsMenu">
            <i class="fa fa-gear"></i>
            <span class="flex-grow-1">Accounts</span>
            <i class="fa fa-chevron-down ms-auto small"></i>
        </a>

        <div class="collapse ps-4" id="accountsMenu">

            <a href="{{ route('settings.index') }}" class="sidebar-submenu">
                <i class="fa fa-gear"></i>
                <span>Invoices</span>
            </a>

            <a href="{{ route('reviews.index') }}" class="sidebar-submenu">
                <i class="fa fa-phone"></i>
                <span>Balance Sheet</span>
            </a>

            <a href="{{ route('settings.index') }}" class="sidebar-submenu">
                <i class="fa fa-gear"></i>
                <span>Salary</span>
            </a>

            <a href="{{ route('settings.index') }}" class="sidebar-submenu">
                <i class="fa fa-gear"></i>
                <span>GST</span>
            </a>

            <a href="{{ route('settings.index') }}" class="sidebar-submenu">
                <i class="fa fa-gear"></i>
                <span>TDS</span>
            </a>

            <a href="{{ route('settings.index') }}" class="sidebar-submenu">
                <i class="fa fa-gear"></i>
                <span>EPF</span>
            </a>

            <a href="{{ route('settings.index') }}" class="sidebar-submenu">
                <i class="fa fa-gear"></i>
                <span>ESI</span>
            </a>

            <a href="{{ route('settings.index') }}" class="sidebar-submenu">
                <i class="fa fa-gear"></i>
                <span>Reports</span>
            </a>
        </div>
    @endif

    {{-- Tools --}}
    @if (auth()->user()->hasRole(['admin']))
        {{-- Tools --}}
        <a data-bs-toggle="collapse" href="#toolsMenu">
            <i class="fa fa-toolbox"></i>
            <span class="flex-grow-1">Tools</span>
            <i class="fa fa-chevron-down ms-auto small"></i>
        </a>

        <div class="collapse ps-4" id="toolsMenu">

            {{-- LMS --}}
            <a data-bs-toggle="collapse" href="#lmsMenu">
                <i class="fa fa-book-open"></i>
                <span class="flex-grow-1">LMS</span>
                <i class="fa fa-chevron-down ms-auto small"></i>
            </a>

            <div class="collapse ps-4" id="lmsMenu">
                <a href="{{ route('lms.clients.index') }}" class="sidebar-submenu">
                    <i class="fa fa-building"></i>
                    <span>Clients</span>
                </a>
            </div>

            {{-- ATS --}}
            <a data-bs-toggle="collapse" href="#atsMenu">
                <i class="fa fa-user-tie"></i>
                <span class="flex-grow-1">ATS</span>
                <i class="fa fa-chevron-down ms-auto small"></i>
            </a>

            <div class="collapse ps-4" id="atsMenu">
                <a href="{{ route('ats.clients.index') }}" class="sidebar-submenu">
                    <i class="fa fa-building"></i>
                    <span>Clients</span>
                </a>
            </div>

            {{-- UNIOne --}}
            <a data-bs-toggle="collapse" href="#unioneMenu">
                <i class="fa fa-user-tie"></i>
                <span class="flex-grow-1">UNIOne</span>
                <i class="fa fa-chevron-down ms-auto small"></i>
            </a>

            <div class="collapse ps-4" id="unioneMenu">
                <a href="" class="sidebar-submenu">
                    <i class="fa fa-building"></i>
                    <span>Clients</span>
                </a>
            </div>

        </div>
    @endif

    {{-- Tools --}}
    @if (auth()->user()->hasRole(['admin']))
        {{-- Tools --}}
        <a data-bs-toggle="collapse" href="#systemSettingsMenu">
            <i class="fa fa-toolbox"></i>
            <span class="flex-grow-1">System Settings</span>
            <i class="fa fa-chevron-down ms-auto small"></i>
        </a>

        <div class="collapse ps-4" id="systemSettingsMenu">

            <a href="{{ route('tools-master.edit') }}" class="sidebar-submenu">
                <i class="fa fa-gear"></i>
                <span>Tool Master</span>
            </a>

            <a href="" class="sidebar-submenu">
                <i class="fa fa-gear"></i>
                <span>Applications</span>
            </a>

            <a href="{{ route('upload-mobile-app.index') }}" class="sidebar-submenu">
                <i class="fa fa-gear"></i>
                <span>Mobile Application</span>
            </a>


        </div>
    @endif

    {{-- db inspector --}}
    @if (auth()->user()->hasRole(['admin', 'db_inspector']))
        {{-- --}}
        <a data-bs-toggle="collapse" href="#dbInspectorMenu">
            <i class="fa fa-database"></i>
            <span class="flex-grow-1">DB Inspector</span>
            <i class="fa fa-chevron-down ms-auto small"></i>
        </a>

        <div class="collapse ps-4" id="dbInspectorMenu">

            <a href="" class="sidebar-submenu">
                <i class="fa fa-gear"></i>
                <span>DB inspector</span>
            </a>

        </div>
    @endif

</aside>

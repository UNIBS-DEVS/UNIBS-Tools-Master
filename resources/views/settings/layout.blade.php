@extends('layouts.app')

@section('title', 'Settings')

@push('styles')
    <style>
        .settings-sidebar .list-group-item {
            border: none;
            padding: 12px 15px;
            font-size: 14px;
            color: #555;
            transition: all 0.2s ease-in-out;
            border-radius: 6px;
            margin-bottom: 5px;
        }

        .settings-sidebar .list-group-item:hover {
            background-color: #f5f7fb;
            color: #0d6efd;
        }

        .settings-sidebar .list-group-item.active {
            background-color: #0d6efd;
            color: #fff;
        }

        .settings-sidebar i {
            font-size: 16px;
        }

        .settings-card {
            border-radius: 10px;
        }
    </style>
@endpush

@section('content')
    <!-- <h1 class="h3 mb-4 text-gray-800">Settings</h1> -->

    <div class="row">
        <div class="col-md-2">
            <div class="list-group">
                {{-- <a href="{{ route('settings.address') }}"
                    class="list-group-item list-group-item-action {{ request()->routeIs('settings.address') ? 'active' : '' }}">
                    <i class="bi bi-geo-alt me-2"></i> Address
                </a>
                <a href="{{ route('settings.industries.index') }}"
                    class="list-group-item list-group-item-action {{ request()->routeIs('settings.industries.*') ? 'active' : '' }}">
                    <i class="bi bi-building me-2"></i> Industries
                </a>
                <a href="{{ route('settings.sources.index') }}"
                    class="list-group-item list-group-item-action {{ request()->routeIs('settings.sources.*') ? 'active' : '' }}">
                    <i class="bi bi-share me-2"></i> Sources
                </a>
                <a href="{{ route('settings.companies.index') }}"
                    class="list-group-item list-group-item-action {{ request()->routeIs('settings.companies.*') ? 'active' : '' }}">
                    <i class="bi bi-briefcase me-2"></i> Companies
                </a>
                <a href="{{ route('settings.skills.index') }}"
                    class="list-group-item list-group-item-action {{ request()->routeIs('settings.skills.*') ? 'active' : '' }}">
                    <i class="bi bi-lightning me-2"></i> Skills
                </a>
                <a href="{{ route('settings.education-types.index') }}"
                    class="list-group-item list-group-item-action {{ request()->routeIs('settings.education-types.*') ? 'active' : '' }}">
                    <i class="bi bi-book me-2"></i> Education Type
                </a> --}}
                <a href="{{ route('settings.system-settings.index') }}"
                    class="list-group-item list-group-item-action {{ request()->routeIs('settings.system-settings.*') ? 'active' : '' }}">
                    <i class="fa fa-gear me-2"></i> System Settings
                </a>
            </div>
        </div>
        <div class="col-md-10">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">@yield('settings-title')</h6>
                    @yield('settings-actions')
                </div>
                <div class="card-body">
                    @yield('settings-content')
                </div>
            </div>
        </div>
    </div>
@endsection

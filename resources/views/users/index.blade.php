@extends('layouts.app')

@section('title', 'Users | Unibs Tools')

@push('styles')
    <style>
        .table tbody tr td,
        .table thead tr th {
            padding: .15rem .5rem;
            font-size: 13px;
        }
    </style>
@endpush

@section('content')

    {{-- Flash Messages --}}
    @include('partials.message')

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-2 sales-header">

        <div class="lh-sm">
            <h4 class="page-title mb-0">Users</h4>
            <span class="page-subtitle">Manage all users</span>
        </div>

        <div class="d-flex align-items-center gap-1">

            @if (auth()->user()->hasRole(['admin']))
                <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i></a>
            @endif
        </div>
    </div>

    {{-- FILTERS --}}
    <div class="card filter-card border-0 mb-3">

        <div class="card-body py-2">

            <form method="POST" action="{{ route('users.filter') }}" id="filterForm">

                @csrf

                <div class="row g-2 align-items-end">

                    {{-- Name --}}
                    <div class="col-lg-2 col-md-6">

                        <input type="text" name="name" class="form-control form-control-sm" placeholder="Name"
                            value="{{ $filters['name'] ?? '' }}">
                    </div>

                    {{-- Email --}}
                    <div class="col-lg-2 col-md-6">

                        <input type="text" name="email" class="form-control form-control-sm" placeholder="Email"
                            value="{{ $filters['email'] ?? '' }}">
                    </div>

                    {{-- Personal Mobile --}}
                    <div class="col-lg-2 col-md-6">

                        <input type="text" name="personal_mobile" class="form-control form-control-sm"
                            placeholder="Personal Mobile" value="{{ $filters['personal_mobile'] ?? '' }}">
                    </div>

                    {{-- Offical Mobile --}}
                    <div class="col-lg-2 col-md-6">

                        <input type="text" name="offical_mobile" class="form-control form-control-sm"
                            placeholder="Offical Mobile" value="{{ $filters['offical_mobile'] ?? '' }}">
                    </div>

                    {{-- Role --}}
                    @php
                        $allRoles = ['Admin', 'Manager', 'API User', 'Accounts', 'Customer', 'DB Integration'];
                    @endphp

                    <div class="col-lg-2 col-md-6">
                        <select name="role" class="form-select form-select-sm">
                            <option value="">-- All Roles --</option>

                            @foreach ($allRoles as $role)
                                <option value="{{ $role }}"
                                    {{ ($filters['role'] ?? '') == $role ? 'selected' : '' }}>
                                    {{ $role }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Status --}}
                    <div class="col-lg-2 col-md-6">
                        <select name="status" class="form-select form-select-sm">
                            <option value="">-- All Status --</option>
                            <option value="active" {{ ($filters['status'] ?? '') == 'active' ? 'selected' : '' }}>
                                Active
                            </option>
                            <option value="inactive" {{ ($filters['status'] ?? '') == 'inactive' ? 'selected' : '' }}>
                                Inactive
                            </option>
                        </select>
                    </div>

                    {{-- SEARCH --}}
                    <div class="col-lg-12 col-md-12 col-12 text-end">
                        <button type="submit" id="searchBtn" class="btn btn-primary compact-search btn-sm">
                            <i class="fa fa-search"></i>
                        </button>

                        <a href="{{ route('users.filter.reset') }}" class="btn btn-secondary compact-reset btn-sm">
                            <i class="fa fa-rotate"></i>
                        </a>
                    </div>

                </div>

            </form>

        </div>

    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle bg-white">
            <thead class="table-dark">

                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Personal Mob.</th>
                    <th>Offical Mob.</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th width="130" class="text-center">Actions</th>
                </tr>

            </thead>

            <tbody>
                @forelse ($users as $user)
                    <tr>
                        <td>{{ $user->name ?? 'None' }}</td>
                        <td>{{ $user->email ?? 'None' }}</td>
                        <td>{{ $user->personal_mobile ?? 'None' }}</td>
                        <td>{{ $user->offical_mobile ?? 'None' }}</td>
                        <td>{{ collect($user->roles)->map(fn($r) => ucfirst($r))->join(', ') }}</td>
                        <td>
                            <span class="badge {{ $user->status === 'inactive' ? 'bg-danger' : 'bg-success' }}">
                                {{ ucfirst($user->status ?? 'active') }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex justify-content-center align-items-center gap-1">

                                <!-- View -->
                                <a href="{{ route('users.show', $user->id) }}" class="btn btn-outline-info btn-sm"
                                    title="View">
                                    <i class="fa fa-eye"></i>
                                </a>

                                <!-- Edit -->
                                @if (Auth::user()->hasRole('admin'))
                                    <a href="{{ route('users.edit', $user->id) }}" class="btn btn-outline-warning btn-sm"
                                        title="Edit">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                @endif

                                <!-- Delete -->
                                @if (Auth::user()->hasRole('admin'))
                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="m-0">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" class="btn btn-outline-danger btn-sm" title="Delete"
                                            onclick="return confirm('Delete this user?')">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                @endif

                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center">
                            No users found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div>
        {{ $users->appends(request()->query())->links() }}
    </div>

@endsection

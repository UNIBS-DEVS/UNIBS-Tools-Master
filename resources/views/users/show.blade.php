@extends('layouts.app')

@section('title', 'View User | Unibs Tools')

@section('content')

    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8 col-sm-12">

            {{-- Header --}}
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="mb-0">User Details</h4>
                <div>
                    @if (Auth::user()->role == 'admin')
                        <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning btn-sm">
                            <i class="fa fa-edit"></i>
                        </a>
                    @endif

                    <a href="{{ route('users.index') }}" class="btn btn-secondary btn-sm ms-1">
                        <i class="fa fa-arrow-left"></i>
                    </a>
                </div>
            </div>

            {{-- Card --}}
            <div class="card shadow-sm">
                <div class="card-body">

                    <div class="row mb-2">
                        <div class="col-4 fw-bold">Name</div>
                        <div class="col-8">{{ $user->name ?? 'None' }}</div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-4 fw-bold">Email</div>
                        <div class="col-8">{{ $user->email ?? 'None' }}</div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-4 fw-bold">Personal Mobile</div>
                        <div class="col-8">{{ $user->personal_mobile ?? 'None' }}</div>
                    </div>


                    <div class="row mb-2">
                        <div class="col-4 fw-bold">Offical Mobile</div>
                        <div class="col-8">{{ $user->offical_mobile ?? 'None' }}</div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-4 fw-bold">Roles</div>
                        <div class="col-8">
                            @foreach ($user->roles ?? [] as $role)
                                <span class="badge bg-info me-1">
                                    {{ ucfirst($role) }}
                                </span>
                            @endforeach
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-4 fw-bold">Status</div>
                        <div class="col-8">
                            <span class="badge {{ $user->status === 'inactive' ? 'bg-danger' : 'bg-success' }}">
                                {{ ucfirst($user->status) }}
                            </span>
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-4 fw-bold">Manager</div>
                        <div class="col-8">
                            {{ $user->manager?->name ?? '-' }}
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-4 fw-bold">Created</div>
                        <div class="col-8">
                            {{ $user->created_at->format('d M Y, h:i A') }}
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>

@endsection

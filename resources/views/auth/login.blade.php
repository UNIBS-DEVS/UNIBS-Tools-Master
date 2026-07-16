@extends('layouts.app')

@section('title', 'Login | Unibs Tools')

@php
    $hideLayout = true;
@endphp

@section('content')

    <div class="card shadow-lg w-100" style="max-width: 370px; border-radius: 12px;">

        <div class="card-header mt-3 text-center bg-transparent border-0">
            <h4 class="fw-bold">Unibs Tools Master</h4>
            <p class="text-muted mb-0">Admin Login</p>
        </div>

        {{-- ✅ MOVE MESSAGE HERE --}}
        <div class="px-4">
            @include('partials.message')
        </div>

        <div class="card-body px-4 py-1">
            @if ($authType === 'basic')
                <form method="POST" action="{{ route('login.authenticate') }}">
                    @csrf

                    <!-- Email -->
                    <div class="">
                        <label class="form-label">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fa fa-envelope"></i>
                            </span>
                            <input type="email" name="email" value="{{ old('email') }}"
                                class="form-control @error('email') is-invalid @enderror" placeholder="Enter email">
                        </div>
                        @error('email')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fa fa-lock"></i>
                            </span>
                            <input type="password" name="password"
                                class="form-control @error('password') is-invalid @enderror" placeholder="Enter password">
                        </div>
                        @error('password')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        Login
                    </button>
                </form>
            @else
                <a href="{{ route('microsoft.redirect') }}" class="btn btn-outline-dark w-100">
                    Login with Microsoft
                </a>
            @endif
        </div>

        <div class="card-footer text-center text-muted bg-transparent border-0">
            {{-- Logo --}}
            <div class="mb-2">
                <img src="{{ asset('assets/images/company-logo.png') }}" alt="Unibs LMS" style="height: 40px;">
            </div>

            © {{ date('Y') }} Unibs Tools Master. All rights reserved.
        </div>
    </div>

@endsection

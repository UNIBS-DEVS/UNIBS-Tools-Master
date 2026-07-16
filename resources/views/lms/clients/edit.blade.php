@extends('layouts.app')

@section('title', 'Edit Client | Unibs Tools')

@section('content')
    <div class="container mt-4">

        <div class="row justify-content-center">
            <div class="col-xl-12 col-lg-12">

                <div class="card shadow-sm border-0">

                    <!-- Header -->
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0 fw-semibold">
                            <i class="fa fa-edit me-2 text-primary"></i>
                            Edit Client
                        </h5>
                    </div>

                    <!-- Body -->
                    <div class="card-body">
                        <form action="{{ route('lms.clients.update', $client->id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="row g-4">

                                {{-- Client Code --}}
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">
                                        Client Code <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="fa fa-hashtag"></i>
                                        </span>
                                        <input type="text" name="client_code"
                                            value="{{ old('client_code', $client->client_code) }}"
                                            class="form-control @error('client_code') is-invalid @enderror">
                                        @error('client_code')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Client Name --}}
                                <div class="col-md-8">
                                    <label class="form-label fw-semibold">
                                        Client Name <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="fa fa-building"></i>
                                        </span>
                                        <input type="text" name="client_name"
                                            value="{{ old('client_name', $client->client_name) }}"
                                            class="form-control @error('client_name') is-invalid @enderror">
                                        @error('client_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Shipping Address --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Ship To Address</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="fa fa-map-marker-alt"></i>
                                        </span>
                                        <textarea name="client_ship_to_address" class="form-control @error('client_ship_to_address') is-invalid @enderror"
                                            rows="2">{{ old('client_ship_to_address', $client->client_ship_to_address) }}</textarea>
                                        @error('client_ship_to_address')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Billing Address --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Bill To Address</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="fa fa-map"></i>
                                        </span>
                                        <textarea name="client_bill_to_address" class="form-control @error('client_bill_to_address') is-invalid @enderror"
                                            rows="2">{{ old('client_bill_to_address', $client->client_bill_to_address) }}</textarea>
                                        @error('client_bill_to_address')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- GST --}}
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">GST</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="fa fa-file-invoice"></i>
                                        </span>
                                        <input type="text" name="client_gst"
                                            value="{{ old('client_gst', $client->client_gst) }}" class="form-control">
                                    </div>
                                </div>

                                {{-- PAN --}}
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">PAN</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="fa fa-id-card"></i>
                                        </span>
                                        <input type="text" name="client_pan"
                                            value="{{ old('client_pan', $client->client_pan) }}" class="form-control">
                                    </div>
                                </div>

                                {{-- Status --}}
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">
                                        Status <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="fa fa-toggle-on"></i>
                                        </span>
                                        <select name="status" class="form-select @error('status') is-invalid @enderror">
                                            <option value="">-- Select Status --</option>
                                            <option value="active"
                                                {{ old('status', $client->status) == 'active' ? 'selected' : '' }}>
                                                Active
                                            </option>
                                            <option value="inactive"
                                                {{ old('status', $client->status) == 'inactive' ? 'selected' : '' }}>
                                                Inactive
                                            </option>
                                        </select>
                                        @error('status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- SPOC Name --}}
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">SPOC Name</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="fa fa-user"></i>
                                        </span>
                                        <input type="text" name="client_spoc_name"
                                            value="{{ old('client_spoc_name', $client->client_spoc_name) }}"
                                            class="form-control">
                                    </div>
                                </div>

                                {{-- SPOC Email --}}
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">SPOC Email</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="fa fa-envelope"></i>
                                        </span>
                                        <input type="email" name="client_spoc_email"
                                            value="{{ old('client_spoc_email', $client->client_spoc_email) }}"
                                            class="form-control">
                                    </div>
                                </div>

                                {{-- SPOC Mobile --}}
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">SPOC Mobile</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="fa fa-phone"></i>
                                        </span>
                                        <input type="text" name="client_spoc_mobile"
                                            value="{{ old('client_spoc_mobile', $client->client_spoc_mobile) }}"
                                            class="form-control">
                                    </div>
                                </div>

                            </div>

                            {{-- Actions --}}
                            <div class="d-flex justify-content-end mt-5 gap-2">
                                <a href="{{ route('lms.clients.index') }}" class="btn btn-secondary">
                                    <i class="fa fa-arrow-left"></i>
                                </a>
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="fa fa-save me-1"></i> Update Client
                                </button>
                            </div>

                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

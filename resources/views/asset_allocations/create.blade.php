@extends('layouts.app')

@section('title', 'Allocate Asset | Unibs Tools')

@section('content')
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm border-0">
                    {{-- Header --}}
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0 fw-semibold text-dark">
                            <i class="fa fa-share-alt me-2 text-primary"></i>
                            Allocate Asset
                        </h5>
                    </div>

                    {{-- Body --}}
                    <div class="card-body p-4">
                        <form method="POST" action="{{ route('asset-allocations.store') }}">
                            @csrf

                            {{-- Select Asset --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    Select Asset <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="fa fa-box-open"></i>
                                    </span>
                                    @if (request('asset_id'))
                                        @php
                                            $preselectedAsset = $assets->firstWhere('id', request('asset_id'));
                                        @endphp
                                        <select class="form-select" disabled>
                                            @if ($preselectedAsset)
                                                <option value="{{ $preselectedAsset->id }}" selected>
                                                    {{ $preselectedAsset->asset_name }} (Code: {{ $preselectedAsset->asset_code }}, Cat: {{ $preselectedAsset->asset_category }})
                                                </option>
                                            @else
                                                @php
                                                    $fallbackAsset = \App\Models\AssetMaster::find(request('asset_id'));
                                                @endphp
                                                @if ($fallbackAsset)
                                                    <option value="{{ $fallbackAsset->id }}" selected>
                                                        {{ $fallbackAsset->asset_name }} (Code: {{ $fallbackAsset->asset_code }}, Cat: {{ $fallbackAsset->asset_category }})
                                                    </option>
                                                @endif
                                            @endif
                                        </select>
                                        <input type="hidden" name="asset_id" value="{{ request('asset_id') }}">
                                    @else
                                        <select name="asset_id" class="form-select @error('asset_id') is-invalid @enderror" required>
                                            <option value="">-- Choose Asset --</option>
                                            @foreach ($assets as $asset)
                                                <option value="{{ $asset->id }}" {{ old('asset_id') == $asset->id ? 'selected' : '' }}>
                                                    {{ $asset->asset_name }} (Code: {{ $asset->asset_code }}, Cat: {{ $asset->AssetMaster_category }})
                                                </option>
                                            @endforeach
                                        </select>
                                    @endif
                                    @error('asset_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-text text-muted small">Only assets with status "Available" are listed here.
                                </div>
                            </div>

                            {{-- Select Employee --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    Allocate To Employee <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="fa fa-user"></i>
                                    </span>
                                    <select name="employee_id"
                                        class="form-select @error('employee_id') is-invalid @enderror" required>
                                        <option value="">-- Choose Employee --</option>
                                        @foreach ($employees as $employee)
                                            <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                                {{ $employee->name }} ({{ $employee->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('employee_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Dates & Status Row --}}
                            {{-- <div class="row g-3 mb-3"> --}}


                                {{--Dates & Status Row --}}
                                <div class="row g-3 mb-2">

                                    {{-- Allocated Date --}}
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">
                                            Allocated Date <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light">
                                                <i class="fa fa-calendar"></i>
                                            </span>
                                            <input type="datetime-local" name="allocated_date"
                                                class="form-control @error('allocated_date') is-invalid @enderror"
                                                value="{{ old('allocated_date', now()->format('Y-m-d\TH:i')) }}" required>
                                            @error('allocated_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    {{-- end Date --}}
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">
                                            End Date <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light">
                                                <i class="fa fa-calendar"></i>
                                            </span>
                                            <input type="datetime-local" name="end_date"
                                                class="form-control @error('end_date') is-invalid @enderror"
                                                value="{{ old('end_date', now()->format('Y-m-d\TH:i')) }}" required>
                                            @error('end_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Allocation Status --}}
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">
                                            Allocation Status <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light">
                                                <i class="fa fa-toggle-on"></i>
                                            </span>
                                            <select name="status" class="form-select @error('status') is-invalid @enderror"
                                                required>
                                                <option value="Allocated" {{ old('status', 'Allocated') == 'Allocated' ? 'selected' : '' }}>Allocated</option>
                                                <option value="Reserved" {{ old('status') == 'Reserved' ? 'selected' : '' }}>
                                                    Reserved</option>
                                            </select>
                                            @error('status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                {{-- Remarks --}}
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Remarks</label>
                                    <textarea name="remarks" class="form-control @error('remarks') is-invalid @enderror"
                                        rows="3"
                                        placeholder="Enter any allocation notes or remarks...">{{ old('remarks') }}</textarea>
                                    @error('remarks')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Submit Actions --}}
                                <div class="d-flex justify-content-end gap-2 border-top pt-3">
                                    <a href="{{ route('asset.index') }}" class="btn btn-outline-secondary px-4">
                                        Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary px-4">
                                        <i class="fa fa-check-circle me-1"></i> Save Allocation
                                    </button>
                                </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
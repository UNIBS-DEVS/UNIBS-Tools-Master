@extends('layouts.app')

@section('title', 'Edit Allocation | Unibs Tools')

@section('content')
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm border-0">
                    {{-- Header --}}
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0 fw-semibold text-dark">
                            <i class="fa fa-edit me-2 text-warning"></i>
                            Update / Return Asset Allocation
                        </h5>
                    </div>

                    {{-- Body --}}
                    <div class="card-body p-4">
                        <form method="POST" action="{{ route('asset-allocations.update', $allocation->id) }}">
                            @csrf
                            @method('PUT')

                            {{-- Select Asset --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    Allocated Asset <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="fa fa-box-open"></i>
                                    </span>
                                    @php
                                        $preselectedAsset = $assets->firstWhere('id', $allocation->asset_id);
                                    @endphp
                                    <select class="form-select" disabled>
                                        @if ($preselectedAsset)
                                            <option value="{{ $preselectedAsset->id }}" selected>
                                                {{ $preselectedAsset->asset_name }} (Code: {{ $preselectedAsset->asset_code }}, Cat: {{ $preselectedAsset->AssetMaster_category }})
                                            </option>
                                        @else
                                            @php
                                                $fallbackAsset = \App\Models\AssetMaster::find($allocation->asset_id);
                                            @endphp
                                            @if ($fallbackAsset)
                                                <option value="{{ $fallbackAsset->id }}" selected>
                                                    {{ $fallbackAsset->asset_name }} (Code: {{ $fallbackAsset->asset_code }}, Cat: {{ $fallbackAsset->AssetMaster_category }})
                                                </option>
                                            @endif
                                        @endif
                                    </select>
                                    <input type="hidden" name="asset_id" value="{{ $allocation->asset_id }}">
                                    @error('asset_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Select Employee --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    Employee <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="fa fa-user"></i>
                                    </span>
                                    @php
                                        $preselectedEmp = $employees->firstWhere('id', $allocation->employee_id);
                                    @endphp
                                    <select class="form-select" disabled>
                                        @if ($preselectedEmp)
                                            <option value="{{ $preselectedEmp->id }}" selected>
                                                {{ $preselectedEmp->name }} ({{ $preselectedEmp->email }})
                                            </option>
                                        @else
                                            @php
                                                $fallbackEmp = \App\Models\User::find($allocation->employee_id);
                                            @endphp
                                            @if ($fallbackEmp)
                                                <option value="{{ $fallbackEmp->id }}" selected>
                                                    {{ $fallbackEmp->name }} ({{ $fallbackEmp->email }})
                                                </option>
                                            @endif
                                        @endif
                                    </select>
                                    <input type="hidden" name="employee_id" value="{{ $allocation->employee_id }}">
                                    @error('employee_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Dates Row --}}
                            <div class="row g-3 mb-3">
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
                                            value="{{ old('allocated_date', $allocation->allocated_date ? $allocation->allocated_date->format('Y-m-d\TH:i') : '') }}"
                                            required>
                                        @error('allocated_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>



                                {{-- Returned Date --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        Returned Date
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="fa fa-calendar-check"></i>
                                        </span>
                                        <input type="datetime-local" name="returned_date"
                                            class="form-control @error('returned_date') is-invalid @enderror"
                                            value="{{ old('returned_date', $allocation->returned_date ? $allocation->returned_date->format('Y-m-d\TH:i') : '') }}">
                                        @error('returned_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-text text-muted small">Fill this in when returning the asset.</div>
                                </div>
                            </div>

                            {{-- Status & Remarks --}}
                            <div class="row g-3 mb-3">
                                {{-- Allocation Status --}}
                                <div class="col-md-12">
                                    <label class="form-label fw-semibold">
                                        Allocation Status <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="fa fa-toggle-on"></i>
                                        </span>
                                        <select name="status" class="form-select @error('status') is-invalid @enderror"
                                            required>
                                            <option value="Allocated" {{ old('status', $allocation->status) == 'Allocated' ? 'selected' : '' }}>Allocated</option>
                                            <option value="Returned" {{ old('status', $allocation->status) == 'Returned' ? 'selected' : '' }}>Returned</option>
                                            <option value="Damaged" {{ old('status', $allocation->status) == 'Damaged' ? 'selected' : '' }}>Damaged</option>
                                            <option value="Lost" {{ old('status', $allocation->status) == 'Lost' ? 'selected' : '' }}>Lost</option>
                                            <option value="Reserved" {{ old('status', $allocation->status) == 'Reserved' ? 'selected' : '' }}>Reserved</option>
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
                                    placeholder="Enter any allocation notes or remarks...">{{ old('remarks', $allocation->remarks) }}</textarea>
                                @error('remarks')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Submit Actions --}}
                            <div class="d-flex justify-content-end gap-2 border-top pt-3">
                                <a href="{{ route('asset.index') }}" class="btn btn-outline-secondary px-4">
                                    Cancel
                                </a>
                                <button type="submit" class="btn btn-warning px-4 text-dark fw-semibold">
                                    <i class="fa fa-save me-1"></i> Update Allocation
                                </button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
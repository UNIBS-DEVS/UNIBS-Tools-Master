@extends('layouts.app')

@section('title', 'Log SIM Recharge | Unibs Tools')

@section('content')
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm border-0">
                    {{-- Header --}}
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0 fw-semibold text-dark">
                            <i class="fa fa-bolt me-2 text-primary"></i>
                            Log SIM Recharge
                        </h5>
                    </div>

                    {{-- Body --}}
                    <div class="card-body p-4">
                        <form method="POST" action="{{ route('sim-recharges.store') }}">
                            @csrf

                            {{-- Select Asset --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    Select SIM Asset <span class="text-danger">*</span>
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
                                                    {{ $preselectedAsset->asset_name }} (Code: {{ $preselectedAsset->asset_code }})
                                                </option>
                                            @else
                                                @php
                                                    $fallbackAsset = \App\Models\AssetMaster::find(request('asset_id'));
                                                @endphp
                                                @if ($fallbackAsset)
                                                    <option value="{{ $fallbackAsset->id }}" selected>
                                                        {{ $fallbackAsset->asset_name }} (Code: {{ $fallbackAsset->asset_code }})
                                                    </option>
                                                @endif
                                            @endif
                                        </select>
                                        <input type="hidden" name="asset_id" value="{{ request('asset_id') }}">
                                    @else
                                        <select name="asset_id" class="form-select @error('asset_id') is-invalid @enderror" required>
                                            <option value="" disabled selected>-- Choose Asset --</option>
                                            @foreach ($assets as $asset)
                                                <option value="{{ $asset->id }}" {{ old('asset_id') == $asset->id ? 'selected' : '' }}>
                                                    {{ $asset->asset_name }} (Code: {{ $asset->asset_code }}, Cat: {{ $asset->asset_category }})
                                                </option>
                                            @endforeach
                                        </select>
                                    @endif
                                    @error('asset_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Plan Name --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Plan Name</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="fa fa-mobile-alt"></i>
                                    </span>
                                    <input type="text" name="plan_name" class="form-control @error('plan_name') is-invalid @enderror" placeholder="e.g. Airtel 2.5GB/Day Plan" value="{{ old('plan_name') }}">
                                    @error('plan_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Recharge Date & Validity --}}
                            <div class="row g-3 mb-3">
                                {{-- Recharge Date --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Recharge Date <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="fa fa-calendar"></i>
                                        </span>
                                        <input type="date" name="recharge_date" class="form-control @error('recharge_date') is-invalid @enderror" value="{{ old('recharge_date', now()->format('Y-m-d')) }}" required>
                                        @error('recharge_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Validity Days --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Validity (Days)</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="fa fa-clock"></i>
                                        </span>
                                        <input type="number" name="validity_days" class="form-control @error('validity_days') is-invalid @enderror" placeholder="e.g. 28, 84, 365" value="{{ old('validity_days') }}">
                                        @error('validity_days')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- Cost & Expiry Date --}}
                            <div class="row g-3 mb-3">
                                {{-- Recharge Amount --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Recharge Amount (₹)</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">₹</span>
                                        <input type="number" step="0.01" name="recharge_amount" class="form-control @error('recharge_amount') is-invalid @enderror" placeholder="0.00" value="{{ old('recharge_amount') }}">
                                        @error('recharge_amount')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Expiry Date --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Expiry Date</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="fa fa-calendar-check"></i>
                                        </span>
                                        <input type="date" name="expiry_date" class="form-control @error('expiry_date') is-invalid @enderror" value="{{ old('expiry_date') }}">
                                        @error('expiry_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- Remarks --}}
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Remarks</label>
                                <textarea name="remarks" class="form-control @error('remarks') is-invalid @enderror" rows="3" placeholder="Enter any additional notes...">{{ old('remarks') }}</textarea>
                                @error('remarks')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Submit Actions --}}
                            <div class="d-flex justify-content-end gap-2 border-top pt-3">
                                <a href="{{ route('sim-recharges.index') }}" class="btn btn-outline-secondary px-4">
                                    Cancel
                                </a>
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="fa fa-check-circle me-1"></i> Log Recharge
                                </button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Auto Expiry Date Calculation --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const rechargeDateInput = document.querySelector('input[name="recharge_date"]');
            const validityInput = document.querySelector('input[name="validity_days"]');
            const expiryInput = document.querySelector('input[name="expiry_date"]');

            function calculateExpiry() {
                const rechargeDateVal = rechargeDateInput.value;
                const validityVal = parseInt(validityInput.value);

                if (rechargeDateVal && !isNaN(validityVal) && validityVal > 0) {
                    const date = new Date(rechargeDateVal);
                    // Add validity days
                    date.setDate(date.getDate() + validityVal);
                    
                    // Format back to YYYY-MM-DD
                    const year = date.getFullYear();
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    const day = String(date.getDate()).padStart(2, '0');
                    
                    expiryInput.value = `${year}-${month}-${day}`;
                }
            }

            rechargeDateInput.addEventListener('change', calculateExpiry);
            validityInput.addEventListener('input', calculateExpiry);
        });
    </script>
@endsection

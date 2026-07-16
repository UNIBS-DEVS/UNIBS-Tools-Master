@extends('layouts.app')

@section('title', 'Edit Asset | Unibs Tools')

@section('content')
    <div class="container mt-4">

        <div class="row justify-content-center">
            <div class="col-xl-12 col-lg-12">

                <div class="card shadow-sm border-0">

                    {{-- Header --}}
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0 fw-semibold text-dark">
                            <i class="fa fa-edit me-2 text-warning"></i>
                            Edit Asset Details
                        </h5>
                    </div>

                    <div class="card-body p-4">

                        <form method="POST" action="{{ route('asset.update', $asset->id) }}">
                            @csrf
                            @method('PUT')

                            {{-- Section 1: Basic Information --}}
                            <div class="mb-4">
                                <h6 class="text-warning fw-bold mb-3 border-bottom pb-2">
                                    <i class="fa fa-info-circle me-1"></i> Basic Asset Information
                                </h6>
                                <div class="row g-3">
                                    {{-- Asset Category --}}
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">
                                            Asset Category <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light">
                                                <i class="fa fa-layer-group"></i>
                                            </span>
                                            <select name="AssetMaster_category" class="form-select @error('AssetMaster_category') is-invalid @enderror" required>
                                                <option value="" disabled {{ old('AssetMaster_category', $asset->asset_category) ? '' : 'selected' }}>-- Select Asset Category --</option>
                                                @foreach ($categories as $category)
                                                    <option value="{{ $category }}"
                                                        {{ old('AssetMaster_category', $asset->asset_category) == $category ? 'selected' : '' }}>
                                                        {{ $category }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('AssetMaster_category')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Asset Code --}}
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Asset Code</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light">
                                                <i class="fa fa-hashtag"></i>
                                            </span>
                                            <input type="text" name="AssetMaster_number" class="form-control"
                                                value="{{ old('AssetMaster_number', $asset->asset_code) }}"
                                                placeholder="Enter Asset Code">
                                        </div>
                                    </div>

                                    {{-- Asset Name --}}
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Asset Name</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light">
                                                <i class="fa fa-tag"></i>
                                            </span>
                                            <input type="text" name="asset_name" class="form-control"
                                                value="{{ old('asset_name', $asset->asset_name) }}"
                                                placeholder="Enter Asset Name">
                                        </div>
                                    </div>

                                    {{-- Serial Number --}}
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Serial Number</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light">
                                                <i class="fa fa-barcode"></i>
                                            </span>
                                            <input type="text" name="serial_number" class="form-control"
                                                value="{{ old('serial_number', $asset->serial_number) }}"
                                                placeholder="Enter Serial Number">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Section 2: Specification & Vendor --}}
                            <div class="mb-4 mt-4">
                                <h6 class="text-warning fw-bold mb-3 border-bottom pb-2">
                                    <i class="fa fa-cogs me-1"></i> Brand & Vendor Details
                                </h6>
                                <div class="row g-3">
                                    {{-- Brand --}}
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Brand Name</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light">
                                                <i class="fa fa-industry"></i>
                                            </span>
                                            <input type="text" name="brand_name" class="form-control"
                                                value="{{ old('brand_name', $asset->brand_name) }}"
                                                placeholder="Enter Brand Name">
                                        </div>
                                    </div>

                                    {{-- Model --}}
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Model Number</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light">
                                                <i class="fa fa-microchip"></i>
                                            </span>
                                            <input type="text" name="model_number" class="form-control"
                                                value="{{ old('model_number', $asset->model_number) }}"
                                                placeholder="Enter Model Number">
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">
                                            Vendor <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light">
                                                <i class="fa fa-building"></i>
                                            </span>
                                            <select name="vendor" class="form-select @error('vendor') is-invalid @enderror" required>
                                                <option value="" disabled {{ old('vendor', $asset->vendor_id) ? '' : 'selected' }}>-- Select Vendor --</option>
                                                @foreach ($vendors as $v)
                                                    <option value="{{ $v->id }}"
                                                        {{ old('vendor', $asset->vendor_id) == $v->id ? 'selected' : '' }}>
                                                        {{ $v->vendor_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('vendor')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Section 3: Acquisition & Status --}}
                            <div class="mb-4 mt-4">
                                <h6 class="text-warning fw-bold mb-3 border-bottom pb-2">
                                    <i class="fa fa-wallet me-1"></i> Acquisition & Status
                                </h6>
                                <div class="row g-3">
                                    {{-- Purchase Date --}}
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Purchase Date</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light">
                                                <i class="fa fa-calendar-alt"></i>
                                            </span>
                                            <input type="date" name="purchase_date" class="form-control"
                                                value="{{ old('purchase_date', $asset->purchase_date) }}">
                                        </div>
                                    </div>

                                    {{-- Purchase Cost --}}
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Purchase Cost</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light">
                                                <i class="fa fa-dollar-sign"></i>
                                            </span>
                                            <input type="number" name="purchase_cost" class="form-control"
                                                value="{{ old('purchase_cost', $asset->purchase_cost) }}"
                                                placeholder="0.00" step="0.01">
                                        </div>
                                    </div>

                                    {{-- Warranty Expiry Date --}}
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Warranty Expiry Date</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light">
                                                <i class="fa fa-shield-alt"></i>
                                            </span>
                                            <input type="date" name="warranty_expiry_date" class="form-control"
                                                value="{{ old('warranty_expiry_date', $asset->warranty_expiry_date) }}">
                                        </div>
                                    </div>

                                    {{-- Status --}}
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Status</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light">
                                                <i class="fa fa-toggle-on"></i>
                                            </span>
                                            <select name="status" class="form-select">
                                                <option value="available" {{ old('status', strtolower($asset->status)) == 'available' ? 'selected' : '' }}>Available</option>
                                                <option value="allocated" {{ old('status', strtolower($asset->status)) == 'allocated' ? 'selected' : '' }}>Allocated</option>
                                                <option value="under repair" {{ old('status', strtolower($asset->status)) == 'under repair' ? 'selected' : '' }}>Under Repair</option>
                                                <option value="damaged" {{ old('status', strtolower($asset->status)) == 'damaged' ? 'selected' : '' }}>Damaged</option>
                                                <option value="disposed" {{ old('status', strtolower($asset->status)) == 'disposed' ? 'selected' : '' }}>Disposed</option>
                                                <option value="reserved" {{ old('status', strtolower($asset->status)) == 'reserved' ? 'selected' : '' }}>Reserved</option>
                                                <option value="lost" {{ old('status', strtolower($asset->status)) == 'lost' ? 'selected' : '' }}>Lost</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div class="d-flex justify-content-end mt-5 gap-2">
                                <a href="{{ route('asset.index') }}" class="btn btn-light px-4">
                                    Cancel
                                </a>

                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="fa fa-save me-1"></i> Update Asset
                                </button>
                            </div>

                        </form>

                    </div>
                </div>

            </div>
        </div>

    </div>
@endsection
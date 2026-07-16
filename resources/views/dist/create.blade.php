@extends('layouts.app')

@section('title', 'Add Asset | Unibs Tools')

@section('content')
    <div class="container mt-4">

        <div class="row justify-content-center">
            <div class="col-xl-12 col-lg-12">

                <div class="card shadow-sm border-0">

                    {{-- Header --}}
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0 fw-semibold">
                            <i class="fa fa-box-open me-2 text-primary"></i>
                            Add Asset
                        </h5>
                    </div>

                    <div class="card-body">

                        <form method="POST" action="{{ route('dists.store') }}">
                            @csrf

                            <div class="row g-4">

                                {{-- Asset Category --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        Asset Category <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="fa fa-layer-group"></i>
                                        </span>
                                        <select name="asset_category" class="form-select" required>
                                            <option value="">-- Select Asset Category --</option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category }}">
                                                    {{ $category }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                {{-- Asset Number --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Asset Number</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="fa fa-hashtag"></i>
                                        </span>
                                        <input type="text" name="asset_number" class="form-control"
                                            placeholder="Enter asset number">
                                    </div>
                                </div>

                                {{-- Brand --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Brand Name</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="fa fa-tag"></i>
                                        </span>
                                        <input type="text" name="brand_name" class="form-control"
                                            placeholder="Enter brand name">
                                    </div>
                                </div>

                                {{-- Model --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Model Number</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="fa fa-barcode"></i>
                                        </span>
                                        <input type="text" name="model_number" class="form-control"
                                            placeholder="Enter model number">
                                    </div>
                                </div>

                                {{-- Vendor --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Vendor</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="fa fa-building"></i>
                                        </span>
                                        <select name="vendor" class="form-select">
                                            <option>ABC</option>
                                            <option>XYZ</option>
                                            <option>TECH</option>
                                            <option>Other</option>
                                            <option selected>None</option>
                                        </select>
                                    </div>
                                </div>

                                {{-- Allocated To --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Allocated To</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="fa fa-user"></i>
                                        </span>
                                        <input type="text" name="allocated_to" class="form-control"
                                            placeholder="Employee / Department">
                                    </div>
                                </div>

                                {{-- Allocation Date --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Allocation Date</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="fa fa-calendar"></i>
                                        </span>
                                        <input type="date" name="allocation_date" class="form-control"
                                            value="{{ date('Y-m-d') }}">
                                    </div>
                                </div>

                                {{-- SIM Name --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">SIM Name</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="fa fa-sim-card"></i>
                                        </span>
                                        <select name="sim_name" class="form-select">
                                            <option>Jio</option>
                                            <option>Vi</option>
                                            <option>Airtel</option>
                                            <option>Other</option>
                                            <option selected>None</option>
                                        </select>
                                    </div>
                                </div>

                                {{-- Item --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Item</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="fa fa-box"></i>
                                        </span>
                                        <select name="item" class="form-select">
                                            <option>Notepad</option>
                                            <option>Pen</option>
                                            <option>Other</option>
                                            <option selected>None</option>
                                        </select>
                                    </div>
                                </div>

                                {{-- Asset Type --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Asset Type</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="fa fa-network-wired"></i>
                                        </span>
                                        <select name="type" class="form-select">
                                            <option>Wired</option>
                                            <option>Wireless</option>
                                            <option>Other</option>
                                            <option selected>None</option>
                                        </select>
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
                                            <option>Available</option>
                                            <option>Allocated</option>
                                            <option>Returned</option>
                                            <option>Damaged</option>
                                            <option selected>None</option>
                                        </select>
                                    </div>
                                </div>

                            </div>

                            {{-- Buttons --}}
                            <div class="d-flex justify-content-end mt-5 gap-2">

                                <a href="{{ route('dists.index') }}" class="btn btn-light px-4">
                                    Cancel
                                </a>

                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="fa fa-save me-1"></i> Save Asset
                                </button>

                            </div>

                        </form>

                    </div>
                </div>

            </div>
        </div>

    </div>
@endsection

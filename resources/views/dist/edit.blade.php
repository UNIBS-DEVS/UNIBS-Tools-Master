@extends('layouts.app')

@section('title', 'Edit Asset | Unibs Tools')

@section('content')
<div class="container mt-4">

    <div class="row justify-content-center">
        <div class="col-xl-12 col-lg-12">

            <div class="card shadow-sm border-0">

                {{-- Header --}}
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fa fa-edit me-2 text-primary"></i>
                        Edit Asset
                    </h5>
                </div>

                <div class="card-body">

                    <form method="POST" action="{{ route('dists.update', $asset->id) }}">
                        @csrf
                        @method('PUT')

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

                                        <option value="">-- Select Category --</option>

                                        @foreach ($categories as $category)
                                            <option value="{{ $category }}"
                                                {{ old('asset_category', $asset->asset_category) == $category ? 'selected' : '' }}>
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

                                    <input type="text"
                                        name="asset_number"
                                        value="{{ old('asset_number', $asset->asset_number) }}"
                                        class="form-control">
                                </div>
                            </div>

                            {{-- Asset Type --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Asset Type <span class="text-danger">*</span>
                                </label>

                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="fa fa-network-wired"></i>
                                    </span>

                                    <input type="text"
                                        name="type"
                                        value="{{ old('type', $asset->type) }}"
                                        class="form-control"
                                        required>
                                </div>
                            </div>

                            {{-- Status --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Status
                                </label>

                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="fa fa-toggle-on"></i>
                                    </span>

                                    <select name="status" class="form-select">

                                        <option value="Available"
                                            {{ $asset->status == 'Available' ? 'selected' : '' }}>
                                            Available
                                        </option>

                                        <option value="Allocated"
                                            {{ $asset->status == 'Allocated' ? 'selected' : '' }}>
                                            Allocated
                                        </option>

                                        <option value="Returned"
                                            {{ $asset->status == 'Returned' ? 'selected' : '' }}>
                                            Returned
                                        </option>

                                        <option value="Damaged"
                                            {{ $asset->status == 'Damaged' ? 'selected' : '' }}>
                                            Damaged
                                        </option>

                                    </select>
                                </div>
                            </div>

                        </div>

                        {{-- Buttons --}}
                        <div class="d-flex justify-content-end mt-5 gap-2">

                            <a href="{{ route('dists.index') }}"
                               class="btn btn-light px-4">
                               Cancel
                            </a>

                            <button type="submit"
                                    class="btn btn-primary px-4">
                                <i class="fa fa-save me-1"></i>
                                Update Asset
                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>
    </div>

</div>
@endsection
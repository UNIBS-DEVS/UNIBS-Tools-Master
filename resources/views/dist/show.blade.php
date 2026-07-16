@extends('layouts.app')

@section('title', 'Asset Details | Unibs Tools')

@section('content')
    <div class="container mt-4">

        <div class="row justify-content-center">
            <div class="col-xl-10">

                <div class="card shadow-sm border-0">

                    {{-- Header --}}
                    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-semibold">
                            <i class="fa fa-box me-2 text-primary"></i>
                            Asset Details
                        </h5>

                        <a href="{{ route('dists.edit', $asset->id) }}" class="btn btn-warning btn-sm">
                            <i class="fa fa-edit me-1"></i> Edit
                        </a>
                    </div>

                    <div class="card-body">

                        <div class="row g-4">

                            <div class="col-md-6">
                                <strong>ID</strong>
                                <div class="text-muted">{{ $asset->id }}</div>
                            </div>

                            <div class="col-md-6">
                                <strong>Category</strong>
                                <div class="text-muted">{{ $asset->asset_category }}</div>
                            </div>

                            <div class="col-md-6">
                                <strong>Asset Number</strong>
                                <div class="text-muted">{{ $asset->asset_number }}</div>
                            </div>

                            <div class="col-md-6">
                                <strong>Serial Number</strong>
                                <div class="text-muted">{{ $asset->serial_number }}</div>
                            </div>

                            <div class="col-md-6">
                                <strong>Brand Name</strong>
                                <div class="text-muted">{{ $asset->brand_name }}</div>
                            </div>

                            <div class="col-md-6">
                                <strong>Model Number</strong>
                                <div class="text-muted">{{ $asset->model_number }}</div>
                            </div>

                            <div class="col-md-6">
                                <strong>Vendor</strong>
                                <div class="text-muted">{{ $asset->vendor }}</div>
                            </div>

                            <div class="col-md-6">
                                <strong>Purchase Type</strong>
                                <div class="text-muted">{{ $asset->purchase_type }}</div>
                            </div>

                            <div class="col-md-6">
                                <strong>Quantity</strong>
                                <div class="text-muted">{{ $asset->quantity }}</div>
                            </div>

                            <div class="col-md-6">
                                <strong>Allocated To</strong>
                                <div class="text-muted">{{ $asset->allocated_to }}</div>
                            </div>

                            <div class="col-md-6">
                                <strong>Allocation Date</strong>
                                <div class="text-muted">
                                    {{ \Carbon\Carbon::parse($asset->allocation_date)->format('d M Y') }}
                                </div>
                            </div>

                            <div class="col-md-6">
                                <strong>Type</strong>
                                <div class="text-muted">{{ $asset->type }}</div>
                            </div>

                            <div class="col-md-6">
                                <strong>SIM Name</strong>
                                <div class="text-muted">{{ $asset->sim_name }}</div>
                            </div>

                            <div class="col-md-6">
                                <strong>Item</strong>
                                <div class="text-muted">{{ $asset->item }}</div>
                            </div>

                            <div class="col-md-6">
                                <strong>Status</strong>

                                <div>
                                    @if ($asset->status == 'Available')
                                        <span class="badge bg-success">Available</span>
                                    @elseif($asset->status == 'Allocated')
                                        <span class="badge bg-primary">Allocated</span>
                                    @elseif($asset->status == 'Returned')
                                        <span class="badge bg-warning text-dark">Returned</span>
                                    @elseif($asset->status == 'Damaged')
                                        <span class="badge bg-danger">Damaged</span>
                                    @else
                                        <span class="badge bg-secondary">None</span>
                                    @endif
                                </div>
                            </div>

                        </div>

                    </div>

                </div>

            </div>
        </div>

    </div>
@endsection

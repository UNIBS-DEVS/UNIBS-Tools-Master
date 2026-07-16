@extends('layouts.app')

@section('title', 'Allocation Details | Unibs Tools')

@section('content')
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm border-0">
                    {{-- Header --}}
                    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-semibold text-dark">
                            <i class="fa fa-info-circle me-2 text-info"></i>
                            Asset Allocation Details
                        </h5>
                        <a href="{{ route('asset-allocations.index') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fa fa-arrow-left me-1"></i> Back to List
                        </a>
                    </div>

                    {{-- Body --}}
                    <div class="card-body p-4">
                        <div class="row mb-4">
                            {{-- Asset info --}}
                            <div class="col-md-6 mb-3">
                                <h6 class="text-primary fw-bold mb-3 border-bottom pb-2">
                                    <i class="fa fa-box-open me-1"></i> Asset Information
                                </h6>
                                <table class="table table-borderless table-sm mb-0">
                                    <tr>
                                        <td class="fw-semibold text-muted" width="120">Category:</td>
                                        <td class="text-dark">{{ $allocation->asset->AssetMaster_category ?? 'None' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold text-muted">Name:</td>
                                        <td class="text-dark fw-bold">{{ $allocation->asset->asset_name ?? 'None' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold text-muted">Asset Code:</td>
                                        <td class="text-dark">{{ $allocation->asset->asset_code ?? 'None' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold text-muted">Serial No:</td>
                                        <td class="text-dark">{{ $allocation->asset->serial_number ?? 'None' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold text-muted">Brand / Model:</td>
                                        <td class="text-dark">{{ $allocation->asset->brand_name ?? 'None' }} /
                                            {{ $allocation->asset->model_number ?? 'None' }}</td>
                                    </tr>
                                </table>
                            </div>

                            {{-- Employee info --}}
                            <div class="col-md-6 mb-3">
                                <h6 class="text-primary fw-bold mb-3 border-bottom pb-2">
                                    <i class="fa fa-user me-1"></i> Employee Information
                                </h6>
                                <table class="table table-borderless table-sm mb-0">
                                    <tr>
                                        <td class="fw-semibold text-muted" width="120">Name:</td>
                                        <td class="text-dark fw-bold">{{ $allocation->employee->name ?? 'None' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold text-muted">Email:</td>
                                        <td class="text-dark">{{ $allocation->employee->email ?? 'None' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold text-muted">Contact:</td>
                                        <td class="text-dark">{{ $allocation->employee->personal_mobile ?? 'None' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        {{-- Allocation info --}}
                        <div class="row">
                            <div class="col-12">
                                <h6 class="text-primary fw-bold mb-3 border-bottom pb-2">
                                    <i class="fa fa-share-alt me-1"></i> Allocation Details
                                </h6>
                                <table class="table table-bordered align-middle bg-light">
                                    <tr>
                                        <th class="text-muted fw-semibold py-2" width="200">Allocated Date</th>
                                        <td class="text-dark py-2">
                                            {{ $allocation->allocated_date ? $allocation->allocated_date->format('d M Y h:i A') : '-' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted fw-semibold py-2">Returned Date</th>
                                        <td class="text-dark py-2">
                                            {{ $allocation->returned_date ? $allocation->returned_date->format('d M Y h:i A') : 'Not Returned Yet' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted fw-semibold py-2">Allocation Status</th>
                                        <td class="py-2">
                                            @php
                                                $status = strtolower($allocation->status);
                                            @endphp
                                            @if ($status === 'allocated')
                                                <span
                                                    class="badge bg-primary text-white px-3 py-2 rounded-pill">Allocated</span>
                                            @elseif ($status === 'returned' || $status === 'available')
                                                <span class="badge bg-success text-white px-3 py-2 rounded-pill">Returned</span>
                                            @elseif ($status === 'damaged')
                                                <span class="badge bg-danger text-white px-3 py-2 rounded-pill">Damaged</span>
                                            @elseif ($status === 'lost')
                                                <span class="badge bg-warning text-dark px-3 py-2 rounded-pill">Lost</span>
                                            @else
                                                <span
                                                    class="badge bg-secondary text-white px-3 py-2 rounded-pill">{{ ucfirst($allocation->status) }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted fw-semibold py-2">Remarks</th>
                                        <td class="text-dark py-2">{{ $allocation->remarks ?? '-' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        {{-- Footer Action Buttons --}}
                        <div class="d-flex justify-content-end gap-2 border-top pt-3 mt-3">
                            <a href="{{ route('asset-allocations.edit', $allocation->id) }}"
                                class="btn btn-warning px-4 text-dark fw-semibold">
                                <i class="fa fa-edit me-1"></i> Edit / Return
                            </a>

                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
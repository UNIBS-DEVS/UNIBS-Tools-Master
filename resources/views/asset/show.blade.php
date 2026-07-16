@extends('layouts.app')

@section('title', 'Asset Details | Unibs Tools')

@section('content')
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-12">

                {{-- Asset Main Details Card --}}
                <div class="card shadow-sm border-0 mb-4">
                    {{-- Header --}}
                    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0 fw-bold text-dark">
                                <i class="fa fa-box me-2 text-primary"></i>
                                {{ $asset->asset_name }} Details
                            </h5>
                            <p class="text-muted small mb-0">Code: {{ $asset->asset_code }} | Category: {{ $asset->asset_category }}</p>
                        </div>

                        <div>
                            <a href="{{ route('asset.edit', $asset->id) }}" class="btn btn-warning btn-sm text-dark fw-semibold me-1">
                                <i class="fa fa-edit me-1"></i> Edit Asset
                            </a>
                            <a href="{{ route('asset.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="fa fa-arrow-left me-1"></i> Back to List
                            </a>
                        </div>
                    </div>

                    <div class="card-body p-4">
                        <div class="row g-4">
                            {{-- Specs Column --}}
                            <div class="col-md-6 border-end">
                                <h6 class="text-primary fw-bold mb-3">
                                    <i class="fa fa-cogs me-1"></i> Specification Details
                                </h6>
                                <table class="table table-borderless table-sm mb-0">
                                    <tr>
                                        <td class="fw-semibold text-muted" width="150">Brand Name:</td>
                                        <td class="text-dark">{{ $asset->brand_name }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold text-muted">Model Number:</td>
                                        <td class="text-dark">{{ $asset->model_number }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold text-muted">Serial Number:</td>
                                        <td class="text-dark fw-bold">{{ $asset->serial_number ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold text-muted">Status:</td>
                                        <td>
                                            @php
                                                $status = strtolower($asset->status);
                                            @endphp
                                            @if ($status === 'available')
                                                <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill">Available</span>
                                            @elseif($status === 'allocated')
                                                <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill">Allocated</span>
                                            @elseif($status === 'under repair')
                                                <span class="badge bg-warning-subtle text-warning px-3 py-2 rounded-pill">Under Repair</span>
                                            @elseif($status === 'damaged')
                                                <span class="badge bg-danger-subtle text-danger px-3 py-2 rounded-pill">Damaged</span>
                                            @else
                                                <span class="badge bg-secondary-subtle text-secondary px-3 py-2 rounded-pill">{{ ucfirst($status) }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            {{-- Purchase & Vendor Column --}}
                            <div class="col-md-6">
                                <h6 class="text-primary fw-bold mb-3">
                                    <i class="fa fa-shopping-cart me-1"></i> Acquisition & Vendor
                                </h6>
                                <table class="table table-borderless table-sm mb-0">
                                    <tr>
                                        <td class="fw-semibold text-muted" width="180">Vendor Name:</td>
                                        <td class="text-dark">
                                            @if ($asset->vendorRelation)
                                                <span class="fw-bold">{{ $asset->vendorRelation->vendor_name }}</span>
                                                <span class="text-muted small">({{ $asset->vendorRelation->email ?? 'No email' }})</span>
                                            @else
                                                {{ $asset->vendor ?? '-' }}
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold text-muted">Purchase Date:</td>
                                        <td class="text-dark">
                                            {{ $asset->purchase_date ? \Carbon\Carbon::parse($asset->purchase_date)->format('d M Y') : '-' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold text-muted">Purchase Cost:</td>
                                        <td class="text-dark fw-semibold text-success">
                                            ₹{{ number_format($asset->purchase_cost, 2) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold text-muted">Warranty Expiry:</td>
                                        <td class="text-dark">
                                            {{ $asset->warranty_expiry_date ? \Carbon\Carbon::parse($asset->warranty_expiry_date)->format('d M Y') : '-' }}
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Active Allocation (if status is Allocated) --}}
                @if ($asset->currentAllocation)
                    <div class="card shadow-sm border-0 mb-4 bg-light-subtle">
                        <div class="card-header bg-primary text-white py-3">
                            <h6 class="mb-0 fw-bold">
                                <i class="fa fa-share-alt me-2"></i> Current Active Allocation
                            </h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="text-muted small fw-semibold">ALLOCATED EMPLOYEE</div>
                                    <div class="fw-bold text-dark fs-5">{{ $asset->currentAllocation->employee->name ?? '-' }}</div>
                                    <div class="text-muted small">{{ $asset->currentAllocation->employee->email ?? '-' }}</div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-muted small fw-semibold">DATE ALLOCATED</div>
                                    <div class="fw-bold text-dark fs-6">{{ $asset->currentAllocation->allocated_date ? $asset->currentAllocation->allocated_date->format('d M Y h:i A') : '-' }}</div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-muted small fw-semibold">REMARKS</div>
                                    <div class="text-dark small">{{ $asset->currentAllocation->remarks ?? 'No remarks provided.' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Allocation History Tab --}}
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 fw-bold text-dark">
                            <i class="fa fa-history me-2 text-primary"></i> Allocation History Log
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="px-4 py-2">Employee</th>
                                        <th class="py-2">Date Allocated</th>
                                        <th class="py-2">Date Returned</th>
                                        <th class="py-2">Status</th>
                                        <th class="px-4 py-2">Remarks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($asset->allocations as $allocation)
                                        <tr>
                                            <td class="px-4 fw-semibold text-dark">{{ $allocation->employee->name ?? 'None' }}</td>
                                            <td>{{ $allocation->allocated_date ? $allocation->allocated_date->format('d M Y h:i A') : '-' }}</td>
                                            <td>{{ $allocation->returned_date ? $allocation->returned_date->format('d M Y h:i A') : 'Active' }}</td>
                                            <td>
                                                @php
                                                    $allocStatus = strtolower($allocation->status);
                                                @endphp
                                                @if ($allocStatus === 'allocated')
                                                    <span class="badge bg-primary-subtle text-primary">Allocated</span>
                                                @elseif ($allocStatus === 'returned' || $allocStatus === 'available')
                                                    <span class="badge bg-success-subtle text-success">Returned</span>
                                                @else
                                                    <span class="badge bg-secondary-subtle text-secondary">{{ ucfirst($allocation->status) }}</span>
                                                @endif
                                            </td>
                                            <td class="px-4 text-muted small">{{ $allocation->remarks ?? '-' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-4 text-muted small">No past allocations recorded.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Repair History Tab --}}
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 fw-bold text-dark">
                            <i class="fa fa-wrench me-2 text-primary"></i> Maintenance & Repair Logs
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="px-4 py-2">Vendor</th>
                                        <th class="py-2">Issue Description</th>
                                        <th class="py-2">Reported Date</th>
                                        <th class="py-2">Received Date</th>
                                        <th class="py-2">Cost</th>
                                        <th class="px-4 py-2">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($asset->repairs as $repair)
                                        <tr>
                                            <td class="px-4 fw-semibold text-dark">{{ $repair->vendor->vendor_name ?? 'None' }}</td>
                                            <td class="text-truncate" style="max-width: 200px;" title="{{ $repair->issue_description }}">{{ $repair->issue_description ?? '-' }}</td>
                                            <td>{{ $repair->reported_date ? $repair->reported_date->format('d M Y') : '-' }}</td>
                                            <td>{{ $repair->received_date ? $repair->received_date->format('d M Y h:i A') : 'Processing' }}</td>
                                            <td class="fw-semibold text-success">
                                                {{ $repair->repair_cost ? '₹' . number_format($repair->repair_cost, 2) : '-' }}
                                            </td>
                                            <td class="px-4">
                                                @php
                                                    $repStatus = strtolower($repair->repair_status);
                                                @endphp
                                                @if (in_array($repStatus, ['sent for repair', 'under repair', 'reported']))
                                                    <span class="badge bg-warning-subtle text-warning">{{ ucfirst($repair->repair_status) }}</span>
                                                @elseif (in_array($repStatus, ['repaired', 'received']))
                                                    <span class="badge bg-success-subtle text-success">{{ ucfirst($repair->repair_status) }}</span>
                                                @else
                                                    <span class="badge bg-secondary-subtle text-secondary">{{ ucfirst($repair->repair_status) }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4 text-muted small">No repair logs recorded.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- SIM Recharge History (Only for SIM category) --}}
                @if (str_contains(strtolower($asset->asset_category), 'sim'))
                    <div class="card shadow-sm border-0 mt-4">
                        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-bold text-dark">
                                <i class="fa fa-bolt me-2 text-primary"></i> SIM Recharge History Logs
                            </h6>
                            <a href="{{ route('sim-recharges.create', ['asset_id' => $asset->id]) }}" class="btn btn-sm btn-outline-primary fw-semibold">
                                <i class="fa fa-plus-circle me-1"></i> Log Recharge
                            </a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="px-4 py-2">Recharge Date</th>
                                            <th class="py-2">Plan Name</th>
                                            <th class="py-2">Amount</th>
                                            <th class="py-2">Validity</th>
                                            <th class="py-2">Expiry Date</th>
                                            <th class="px-4 py-2">Remarks</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($asset->recharges as $recharge)
                                            <tr>
                                                <td class="px-4 fw-semibold">{{ $recharge->recharge_date ? $recharge->recharge_date->format('d M Y') : '-' }}</td>
                                                <td>{{ $recharge->plan_name ?? '-' }}</td>
                                                <td class="fw-bold text-success">
                                                    {{ $recharge->recharge_amount ? '₹' . number_format($recharge->recharge_amount, 2) : '-' }}
                                                </td>
                                                <td>{{ $recharge->validity_days ? $recharge->validity_days . ' Days' : '-' }}</td>
                                                <td>
                                                    @if ($recharge->expiry_date)
                                                        <span class="{{ $recharge->expiry_date->isPast() ? 'text-danger fw-bold' : '' }}">
                                                            {{ $recharge->expiry_date->format('d M Y') }}
                                                        </span>
                                                        @if ($recharge->expiry_date->isPast())
                                                            <span class="badge bg-danger-subtle text-danger ms-1 small">Expired</span>
                                                        @endif
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td class="px-4 text-muted small">{{ $recharge->remarks ?? '-' }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center py-4 text-muted small">No SIM recharge logs recorded.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Asset Documents Logs (Visible for all assets) --}}
                <div class="card shadow-sm border-0 mt-4">
                    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold text-dark">
                            <i class="fa fa-file-text me-2 text-primary"></i> Asset Documents Logs
                        </h6>
                        <a href="{{ route('asset-documents.create', ['asset_id' => $asset->id]) }}" class="btn btn-sm btn-outline-primary fw-semibold">
                            <i class="fa fa-upload me-1"></i> Upload Document
                        </a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="px-4 py-2">Document Type</th>
                                        <th class="py-2">File Name</th>
                                        <th class="py-2">Uploaded On</th>
                                        <th class="px-4 py-2 text-center" width="150">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($asset->documents as $doc)
                                        <tr>
                                            <td class="px-4 fw-semibold">
                                                <span class="badge bg-secondary px-2 py-1 rounded-pill">
                                                    {{ $doc->document_type }}
                                                </span>
                                            </td>
                                            <td class="text-dark">
                                                <i class="fa fa-file-alt me-1 text-muted"></i>
                                                {{ $doc->file_name }}
                                            </td>
                                            <td>{{ $doc->uploaded_on ? $doc->uploaded_on->format('d M Y h:i A') : '-' }}</td>
                                            <td class="px-4 text-center">
                                                <div class="d-flex justify-content-center gap-1">
                                                    <a href="{{ route('asset-documents.view', $doc->document_id) }}" target="_blank" class="btn btn-sm btn-outline-primary" title="View Document">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('asset-documents.download', $doc->document_id) }}" class="btn btn-sm btn-outline-success" title="Download">
                                                        <i class="fa fa-download"></i>
                                                    </a>
                                                    <form action="{{ route('asset-documents.destroy', $doc->document_id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this document?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button class="btn btn-sm btn-outline-danger" title="Delete">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-4 text-muted small">No documents uploaded for this asset.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
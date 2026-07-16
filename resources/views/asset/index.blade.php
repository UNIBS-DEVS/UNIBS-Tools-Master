@extends('layouts.app')

@section('title', 'Assets List | Unibs Tools')

@section('content')

    {{-- Flash Messages --}}
    @include('partials.message')

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1 text-dark fw-bold">Assets List</h3>
            <p class="text-muted small mb-0">Manage company assets, allocations, and repairs</p>
        </div>

        <a href="{{ route('asset.create') }}" class="btn btn-primary shadow-sm px-4">
            <i class="fa fa-plus-circle me-2"></i> Add Asset
        </a>
    </div>

    {{-- Table --}}
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th class="px-4 py-3">Category</th>
                            <th class="py-3">ID</th>
                            <th class="py-3">Asset No.</th>
                            <th class="py-3">Brand</th>
                            <th class="py-3">Model</th>
                            <th class="py-3">Vendor</th>
                            <th class="py-3">Allocated User</th>
                            <th class="py-3">Date</th>
                            <th class="py-3">Status</th>
                            <th class="px-4 py-3 text-center" width="220">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($assets as $asset)
                            <tr>
                                <td class="px-4 fw-semibold text-dark">{{ $asset->asset_category }}</td>
                                <td>{{ $asset->id }}</td>
                                <td>{{ $asset->asset_code }}</td>
                                <td>{{ $asset->brand_name }}</td>
                                <td>{{ $asset->model_number }}</td>
                                <td>{{ $asset->vendorRelation->vendor_name ?? $asset->vendor ?? '-' }}</td>
                                
                                {{-- Eager-loaded current allocation user --}}
                                <td>
                                    @if ($asset->currentAllocation && $asset->currentAllocation->employee)
                                        <div class="fw-semibold text-dark">{{ $asset->currentAllocation->employee->name }}</div>
                                        <div class="text-muted small">{{ $asset->currentAllocation->employee->email }}</div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                
                                {{-- Allocation date --}}
                                <td>
                                    {{ $asset->currentAllocation && $asset->currentAllocation->allocated_date ? $asset->currentAllocation->allocated_date->format('d-m-Y') : '-' }}
                                </td>

                                {{-- Status --}}
                                <td>
                                    @php
                                        $status = strtolower($asset->status);
                                    @endphp
                                    @if ($status === 'available')
                                        <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill">
                                            Available
                                        </span>
                                    @elseif($status === 'allocated')
                                        <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill">
                                            Allocated
                                        </span>
                                    @elseif($status === 'under repair')
                                        <span class="badge bg-warning-subtle text-warning px-3 py-2 rounded-pill">
                                            Under Repair
                                        </span>
                                    @elseif($status === 'damaged')
                                        <span class="badge bg-danger-subtle text-danger px-3 py-2 rounded-pill">
                                            Damaged
                                        </span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary px-3 py-2 rounded-pill">
                                            {{ ucfirst($status) }}
                                        </span>
                                    @endif
                                </td>

                                {{-- Actions --}}
                                <td>
                                    <div class="d-flex justify-content-center gap-1">
                                        {{-- Allocation Button --}}
                                        @if ($status === 'available')
                                            <a href="{{ route('asset-allocations.create', ['asset_id' => $asset->id]) }}" 
                                               class="btn btn-sm btn-outline-primary" title="Allocate Asset">
                                                <i class="fa fa-share-alt"></i>
                                            </a>
                                        @elseif ($status === 'allocated' && $asset->currentAllocation)
                                            <a href="{{ route('asset-allocations.edit', $asset->currentAllocation->id) }}" 
                                               class="btn btn-sm btn-outline-success" title="Return Asset">
                                                <i class="fa fa-reply"></i>
                                            </a>
                                        @else
                                            <button class="btn btn-sm btn-outline-secondary" disabled title="Not available for allocation">
                                                <i class="fa fa-share-alt"></i>
                                            </button>
                                        @endif

                                        {{-- Repair Button --}}
                                        @if ($status === 'under repair' && $asset->currentRepair)
                                            <a href="{{ route('asset-repairs.edit', $asset->currentRepair->id) }}" 
                                               class="btn btn-sm btn-outline-warning" title="Complete / Update Repair">
                                                <i class="fa fa-wrench"></i>
                                            </a>
                                        @else
                                            <a href="{{ route('asset-repairs.create', ['asset_id' => $asset->id]) }}" 
                                               class="btn btn-sm btn-outline-secondary" title="Send to Repair">
                                                <i class="fa fa-wrench"></i>
                                            </a>
                                        @endif

                                        {{-- Recharge Button --}}
                                        @if (str_contains(strtolower($asset->asset_category), 'sim'))
                                            <a href="{{ route('sim-recharges.create', ['asset_id' => $asset->id]) }}" 
                                               class="btn btn-sm btn-outline-dark" title="SIM Recharge">
                                                <i class="fa fa-bolt"></i>
                                            </a>
                                        @endif

                                        {{-- Document Upload Button --}}
                                        <a href="{{ route('asset-documents.create', ['asset_id' => $asset->id]) }}" 
                                           class="btn btn-sm btn-outline-secondary" title="Upload Document">
                                            <i class="fa fa-paperclip"></i>
                                        </a>

                                        {{-- Standard Actions --}}
                                        <a href="{{ route('asset.show', $asset->id) }}" class="btn btn-sm btn-outline-info"
                                            title="View Details">
                                            <i class="fa fa-eye"></i>
                                        </a>

                                        <a href="{{ route('asset.edit', $asset->id) }}" class="btn btn-sm btn-outline-warning"
                                            title="Edit Asset">
                                            <i class="fa fa-edit"></i>
                                        </a>

                                        <form action="{{ route('asset.destroy', $asset->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete Asset?');">
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
                                <td colspan="10" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fa fa-box-open fs-2 mb-3 text-secondary"></i>
                                        <p class="mb-0 fw-semibold">No Assets Found</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($assets->hasPages())
                <div class="card-footer bg-white border-0 py-3">
                    {{ $assets->links() }}
                </div>
            @endif
        </div>
    </div>

@endsection
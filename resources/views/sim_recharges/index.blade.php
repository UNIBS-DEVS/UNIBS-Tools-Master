@extends('layouts.app')

@section('title', 'SIM Recharges | Unibs Tools')

@section('content')

    {{-- Flash Messages --}}
    @include('partials.message')

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1 text-dark fw-bold">SIM Recharges History</h3>
            <p class="text-muted small mb-0">Track and manage mobile SIM recharge plans and expiries</p>
        </div>

        <a href="{{ route('sim-recharges.create') }}" class="btn btn-primary shadow-sm px-4">
            <i class="fa fa-plus-circle me-2"></i> Log SIM Recharge
        </a>
    </div>

    {{-- Table --}}
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th class="px-4 py-3">ID</th>
                            <th class="py-3">Asset Code / Name</th>
                            <th class="py-3">Recharge Date</th>
                            <th class="py-3">Plan Name</th>
                            <th class="py-3">Amount</th>
                            <th class="py-3">Validity (Days)</th>
                            <th class="py-3">Expiry Date</th>
                            <th class="py-3">Remarks</th>
                            <th class="px-4 py-3 text-center" width="150">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($recharges as $recharge)
                            <tr>
                                <td class="px-4">{{ $recharge->recharge_id }}</td>
                                <td>
                                    @if ($recharge->asset)
                                        <a href="{{ route('asset.show', $recharge->asset->id) }}" class="fw-semibold text-primary">
                                            {{ $recharge->asset->asset_name }}
                                        </a>
                                        <div class="text-muted small">Code: {{ $recharge->asset->asset_code }}</div>
                                    @else
                                        <span class="text-muted">Deleted Asset</span>
                                    @endif
                                </td>
                                <td>{{ $recharge->recharge_date ? $recharge->recharge_date->format('d M Y') : '-' }}</td>
                                <td class="fw-semibold">{{ $recharge->plan_name ?? '-' }}</td>
                                <td class="text-success fw-bold">
                                    {{ $recharge->recharge_amount ? '₹' . number_format($recharge->recharge_amount, 2) : '-' }}
                                </td>
                                <td>{{ $recharge->validity_days ? $recharge->validity_days . ' Days' : '-' }}</td>
                                <td>
                                    @if ($recharge->expiry_date)
                                        <span class="fw-semibold {{ $recharge->expiry_date->isPast() ? 'text-danger' : 'text-dark' }}">
                                            {{ $recharge->expiry_date->format('d M Y') }}
                                        </span>
                                        @if ($recharge->expiry_date->isPast())
                                            <span class="badge bg-danger-subtle text-danger ms-1 small">Expired</span>
                                        @endif
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="text-muted small" style="max-width: 200px;" title="{{ $recharge->remarks }}">
                                    {{ $recharge->remarks ?? '-' }}
                                </td>
                                <td class="px-4 text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <a href="{{ route('sim-recharges.edit', $recharge->recharge_id) }}" class="btn btn-sm btn-outline-warning" title="Edit Recharge">
                                            <i class="fa fa-edit"></i>
                                        </a>

                                        <form action="{{ route('sim-recharges.destroy', $recharge->recharge_id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this recharge log?');">
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
                                <td colspan="9" class="text-center py-4 text-muted small">No SIM recharges logged yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Pagination --}}
    @if ($recharges->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $recharges->links() }}
        </div>
    @endif
@endsection

@extends('layouts.app')

@section('title', 'Assets List | Unibs Tools')

@section('content')

    {{-- Flash Messages --}}
    @include('partials.message')

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Assets List</h3>

        <a href="{{ route('dists.create') }}" class="btn btn-primary">
            <i class="fa fa-plus me-1"></i> Add Asset
        </a>
    </div>

    {{-- Table --}}
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle bg-white shadow-sm">

            <thead class="table-dark text-center">
                <tr>
                    <th>Category</th>
                    <th>ID</th>
                    <th>Asset No.</th>
                    <th>Brand</th>
                    <th>Model</th>
                    <th>Vendor</th>
                    <th>Qty</th>
                    <th>Allocated User</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th width="160">Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse($assets as $asset)
                    <tr class="text-center">

                        <td>{{ $asset->asset_category }}</td>
                        <td>{{ $asset->id }}</td>
                        <td>{{ $asset->asset_number }}</td>
                        <td>{{ $asset->brand_name }}</td>
                        <td>{{ $asset->model_number }}</td>
                        <td>{{ $asset->vendor }}</td>
                        <td>{{ $asset->quantity }}</td>
                        <td>{{ $asset->allocated_to }}</td>
                        <td>{{ date('d-m-Y', strtotime($asset->allocation_date)) }}</td>

                        {{-- Status --}}
                        <td>
                            @if ($asset->status == 'Available')
                                <span class="badge bg-success-subtle text-success px-3 py-2">
                                    Available
                                </span>
                            @elseif($asset->status == 'Allocated')
                                <span class="badge bg-primary-subtle text-primary px-3 py-2">
                                    Allocated
                                </span>
                            @elseif($asset->status == 'Returned')
                                <span class="badge bg-warning-subtle text-warning px-3 py-2">
                                    Returned
                                </span>
                            @elseif($asset->status == 'Damaged')
                                <span class="badge bg-danger-subtle text-danger px-3 py-2">
                                    Damaged
                                </span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary px-3 py-2">
                                    None
                                </span>
                            @endif
                        </td>

                        {{-- Actions --}}
                        <td>
                            <a href="{{ route('dists.show', $asset->id) }}" class="btn btn-sm btn-outline-info me-1"
                                title="View">
                                <i class="fa fa-eye"></i>
                            </a>

                            <a href="{{ route('dists.edit', $asset->id) }}" class="btn btn-sm btn-outline-warning me-1"
                                title="Edit">
                                <i class="fa fa-edit"></i>
                            </a>

                            <form action="{{ route('dists.destroy', $asset->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')

                                <button class="btn btn-sm btn-outline-danger" title="Delete"
                                    onclick="return confirm('Delete asset?')">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </form>
                        </td>

                    </tr>

                @empty
                    <tr>
                        <td colspan="11" class="text-center">
                            <div class="text-muted py-3">
                                <i class="fa fa-box-open fs-3 mb-2"></i>
                                <p class="mb-0">No Assets Found</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>

        </table>
    </div>

@endsection

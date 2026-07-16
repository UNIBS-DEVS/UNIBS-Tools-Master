@extends('layouts.app')

@section('title', 'Vendors List | Unibs Tools')

@section('content')

    {{-- Flash Messages --}}
    @include('partials.message')

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0 fw-bold text-dark">Vendors List</h4>

        <a href="{{ route('vendors.create') }}" class="btn btn-primary btn-sm px-3 fw-semibold shadow-sm">
            <i class="fa fa-plus me-1"></i> Add Vendor
        </a>
    </div>

    {{-- Table --}}
    <div class="table-responsive">
        <table id="vendorsTable" class="table table-bordered table-hover align-middle bg-white shadow-sm">
            <thead class="table-dark">
                <tr>
                    <th width="80" class="text-center">S.No</th>
                    <th>Vendor Name</th>
                    <th>Contact Person</th>
                    <th>Email</th>
                    <th>Mobile No</th>
                    <th>GST No</th>
                    <th width="150" class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($vendors as $vendor)
                    <tr>
                        <td class="text-center fw-semibold">{{ $loop->iteration }}</td>
                        <td>{{ $vendor->vendor_name }}</td>
                        <td>{{ $vendor->contact_person ?? 'N/A' }}</td>
                        <td>{{ $vendor->email ?? 'N/A' }}</td>
                        <td>{{ $vendor->mobile_no ?? 'N/A' }}</td>
                        <td>{{ $vendor->gst ?? 'N/A' }}</td>
                        <td class="text-center">
                            <a href="{{ route('vendors.edit', $vendor->id) }}" class="btn btn-outline-warning btn-sm me-1" title="Edit">
                                <i class="fa-solid fa-pen"></i>
                            </a>

                            <form action="{{ route('vendors.destroy', $vendor->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Delete this vendor?')" title="Delete">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="fa fa-handshake fs-3 mb-2 text-secondary"></i>
                            <p class="mb-0">No Vendors Found</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Pagination --}}
        <div class="d-flex justify-content-end mt-3">
            {{ $vendors->links() }}
        </div>
    </div>

@endsection

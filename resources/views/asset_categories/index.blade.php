@extends('layouts.app')

@section('title', 'Asset Categories')

@section('content')

    {{-- Flash Messages --}}
    @include('partials.message')

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0 fw-bold text-dark">Asset Categories</h4>

        <a href="{{ route('asset_categories.create') }}" class="btn btn-primary btn-sm px-3 fw-semibold shadow-sm">
            <i class="fa fa-plus me-1"></i> Add Category
        </a>
    </div>

    <div class="table-responsive">
        <table id="categoryTable" class="table table-bordered table-hover align-middle bg-white shadow-sm">
            <thead class="table-dark">
                <tr>
                    <th width="80" class="text-center">S.No</th>
                    <th>Category Name</th>
                    <th width="150" class="text-center">Status</th>
                    <th width="150" class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($assetcategories as $category)
                    <tr>
                        <td class="text-center fw-semibold">{{ $loop->iteration }}</td>
                        <td>{{ $category->category_name }}</td>
                        <td class="text-center">
                            @if(strtolower($category->status) === 'active')
                                <span class="badge bg-success-subtle text-success px-3 py-2">Active</span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary px-3 py-2">Inactive</span>
                            @endif
                        </td>

                        <td class="text-center">

                            <a href="{{ route('asset_categories.edit', $category->id) }}"
                                class="btn btn-outline-warning btn-sm">
                                <i class="fa-solid fa-pen"></i>
                            </a>

                            <form action="{{ route('asset_categories.destroy', $category->id) }}" method="POST"
                                class="d-inline">

                                @csrf
                                @method('DELETE')

                                <button type="submit" class="btn btn-outline-danger btn-sm"
                                    onclick="return confirm('Delete this category?')">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>

                            </form>

                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center py-4 text-muted">
                            <i class="fa fa-folder-open fs-3 mb-2"></i>
                            <p class="mb-0">No Asset Categories Found</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

@endsection
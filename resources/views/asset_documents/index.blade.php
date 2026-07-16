@extends('layouts.app')

@section('title', 'Asset Documents | Unibs Tools')

@section('content')

    {{-- Flash Messages --}}
    @include('partials.message')

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1 text-dark fw-bold">Asset Documents</h3>
            <p class="text-muted small mb-0">Upload and manage invoices, agreements, and warranties for assets</p>
        </div>

        <a href="{{ route('asset-documents.create') }}" class="btn btn-primary shadow-sm px-4">
            <i class="fa fa-upload me-2"></i> Upload Document
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
                            <th class="py-3">Document Type</th>
                            <th class="py-3">File Name</th>
                            <th class="py-3">Uploaded On</th>
                            <th class="px-4 py-3 text-center" width="180">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($documents as $doc)
                            <tr>
                                <td class="px-4">{{ $doc->document_id }}</td>
                                <td>
                                    @if ($doc->asset)
                                        <a href="{{ route('asset.show', $doc->asset->id) }}" class="fw-semibold text-primary">
                                            {{ $doc->asset->asset_name }}
                                        </a>
                                        <div class="text-muted small">Code: {{ $doc->asset->asset_code }}</div>
                                    @else
                                        <span class="text-muted">Deleted Asset</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-secondary px-3 py-2 rounded-pill">
                                        {{ $doc->document_type }}
                                    </span>
                                </td>
                                <td class="text-dark">
                                    <i class="fa fa-file-alt me-1 text-muted"></i>
                                    <span title="{{ $doc->file_name }}">{{ Str::limit($doc->file_name, 50) }}</span>
                                </td>
                                <td>{{ $doc->uploaded_on ? $doc->uploaded_on->format('d M Y h:i A') : '-' }}</td>
                                <td class="px-4 text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <a href="{{ route('asset-documents.download', $doc->document_id) }}" class="btn btn-sm btn-outline-success" title="Download Document">
                                            <i class="fa fa-download"></i>
                                        </a>

                                        <a href="{{ route('asset-documents.edit', $doc->document_id) }}" class="btn btn-sm btn-outline-warning" title="Edit Metadata">
                                            <i class="fa fa-edit"></i>
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
                                <td colspan="6" class="text-center py-4 text-muted small">No asset documents uploaded yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Pagination --}}
    @if ($documents->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $documents->links() }}
        </div>
    @endif
@endsection

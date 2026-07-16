@extends('layouts.app')

@section('title', 'Edit Asset Document | Unibs Tools')

@section('title', 'Edit Asset Document | Unibs Tools')

@section('content')
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm border-0">
                    {{-- Header --}}
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0 fw-semibold text-dark">
                            <i class="fa fa-edit me-2 text-warning"></i>
                            Edit Asset Document Metadata
                        </h5>
                    </div>

                    {{-- Body --}}
                    <div class="card-body p-4">
                        <form method="POST" action="{{ route('asset-documents.update', $document->document_id) }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            {{-- Select Asset --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    Asset <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="fa fa-box-open"></i>
                                    </span>
                                    @php
                                        $preselectedAsset = $assets->firstWhere('id', $document->asset_id);
                                    @endphp
                                    <select class="form-select" disabled>
                                        @if ($preselectedAsset)
                                            <option value="{{ $preselectedAsset->id }}" selected>
                                                {{ $preselectedAsset->asset_name }} (Code: {{ $preselectedAsset->asset_code }})
                                            </option>
                                        @else
                                            @php
                                                $fallbackAsset = \App\Models\AssetMaster::find($document->asset_id);
                                            @endphp
                                            @if ($fallbackAsset)
                                                <option value="{{ $fallbackAsset->id }}" selected>
                                                    {{ $fallbackAsset->asset_name }} (Code: {{ $fallbackAsset->asset_code }})
                                                </option>
                                            @endif
                                        @endif
                                    </select>
                                    <input type="hidden" name="asset_id" value="{{ $document->asset_id }}">
                                    @error('asset_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Document Type --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    Document Type <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="fa fa-tags"></i>
                                    </span>
                                    <select name="document_type" class="form-select @error('document_type') is-invalid @enderror" required>
                                        <option value="">-- Select Document Type --</option>
                                        <option value="Invoice" {{ old('document_type', $document->document_type) == 'Invoice' ? 'selected' : '' }}>Invoice</option>
                                        <option value="Warranty Card" {{ old('document_type', $document->document_type) == 'Warranty Card' ? 'selected' : '' }}>Warranty Card</option>
                                        <option value="User Manual" {{ old('document_type', $document->document_type) == 'User Manual' ? 'selected' : '' }}>User Manual</option>
                                        <option value="Agreement" {{ old('document_type', $document->document_type) == 'Agreement' ? 'selected' : '' }}>Agreement</option>
                                        <option value="Other" {{ old('document_type', $document->document_type) == 'Other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('document_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Current File Details --}}
                            <div class="mb-3 p-3 bg-light rounded border">
                                <div class="fw-semibold text-dark mb-1">Current File:</div>
                                <div class="text-muted small">
                                    <i class="fa fa-file-alt me-1 text-primary"></i>
                                    {{ $document->file_name }}
                                </div>
                            </div>

                            {{-- Replace File --}}
                            <div class="mb-4">
                                <label class="form-label fw-semibold">
                                    Replace File (Optional)
                                </label>
                                <div class="input-group">
                                    <input type="file" name="document_file" class="form-control @error('document_file') is-invalid @enderror">
                                    @error('document_file')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-text text-muted small">
                                    Leave blank if you do not want to replace the current file. Allowed: PDF, DOC, DOCX, XLS, XLSX, JPG, JPEG, PNG, ZIP. Max 5MB.
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div class="d-flex justify-content-end gap-2 border-top pt-3">
                                <a href="{{ route('asset.show', $document->asset_id) }}" class="btn btn-outline-secondary px-4">
                                    Cancel
                                </a>
                                <button type="submit" class="btn btn-warning px-4 text-dark fw-semibold">
                                    <i class="fa fa-save me-1"></i> Update Details
                                </button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@extends('layouts.app')

@section('title', 'Upload Asset Document | Unibs Tools')

@section('content')
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm border-0">
                    {{-- Header --}}
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0 fw-semibold text-dark">
                            <i class="fa fa-upload me-2 text-primary"></i>
                            Upload Asset Document
                        </h5>
                    </div>

                    {{-- Body --}}
                    <div class="card-body p-4">
                        <form method="POST" action="{{ route('asset-documents.store') }}" enctype="multipart/form-data">
                            @csrf

                            {{-- Select Asset --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    Select Asset <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="fa fa-box-open"></i>
                                    </span>
                                    @if (request('asset_id'))
                                        @php
                                            $preselectedAsset = $assets->firstWhere('id', request('asset_id'));
                                        @endphp
                                        <select class="form-select" disabled>
                                            @if ($preselectedAsset)
                                                <option value="{{ $preselectedAsset->id }}" selected>
                                                    {{ $preselectedAsset->asset_name }} (Code: {{ $preselectedAsset->asset_code }})
                                                </option>
                                            @else
                                                @php
                                                    $fallbackAsset = \App\Models\AssetMaster::find(request('asset_id'));
                                                @endphp
                                                @if ($fallbackAsset)
                                                    <option value="{{ $fallbackAsset->id }}" selected>
                                                        {{ $fallbackAsset->asset_name }} (Code: {{ $fallbackAsset->asset_code }})
                                                    </option>
                                                @endif
                                            @endif
                                        </select>
                                        <input type="hidden" name="asset_id" value="{{ request('asset_id') }}">
                                    @else
                                        <select name="asset_id" class="form-select @error('asset_id') is-invalid @enderror" required>
                                            <option value="">-- Choose Asset --</option>
                                            @foreach ($assets as $asset)
                                                <option value="{{ $asset->id }}" {{ old('asset_id') == $asset->id ? 'selected' : '' }}>
                                                    {{ $asset->asset_name }} (Code: {{ $asset->asset_code }}, Cat: {{ $asset->asset_category }})
                                                </option>
                                            @endforeach
                                        </select>
                                    @endif
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
                                        <option value="Invoice" {{ old('document_type') == 'Invoice' ? 'selected' : '' }}>Invoice</option>
                                        <option value="Warranty Card" {{ old('document_type') == 'Warranty Card' ? 'selected' : '' }}>Warranty Card</option>
                                        <option value="User Manual" {{ old('document_type') == 'User Manual' ? 'selected' : '' }}>User Manual</option>
                                        <option value="Agreement" {{ old('document_type') == 'Agreement' ? 'selected' : '' }}>Agreement</option>
                                        <option value="Other" {{ old('document_type') == 'Other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('document_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- File Upload --}}
                            <div class="mb-4">
                                <label class="form-label fw-semibold">
                                    Choose Document File <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="file" name="document_file" class="form-control @error('document_file') is-invalid @enderror" required>
                                    @error('document_file')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-text text-muted small">
                                    Allowed file formats: PDF, DOC, DOCX, XLS, XLSX, JPG, JPEG, PNG, ZIP. Max file size: 5MB.
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div class="d-flex justify-content-end gap-2 border-top pt-3">
                                <a href="{{ request('asset_id') ? route('asset.show', request('asset_id')) : route('asset-documents.index') }}" class="btn btn-outline-secondary px-4">
                                    Cancel
                                </a>
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="fa fa-upload me-1"></i> Upload File
                                </button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

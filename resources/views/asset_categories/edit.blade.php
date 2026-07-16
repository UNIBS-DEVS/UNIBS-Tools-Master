@extends('layouts.app')

@section('title', 'Edit Asset Category | Unibs Tools')

@section('content')

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-xl-6 col-lg-8">

                <div class="card shadow-sm border-0 bg-white">

                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0 fw-semibold text-dark">
                            <i class="fa fa-folder me-2 text-primary"></i>
                            Edit Asset Category
                        </h5>
                    </div>

                    <div class="card-body">

                        @if ($errors->any())
                            <div class="alert alert-danger shadow-sm border-0 mb-3">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('asset_categories.update', $assetcategory->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="category_name" class="form-label fw-bold text-dark">Category Name</label>
                                <input type="text" name="category_name" id="category_name" class="form-control shadow-sm"
                                    value="{{ old('category_name', $assetcategory->category_name) }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="status" class="form-label fw-bold text-dark">Status</label>
                                <select name="status" id="status" class="form-select shadow-sm" required>
                                    <option value="Active" {{ old('status', $assetcategory->status) == 'Active' ? 'selected' : '' }}>Active</option>
                                    <option value="Inactive" {{ old('status', $assetcategory->status) == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>

                            <div class="d-flex justify-content-end mt-4 gap-2">
                                <a href="{{ route('asset_categories.index') }}" class="btn btn-light px-4">
                                    Cancel
                                </a>

                                <button type="submit" class="btn btn-primary px-4 fw-semibold">
                                    <i class="fa fa-save me-1"></i>
                                    Update
                                </button>
                            </div>

                        </form>

                    </div>

                </div>

            </div>
        </div>

    </div>
@endsection

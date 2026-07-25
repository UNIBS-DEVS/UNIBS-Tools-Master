@php
    $selectedApplication = $application ?? $module->application;
@endphp

<input type="hidden" name="app_id" value="{{ $selectedApplication->id }}">

<div class="row">

    {{-- <div class="col-md-6 mb-3">
        <label class="form-label">Application</label>
        <input type="text" class="form-control"
            value="{{ $selectedApplication->appCode }} - {{ $selectedApplication->appName }}" readonly>
    </div> --}}

    <div class="col-md-6 mb-3">
        <label class="form-label">
            Module Name <span class="text-danger">*</span>
        </label>

        <input type="text" name="name" class="form-control" value="{{ old('name', $module->name ?? '') }}" required>

        @error('name')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

</div>

<div class="text-end mt-3">

    <a href="{{ route('modules.index', ['app_id' => $selectedApplication->id]) }}" class="btn btn-secondary">
        <i class="fa fa-arrow-left"></i> Back
    </a>

    <button type="submit" class="btn btn-primary">
        {{ isset($module) ? 'Update' : 'Save' }}
    </button>

</div>

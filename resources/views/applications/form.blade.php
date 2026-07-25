<div class="mb-3">
    <label class="form-label">Application Code <span class="text-danger">*</span></label>
    <input type="text" name="appCode" class="form-control @error('appCode') is-invalid @enderror"
        value="{{ old('appCode', $application->appCode ?? '') }}">

    @error('appCode')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label class="form-label">Application Name <span class="text-danger">*</span></label>
    <input type="text" name="appName" class="form-control @error('appName') is-invalid @enderror"
        value="{{ old('appName', $application->appName ?? '') }}">

    @error('appName')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label class="form-label">Status</label>

    <div class="mb-3">
        {{-- <label class="form-label">Status</label> --}}

        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" name="status" value="1"
                {{ old('status', $application->status ?? 1) ? 'checked' : '' }}>

            <label class="form-check-label">
                Active
            </label>
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label">Status Message</label>

        <textarea name="status_message" class="form-control" rows="3">{{ old('status_message', $application->status_message ?? '') }}</textarea>
    </div>

    <div class="text-end">
        <a href="{{ route('applications.index') }}" class="btn btn-secondary">
            Cancel
        </a>

        <button type="submit" class="btn btn-primary">
            {{ isset($application) && $application->exists ? 'Update' : 'Save' }}
        </button>
    </div>

</div>

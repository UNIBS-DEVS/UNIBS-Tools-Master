<div class="row g-3">

    {{-- Application --}}
    <div class="col-md-3">

        <label class="form-label fw-semibold">
            Application <span class="text-danger">*</span>
        </label>

        <select name="application" class="form-select">

            <option value="">Select Application</option>

            <option value="attendance"
                {{ old('application', $uploadMobileApp->application ?? '') == 'attendance' ? 'selected' : '' }}>
                Attendance
            </option>

            <option value="call review"
                {{ old('application', $uploadMobileApp->application ?? '') == 'call review' ? 'selected' : '' }}>
                Call Review
            </option>

        </select>

        @error('application')
            <small class="text-danger">{{ $message }}</small>
        @enderror

    </div>

    {{-- APK File --}}
    <div class="col-md-3">

        <label class="form-label fw-semibold">
            APK File
            @if (!isset($uploadMobileApp))
                <span class="text-danger">*</span>
            @endif
        </label>

        <input type="file" name="apk_url" class="form-control" accept=".apk">

        @if (!empty($uploadMobileApp->apk_url))
            <small class="text-success d-block mt-1">
                <i class="fa fa-check-circle"></i>
                Current APK Available
            </small>

            <a href="{{ asset('storage/' . $uploadMobileApp->apk_url) }}" target="_blank" class="small text-primary">

                View Current APK

            </a>
        @endif

        @error('apk_url')
            <small class="text-danger">{{ $message }}</small>
        @enderror

    </div>

    {{-- Version Name --}}
    <div class="col-md-3">

        <label class="form-label fw-semibold">
            Version Name <span class="text-danger">*</span>
        </label>

        <input type="text" name="version_name" class="form-control" placeholder="e.g. 1.0.0"
            value="{{ old('version_name', $uploadMobileApp->version_name ?? '') }}">

        @error('version_name')
            <small class="text-danger">{{ $message }}</small>
        @enderror

    </div>

    {{-- Version Code --}}
    <div class="col-md-3">

        <label class="form-label fw-semibold">
            Version Code <span class="text-danger">*</span>
        </label>

        <input type="text" name="version_code" class="form-control" placeholder="e.g. v001"
            value="{{ old('version_code', $uploadMobileApp->version_code ?? '') }}">

        @error('version_code')
            <small class="text-danger">{{ $message }}</small>
        @enderror

    </div>

    {{-- Update Message --}}
    <div class="col-12">

        <label class="form-label fw-semibold">
            Update Message
        </label>

        <textarea name="update_message" class="form-control" rows="4" placeholder="Describe changes in this release...">{{ old('update_message', $uploadMobileApp->update_message ?? '') }}</textarea>

        @error('update_message')
            <small class="text-danger">{{ $message }}</small>
        @enderror

    </div>

    {{-- Force Update --}}
    <div class="col-12 mb-2">

        <div class="form-check form-switch">

            <input type="checkbox" class="form-check-input" name="force_update" id="force_update" value="1"
                {{ old('force_update', $uploadMobileApp->force_update ?? true) ? 'checked' : '' }}>

            <label class="form-check-label fw-semibold" for="force_update">

                Force Update

            </label>

        </div>

        <small class="text-muted">
            If enabled, users must update before using the app.
        </small>

    </div>

</div>

@push('scripts')
    <script>
        $(document).ready(function() {

            $('#mobileAppForm').on('submit', function() {
                $('#saveBtn')
                    .prop('disabled', true)
                    .html(
                        '<span class="spinner-border spinner-border-sm me-2"></span> Saving...'
                    );

            });

        });
    </script>
@endpush

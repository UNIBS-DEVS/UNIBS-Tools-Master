<div class="row">

    <div class="col-12">
        @include('partials.message')
    </div>

    @if ($errors->any())
        <div class="col-12 mb-3">
            <div class="alert alert-danger mb-0">
                {{ $errors->first() }}
            </div>
        </div>
    @endif

    <div class="col-md-6 mb-4">
        <label class="form-label">
            Date
            <span class="text-danger">*</span>
        </label>
        <input type="date" name="date" class="form-control" value="{{ old('date', $wfh->date ?? '') }}" required>
    </div>

    <div class="col-md-6 mb-4">
        <label class="form-label">
            Type
            <span class="text-danger">*</span>
        </label>
        <select name="type" class="form-select" required>
            <option value="" disabled {{ !isset($wfh->type) ? 'selected' : '' }}>Select Type</option>
            <option value="fullday" {{ old('type', $wfh->type ?? '') == 'fullday' ? 'selected' : '' }}>Full Day</option>
            <option value="halfday- first" {{ old('type', $wfh->type ?? '') == 'halfday- first' ? 'selected' : '' }}>Half Day (First Half)</option>
            <option value="halfday- second" {{ old('type', $wfh->type ?? '') == 'halfday- second' ? 'selected' : '' }}>Half Day (Second Half)</option>
        </select>
    </div>

</div>

<hr>

<div class="row">

    <div class="col-md-12">
        <label class="form-label">
            Reason
            <span class="text-danger">*</span>
        </label>
        <textarea name="reason" rows="5" maxlength="500" class="form-control"
            required>{{ old('reason', $wfh->reason ?? '') }}</textarea>
    </div>

</div>

<div class="mt-4 d-flex justify-content-between align-items-center">

    <div>
        <a href="{{ route('wfh.index') }}" class="btn btn-sm btn-secondary">
            <i class="fa fa-arrow-left me-1"></i>
            Back
        </a>

        <button type="submit" class="btn btn-sm btn-primary">
            <i class="fa fa-paper-plane me-1"></i>
            Submit Request
        </button>
    </div>

</div>
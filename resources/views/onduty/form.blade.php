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

    <div class="col-md-4 mb-4">
        <label class="form-label">
            Date
            <span class="text-danger">*</span>
        </label>
        <input type="date" name="date" class="form-control"
            value="{{ old('date', $onduty->date ?? '') }}" required>
    </div>

    <div class="col-md-4 mb-4">
        <label class="form-label">
            Start Time
            <span class="text-danger">*</span>
        </label>
        <input type="time" name="start_time" class="form-control"
            value="{{ old('start_time', (isset($onduty->start_time) && $onduty->start_time instanceof \DateTimeInterface) ? $onduty->start_time->format('H:i') : '') }}" required>
    </div>

    <div class="col-md-4 mb-4">
        <label class="form-label">
            End Time
            <span class="text-danger">*</span>
        </label>
        <input type="time" name="end_time" class="form-control"
            value="{{ old('end_time', (isset($onduty->end_time) && $onduty->end_time instanceof \DateTimeInterface) ? $onduty->end_time->format('H:i') : '') }}" required>
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
            required>{{ old('reason', $onduty->reason ?? '') }}</textarea>
    </div>

</div>

<div class="mt-4 d-flex justify-content-between align-items-center">

    <div>
        <a href="{{ route('onduty.index') }}" class="btn btn-sm btn-secondary">
            <i class="fa fa-arrow-left me-1"></i>
            Back
        </a>

        <button type="submit" class="btn btn-sm btn-primary">
            <i class="fa fa-paper-plane me-1"></i>
            Submit Request
        </button>
    </div>

</div>

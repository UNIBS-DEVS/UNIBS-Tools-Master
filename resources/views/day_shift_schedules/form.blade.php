<div class="row">

    <div class="col-md-6 mb-3">
        <label class="form-label fw-semibold">
            Shift Schedule
            <span class="text-danger">*</span>
        </label>

        <input type="hidden" name="shift_schedule_id"
            value="{{ old('shift_schedule_id', $dayshift->shift_schedule_id ?? $shift->id) }}">

        <input type="text" class="form-control"
            value="{{ $dayshift->shiftSchedule->shift_schedule ?? $shift->shift_schedule }}" readonly>

    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label fw-semibold">
            Day
            <span class="text-danger">*</span>
        </label>

        <select name="day" class="form-select @error('day') is-invalid @enderror" required>

            <option value="">Select Day</option>

            @foreach (['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'] as $day)
                <option value="{{ $day }}" {{ old('day', $dayshift->day ?? '') == $day ? 'selected' : '' }}>
                    {{ $day }}
                </option>
            @endforeach

        </select>

        @error('day')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>

    <div class="col-md-4 mb-3">
        <label class="form-label fw-semibold">
            Start Time
        </label>

        <input type="time" name="start_time" class="form-control @error('start_time') is-invalid @enderror"
            value="{{ old('start_time', $dayshift->start_time ?? '') }}" required>

        @error('start_time')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>

    <div class="col-md-4 mb-3">
        <label class="form-label fw-semibold">
            End Time
        </label>

        <input type="time" name="end_time" class="form-control @error('end_time') is-invalid @enderror"
            value="{{ old('end_time', $dayshift->end_time ?? '') }}" required>

        @error('end_time')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>

    <div class="col-md-4 mb-3">
        <label class="form-label fw-semibold">
            Grace Minutes
        </label>

        <input type="number" name="grace_minutes" class="form-control @error('grace_minutes') is-invalid @enderror"
            value="{{ old('grace_minutes', $dayshift->grace_minutes ?? '') }}" placeholder="Enter Grace Minutes"
            required>

        @error('grace_minutes')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>

</div>

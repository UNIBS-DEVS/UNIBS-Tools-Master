<div class="row">

    <div class="col-md-12">

        <label class="form-label fw-semibold">
            Shift Schedule
            <span class="text-danger">*</span>
        </label>

        <div class="input-group">

            <span class="input-group-text bg-light">
                <i class="fa fa-calendar"></i>
            </span>

            <input type="text" name="shift_schedule" class="form-control @error('shift_schedule') is-invalid @enderror"
                value="{{ old('shift_schedule', $shiftSchedule->shift_schedule ?? '') }}"
                placeholder="Enter Shift Schedule" required>

            @error('shift_schedule')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror

        </div>

    </div>

</div>

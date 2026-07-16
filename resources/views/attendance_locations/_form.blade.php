<div class="row g-3">

    <div class="col-md-3">
        <label class="form-label">Location Name <span class="text-danger">*</span></label>
        <input type="text" name="location_name" class="form-control @error('location_name') is-invalid @enderror"
            value="{{ old('location_name', $attendanceLocation->location_name ?? '') }}">
        @error('location_name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-3">
        <label class="form-label">Type <span class="text-danger">*</span></label>
        <select name="type" class="form-select @error('type') is-invalid @enderror">
            <option value="office" {{ old('type', $attendanceLocation->type ?? '') == 'office' ? 'selected' : '' }}>
                Office
            </option>

            <option value="home" {{ old('type', $attendanceLocation->type ?? '') == 'home' ? 'selected' : '' }}>
                Home
            </option>
        </select>
        @error('type')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-3">
        <label class="form-label">
            Shift Schedule <span class="text-danger">*</span>
        </label>

        <select name="shift_schedule_id" class="form-select @error('shift_schedule_id') is-invalid @enderror">

            @foreach ($shiftSchedules as $schedule)
                <option value="{{ $schedule->id }}"
                    {{ old('shift_schedule_id', $attendanceLocation->shift_schedule_id ?? '') == $schedule->id ? 'selected' : '' }}>
                    {{ $schedule->shift_schedule }}
                </option>
            @endforeach

        </select>
        @error('shift_schedule_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-3 d-flex justify-content-center align-items-end">
        <div class="form-check">
            <input type="checkbox" class="form-check-input" name="is_active" id="is_active" value="1"
                {{ old('is_active', $attendanceLocation->is_active ?? true) ? 'checked' : '' }}>

            <label class="form-check-label" for="is_active">
                Active
            </label>
        </div>
    </div>

    <div class="col-md-4">
        <label class="form-label">Latitude</label>
        <input type="text" name="latitude" class="form-control @error('latitude') is-invalid @enderror"
            value="{{ old('latitude', $attendanceLocation->latitude ?? '') }}">
        @error('latitude')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Longitude</label>
        <input type="text" name="longitude" class="form-control @error('longitude') is-invalid @enderror"
            value="{{ old('longitude', $attendanceLocation->longitude ?? '') }}">
        @error('longitude')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>


    <div class="col-md-3">
        <label class="form-label">Radius (Meters)</label>
        <input type="number" name="radius" class="form-control @error('radius') is-invalid @enderror"
            value="{{ old('radius', $attendanceLocation->radius ?? 100) }}">
        @error('radius')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

</div>

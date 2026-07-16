<div class="row">

    {{-- Holiday Year --}}
    <div class="col-md-6 mb-3">
        <label class="form-label fw-semibold">
            Holiday Year <span class="text-danger">*</span>
        </label>

        <input type="number" name="holiday_year" class="form-control @error('holiday_year') is-invalid @enderror"
            value="{{ old('holiday_year', $holiday->holiday_year ?? date('Y')) }}" placeholder="Enter Holiday Year">

        @error('holiday_year')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Holiday Date --}}
    <div class="col-md-6 mb-3">
        <label class="form-label fw-semibold">
            Holiday Date <span class="text-danger">*</span>
        </label>

        <input type="date" name="holiday_date" class="form-control @error('holiday_date') is-invalid @enderror"
            value="{{ old('holiday_date', isset($holiday) ? \Carbon\Carbon::parse($holiday->holiday_date)->format('Y-m-d') : '') }}">

        @error('holiday_date')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Description --}}
    <div class="col-md-6 mb-3">
        <label class="form-label fw-semibold">
            Description <span class="text-danger">*</span>
        </label>

        <input type="text" name="description" class="form-control @error('description') is-invalid @enderror"
            value="{{ old('description', $holiday->description ?? '') }}" placeholder="Enter Holiday Description">

        @error('description')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Holiday Type --}}
    <div class="col-md-6 mb-3">
        <label class="form-label fw-semibold">
            Holiday Type <span class="text-danger">*</span>
        </label>

        <select name="holiday_type" class="form-select @error('holiday_type') is-invalid @enderror">

            @foreach (['National', 'Festival', 'Optional'] as $type)
                <option value="{{ $type }}"
                    {{ old('holiday_type', $holiday->holiday_type ?? '') == $type ? 'selected' : '' }}>
                    {{ $type }}
                </option>
            @endforeach

        </select>

        @error('holiday_type')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

</div>

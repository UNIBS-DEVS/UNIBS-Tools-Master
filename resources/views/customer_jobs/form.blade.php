<div class="row">

    <div class="col-md-6 mb-3">
        <label class="form-label">Customer <span class="text-danger">*</span></label>

        <select name="customer_id" class="form-control @error('customer_id') is-invalid @enderror">

            <option value="">Select Customer</option>

            @foreach ($customers as $customer)
                <option value="{{ $customer->id }}"
                    {{ old('customer_id', $customerJob->customer_id ?? '') == $customer->id ? 'selected' : '' }}>

                    {{ $customer->customer }}
                </option>
            @endforeach

        </select>

        @error('customer_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Job Position <span class="text-danger">*</span></label>

        <input type="text" name="position" class="form-control @error('position') is-invalid @enderror"
            value="{{ old('position', $customerJob->position ?? '') }}">

        @error('position')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>


    <div class="col-md-6 mb-3">
        <label class="form-label">Skill <span class="text-danger">*</span> </label>

        <input type="text" name="skill" class="form-control @error('skill') is-invalid @enderror"
            value="{{ old('skill', $customerJob->skill ?? '') }}">

        @error('skill')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Experience</label>

        <input type="text" name="experience" class="form-control"
            value="{{ old('experience', $customerJob->experience ?? '') }}" placeholder="e.g. 3-5 Years">
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Status</label>

        <select name="status" class="form-control">

            <option value="Open" {{ old('status', $customerJob->status ?? '') == 'Open' ? 'selected' : '' }}>
                Open
            </option>

            <option value="Closed" {{ old('status', $customerJob->status ?? '') == 'Closed' ? 'selected' : '' }}>
                Closed
            </option>

            <option value="On-Hold" {{ old('status', $customerJob->status ?? '') == 'On-Hold' ? 'selected' : '' }}>
                On-Hold
            </option>

        </select>
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Budget</label>

        <input type="text" name="budget" class="form-control"
            value="{{ old('budget', $customerJob->budget ?? '') }}">
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Location</label>

        <input type="text" name="location" class="form-control"
            value="{{ old('location', $customerJob->location ?? '') }}">
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Count</label>

        <input type="number" min="1" max="500" name="count" class="form-control"
            value="{{ old('count', $customerJob->count ?? 1) }}">
    </div>

    <div class="col-md-12 mb-3">
        <label class="form-label">JD Path</label>

        <input type="text" name="jd_path" class="form-control"
            value="{{ old('jd_path', $customerJob->jd_path ?? '') }}" placeholder="Paste JD URL or file path">
    </div>

    <div class="col-md-12 mb-3">
        <label class="form-label">Remarks</label>

        <textarea name="remarks" rows="4" class="form-control">{{ old('remarks', $customerJob->remarks ?? '') }}</textarea>
    </div>

</div>

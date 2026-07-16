<div class="row">

    {{-- Leave Name --}}
    <div class="col-md-6 mb-3">
        <label class="form-label fw-semibold">
            Leave Name <span class="text-danger">*</span>
        </label>

        <input type="text" name="leave_name" class="form-control @error('leave_name') is-invalid @enderror"
            value="{{ old('leave_name', $leaveType->leave_name ?? '') }}" placeholder="Enter Leave Name">

        @error('leave_name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Accrual Type --}}
    <div class="col-md-6 mb-3">
        <label class="form-label fw-semibold">
            Accrual Type <span class="text-danger">*</span>
        </label>

        <select name="accrual_type" class="form-select @error('accrual_type') is-invalid @enderror">

            <option value="">Select Accrual Type</option>

            @foreach (['Monthly', 'Quarterly', 'Yearly'] as $type)
                <option value="{{ $type }}"
                    {{ old('accrual_type', $leaveType->accrual_type ?? '') == $type ? 'selected' : '' }}>
                    {{ $type }}
                </option>
            @endforeach

        </select>

        @error('accrual_type')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Accrual --}}
    <div class="col-md-6 mb-3">
        <label class="form-label fw-semibold">
            Accrual <span class="text-danger">*</span>
        </label>

        <input type="number" step="0.01" name="accrual" class="form-control @error('accrual') is-invalid @enderror"
            value="{{ old('accrual', $leaveType->accrual ?? '') }}" placeholder="Enter Accrual">

        @error('accrual')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Max Balance --}}
    <div class="col-md-6 mb-3">
        <label class="form-label fw-semibold">
            Max Balance <span class="text-danger">*</span>
        </label>

        <input type="number" step="0.01" name="max_balance"
            class="form-control @error('max_balance') is-invalid @enderror"
            value="{{ old('max_balance', $leaveType->max_balance ?? '') }}" placeholder="Enter Max Balance">

        @error('max_balance')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Status --}}
    <div class="col-md-6 mb-3">
        <label class="form-label fw-semibold">
            Status <span class="text-danger">*</span>
        </label>

        <select name="status" class="form-select @error('status') is-invalid @enderror">

            <option value="active" {{ old('status', $leaveType->status ?? '') == 'active' ? 'selected' : '' }}>
                Active
            </option>

            <option value="inactive" {{ old('status', $leaveType->status ?? '') == 'inactive' ? 'selected' : '' }}>
                Inactive
            </option>

        </select>

        @error('status')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

</div>

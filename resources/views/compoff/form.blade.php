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
            Day Worked
            <span class="text-danger">*</span>
        </label>

        <input type="date" name="day_worked" class="form-control"
            value="{{ old('day_worked', $compOff->day_worked ?? '') }}"
            required>

    </div>

    {{-- <div class="col-md-6 mb-4">

        {{-- <label class="form-label">
            Request Date
        </label> --}}

        {{-- <input type="text" class="form-control" value="{{ now()->format('d-M-Y') }}" readonly>

    </div> --}}

</div>

<hr>

<div class="row">

    <div class="col-md-12">

        <label class="form-label">
            Reason
            <span class="text-danger">*</span>
        </label>

        <textarea name="reason" rows="5" maxlength="500" class="form-control"
            required>{{ old('reason', $compOff->reason ?? '') }}</textarea>

    </div>

</div>

<div class="mt-4 d-flex justify-content-between align-items-center">

    <div>

        <a href="{{ route('compoff.index') }}" class="btn btn-sm btn-secondary">

            <i class="fa fa-arrow-left me-1"></i>

            Back

        </a>



        <button type="submit" name="action" value="submit" class="btn btn-sm btn-primary">

            <i class="fa fa-paper-plane me-1"></i>

            Submit Request

        </button>

    </div>

</div>
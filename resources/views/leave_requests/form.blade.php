<div class="row">

    <div class="col-md-3 mb-3">
        <label class="form-label">Leave Type</label>

        <select name="leave_type_id" id="leave_type_id" class="form-control @error('leave_type_id') is-invalid @enderror"
            required>

            <option value="">Select Leave Type</option>
            {{--
            @foreach ($leaveTypes as $leaveType)
                <option value="{{ $leaveType->id }}" data-balance="{{ $leaveType->balance ?? 0 }}">
                    {{ $leaveType->leave_name }}
                    (Balance:{{ $leaveType->leaveBalances->first()->balance ?? 0 }})
                </option>
            @endforeach --}}

            @foreach ($leaveTypes as $leaveType)
                <option value="{{ $leaveType->id }}">

                    @if ($leaveType->require_balance)
                        {{ $leaveType->leave_name }}
                        (Balance : {{ $leaveType->leaveBalances->first()->balance ?? 0 }})
                    @else
                        {{ $leaveType->leave_name }}
                    @endif

                </option>
            @endforeach

        </select>

        @error('leave_type_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- <input type="hidden" name="leave_balance" id="leave_balance"> --}}

    <div class="col-md-3 mb-3">
        <label class="form-label">Duration</label>

        <select name="duration" class="form-control @error('leave_type_id') is-invalid @enderror" required>
            <option value="">Select Duration</option>
            <option value="Full Day">Full Day</option>
            <option value="First Half">First Half</option>
            <option value="Second Half">Second Half</option>
        </select>
    </div>

    <div class="col-md-3 mb-3">
        <label class="form-label">Start Date</label>
        <input type="date" name="start_date" class="form-control " required>
    </div>

    <div class="col-md-3 mb-3">
        <label class="form-label">End Date</label>
        <input type="date" name="end_date" class="form-control" required>
    </div>

    <div class="col-md-12 mb-3">
        <label class="form-label">User Remarks</label>
        <textarea name="remarks" class="form-control" rows="3"></textarea>
    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('leave_type_id')
            .addEventListener('change', function() {

                let balance = this.options[this.selectedIndex]
                    .getAttribute('data-balance');

                document.getElementById('leave_balance').value = balance ?? '';
            });
    });
</script>

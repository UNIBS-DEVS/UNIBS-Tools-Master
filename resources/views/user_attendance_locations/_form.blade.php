<div class="mb-3">
    <label>User</label>

    <select name="user_id" class="form-control" required>

        <option value="">Select User</option>

        @foreach ($users as $user)
            <option value="{{ $user->id }}"
                {{ old('user_id', $userAttendanceLocation->user_id ?? '') == $user->id ? 'selected' : '' }}>

                {{ $user->name }}

            </option>
        @endforeach

    </select>
</div>

{{-- <div class="mb-3">
    <label>Location</label>

    <select name="attendance_location_id" class="form-control" required>

        <option value="">Select Location</option>

        @foreach ($locations as $location)
            <option value="{{ $location->id }}"
                {{ old(
                    'attendance_location_id',
                    $userAttendanceLocation->attendance_location_id ?? ($selectedLocationId ?? ''),
                ) == $location->id
                    ? 'selected'
                    : '' }}>

                {{ $location->location_name }}

            </option>
        @endforeach

    </select>
</div> --}}

<input type="hidden" name="attendance_location_id" value="{{ $location->id }}">

<div class="mb-3">
    <label>Status</label>

    <select name="status" class="form-control">

        <option value="active"
            {{ old('status', $userAttendanceLocation->status ?? 'active') == 'active' ? 'selected' : '' }}>
            Active
        </option>

        <option value="inactive"
            {{ old('status', $userAttendanceLocation->status ?? '') == 'inactive' ? 'selected' : '' }}>
            Inactive
        </option>

    </select>
</div>

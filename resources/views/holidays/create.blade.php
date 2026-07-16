@extends('layouts.app')

@section('content')
    <div class="container">

        <h2>Add Holiday</h2>

        <form action="{{ route('holidays.store') }}" method="POST">
            @csrf

            <div class="row">

                <!-- Year -->
                <div class="col-md-6 mb-3">
                    <label class="form-label">Year</label>
                    <input type="number" name="holiday_year" class="form-control" value="{{ old('holiday_year') }}" required>
                </div>

                <!-- Date -->
                <div class="col-md-6 mb-3">
                    <label class="form-label">Date</label>
                    <input type="date" name="holiday_date" id="holiday_date" class="form-control"
                        value="{{ old('holiday_date') }}" required>

                    <small id="day_name" class="text-primary fw-bold mt-1 d-block"></small>
                </div>

                <script>
                    document.getElementById('holiday_date').addEventListener('change', function() {
                        const date = new Date(this.value);

                        if (!isNaN(date)) {
                            const day = date.toLocaleDateString('en-US', {
                                weekday: 'long'
                            });

                            document.getElementById('day_name').textContent = day;
                        } else {
                            document.getElementById('day_name').textContent = '';
                        }
                    });
                </script>

                <!-- Description -->
                <div class="col-md-6 mb-3">
                    <label class="form-label">Description</label>
                    <input type="text" name="description" class="form-control" value="{{ old('description') }}" required>
                </div>



                <!-- Holiday Type -->
                <div class="col-md-6 mb-3">
                    <label class="form-label">Holiday Type</label>

                    <select name="holiday_type" class="form-control" required>
                        <option value="">Select Type</option>
                        <option value="National">National</option>
                        <option value="Festival">Festival</option>
                        <option value="Optional">Optional</option>
                    </select>
                </div>

            </div>

            <button class="btn btn-success">
                Save
            </button>

        </form>

    </div>
@endsection

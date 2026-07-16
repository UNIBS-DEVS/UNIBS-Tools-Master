@extends('layouts.app')

@section('content')
    <div class="container">

        <h2>Edit Holiday</h2>

        <form action="{{ route('holidays.update', $holiday->id) }}" method="POST">

            @csrf
            @method('PUT')

            <div class="mb-3">
                <label>Holiday Year</label>

                <input type="number" name="holiday_year" value="{{ $holiday->holiday_year }}" class="form-control">
            </div>

            <div class="mb-3">
                <label>Holiday Date</label>

                <input type="date" name="holiday_date" value="{{ $holiday->holiday_date }}" class="form-control">
            </div>

            <div class="mb-3">
                <label>Description</label>

                <input type="text" name="description" value="{{ $holiday->description }}" class="form-control">
            </div>

            <div class="mb-3">
                <label>Holiday Type</label>

                <input type="text" name="holiday_type" value="{{ $holiday->holiday_type }}" class="form-control">
            </div>

            <button class="btn btn-primary">
                Update
            </button>

        </form>

    </div>
@endsection

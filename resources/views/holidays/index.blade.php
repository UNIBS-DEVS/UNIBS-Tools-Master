@extends('layouts.app')

@section('content')
    <div class="container">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3>Holiday List</h3>

            @if (Auth::user()->hasRole('admin'))
                <a href="{{ route('holidays.create') }}" class="btn btn-primary">
                    Add Holiday
                </a>
            @endif
        </div>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="card">
            <div class="card-body">

                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Year</th>
                            <th>Date</th>
                            <th>Description</th>
                            <th>Type</th>

                            {{-- @role('admin') --}}
                            <th width="170">Action</th>
                            {{-- @endrole --}}
                        </tr>
                    </thead>

                    <tbody>

                        @forelse ($holidays as $key => $holiday)
                            <tr>
                                <td>{{ $key + 1 }}</td>

                                <td>{{ $holiday->holiday_year }}</td>

                                <td>
                                    {{ \Carbon\Carbon::parse($holiday->holiday_date)->format('d M Y') }}
                                    (<strong>{{ \Carbon\Carbon::parse($holiday->holiday_date)->format('l') }}</strong>)
                                </td>

                                <td>{{ $holiday->description }}</td>

                                <td>{{ $holiday->holiday_type }}</td>

                                @if (Auth::user()->hasRole('admin'))
                                    <td>
                                        <a href="{{ route('holidays.edit', $holiday->id) }}" class="btn btn-warning btn-sm">
                                            Edit
                                        </a>

                                        <form action="{{ route('holidays.destroy', $holiday->id) }}" method="POST"
                                            style="display:inline-block"
                                            onsubmit="return confirm('Are you sure you want to delete this holiday?')">

                                            @csrf
                                            @method('DELETE')

                                            <button class="btn btn-danger btn-sm">
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                @endif
                            @empty
                            <tr>
                                <td colspan="{{ Auth::user()->role == 'admin' ? 6 : 5 }}" class="text-center">
                                    No holidays found.
                                </td>
                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>
        </div>

    </div>
@endsection

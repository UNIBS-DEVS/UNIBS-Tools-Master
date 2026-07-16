@extends('layouts.app')

@section('title', 'My Assignments')

@section('content')

    <div class="container-fluid">

        <div class="card shadow-sm border-0">

            <div class="card-header bg-primary text-white">

                <h5 class="mb-0">
                    My Assignments
                </h5>

            </div>

            <div class="card-body">

                <div class="mb-3">

                    <div class="mb-3">

                        <form method="GET" id="dateForm">

                            <input type="date" name="date" value="{{ $date }}" class="form-control"
                                style="width:200px;" onchange="this.form.submit()">

                        </form>

                    </div>

                </div>

                <div class="table-responsive">

                    <table class="table table-bordered align-middle">

                        <thead>

                            <tr>
                                <th>#</th>
                                <th>Customer</th>
                                <th>Position</th>
                                <th>Skill</th>
                                <th>Experience</th>
                                <th>Location</th>
                                <th>JD</th>
                            </tr>

                        </thead>

                        <tbody>

                            @forelse($jobs as $job)
                                <tr>

                                    <td>{{ $loop->iteration }}</td>

                                    <td>{{ $job->customer->customer ?? '-' }}</td>

                                    <td>{{ $job->position }}</td>

                                    <td>{{ $job->skill }}</td>

                                    <td>{{ $job->experience }}</td>

                                    <td>{{ $job->location }}</td>


                                    <td><a href="{{ $job->jd_path }}"><i class="fa-solid fa-link"></i></a></td> 

                                </tr>

                            @empty

                                <tr>
                                    <td colspan="7" class="text-center">
                                        No Assignments Found
                                    </td>
                                </tr>
                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

@endsection

@push('scripts')
    <script>
        $(document).on('change', 'input[name="date"]', function() {
            $('#dateForm').submit();
        });
    </script>
@endpush

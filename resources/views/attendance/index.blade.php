@extends('layouts.app')

@section('content')
    <div class="container">

        @if (session('success'))
            <div class="alert alert-success shadow-sm border-0">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger shadow-sm border-0">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger shadow-sm border-0">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card shadow border-0 mb-4 bg-white">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <h4 class="mb-0 fw-bold text-primary">Attendance Logs</h4>
                <a href="{{ route('attendance.create') }}" class="btn btn-primary px-4 py-2 shadow-sm fw-bold">
                    Add Punch Request
                </a>
            </div>

            <div class="card-body">
                <!-- Side-by-Side Tables -->
                <div class="row">
                    <!-- Table 1: Last 7 Days -->
                    <div class="col-md-6 mb-4">
                        <div class="card border-0 shadow-sm bg-white">
                            <div class="card-header bg-white py-3 border-bottom">
                                <h5 class="mb-0 fw-bold text-dark">Last 7 Days</h5>
                            </div>
                            <div class="card-body p-0 table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Punch Time (In/Out)</th>
                                            <th>Location</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($last7DaysPunches as $punch)
                                            <tr>
                                                <td class="fw-semibold">
                                                    {{ $punch->punch_at->format('d M Y h:i A') }} ({{ ucfirst($punch->punch_type) }})
                                                </td>
                                                <td>
                                                    {{ $punch->attendanceLocation->location_name ?? '-' }} ({{ ucfirst($punch->attendanceLocation->type ?? 'Office') }})
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="2" class="text-center text-muted py-4">
                                                    No punch history found for the last 7 days.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Table 2: My Punch In Request -->
                    <div class="col-md-6 mb-4">
                        <div class="card border-0 shadow-sm bg-white">
                            <div class="card-header bg-white py-3 border-bottom">
                                <h5 class="mb-0 fw-bold text-dark">My Punch In Request</h5>
                            </div>
                            <div class="card-body p-0 table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Punch Time (In/Out)</th>
                                            <th>Location</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($punchRequests as $punch)
                                            <tr>
                                                <td class="fw-semibold">
                                                    {{ $punch->punch_at->format('d M Y h:i A') }} ({{ ucfirst($punch->punch_type) }})
                                                </td>
                                                <td>
                                                    {{ $punch->attendanceLocation->location_name ?? '-' }} ({{ ucfirst($punch->attendanceLocation->type ?? 'Office') }})
                                                </td>
                                                <td>
                                                    <span class="badge 
                                                        @if($punch->status == 'approved' || $punch->status == 'auto_approved') bg-success
                                                        @elseif($punch->status == 'rejected') bg-danger
                                                        @else bg-warning text-dark @endif">
                                                        {{ ucfirst(str_replace('_', ' ', $punch->status)) }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center text-muted py-4">
                                                    No punch requests found.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
@extends('layouts.app')

@section('title', 'Work From Home | Unibs Tools')

@section('content')

    <div class="container mt-4">

        <div class="card shadow-sm border-0">

            <div class="card-header bg-white d-flex justify-content-between align-items-center">

                <h5 class="mb-0 fw-semibold">
                    Work From Home Requests
                </h5>

                <div class="d-flex gap-2 align-items-center">

                    <form method="GET" action="{{ route('wfh.index') }}" class="d-flex">
                        <select name="status" onchange="this.form.submit()" class="form-select form-select-sm">
                            <option value="" {{ request('status') == '' ? 'selected' : '' }}>
                                All Status
                            </option>
                            <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>
                                Submitted
                            </option>
                            <option value="Approved" {{ request('status') == 'Approved' ? 'selected' : '' }}>
                                Approved
                            </option>
                            <option value="Rejected" {{ request('status') == 'Rejected' ? 'selected' : '' }}>
                                Rejected
                            </option>
                        </select>
                    </form>

                    <a href="{{ route('wfh.create') }}" class="btn btn-primary btn-sm">
                        Create Request
                    </a>

                </div>

            </div>

            <div class="card-body table-responsive">

                <table class="table table-hover align-middle">

                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Reason</th>
                            <th>Status</th>
                            <th>Manager Remarks</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($requests as $request)
                            <tr>
                                <td>
                                    {{ \Carbon\Carbon::parse($request->date)->format('d-M-Y') }}
                                </td>
                                <td>
                                    @if($request->type == 'fullday')
                                        Full Day
                                    @elseif($request->type == 'halfday- first')
                                        First Half
                                    @elseif($request->type == 'halfday- second')
                                        Second Half
                                    @else
                                        {{ ucfirst($request->type) }}
                                    @endif
                                </td>
                                <td>
                                    {{ $request->reason }}
                                </td>
                                <td>
                                    <span class="badge
                                        @if(strtolower($request->status) == 'approved') bg-success
                                        @elseif(strtolower($request->status) == 'rejected') bg-danger
                                        @elseif(strtolower($request->status) == 'submitted') bg-warning text-dark
                                        @else bg-secondary @endif">
                                        @if(strtolower($request->status) == 'submitted')
                                            Submitted
                                        @else
                                            {{ ucfirst($request->status) }}
                                        @endif
                                    </span>
                                </td>
                                <td>
                                    {{ $request->manager_remarks ?? '-' }}
                                </td>
                                <td class="text-center">
                                    @if(strtolower($request->status) == 'rejected')
                                        <a href="{{ route('wfh.edit', $request->id) }}" class="btn btn-outline-warning btn-sm" title="Edit Request">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">
                                    No Work From Home Requests Found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>

                <div class="mt-3">
                    {{ $requests->links() }}
                </div>

            </div>

        </div>

    </div>

@endsection

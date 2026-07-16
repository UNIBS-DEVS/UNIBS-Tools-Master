@extends('layouts.app')

@section('title', 'Comp Off | Unibs Tools')

@section('content')

    <div class="container mt-4">

        <div class="card shadow-sm border-0">

            <div class="card-header bg-white d-flex justify-content-between align-items-center">

                <h5 class="mb-0 fw-semibold">
                    Comp Off Requests
                </h5>

                <div class="d-flex gap-2 align-items-center">

                    <form method="GET" action="{{ route('compoff.index') }}" class="d-flex">

                        <select name="status" onchange="this.form.submit()" class="form-select form-select-sm">

                            <option value="" {{ request('status') == '*' ? 'selected' : '' }}>
                                All Status
                            </option>

                            <option value="Pending" {{ request('status') == 'Submitted' ? 'selected' : '' }}>
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

                    <a href="{{ route('compoff.create') }}" class="btn btn-primary btn-sm">

                        Create Request

                    </a>

                </div>

            </div>

            <div class="card-body table-responsive">

                <table class="table table-hover align-middle">

                    <thead>

                        <tr>

                            <th>Worked Date</th>

                            <th>Reason</th>

                            <th>Status</th>

                            <th>Manager Remarks</th>

                            <th class="text-center">
                                Actions
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse($requests as $request)

                            <tr>

                                <td>

                                    {{ \Carbon\Carbon::parse($request->day_worked)->format('d-M-Y') }}

                                </td>

                                <td>

                                    {{ $request->reason }}

                                </td>

                                <td>

                                    <span class="badge
                                                                    @if(strtolower($request->status) == 'approved')
                                                                        bg-success
                                                                    @elseif(strtolower($request->status) == 'rejected')
                                                                        bg-danger
                                                                    @elseif(strtolower($request->status) == 'submitted')
                                                                        bg-warning text-dark
                                                                    @else
                                                                        bg-secondary
                                                                    @endif">

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

                                        <a href="{{ route('compoff.edit', $request->id) }}" class="btn btn-outline-warning btn-sm"
                                            title="Edit Request">

                                            <i class="fa-solid fa-pen"></i>

                                        </a>

                                    @else

                                        <span class="text-muted">
                                            -
                                        </span>

                                    @endif

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="5" class="text-center text-muted">

                                    No Comp Off Requests Found.

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
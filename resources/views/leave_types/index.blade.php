@extends('layouts.app')

@section('title', 'Leave Types | Unibs Tools')

@push('styles')
    <style>
        .table tbody tr td,
        .table thead tr th {
            padding: .15rem .5rem;
            font-size: 13px;
        }
    </style>
@endpush

@section('content')

    @include('partials.message')

    <div class="d-flex justify-content-between align-items-center mb-1">
        <h5>Leave Types</h5>

        <a href="{{ route('leave-types.create') }}" class="btn btn-primary btn-sm">
            <i class="fa fa-plus"></i>
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle bg-white">

            <thead class="table-dark">
                <tr>
                    <th>Leave Name</th>
                    <th>Accrual Type</th>
                    <th>Accrual</th>
                    <th>Max Balance</th>
                    <th>Status</th>
                    <th width="130" class="text-center">Actions</th>
                </tr>
            </thead>

            <tbody>

                @forelse($leaveTypes as $leaveType)
                    <tr>

                        <td>{{ $leaveType->leave_name }}</td>

                        <td>{{ ucfirst($leaveType->accrual_type) }}</td>

                        <td>{{ $leaveType->accrual }}</td>

                        <td>{{ $leaveType->max_balance }}</td>

                        <td>
                            @switch(strtolower($leaveType->status))
                                @case('active')
                                    <span class="badge bg-success">
                                        Active
                                    </span>
                                @break

                                @case('inactive')
                                    <span class="badge bg-danger">
                                        Inactive
                                    </span>
                                @break

                                @default
                                    <span class="badge bg-secondary">
                                        {{ ucfirst($leaveType->status) }}
                                    </span>
                            @endswitch
                        </td>

                        <td class="text-center">

                            <a href="{{ route('leave-types.edit', $leaveType->id) }}"
                                class="btn btn-outline-warning btn-sm">
                                <i class="fa-solid fa-pen"></i>
                            </a>

                            <form action="{{ route('leave-types.destroy', $leaveType->id) }}" method="POST"
                                class="d-inline">

                                @csrf
                                @method('DELETE')

                                <button type="submit" class="btn btn-outline-danger btn-sm"
                                    onclick="return confirm('Delete this leave type?')">

                                    <i class="fa-solid fa-trash-can"></i>

                                </button>

                            </form>

                        </td>

                    </tr>

                    @empty

                        <tr>
                            <td colspan="6" class="text-center">
                                No leave types found.
                            </td>
                        </tr>
                    @endforelse

                </tbody>

            </table>
        </div>

    @endsection

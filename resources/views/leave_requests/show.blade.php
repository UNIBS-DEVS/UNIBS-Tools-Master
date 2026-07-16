@extends('layouts.app')

@section('title', 'View Leave')

@section('content')

    <div class="container">

        <div class="card">

            <div class="card-header">
                <h4>Leave Application Details</h4>
            </div>

            <div class="card-body">

                <table class="table table-bordered">

                    <tr>
                        <th width="200">Leave Type</th>
                        <td>{{ $leaveRequest->leaveType->leave_name -  {{ $leaveRequest->leaveType->max_balance }}?? '' }}</td>
                    </tr>

                    <tr>
                        <th>Leave Balance</th>
                        <td>{{ $leaveRequest->leave_balance }}</td>
                    </tr>

                    <tr>
                        <th>Duration</th>
                        <td>{{ $leaveRequest->duration }}</td>
                    </tr>

                    <tr>
                        <th>Start Date</th>
                        <td>{{ $leaveRequest->start_date }}</td>
                    </tr>

                    <tr>
                        <th>End Date</th>
                        <td>{{ $leaveRequest->end_date }}</td>
                    </tr>

                    <tr>
                        <th>Status</th>
                        <td>{{ $leaveRequest->status }}</td>
                    </tr>

                    <tr>
                        <th>Remarks</th>
                        <td>{{ $leaveRequest->remarks }}</td>
                    </tr>

                </table>

                <a href="{{ route('leave-requests.index') }}" class="btn btn-secondary">
                    Back
                </a>

            </div>

        </div>

    </div>

@endsection

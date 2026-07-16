@extends('layouts.app')

@section('title', 'Account Processing | Unibs Tools')

@section('content')

    <div class="container mt-4">


        @include('partials.message')

        <div class="card shadow-sm border-0">

            <div class="card-header bg-white d-flex justify-content-between align-items-center">

                <h5 class="mb-0 fw-semibold">
                    Account Processing
                </h5>

            </div>

            <div class="card-body table-responsive">

                <table class="table table-hover align-middle">

                    <thead>

                        <tr>

                            <th>Employee</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Reason</th>
                            <th>Status</th>
                            <th class="text-center">
                                Actions
                            </th>
                        </tr>

                    </thead>

                    <tbody>

                        @forelse($requests as $expense)

                            <tr>



                                <td>
                                    {{ $expense->employee?->name ?? '-' }}
                                </td>

                                <td>
                                    {{ $expense->created_at?->format('d-M-Y') }}
                                </td>

                                <td>
                                    ₹ {{ number_format($expense->total_amount, 2) }}
                                </td>

                                <td>
                                    {{ $expense->user_remarks }}
                                </td>

                                <td>

                                    <span class="badge
                                                                @if($expense->status == 'Approved')
                                                                    bg-success
                                                                @elseif($expense->status == 'Rejected')
                                                                    bg-danger
                                                                @elseif($expense->status == 'Pending')
                                                                    bg-warning text-dark
                                                                @elseif($expense->status == 'Reimbursed')
                                                                    bg-info
                                                                @else
                                                                    bg-secondary
                                                                @endif
                                                            ">
                                        {{ $expense->status }}
                                    </span>

                                </td>

                                <td class="text-center">

                                    @if($expense->status == 'Approved')

                                        <a href="{{ url('/account/process/' . $expense->id) }}"
                                            class="btn btn-outline-primary btn-sm">

                                            <i class="fa fa-money-check-dollar"></i>

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

                                <td colspan="7" class="text-center text-muted">

                                    No Requests Found

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

@endsection
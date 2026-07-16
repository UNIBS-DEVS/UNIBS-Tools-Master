@extends('layouts.app')

@section('title', 'Edit Expense Request')

@section('content')

    <div class="container">

        <div class="card shadow-sm border-0 rounded-4">

            <div class="card-header bg-white border-0 py-3 px-4">

                <div class="d-flex justify-content-between align-items-center">

                    <div>
                        <h4 class="mb-0 fw-bold">
                            Edit Reimbursement Request
                        </h4>
                        <small class="text-muted">
                            Update your reimbursement request details
                        </small>
                    </div>

                    <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary rounded-pill px-3">
                        <i class="fa fa-arrow-left"></i>
                    </a>

                </div>

            </div>

            <div class="card-body px-4 py-2">

                <form action="{{ route('expenses.update', $expense->id) }}" method="POST" enctype="multipart/form-data">

                    @csrf
                    @method('PUT')

                    @php
                        $attachments = $expense->items->flatMap->attachments;
                    @endphp

                    @include('expenses.form')

                </form>

            </div>

        </div>

    </div>

@endsection

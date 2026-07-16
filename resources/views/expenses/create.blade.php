@extends('layouts.app')

@section('title', 'Create Expense')

@section('content')

    <div class="container">

        <div class="card">

            <div class="card-header d-flex justify-content-between align-items-center">

                <h4 class="mb-0">
                    Reimbursement Request
                </h4>

            </div>

            <div class="card-body">

                <form action="{{ route('expenses.store') }}" method="POST" enctype="multipart/form-data">

                    @csrf

                    @php

                        $expense = new \stdClass();

                        $expense->items = collect();

                        $attachments = collect();

                    @endphp

                    @include('expenses.form')

                </form>

            </div>

        </div>

    </div>

@endsection
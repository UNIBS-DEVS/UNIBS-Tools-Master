@extends('layouts.app')

@section('title', 'Create Work From Home Request')

@section('content')

    <div class="container mt-4">

        <div class="card shadow-sm border-0">

            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">

                <h5 class="mb-0 fw-semibold text-primary">
                    Create Work From Home Request
                </h5>

            </div>

            <div class="card-body">

                <form action="{{ route('wfh.store') }}" method="POST">

                    @csrf

                    @php
                        $wfh = new \stdClass();
                        $wfh->id = null;
                        $wfh->date = '';
                        $wfh->type = '';
                        $wfh->reason = '';
                    @endphp

                    @include('wfh.form')

                </form>

            </div>

        </div>

    </div>

@endsection

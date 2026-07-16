@extends('layouts.app')

@section('title', 'Create Comp Off Request')

@section('content')

    <div class="container">

        <div class="card">

            <div class="card-header d-flex justify-content-between align-items-center">

                <h4 class="mb-0">
                    Comp Off Request
                </h4>

            </div>

            <div class="card-body">

                <form action="{{ route('compoff.store') }}" method="POST">

                    @csrf

                    @php

                        $compOff = new \stdClass();

                        $compOff->id = null;
                        $compOff->day_worked = '';
                        $compOff->reason = '';

                    @endphp

                    @include('compoff.form')

                </form>

            </div>

        </div>

    </div>

@endsection
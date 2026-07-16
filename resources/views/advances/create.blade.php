@extends('layouts.app')

@section('title', 'Create Advance Request')

@section('content')

    <div class="container">

        <div class="card">

            <div class="card-header d-flex justify-content-between align-items-center">

                <h4 class="mb-0">
                    Advance Request
                </h4>

            </div>

            <div class="card-body">

                <form action="{{ route('advances.store') }}" method="POST">

                    @csrf

                    @php

                        $advance = new \stdClass();

                        $advance->items = collect();

                    @endphp

                    @include('advances.form')

                </form>

            </div>

        </div>


    </div>

@endsection
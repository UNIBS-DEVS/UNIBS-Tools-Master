@extends('layouts.app')

@section('content')
    <div class="container">

        <div class="card">

            <div class="card-header">
                <h4>Add Application</h4>
            </div>

            <div class="card-body">

                <form action="{{ route('applications.store') }}" method="POST">

                    @csrf

                    @include('applications.form')

                </form>

            </div>

        </div>

    </div>
@endsection

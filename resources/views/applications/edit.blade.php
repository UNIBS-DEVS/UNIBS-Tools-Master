@extends('layouts.app')

@section('content')
    <div class="container">

        <div class="card">

            <div class="card-header">
                <h4>Edit Application</h4>
            </div>

            <div class="card-body">

                <form action="{{ route('applications.update', $application) }}" method="POST">

                    @csrf
                    @method('PUT')

                    @include('applications.form')

                </form>

            </div>

        </div>

    </div>
@endsection

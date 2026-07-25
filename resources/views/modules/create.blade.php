@extends('layouts.app')

@section('title', 'Create Module')

@section('content')

    <div class="container-fluid">

        <div class="card shadow">

            <div class="card-header d-flex justify-content-between align-items-center">

                <h4 class="mb-0">
                    <i class="fa fa-plus-circle"></i>
                    Create Module
                    @if (isset($application))
                        - {{ $application->appName }}
                    @endif
                </h4>

                {{-- <a href="{{ route('modules.index', ['app_id' => $application->id]) }}" class="btn btn-secondary">
                    <i class="fa fa-arrow-left"></i> Back
                </a> --}}

            </div>

            <div class="card-body">

                @if ($errors->any())

                    <div class="alert alert-danger">

                        <ul class="mb-0">

                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach

                        </ul>

                    </div>

                @endif

                <form action="{{ route('modules.store') }}" method="POST">
                    @csrf

                    @include('modules.form')

                </form>

            </div>

        </div>

    </div>

@endsection

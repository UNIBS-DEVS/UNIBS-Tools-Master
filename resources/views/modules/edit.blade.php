@extends('layouts.app')

@section('title', 'Edit Module')

@section('content')

    <div class="container-fluid">

        <div class="card shadow">
            <div class="card-header d-flex justify-content-between align-items-center">

                <h4 class="mb-0">
                    <i class="fa fa-edit"></i>
                    Edit Module
                    @if ($module->application)
                        - {{ $module->application->appName }}
                    @endif
                </h4>

                {{-- <a href="{{ route('modules.index', ['app_id' => $module->app_id]) }}" class="btn btn-secondary">
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

                <form action="{{ route('modules.update', $module) }}" method="POST">

                    @csrf
                    @method('PUT')

                    @include('modules.form')

                </form>

            </div>

        </div>

    </div>

@endsection

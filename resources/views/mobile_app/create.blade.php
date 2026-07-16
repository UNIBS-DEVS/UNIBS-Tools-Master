@extends('layouts.app')

@section('title', 'Create Leave Type')

@section('content')

    <div class="container">

        <div class="card">

            <div class="card-header">
                <h4>Create Mobile Apps</h4>
            </div>

            <div class="card-body">

                <form action="{{ route('upload-mobile-app.store') }}" method="POST" enctype="multipart/form-data"
                    id="mobileAppForm">

                    @csrf

                    @include('mobile_app.form')

                    <button type="submit" class="btn btn-primary me-2" id="saveBtn">Save</button>

                    <a href="{{ route('upload-mobile-app.index') }}" class="btn btn-outline-secondary bg-light">
                        <i class="fa fa-arrow-left"></i>
                    </a>

                </form>

            </div>

        </div>

    </div>

@endsection

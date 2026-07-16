@extends('layouts.app')

@section('title', 'Edit Leave Type')

@section('content')

    <div class="container">

        <div class="card">

            <div class="card-header">
                <h4>Edit Mobile Apps</h4>
            </div>

            <div class="card-body">
                <form action="{{ route('upload-mobile-app.update', $uploadMobileApp->id) }}" method="POST"
                    enctype="multipart/form-data" id="mobileAppForm">

                    @csrf
                    @method('PUT')

                    @include('mobile_app.form')

                    <button type="submit" class="btn btn-primary me-2" id="saveBtn">Update</button>

                    <a href="{{ route('upload-mobile-app.index') }}" class="btn btn-outline-secondary bg-light">
                        <i class="fa fa-arrow-left"></i>
                    </a>

                </form>

            </div>

        </div>

    </div>

@endsection

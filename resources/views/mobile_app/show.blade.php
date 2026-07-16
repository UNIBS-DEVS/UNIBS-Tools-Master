@extends('layouts.app')

@section('title', 'View Mobile App')

@section('content')

<div class="container">

    <div class="card shadow-sm border-0">

        <div class="card-header bg-white">
            <h4>Mobile App Details</h4>
        </div>

        <div class="card-body">

            <table class="table table-bordered">

                <tr>
                    <th>Application</th>
                    <td>{{ $uploadMobileApp->application }}</td>
                </tr>

                <tr>
                    <th>Version Name</th>
                    <td>{{ $uploadMobileApp->version_name }}</td>
                </tr>

                <tr>
                    <th>Version Code</th>
                    <td>{{ $uploadMobileApp->version_code }}</td>
                </tr>

                <tr>
                    <th>Force Update</th>
                    <td>{{ $uploadMobileApp->force_update ? 'Yes' : 'No' }}</td>
                </tr>

                <tr>
                    <th>Update Message</th>
                    <td>{{ $uploadMobileApp->update_message }}</td>
                </tr>

                <tr>
                    <th>APK URL</th>
                    <td>{{ $uploadMobileApp->apk_url }}</td>
                </tr>

            </table>

            <a href="{{ route('upload-mobile-app.index') }}"
               class="btn btn-secondary">

                <i class="fa fa-arrow-left"></i> Back

            </a>

        </div>

    </div>

</div>

@endsection
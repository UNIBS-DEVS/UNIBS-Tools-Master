@extends('layouts.app')

@section('title', 'Mobile App List')

@section('content')

    <div class="container">

        <div class="card shadow-sm border-0">

            {{-- HEADER --}}
            <div class="d-flex justify-content-between align-items-center p-3">
                <div class="lh-sm">
                    <h4 class="page-title mb-0">Mobile App List</h4>
                </div>

                <div class="d-flex align-items-center gap-1">

                    <a href="{{ route('upload-mobile-app.create') }}" class="btn btn-primary top-btn">
                        <i class="fa fa-plus"></i>
                    </a>

                    <a href="{{ route('upload-mobile-app.index') }}" class="btn btn-secondary top-btn">
                        <i class="fa fa-arrow-left"></i>
                    </a>

                </div>
            </div>

            <div class="card-body">

                <table class="table table-bordered table-striped">

                    <thead>

                        <tr>
                            <th>Application</th>
                            <th>Version Name</th>
                            <th>Version Code</th>
                            <th>Force Update</th>
                            <th>Action</th>
                        </tr>

                    </thead>

                    <tbody>

                        @foreach ($uploadMobileApps as $app)
                            <tr>
                                <td>{{ $app->application }}</td>
                                <td>{{ $app->version_name }}</td>
                                <td>{{ $app->version_code }}</td>

                                <td>
                                    {{ $app->force_update ? 'Yes' : 'No' }}
                                </td>

                                <td>

                                    <a href="{{ route('upload-mobile-app.show', $app->id) }}" class="btn btn-info btn-sm">

                                        <i class="fa fa-eye"></i>

                                    </a>

                                    <a href="{{ route('upload-mobile-app.edit', $app->id) }}" class="btn btn-warning btn-sm">

                                        <i class="fa fa-edit"></i>

                                    </a>

                                    <form action="{{ route('upload-mobile-app.destroy', $app->id) }}" method="POST"
                                        style="display:inline">

                                        @csrf
                                        @method('DELETE')

                                        <button class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">

                                            <i class="fa fa-trash"></i>

                                        </button>

                                    </form>

                                </td>

                            </tr>
                        @endforeach

                    </tbody>

                </table>

            </div>

        </div>

    </div>

@endsection

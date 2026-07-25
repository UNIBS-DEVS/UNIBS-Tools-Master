@extends('layouts.app')

@section('title', 'Add Client | Unibs Tools')

@section('content')
    <div class="container mt-4">
        <div class="card shadow-sm border-0">
            <div class="card-header">
                <h5>Add Client</h5>
            </div>

            <div class="card-body">
                <form action="{{ route('lms.clients.store') }}" method="POST">
                    @csrf

                    @include('lms.clients.form', [
                        'client' => null,
                        'buttonText' => 'Save Client',
                    ])
                </form>
            </div>
        </div>
    </div>
@endsection

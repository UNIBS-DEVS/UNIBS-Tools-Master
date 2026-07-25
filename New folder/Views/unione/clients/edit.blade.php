@extends('layouts.app')

@section('title', 'Edit Client | Unibs Tools')

@section('content')
    <div class="container mt-4">
        <div class="card shadow-sm border-0">
            <div class="card-header">
                <h5>Edit Client</h5>
            </div>

            <div class="card-body">
                <form action="{{ route('unione.clients.update', $client->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    @include('unione.clients.form', [
                        'client' => $client,
                        'buttonText' => 'Update Client',
                    ])
                </form>
            </div>
        </div>
    </div>
@endsection

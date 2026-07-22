@extends('layouts.app')

@section('title', 'View Client | Unibs Tools')

@section('content')

    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8 col-sm-12">

            {{-- Header --}}
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="mb-0">Client Details</h4>
                <div>
                    <a href="{{ route('unione.clients.edit', $client->id) }}" class="btn btn-warning btn-sm">
                        <i class="fa fa-edit"></i>
                    </a>

                    <a href="{{ route('unione.clients.index') }}" class="btn btn-secondary btn-sm ms-1">
                        <i class="fa fa-arrow-left"></i>
                    </a>
                </div>
            </div>

            {{-- Card --}}
            <div class="card shadow-sm">
                <div class="card-body">

                    <div class="row mb-2">
                        <div class="col-4 fw-bold">Client Code</div>
                        <div class="col-8">{{ $client->client_code }}</div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-4 fw-bold">Client Name</div>
                        <div class="col-8">{{ $client->client_name }}</div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-4 fw-bold">Email</div>
                        <div class="col-8">{{ $client->client_spoc_email ?? '-' }}</div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-4 fw-bold">Mobile</div>
                        <div class="col-8">{{ $client->client_spoc_mobile ?? '-' }}</div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-4 fw-bold">Status</div>
                        <div class="col-8">
                            <span class="badge {{ $client->status === 'inactive' ? 'bg-danger' : 'bg-success' }}">
                                {{ ucfirst($client->status) }}
                            </span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-4 fw-bold">Created</div>
                        <div class="col-8">
                            {{ $client->created_at->format('d M Y, h:i A') }}
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>

@endsection

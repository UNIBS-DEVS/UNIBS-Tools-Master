@extends('layouts.app')

@section('title', 'Client Master | Unibs Tools')

@section('content')

    {{-- Flash Messages --}}
    @include('partials.message')

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Client Master</h3>

        <a href="{{ route('lms.clients.create') }}" class="btn btn-primary">
            <i class="fa fa-plus"></i>
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle bg-white">
            <thead class="table-dark">
                <tr>
                    <th>Client Code</th>
                    <th>Name</th>
                    <th>SPOC Name</th>
                    <th>SPOC Email</th>
                    <th>SPOC Mobile</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th width="170">Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($clients as $client)
                    <tr>

                        <td>{{ $client->client_code }}</td>

                        <td>{{ $client->client_name }}</td>

                        <td>{{ $client->client_spoc_name ?? '-' }}</td>

                        <td>{{ $client->client_spoc_email ?? '-' }}</td>

                        <td>{{ $client->client_spoc_mobile ?? '-' }}</td>

                        <td>
                            <span class="badge {{ $client->status === 'inactive' ? 'bg-danger' : 'bg-success' }}">
                                {{ ucfirst($client->status ?? 'active') }}
                            </span>
                        </td>

                        <td>{{ $client->created_at->format('Y-m-d H:i') }}</td>

                        <td>

                            {{-- View --}}
                            <a href="{{ route('lms.clients.show', $client->id) }}" class="btn btn-outline-info btn-sm">
                                <i class="fa fa-eye"></i>
                            </a>

                            {{-- Edit --}}
                            <a href="{{ route('lms.clients.edit', $client->id) }}" class="btn btn-outline-warning btn-sm">
                                <i class="fa-solid fa-pen"></i>
                            </a>

                            {{-- SMTP Config --}}
                            <a href="{{ route('lms.clientsSysConfigs.create', $client->id) }}"
                                class="btn btn-outline-secondary btn-sm" title="Configuration">
                                <i class="fa-solid fa-gear"></i>
                            </a>

                            {{-- Delete --}}
                            <form action="{{ route('lms.clients.destroy', $client->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')

                                <button class="btn btn-outline-danger btn-sm"
                                    onclick="return confirm('Delete this client?')">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </form>

                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center">
                            No clients found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div>
        {{ $clients->links() }}
    </div>

@endsection

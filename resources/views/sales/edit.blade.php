@extends('layouts.app')

@section('title', 'Edit Sale')

@section('content')

    <div class="container">

        <div class="card shadow-sm border-0 rounded-4">

            <div class="card-header bg-white border-0 py-2 px-4">

                <div class="d-flex justify-content-between align-items-center">

                    <div>
                        <h4 class="mb-0 fw-bold">
                            Edit Lead
                        </h4>

                        <small class="text-muted">
                            Update sales lead information
                        </small>
                    </div>

                    <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary rounded-pill px-3">

                        <i class="fa fa-arrow-left"></i>

                    </a>

                </div>

            </div>

            <div class="card-body px-4 py-2">

                <form action="{{ route('sales.update', $sale->id) }}" method="POST">

                    @csrf
                    @method('PUT')

                    <input type="hidden" name="page" value="{{ request('page') }}">

                    @include('sales.form')

                    <div class="text-end pt-3">

                        <button type="submit" class="btn btn-warning rounded-pill px-4 shadow-sm">

                            <i class="fa fa-save me-1"></i>
                            Update

                        </button>


                        <a href="{{ route('sales.index', [
                            'page' => request('page'),
                        ]) }}"
                            class="btn btn-outline-secondary">
                            <i class="fa fa-arrow-left"></i>
                        </a>

                    </div>

                </form>

            </div>

        </div>

    </div>

@endsection

@push('scripts')
    @if (session('success'))
        <script>
            $(document).ready(function() {

                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: "{{ session('success') }}",
                    showConfirmButton: false,
                    timer: 2500,
                    timerProgressBar: true
                });

            });
        </script>
    @endif
@endpush


{{-- @push('scripts')
    @if (session('success'))
        <script>
            $(function() {

                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: @json(session('success')),
                    showConfirmButton: false,
                    timer: 2500,
                    timerProgressBar: true,
                    didOpen: (toast) => {

                        toast.addEventListener('mouseenter', Swal.stopTimer);
                        toast.addEventListener('mouseleave', Swal.resumeTimer);

                    }
                });

            });
        </script>
    @endif
@endpush --}}

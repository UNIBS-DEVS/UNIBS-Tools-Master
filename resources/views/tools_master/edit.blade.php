@extends('layouts.app')

@section('title', 'Edit Tool')

@section('content')

    <div class="container mt-4">
        <div class="card shadow-sm border-0">

            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0 fw-semibold">
                    <i class="fa fa-pen me-2 text-warning"></i>
                    Edit Tool
                </h5>
            </div>

            <div class="card-body">
                <form action="{{ route('tools-master.update', $tool->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    @include('tools_master.form')

                    <div class="d-flex justify-content-end mt-4 gap-2">
                        <a href="" class="btn btn-secondary">
                            <i class="fa fa-arrow-left"></i>
                        </a>
                        <button class="btn btn-warning">
                            <i class="fa fa-save me-1"></i> Update
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const btn = document.getElementById('authDropdownBtn');
            const radios = document.querySelectorAll('.auth-radio');

            function updateText() {
                const selected = document.querySelector('.auth-radio:checked');

                if (selected) {
                    btn.textContent = selected.value.charAt(0).toUpperCase() + selected.value.slice(1);
                } else {
                    btn.textContent = 'Select Authentication Type';
                }
            }

            radios.forEach(r => r.addEventListener('change', updateText));

            updateText();
        });
    </script>
@endpush

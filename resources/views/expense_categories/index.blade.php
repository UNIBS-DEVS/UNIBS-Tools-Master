@extends('layouts.app')

@section('title', 'Expense Categories')

@push('styles')

    <style>
        .table tbody tr td,
        .table thead tr th {
            padding: .15rem .5rem;
            font-size: 13px;
        }
    </style>

@endpush

@section('content')

    {{-- Flash Messages --}}
    @include('partials.message')

    <div class="d-flex justify-content-between align-items-center mb-2">
        <h5>Expense Categories</h5>

        <a href="{{ route('expense-categories.create') }}" class="btn btn-primary btn-sm">
            <i class="fa fa-plus"></i>
        </a>
    </div>

    <div class="table-responsive">
        <table id="categoryTable" class="table table-bordered table-hover align-middle bg-white">

            <thead class="table-dark">

                <tr>
                    <th>S.No</th>
                    <th>Category Name</th>
                    <th width="130" class="text-center">Actions</th>
                </tr>

                {{-- <tr class="table-light">
                    <th>
                        <input type="text" class="form-control form-control-sm category-filter" data-col="0">
                    </th>

                    <th>
                        <input type="text" class="form-control form-control-sm category-filter" data-col="1">
                    </th>

                    <th></th>
                </tr> --}}

            </thead>

            <tbody>

                @forelse($categories as $category)

                    <tr>
                        <td>{{ $loop->iteration }}</td>

                        <td>{{ $category->category_name }}</td>

                        <td class="text-center">

                            <a href="{{ route('expense-categories.edit', $category->id) }}"
                                class="btn btn-outline-warning btn-sm">
                                <i class="fa-solid fa-pen"></i>
                            </a>

                            <form action="{{ route('expense-categories.destroy', $category->id) }}" method="POST"
                                class="d-inline">

                                @csrf
                                @method('DELETE')

                                <button type="submit" class="btn btn-outline-danger btn-sm"
                                    onclick="return confirm('Delete this category?')">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>

                            </form>

                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="3" class="text-center">
                            No Categories Found
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>
    </div>

@endsection

{{-- @push('scripts')

<script>

    $(document).ready(function () {

        $('.category-filter').on('keyup change', function () {

            let filters = [];

            $('.category-filter').each(function () {
                filters.push($(this).val().toLowerCase().trim());
            });

            $('#categoryTable tbody tr').each(function () {

                let show = true;

                $(this).find('td').each(function (index) {

                    if (index >= 2) {
                        return false;
                    }

                    let filter = filters[index];

                    if (filter !== '') {

                        let text = $(this).text().toLowerCase().trim();

                        if (text.indexOf(filter) === -1) {
                            show = false;
                            return false;
                        }
                    }

                });

                $(this).toggle(show);

            });

        });

    });

</script>

@endpush --}}
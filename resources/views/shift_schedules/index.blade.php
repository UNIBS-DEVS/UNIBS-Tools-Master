@extends('layouts.app')

@section('title', 'Shift Schedule | Unibs Tools')

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

    @include('partials.message')

    <div class="d-flex justify-content-between align-items-center mb-1">

        <h5>Shift Schedules</h5>

        <a href="{{ route('shift-schedule.create') }}" class="btn btn-primary btn-sm">
            <i class="fa fa-plus"></i>
        </a>

    </div>

    <div class="table-responsive">

        <table id="shiftTable" class="table table-bordered table-hover align-middle bg-white">

            <thead class="table-dark">

                <tr>
                    <th>Shift Schedule</th>
                    <th>Created By</th>
                    <th>Updated By</th>
                    <th>Created At</th>
                    <th>Updated At</th>
                    <th width="130" class="text-center">
                        Actions
                    </th>
                </tr>

                <tr class="table-light filter-row">

                    <th>
                        <input type="text" class="form-control form-control-sm shift-filter" data-col="1">
                    </th>

                    <th>
                        <input type="text" class="form-control form-control-sm shift-filter" data-col="2">
                    </th>

                    <th>
                        <input type="text" class="form-control form-control-sm shift-filter" data-col="3">
                    </th>

                    <th>
                        <input type="text" class="form-control form-control-sm shift-filter" data-col="1">
                    </th>

                    <th>
                        <input type="text" class="form-control form-control-sm shift-filter" data-col="2">
                    </th>

                    <th>
                        <input type="text" class="form-control form-control-sm shift-filter" data-col="3">
                    </th>
                </tr>

            </thead>

            <tbody>

                @forelse($shifts as $shift)
                    <tr>



                        <td>{{ $shift->shift_schedule }}</td>

                        <td>{{ $shift->creator->name ?? '-' }}</td>
                        <td>{{ $shift->updater->name ?? '-' }}</td>

                        <td>
                            {{ $shift->created_at ? $shift->created_at->format('d M Y h:i A') : '-' }}
                        </td>

                        <td>
                            {{ $shift->updated_at ? $shift->updated_at->format('d M Y h:i A') : '-' }}
                        </td>
                        <td class="text-center">

                            <a href="{{ route('day-shift-schedule.index', ['shift_schedule_id' => $shift->id]) }}"
                                class="btn btn-outline-primary btn-sm">
                                <i class="fa fa-calendar"></i>
                            </a>

                            <a href="{{ route('shift-schedule.edit', $shift->id) }}" title="Day Shift Schedules"
                                class="btn btn-outline-warning btn-sm">
                                <i class="fa-solid fa-pen"></i>
                            </a>

                            <form action="{{ route('shift-schedule.destroy', $shift->id) }}" method="POST"
                                class="d-inline">

                                @csrf
                                @method('DELETE')

                                <button type="submit" class="btn btn-outline-danger btn-sm"
                                    onclick="return confirm('Delete this record?')">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>

                            </form>

                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="5" class="text-center">
                            No Records Found
                        </td>
                    </tr>
                @endforelse

            </tbody>

        </table>

    </div>

@endsection

@push('scripts')
    <script>
        $(document).ready(function() {

            $('.shift-filter').on('keyup change', function() {

                let filters = [];

                $('.shift-filter').each(function() {
                    filters.push($(this).val().toLowerCase().trim());
                });

                $('#shiftTable tbody tr').each(function() {

                    let show = true;

                    $(this).find('td').each(function(index) {

                        if (index >= 4) {
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
@endpush

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

        <h5>
            {{ $shift ? $shift->shift_schedule . ' - Shift Schedule' : 'Day Shift Schedule' }}
        </h5>

        <div>

            <a href="{{ route('day-shift-schedule.create', ['shift_schedule_id' => $shiftId]) }}"
                class="btn btn-primary btn-sm">
                <i class="fa fa-plus"></i>
            </a>

            <a href="{{ route('shift-schedule.index') }}" class="btn btn-outline-dark btn-sm">
                <i class="fa fa-arrow-left"></i>
            </a>

        </div>

    </div>

    <div class="table-responsive">

        <table id="dayShiftTable" class="table table-bordered table-hover align-middle bg-white">

            <thead class="table-dark">

                <tr>
                    <th>Day</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Grace Minutes</th>
                    <th width="130" class="text-center">
                        Actions
                    </th>
                </tr>

                <tr class="table-light filter-row">
                    <th><input type="text" class="form-control form-control-sm day-filter"></th>
                    <th><input type="text" class="form-control form-control-sm day-filter"></th>
                    <th><input type="text" class="form-control form-control-sm day-filter"></th>
                    <th><input type="text" class="form-control form-control-sm day-filter"></th>
                    <th></th>
                </tr>

            </thead>
            <tbody>

                @forelse($dayshifts as $dayshift)
                    <tr>

                        <td>{{ $dayshift->day }}</td>

                        <td>
                            {{ \Carbon\Carbon::parse($dayshift->start_time)->format('h:i A') }}
                        </td>

                        <td>
                            {{ \Carbon\Carbon::parse($dayshift->end_time)->format('h:i A') }}
                        </td>

                        <td>{{ $dayshift->grace_minutes }}</td>

                        <td class="text-center">

                            <a href="{{ route('day-shift-schedule.edit', $dayshift->id) }}"
                                class="btn btn-outline-warning btn-sm">
                                <i class="fa-solid fa-pen"></i>
                            </a>

                            <form action="{{ route('day-shift-schedule.destroy', $dayshift->id) }}" method="POST"
                                class="d-inline">

                                @csrf
                                @method('DELETE')

                                <button class="btn btn-outline-danger btn-sm"
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

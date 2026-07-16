<div class="table-responsive">

    <table id="reportTable" class="table table-bordered table-hover align-middle bg-white">

        <thead class="table-dark">
            <tr>
                <th>Month</th>
                <th>Tender Number</th>
                <th>Primary User</th>
                <th>Secondary User</th>
                <th>Submission Date</th>
                <th>Type</th>
                <th>Status</th>
                <th>Due Date</th>
                <th>Estimated Value</th>
                <th>State</th>
                <th>Department</th>
                <th>Bid Price</th>
                <th>Platform</th>
                <th>Created Date</th>
            </tr>

            <tr class="table-light filter-row">
                @for ($i = 0; $i < 14; $i++)
                    <th>
                        <input type="text" class="form-control form-control-sm column-filter"
                            data-col="{{ $i }}">
                    </th>
                @endfor
            </tr>
        </thead>

        <tbody>

            @forelse($records as $row)
                <tr data-id="{{ $row->id }}">

                    <td>{{ $row->created_at?->format('M Y') }}</td>

                    <td>{{ $row->tender_num }}</td>

                    <td>{{ $row->primaryUser?->name ?? '-' }}</td>

                    <td>{{ $row->secondaryUser?->name ?? '-' }}</td>

                    <td>
                        {{ $row->submission_date?->format('j M, Y') ?? '-' }}
                    </td>

                    <td>{{ $row->type ?? '-' }}</td>

                    <td>

                        @php
                            $statusClass = match ($row->status) {
                                'Pending' => 'bg-warning text-dark',
                                'Submitted' => 'bg-primary',
                                'Under Evaluation' => 'bg-info text-dark',
                                'Won' => 'bg-success',
                                'Lost' => 'bg-danger',
                                default => 'bg-secondary',
                            };
                        @endphp

                        <span class="badge {{ $statusClass }}">
                            {{ $row->status }}
                        </span>

                    </td>

                    <td>
                        {{ $row->due_date?->format('j M, Y') ?? '-' }}
                    </td>

                    <td>{{ $row->estimated_value ?? '-' }}</td>

                    <td>{{ $row->state ?? '-' }}</td>

                    <td>{{ $row->department ?? '-' }}</td>

                    <td>{{ $row->bid_price ?? '-' }}</td>

                    <td>{{ $row->platform ?? '-' }}</td>

                    <td>
                        {{ $row->created_at?->format('j M, Y h:i A') }}
                    </td>

                </tr>

            @empty

                <tr>
                    <td colspan="14" class="text-center">
                        No Records Found
                    </td>
                </tr>
            @endforelse

        </tbody>

    </table>

</div>


@push('scripts')
    <script>
        $(document).ready(function() {

            $('.column-filter').on('keyup change', function() {

                let filters = [];

                $('.column-filter').each(function() {
                    filters.push($(this).val().toLowerCase().trim());
                });

                $('#reportTable tbody tr').each(function() {

                    let show = true;

                    $(this).find('td').each(function(index) {

                        $(this).find('td').each(function(index) {

                            let filter = filters[index];

                            if (filter !== '') {

                                let text = $(this).text().toLowerCase().trim();

                                if (text.indexOf(filter) === -1) {
                                    show = false;
                                    return false;
                                }
                            }

                        });

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

        $('#exportForm').on('submit', function() {

            let ids = [];

            $('#reportTable tbody tr:visible').each(function() {

                let id = $(this).data('id');

                if (id) {
                    ids.push(id);
                }

            });

            $('#visible_ids').val(ids.join(','));

        });
    </script>
@endpush

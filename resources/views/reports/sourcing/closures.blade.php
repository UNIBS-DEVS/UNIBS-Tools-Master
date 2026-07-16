<div class="table-responsive">

    <table id="reportTable" class="table table-bordered table-hover align-middle bg-white">

        <thead class="table-dark">

            <tr>
                <th>Month</th>
                <th>Recruiter</th>
                <th>Customer Name</th>
                <th>Job</th>
                <th>Candidate Name</th>
                <th>Mobile</th>
                <th>Email</th>
                <th>Status</th>
                <th>Create Date</th>
            </tr>

            <tr class="table-light filter-row">
                <th><input type="text" class="form-control form-control-sm column-filter" data-col="0"></th>
                <th><input type="text" class="form-control form-control-sm column-filter" data-col="1"></th>
                <th><input type="text" class="form-control form-control-sm column-filter" data-col="2"></th>
                <th><input type="text" class="form-control form-control-sm column-filter" data-col="3"></th>
                <th><input type="text" class="form-control form-control-sm column-filter" data-col="4"></th>
                <th><input type="text" class="form-control form-control-sm column-filter" data-col="5"></th>
                <th><input type="text" class="form-control form-control-sm column-filter" data-col="6"></th>
                <th><input type="text" class="form-control form-control-sm column-filter" data-col="7"></th>
                <th><input type="text" class="form-control form-control-sm column-filter" data-col="8"></th>
            </tr>
        </thead>

        <tbody>

            @forelse($records as $row)
                <tr data-id="{{ $row->id }}">
                    <td>{{ $row->created_at->format('M Y') }}</td>
                    <td>{{ $row->creator?->name ?? '-' }}</td>
                    <td>{{ $row->customer?->customer ?? '-' }}</td>
                    <td>{{ $row->customerJob?->position ?? '-' }}</td>
                    <td>{{ $row->candidate_name }}</td>
                    <td>{{ $row->mobile ?? '-' }}</td>
                    <td>{{ $row->email ?? '-' }}</td>
                    <td>{{ $row->status ?? '-' }}</td>

                    <td>
                        @if ($type == 'closures')
                            {{ $row->created_at->format('d M Y') }}
                        @else
                            {{ optional($row->created_at)->format('d M Y h:i A') }}
                        @endif
                    </td>
                </tr>

            @empty

                <tr>
                    <td colspan="12" class="text-center">
                        No Records Found
                    </td>
                </tr>
            @endforelse

        </tbody>

    </table>

</div>

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

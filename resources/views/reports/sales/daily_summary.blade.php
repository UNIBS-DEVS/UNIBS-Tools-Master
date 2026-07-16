<div class="table-responsive">

    <table id="reportTable" class="table table-bordered table-hover align-middle bg-white">

        <thead class="table-dark">

            <tr>
                <th>Sales Associate</th>
                <th>Client Contact</th>
                <th>Company</th>
                <th>Email</th>
                <th>Mobile</th>

                <th>Requirement</th>
                <th>Type</th>
                <th>Source</th>
                <th>Status</th>
                <th>Follow Up Date</th>
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
                <th><input type="text" class="form-control form-control-sm column-filter" data-col="9"></th>
            </tr>
        </thead>

        <tbody>

            @forelse($records as $row)
                <tr data-id="{{ $row->id }}">
                    <td>{{ $row->creator?->name ?? '-' }}</td>
                    <td>{{ $row->client_contact ?? '-' }}</td>
                    <td>{{ $row->company ?? '-' }}</td>
                    <td>{{ $row->email ?? '-' }}</td>
                    <td>{{ $row->mobile ?? '-' }}</td>

                    <td>{{ $row->requirement ?? '-' }}</td>
                    <td>{{ $row->type ?? '-' }}</td>
                    <td>{{ $row->source ?? '-' }}</td>
                    <td>{{ $row->status ?? '-' }}</td>
                    <td>
                        {{ $row->follow_up_date ? \Carbon\Carbon::parse($row->follow_up_date)->format('j F, Y') : '-' }}
                    </td>
                </tr>

            @empty

                <tr>
                    <td colspan="10" class="text-center">
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

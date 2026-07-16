<div class="table-responsive">

    <table id="reportTable" class="table table-bordered table-hover align-middle bg-white">

        <thead class="table-dark">
            <tr>
                <th>Source</th>
                <th>Type</th>
                <th>Count</th>
            </tr>
        </thead>

        <tbody>

            @forelse($records as $row)
                <tr data-id="{{ $row->id }}">
                    <td>{{ $row->source }}</td>
                    <td>{{ $row->type }}</td>
                    <td>
                        <span class="badge bg-success">
                            {{ $row->total }}
                        </span>
                    </td>
                </tr>

            @empty

                <tr>
                    <td colspan="3" class="text-center">
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

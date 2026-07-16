<div class="table-responsive">

    <table id="reportTable" class="table table-bordered table-hover align-middle bg-white">

        <thead class="table-dark">
            <tr>
                <th>Customer Name</th>
                <th>Job</th>
                <th>Skill</th>
                <th>Joined</th>
                <th>Under Discussion</th>
                <th>Shared With Customer</th>
                <th>Under Interview</th>
            </tr>

            <tr class="table-light">
                <th><input type="text" class="form-control form-control-sm column-filter" data-col="0"></th>
                <th><input type="text" class="form-control form-control-sm column-filter" data-col="1"></th>
                <th><input type="text" class="form-control form-control-sm column-filter" data-col="2"></th>
                <th><input type="text" class="form-control form-control-sm column-filter" data-col="3"></th>
                <th><input type="text" class="form-control form-control-sm column-filter" data-col="4"></th>
                <th><input type="text" class="form-control form-control-sm column-filter" data-col="5"></th>
                <th><input type="text" class="form-control form-control-sm column-filter" data-col="6"></th>
            </tr>
        </thead>

        <tbody>

            @forelse($records as $customer)
                @foreach ($customer->jobs as $job)
                    <tr data-id="{{ $customer->id }}">
                        <td>{{ $customer->customer }}</td>
                        <td>{{ $job->position }}</td>
                        <td>{{ $job->skill }}</td>

                        <td>
                            <span class="badge bg-success">
                                {{ $job->joined_count }}
                            </span>
                        </td>

                        <td>
                            <span class="badge bg-warning text-dark">
                                {{ $job->under_discussion_count }}
                            </span>
                        </td>

                        <td>
                            <span class="badge bg-info text-dark">
                                {{ $job->shared_count }}
                            </span>
                        </td>

                        <td>
                            <span class="badge bg-info text-dark">
                                {{ $job->under_interview_count }}
                            </span>
                        </td>
                    </tr>
                @endforeach

            @empty

                <tr>
                    <td colspan="6" class="text-center">
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

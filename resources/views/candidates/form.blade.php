<div class="container">

    <div class="row gx-4 gy-2">

        {{-- Customer --}}
        <div class="col-md-4">
            <label class="form-label fw-semibold">
                Customer <span class="text-danger">*</span>
            </label>

            <select name="customer_id" id="customer_id"
                class="form-select shadow-sm @error('customer_id') is-invalid @enderror">
                <option value="">-- Select Customer --</option>

                @foreach ($customers as $customer)
                    <option value="{{ $customer->id }}"
                        {{ old('customer_id', $candidate->customer_id ?? '') == $customer->id ? 'selected' : '' }}>
                        {{ $customer->customer }}
                    </option>
                @endforeach
            </select>

            @error('customer_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Job Position --}}
        <div class="col-md-4">
            <label class="form-label fw-semibold">
                Job Position <span class="text-danger">*</span>
            </label>

            <select name="customer_job_id" id="customer_job_id"
                class="form-select shadow-sm @error('customer_job_id') is-invalid @enderror">
                <option value="">-- Select Job Position --</option>
            </select>
            @error('customer_job_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Candidate Name --}}
        <div class="col-md-4">
            <label class="form-label fw-semibold">
                Candidate Name <span class="text-danger">*</span>
            </label>

            <div class="input-group shadow-sm">
                <span class="input-group-text">
                    <i class="fa fa-user"></i>
                </span>

                <input type="text" name="candidate_name"
                    class="form-control @error('candidate_name') is-invalid @enderror"
                    value="{{ old('candidate_name', $candidate->candidate_name ?? '') }}"
                    placeholder="Enter candidate name">
            </div>

            @error('candidate_name')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        {{-- Mobile --}}
        <div class="col-md-4">
            <label class="form-label fw-semibold">
                Mobile <span class="text-danger">*</span>
            </label>

            <div class="input-group shadow-sm">
                <span class="input-group-text">
                    <i class="fa fa-phone"></i>
                </span>

                <input type="text" name="mobile" class="form-control @error('mobile') is-invalid @enderror"
                    value="{{ old('mobile', $candidate->mobile ?? '') }}" placeholder="+91 9876543210">
            </div>

            @error('mobile')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        {{-- Email --}}
        <div class="col-md-4">
            <label class="form-label fw-semibold">
                Email Address
            </label>

            <div class="input-group shadow-sm">
                <span class="input-group-text">
                    <i class="fa fa-envelope"></i>
                </span>

                <input type="email" name="email" class="form-control"
                    value="{{ old('email', $candidate->email ?? '') }}" placeholder="john@example.com">
            </div>
        </div>

        {{-- Gender --}}
        <div class="col-md-4">
            <label class="form-label fw-semibold">
                Gender
            </label>

            <select name="gender" class="form-select shadow-sm  @error('gender') is-invalid @enderror">

                <option value="Male" {{ old('gender', $candidate->gender ?? '') == 'Male' ? 'selected' : '' }}>
                    Male
                </option>

                <option value="Female" {{ old('gender', $candidate->gender ?? '') == 'Female' ? 'selected' : '' }}>
                    Female
                </option>

                <option value="Other" {{ old('gender', $candidate->gender ?? '') == 'Other' ? 'selected' : '' }}>
                    Other
                </option>
            </select>

            @error('gender')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Current Company --}}
        <div class="col-md-4">
            <label class="form-label fw-semibold">
                Current Company
            </label>

            <input type="text" name="current_company" class="form-control shadow-sm"
                value="{{ old('current_company', $candidate->current_company ?? '') }}" placeholder="ABC Technologies">
        </div>

        {{-- Skill --}}
        <div class="col-md-4">
            <label class="form-label fw-semibold">
                Skills
            </label>

            <input type="text" name="skill" class="form-control shadow-sm"
                value="{{ old('skill', $candidate->skill ?? '') }}" placeholder="Laravel, PHP, MySQL">
        </div>

        {{-- Notice Period --}}
        <div class="col-md-2">
            <label class="form-label fw-semibold">
                Notice Period
            </label>

            @php
                $notice = old('notice_period', $candidate->notice_period ?? '');
            @endphp

            <select name="notice_period" class="form-control shadow-sm">

                @foreach (['Immediate', 'Serving Notice', 'Under 15 Days', 'Under 30 Days', 'Under 60 Days', '60 Days and Above'] as $np)
                    <option value="{{ $np }}" {{ $notice == $np ? 'selected' : '' }}>
                        {{ $np }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Last Working Day --}}
        <div class="col-md-2 mb-3">
            <label class="form-label fw-semibold">
                Last Working Day
            </label>

            <input type="date" name="last_working_day" class="form-control"
                value="{{ old('last_working_day', $candidate->last_working_day ?? '') }}">
            @error('last_working_day')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        {{-- Total Experience --}}
        <div class="col-md-4">
            <label class="form-label fw-semibold">
                Total Experience
            </label>

            <div class="row g-2">
                {{-- Years --}}
                <div class="col-6">
                    <select name="experience_years" class="form-select  shadow-sm">
                        @for ($i = 0; $i <= 35; $i++)
                            <option value="{{ $i }}"
                                {{ old('experience_years', $candidate->experience_years ?? 0) == $i ? 'selected' : '' }}>
                                {{ $i }} Year{{ $i != 1 ? 's' : '' }}
                            </option>
                        @endfor
                    </select>
                </div>

                {{-- Months --}}
                <div class="col-6">
                    <select name="experience_months" class="form-select shadow-sm">
                        @for ($i = 0; $i <= 11; $i++)
                            <option value="{{ $i }}"
                                {{ old('experience_months', $candidate->experience_months ?? 0) == $i ? 'selected' : '' }}>
                                {{ $i }} Month{{ $i != 1 ? 's' : '' }}
                            </option>
                        @endfor
                    </select>
                </div>
            </div>

            @error('experience_years')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        {{-- Relevant Experience --}}
        <div class="col-md-4">
            <label class="form-label fw-semibold">
                Relevant Experience
            </label>

            <div class="row g-2">
                {{-- Years --}}
                <div class="col-6">
                    <select name="relevant_experience_years" class="form-select shadow-sm">
                        @for ($i = 0; $i <= 35; $i++)
                            <option value="{{ $i }}"
                                {{ old('relevant_experience_years', $candidate->relevant_experience_years ?? 0) == $i ? 'selected' : '' }}>
                                {{ $i }} Year{{ $i != 1 ? 's' : '' }}
                            </option>
                        @endfor
                    </select>
                </div>

                {{-- Months --}}
                <div class="col-6">
                    <select name="relevant_experience_months" class="form-select shadow-sm">
                        @for ($i = 0; $i <= 11; $i++)
                            <option value="{{ $i }}"
                                {{ old('relevant_experience_months', $candidate->relevant_experience_months ?? 0) == $i ? 'selected' : '' }}>
                                {{ $i }} Month{{ $i != 1 ? 's' : '' }}
                            </option>
                        @endfor
                    </select>
                </div>
            </div>

            @error('relevant_experience_years')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        {{-- Resume --}}
        <div class="col-md-4">
            <label class="form-label fw-semibold">
                Resume URL / Path
            </label>

            <div class="input-group shadow-sm">
                <span class="input-group-text">
                    <i class="fa fa-file"></i>
                </span>

                <input type="text" name="resume_path" class="form-control"
                    value="{{ old('resume_path', $candidate->resume_path ?? '') }}"
                    placeholder="Paste resume URL or path">

                @error('resume_path')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
        </div>

        {{-- Candidate Status --}}
        <div class="col-md-2">
            <label class="form-label fw-semibold">
                Candidate Status
            </label>

            <select name="status" class="form-select shadow-sm">

                @foreach (['Mapped', 'Under Discussion', 'Shared with Customer', 'Under Interview', 'Offered', 'Joined', 'Back Out', 'Closed', 'Rejected'] as $status)
                    <option value="{{ $status }}"
                        {{ old('status', $candidate->status ?? '') == $status ? 'selected' : '' }}>
                        {{ $status }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Education --}}
        <div class="col-md-2">
            <label class="form-label fw-semibold">
                Education
            </label>

            <div class="input-group shadow-sm">
                <span class="input-group-text">
                    <i class="fa fa-graduation-cap"></i>
                </span>

                <input type="text" name="education" class="form-control"
                    value="{{ old('education', $candidate->education ?? '') }}" placeholder="Enter education name">
            </div>

            @error('candidate_name')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        {{-- Current Location --}}
        <div class="col-md-4">
            <label class="form-label fw-semibold">Current Location</label>

            <input type="text" name="current_location" class="form-control"
                value="{{ old('current_location', $candidate->current_location ?? '') }}"
                placeholder="E.g. Bangalore">

            @error('current_location')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        {{-- Preferred Location --}}
        <div class="col-md-4">
            <label class="form-label fw-semibold">
                Preferred Location
            </label>

            <input type="text" name="preferred_location" class="form-control"
                value="{{ old('preferred_location', $candidate->preferred_location ?? '') }}"
                placeholder="E.g. Bangalore, Hyderabad">

            @error('preferred_location')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        {{-- Current CTC --}}
        <div class="col-md-2">
            <label class="form-label fw-semibold">
                Current Fixed CTC
            </label>

            <div class="input-group shadow-sm">
                <span class="input-group-text">₹</span>

                <input type="text" name="current_fixed_ctc" class="form-control"
                    value="{{ old('current_fixed_ctc', $candidate->current_fixed_ctc ?? '') }}" placeholder="5 LPA">
            </div>
        </div>

        {{-- Current Variable CTC --}}
        <div class="col-md-2 mb-3">
            <label class="form-label fw-semibold">
                Current Variable CTC
            </label>

            <div class="input-group">
                <span class="input-group-text">₹</span>

                <input type="text" name="current_variable_ctc" class="form-control" step="0.01"
                    min="0" placeholder="2 LPA"
                    value="{{ old('current_variable_ctc', $candidate->current_variable_ctc ?? '') }}">
            </div>

            @error('current_variable_ctc')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        {{-- Expected CTC --}}
        <div class="col-md-2">
            <label class="form-label fw-semibold">
                Expected CTC
            </label>

            <div class="input-group shadow-sm">
                <span class="input-group-text">₹</span>

                <input type="text" name="expected_ctc" class="form-control"
                    value="{{ old('expected_ctc', $candidate->expected_ctc ?? '') }}" placeholder="7 LPA">
            </div>
        </div>

        {{-- Interview Date --}}
        <div class="col-md-2">
            <label class="form-label fw-semibold">
                Interview Date
            </label>

            <input type="datetime-local" name="interview_date" class="form-control shadow-sm"
                value="{{ old('interview_date', isset($candidate->interview_date) ? \Carbon\Carbon::parse($candidate->interview_date)->format('Y-m-d\TH:i') : '') }}">
        </div>


        {{-- Interview Level --}}
        <div class="col-md-2 mb-3">
            <label class="form-label fw-semibold">
                Interview Level
            </label>

            <select name="interview_level" class="form-control">
                <option value="">-- Select --</option>

                @foreach (['L1', 'L2', 'Manager', 'C Level', 'HR'] as $level)
                    <option value="{{ $level }}"
                        {{ old('interview_level', $candidate->interview_level ?? '') == $level ? 'selected' : '' }}>
                        {{ $level }}
                    </option>
                @endforeach
            </select>

            @error('interview_level')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <div class="col-md-2">
            <label class="form-label fw-semibold">
                Remark Type
            </label>

            <select name="remark_type" class="form-select shadow-sm">

                @foreach (['Candidate Update', 'Candidate Issue', 'Customer Update', 'Customer Issue', 'Interview Update', 'Offer Update', 'Delay', 'Escalation', 'Internal Note'] as $type)
                    <option value="{{ $type }}" {{ old('remark_type') == $type ? 'selected' : '' }}>
                        {{ $type }}
                    </option>
                @endforeach
            </select>

            @error('remark_type')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        {{-- Remarks --}}
        @if (isset($candidate))
            <div class="col-md-4">
                <label class="form-label fw-semibold">
                    Remarks
                </label>

                <textarea name="remarks" rows="4" class="form-control shadow-sm" placeholder="Write new remark here...">{{ old('remarks') }}</textarea>
            </div>
        @else
            <div class="col-md-12">
                <label class="form-label fw-semibold">
                    Remarks
                </label>

                <textarea name="remarks" rows="4" class="form-control shadow-sm" placeholder="Write new remark here...">{{ old('remarks') }}</textarea>
            </div>
        @endif


        {{-- Remarks History --}}
        @if (isset($candidate))
            <div class="col-md-8">

                <label class="form-label fw-semibold">
                    Remarks History
                </label>

                {{-- Remark History --}}
                <div class="card shadow-sm border-0">

                    <div class="card-body p-0">

                        <div class="table-responsive" style="max-height: 320px; overflow-y: auto;">

                            <table class="table table-sm table-hover align-middle mb-0">

                                <thead class="table-light">
                                    <tr>
                                        <th width="180">Type</th>

                                        <th>
                                            Remark
                                        </th>

                                        <th width="180" class="text-center">
                                            Recruiter
                                        </th>

                                        <th width="180">
                                            Created At
                                        </th>
                                    </tr>
                                </thead>

                                <tbody>

                                    @if ($candidate->remarkHistories->count())
                                        @php
                                            $latestRemark = $candidate->remarkHistories
                                                ->sortByDesc('created_at')
                                                ->first();
                                        @endphp

                                        <tr class="table-warning fw-semibold">
                                            <td>
                                                {{ $latestRemark->remark_type ?? '-' }}
                                            </td>

                                            <td>
                                                {!! nl2br(e($latestRemark->remarks)) !!}
                                                <span class="badge bg-danger ms-2">
                                                    Latest
                                                </span>
                                            </td>

                                            <td>
                                                {{ $latestRemark->creator->name ?? '-' }}
                                            </td>

                                            <td>
                                                {{ $latestRemark->created_at->format('d M Y h:i A') }}
                                            </td>
                                        </tr>
                                    @endif

                                    @forelse($candidate->remarkHistories as $remark)
                                        <tr>
                                            <td>
                                                {{ $remark->remark_type ?? '-' }}
                                            </td>

                                            <td>
                                                {!! nl2br(e($remark->remarks)) !!}
                                            </td>

                                            <td>
                                                {{ $remark->creator->name ?? '-' }}
                                            </td>

                                            <td>
                                                {{ $remark->created_at->format('d M Y h:i A') }}
                                            </td>
                                        </tr>

                                    @empty

                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-3">
                                                No remark history found.
                                            </td>
                                        </tr>
                                    @endforelse

                                </tbody>


                            </table>

                        </div>

                    </div>

                </div>

            </div>


        @endif
    </div>
</div>


@push('scripts')
    <script>
        $(document).ready(function() {

            let positions = @json($positions);

            // Old/Edit values
            let selectedCustomer =
                "{{ old('customer_id', $candidate->customer_id ?? '') }}";

            let selectedPosition =
                "{{ old('customer_job_id', $candidate->customer_job_id ?? '') }}";

            // Set customer selected
            $('#customer_id').val(selectedCustomer);

            function loadPositions(customerId, selectedPosition = '') {

                $('#customer_job_id').html(
                    '<option value="">Loading...</option>'
                );

                setTimeout(function() {

                    let html =
                        '<option value="">-- Select Job Position --</option>';

                    $.each(positions, function(index, position) {

                        if (position.customer_id == customerId) {

                            let selected =
                                position.id == selectedPosition ?
                                'selected' :
                                '';

                            html += `
                            <option value="${position.id}" ${selected}>
                                ${position.position}
                            </option>
                        `;
                        }
                    });

                    if (html ===
                        '<option value="">-- Select Job Position --</option>') {

                        html += `
                        <option value="">
                            No Position Available
                        </option>
                    `;
                    }

                    $('#customer_job_id').html(html);

                }, 300);
            }

            // On customer change
            $('#customer_id').on('change', function() {

                let customerId = $(this).val();

                loadPositions(customerId);
            });

            // Edit mode auto load
            if (selectedCustomer !== '') {

                loadPositions(selectedCustomer, selectedPosition);
            }

        });
    </script>
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
@endpush

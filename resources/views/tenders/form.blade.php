<div class="row gx-4 gy-3">

    {{-- Tender Number --}}
    <div class="col-md-3">

        <label class="form-label fw-semibold">
            Tender Number
        </label>

        <div class="input-group shadow-sm">

            <span class="input-group-text">
                <i class="fa fa-hashtag"></i>
            </span>

            <input type="text" name="tender_num" value="{{ old('tender_num', $tender->tender_num ?? '') }}"
                class="form-control">

        </div>

    </div>

    {{-- Primary User --}}
    <div class="col-md-3">

        <label class="form-label fw-semibold">
            Primary User
        </label>

        <select name="primary_user_id" class="form-select shadow-sm">

            <option value="">
                Select User
            </option>

            @foreach ($tenderUsers as $user)
                <option value="{{ $user->id }}"
                    {{ old('primary_user_id', $tender->primary_user_id ?? '') == $user->id ? 'selected' : '' }}>
                    {{ $user->name }}
                </option>
            @endforeach

        </select>

    </div>

    {{-- Secondary User --}}
    <div class="col-md-3">

        <label class="form-label fw-semibold">
            Secondary User
        </label>

        <select name="secondary_user_id" class="form-select shadow-sm">

            <option value="">
                Select User
            </option>

            @foreach ($tenderUsers as $user)
                <option value="{{ $user->id }}"
                    {{ old('secondary_user_id', $tender->secondary_user_id ?? '') == $user->id ? 'selected' : '' }}>
                    {{ $user->name }}
                </option>
            @endforeach

        </select>

    </div>

    {{-- Submission Date --}}
    <div class="col-md-3">

        <label class="form-label fw-semibold">
            Submission Date
        </label>

        <input type="date" name="submission_date"
            value="{{ old('submission_date', isset($tender->submission_date) ? $tender->submission_date->format('Y-m-d') : '') }}"
            class="form-control shadow-sm">

    </div>

    {{-- Type --}}
    <div class="col-md-3">

        <label class="form-label fw-semibold">
            Type
        </label>

        <select name="type" class="form-select shadow-sm">

            @foreach (['IT Manpower', 'Non-IT Manpower', 'SAP', 'Trainings', 'IT Projects', 'Others'] as $type)
                <option value="{{ $type }}"
                    {{ old('type', $tender->type ?? 'Others') == $type ? 'selected' : '' }}>
                    {{ $type }}
                </option>
            @endforeach

        </select>

    </div>

    {{-- Status --}}
    <div class="col-md-3">

        <label class="form-label fw-semibold">
            Status
        </label>

        <select name="status" class="form-select shadow-sm">

            @foreach (['Submitted', 'Under Evaluation', 'Won', 'Lost', 'Pending'] as $status)
                <option value="{{ $status }}"
                    {{ old('status', $tender->status ?? 'Pending') == $status ? 'selected' : '' }}>
                    {{ $status }}
                </option>
            @endforeach

        </select>

    </div>

    {{-- Due Date --}}
    <div class="col-md-3">

        <label class="form-label fw-semibold">
            Due Date
        </label>

        <input type="date" name="due_date"
            value="{{ old('due_date', isset($tender->due_date) ? $tender->due_date->format('Y-m-d') : '') }}"
            class="form-control shadow-sm">

    </div>

    {{-- Estimated Value --}}
    <div class="col-md-3">

        <label class="form-label fw-semibold">
            Estimated Value (INR Lakhs)
        </label>

        <input type="text" name="estimated_value"
            value="{{ old('estimated_value', $tender->estimated_value ?? '') }}" class="form-control shadow-sm">

    </div>

    {{-- State --}}
    <div class="col-md-3">

        <label class="form-label fw-semibold">
            State
        </label>

        <select name="state" class="form-select shadow-sm">

            @foreach ($states as $state)
                <option value="{{ $state }}"
                    {{ old('state', $tender->state ?? '') == $state ? 'selected' : '' }}>
                    {{ $state }}
                </option>
            @endforeach

        </select>

    </div>

    {{-- Department --}}
    <div class="col-md-3">

        <label class="form-label fw-semibold">
            Department
        </label>

        <input type="text" name="department" value="{{ old('department', $tender->department ?? '') }}"
            class="form-control shadow-sm">

    </div>

    {{-- Bid Price --}}
    <div class="col-md-3">

        <label class="form-label fw-semibold">
            Bid Price
        </label>

        <input type="text" name="bid_price" value="{{ old('bid_price', $tender->bid_price ?? '') }}"
            class="form-control shadow-sm">

    </div>

    {{-- Platform --}}
    <div class="col-md-3">

        <label class="form-label fw-semibold">
            Platform
        </label>

        <select name="platform" class="form-select shadow-sm">

            @foreach (['GeM', 'CPPP', 'IREPS', 'State eProcurement Portals', 'NHAI', 'NTPC', 'ONGC', 'BHEL', 'OTHERS'] as $platform)
                <option value="{{ $platform }}"
                    {{ old('platform', $tender->platform ?? 'GeM') == $platform ? 'selected' : '' }}>
                    {{ $platform }}
                </option>
            @endforeach

        </select>

    </div>

    {{-- Remarks --}}
    @if (isset($tender))
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
    @if (isset($tender))

        <div class="col-md-8">

            <label class="form-label fw-semibold">
                Remarks History
            </label>

            <div class="card shadow-sm border-0">

                <div class="card-body p-0">

                    <div class="table-responsive" style="max-height:320px; overflow-y:auto;">

                        <table class="table table-sm table-hover align-middle mb-0">

                            <thead class="table-light">
                                <tr>

                                    <th>Remark</th>
                                    <th width="180" class="text-center">Tender Person</th>
                                    <th width="170" class="text-center">Created Date</th>
                                </tr>
                            </thead>

                            <tbody>

                                @forelse($tender->remarkHistories as $remark)
                                    <tr>


                                        <td style="text-align: justify; padding:10px;">
                                            {!! nl2br(e($remark->remarks)) !!}
                                        </td>

                                        <td class="text-center">
                                            {{ $remark->creator->name ?? '-' }}
                                        </td>

                                        <td>
                                            {{ $remark->created_at->format('d M Y h:i A') }}
                                        </td>
                                    </tr>

                                @empty

                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-3">
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

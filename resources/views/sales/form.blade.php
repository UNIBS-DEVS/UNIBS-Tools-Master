<div class="row gx-4 gy-3">

    {{-- Client Contact --}}
    <div class="col-md-3">

        <label class="form-label fw-semibold">
            Client Contact Name
        </label>

        <div class="input-group shadow-sm">

            <span class="input-group-text">
                <i class="fa fa-user"></i>
            </span>

            <input type="text" name="client_contact" value="{{ old('client_contact', $sale->client_contact ?? '') }}"
                class="form-control  @error('client_contact') is-invalid @enderror" placeholder="E.x John Doe">

            @error('client_contact')
                <div class="text-danger small mt-1">
                    {{ $message }}
                </div>
            @enderror

        </div>

    </div>

    {{-- Company --}}
    <div class="col-md-3">

        <label class="form-label fw-semibold">
            Company Name
        </label>

        <div class="input-group shadow-sm">

            <span class="input-group-text">
                <i class="fa fa-building"></i>
            </span>

            <input type="text" name="company" value="{{ old('company', $sale->company ?? '') }}" class="form-control"
                placeholder="Enter company name">

        </div>

    </div>

    {{-- Email --}}
    <div class="col-md-3">

        <label class="form-label fw-semibold">
            Email Address
        </label>

        <div class="input-group shadow-sm">

            <span class="input-group-text">
                <i class="fa fa-envelope"></i>
            </span>

            <input type="email" name="email" value="{{ old('email', $sale->email ?? '') }}" class="form-control"
                placeholder="example@gmail.com">

        </div>

    </div>

    {{-- Location --}}
    <div class="col-md-3">

        <label class="form-label fw-semibold">
            Location
        </label>

        <div class="input-group shadow-sm">

            <span class="input-group-text">
                <i class="fa fa-map-marker-alt"></i>
            </span>

            <input type="text" name="location" value="{{ old('location', $sale->location ?? '') }}"
                class="form-control" placeholder="Enter location">

        </div>

    </div>

    {{-- Mobile --}}
    <div class="col-md-3">

        <label class="form-label fw-semibold">
            Mobile Number
        </label>

        <div class="input-group shadow-sm">

            <span class="input-group-text">
                <i class="fa fa-phone"></i>
            </span>

            <input type="text" name="mobile" value="{{ old('mobile', $sale->mobile ?? '') }}" class="form-control"
                placeholder="+91 9876543210">

        </div>

    </div>

    {{-- Type --}}
    <div class="col-md-3">

        <label class="form-label fw-semibold">
            Type
        </label>

        <select name="type" class="form-select shadow-sm  @error('type') is-invalid @enderror">

            <option value="">
                Select Type
            </option>

            @foreach (['Sourcing', 'Training', 'Job Seeker', 'Microsoft', 'Tally', 'Google', 'Zoho', 'Software Services', 'Digital Marketing', 'Razorpay', 'BGC', 'Others'] as $type)
                <option value="{{ $type }}"
                    {{ old('type', $sale->type ?? 'Sourcing') == $type ? 'selected' : '' }}>

                    {{ $type }}

                </option>
            @endforeach

        </select>

        @error('type')
            <span class="text-danger small">
                {{ $message }}
            </span>
        @enderror

    </div>

    {{-- Source --}}
    <div class="col-md-3">

        <label class="form-label fw-semibold">
            Source
        </label>

        <select name="source" class="form-select shadow-sm">

            <option value="">
                Select Source
            </option>

            @foreach (['IndiaMart', 'Justdial', 'Linkedin', 'Facebook', 'Instagram', 'Twitter', 'References', 'Others'] as $source)
                <option value="{{ $source }}"
                    {{ old('source', $sale->source ?? 'Others') == $source ? 'selected' : '' }}>

                    {{ $source }}

                </option>
            @endforeach

        </select>
        @error('source')
            <span class="text-danger small">
                {{ $message }}
            </span>
        @enderror
    </div>

    {{-- Status --}}
    <div class="col-md-3">

        <label class="form-label fw-semibold">
            Status
        </label>

        <select name="status" class="form-select shadow-sm">

            @foreach (['New', 'Won', 'Lost', 'Under Discussion', 'On-Hold', 'Fake', 'Spam', 'Irrelevant', 'Repeatedly Unreachable'] as $status)
                <option value="{{ $status }}"
                    {{ old('status', $sale->status ?? 'New') == $status ? 'selected' : '' }}>

                    {{ $status }}

                </option>
            @endforeach

        </select>

        @error('status')
            <span class="text-danger small">
                {{ $message }}
            </span>
        @enderror

    </div>

    {{-- Remarks --}}
    @if (isset($sale))
        <div class="col-md-3">
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

    {{-- Requirement --}}
    <div class="col-md-6">

        <label class="form-label fw-semibold">
            Requirement
        </label>

        <textarea name="requirement" class="form-control shadow-sm" rows="4" placeholder="Write requirement here...">{{ old('requirement', $sale->requirement ?? '') }}</textarea>

    </div>

    {{-- Follow Up Date --}}
    <div class="col-md-3">

        <label class="form-label fw-semibold">
            Follow Up Date
        </label>

        <input type="date" name="follow_up_date" value="{{ old('follow_up_date', $sale->follow_up_date ?? '') }}"
            class="form-control shadow-sm">

    </div>

    {{-- Remarks History --}}
    @if (isset($sale))

        <div class="col-md-12">

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
                                    <th width="180" class="text-center">Sales Person</th>
                                    <th width="170" class="text-center">Created Date</th>
                                </tr>
                            </thead>

                            <tbody>

                                @forelse($sale->remarkHistories as $remark)
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

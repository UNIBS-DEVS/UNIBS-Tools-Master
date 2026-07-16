<div class="row">

    <div class="col-md-6 mb-3">
        <label class="form-label">Customer Name <span class="text-danger">*</span> </label>

        <input type="text" name="customer" class="form-control @error('customer') is-invalid @enderror"
            value="{{ old('customer', $customer->customer ?? '') }}">

        @error('customer')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>



    <div class="col-md-6 mb-3">
        <label class="form-label">Contact Person</label>

        <input type="text" name="contact" class="form-control"
            value="{{ old('contact', $customer->contact ?? '') }}">

    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Email</label>

        <input type="email" name="email" class="form-control " value="{{ old('email', $customer->email ?? '') }}">

    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Mobile</label>
        <input type="number" name="mobile" class="form-control " value="{{ old('mobile', $customer->mobile ?? '') }}">

    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Status</label>

        <select name="status" class="form-control">
            <option value="Active" {{ old('status', $customer->status ?? '') == 'Active' ? 'selected' : '' }}>
                Active
            </option>

            <option value="Inactive" {{ old('status', $customer->status ?? '') == 'Inactive' ? 'selected' : '' }}>
                Inactive
            </option>

            <option value="On-Hold" {{ old('status', $customer->status ?? '') == 'On-Hold' ? 'selected' : '' }}>
                On-Hold
            </option>

            <option value="Blacklisted" {{ old('status', $customer->status ?? '') == 'Blacklisted' ? 'selected' : '' }}>
                Blacklisted
            </option>
        </select>

    </div>

    <div class="col-md-4 mb-3">
        <label class="form-label">Domain</label>

        <select name="domain" class="form-control">
            <option value="IT" {{ old('domain', $customer->domain ?? '') == 'IT' ? 'selected' : '' }}>
                IT
            </option>

            <option value="Non-IT" {{ old('domain', $customer->domain ?? '') == 'Non-IT' ? 'selected' : '' }}>
                Non-IT
            </option>
        </select>

    </div>

    <div class="col-md-4 mb-3">
        <label class="form-label">SPOC</label>

        <select name="spoc" class="form-control">
            <option value="">-- Select SPOC --</option>

            @foreach ($spocs as $spoc)
                <option value="{{ $spoc->id }}"
                    {{ old('spoc', $customer->spoc ?? '') == $spoc->id ? 'selected' : '' }}>
                    {{ $spoc->name }}
                </option>
            @endforeach
        </select>

    </div>

    <div class="col-md-4 mb-3">
        <label class="form-label">Backup SPOC</label>

        <select name="backup_spoc" class="form-control">
            <option value="">-- Select Backup SPOC --</option>

            @foreach ($backupSpocs as $backupSpoc)
                <option value="{{ $backupSpoc->id }}"
                    {{ old('backup_spoc', $customer->backup_spoc ?? '') == $backupSpoc->id ? 'selected' : '' }}>
                    {{ $backupSpoc->name }}
                </option>
            @endforeach
        </select>


    </div>

    <div class="col-md-12 mb-3">
        <label class="form-label">Remarks</label>

        <textarea name="remarks" rows="4" class="form-control">{{ old('remarks', $customer->remarks ?? '') }}</textarea>

    </div>

</div>

<div class="row g-4">

    {{-- Name --}}
    <div class="col-md-3">
        <label class="form-label fw-semibold">
            Name <span class="text-danger">*</span>
        </label>
        <div class="input-group">
            <span class="input-group-text bg-light">
                <i class="fa fa-user"></i>
            </span>
            <input type="text" name="name" value="{{ old('name', $user->name ?? '') }}"
                class="form-control @error('name') is-invalid @enderror">

            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    {{-- Email --}}
    <div class="col-md-3">
        <label class="form-label fw-semibold">
            Email <span class="text-danger">*</span>
        </label>
        <div class="input-group">
            <span class="input-group-text bg-light">
                <i class="fa fa-envelope"></i>
            </span>
            <input type="email" name="email" value="{{ old('email', $user->email ?? '') }}"
                class="form-control @error('email') is-invalid @enderror">

            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    {{-- Personal Mobile --}}
    <div class="col-md-3">
        <label class="form-label fw-semibold">Personal Mobile <span class="text-danger">*</span></label>
        <div class="input-group">
            <span class="input-group-text bg-light">
                <i class="fa fa-phone"></i>
            </span>
            <input type="text" name="personal_mobile"
                value="{{ old('personal_mobile', $user->personal_mobile ?? '') }}"
                class="form-control @error('personal_mobile') is-invalid @enderror">

            @error('personal_mobile')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    {{-- Offical Mobile --}}
    <div class="col-md-3">
        <label class="form-label fw-semibold">Offical Mobile <span class="text-danger">*</span></label>
        <div class="input-group">
            <span class="input-group-text bg-light">
                <i class="fa fa-phone"></i>
            </span>
            <input type="text" name="offical_mobile" value="{{ old('offical_mobile', $user->offical_mobile ?? '') }}"
                class="form-control @error('offical_mobile') is-invalid @enderror">

            @error('offical_mobile')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    {{-- Roles --}}
    @php
        $allRoles = ['Admin', 'Manager', 'API User', 'Accounts', 'Customer', 'DB Integration'];

        $selectedRoles = old('roles', $user->roles ?? []);
        // $selectedCustomers = old('customers', $user->customers ?? []);

        $selectedRoles = (array) old('roles', $user->roles ?? []);

    @endphp

    <div class="col-md-2">
        <label class="form-label fw-semibold">
            Roles <span class="text-danger">*</span>
        </label>

        <div class="dropdown">
            <button id="rolesDropdownBtn"
                class="btn btn-outline-secondary dropdown-toggle w-100 text-start @error('roles') border-danger @enderror"
                type="button" data-bs-toggle="dropdown">
                Select Roles
            </button>

            <ul class="dropdown-menu w-100 p-3" style="max-height:250px;overflow-y:auto;">
                @foreach ($allRoles as $role)
                    <li>
                        <div class="form-check">
                            <input class="form-check-input role-checkbox" type="checkbox" name="roles[]"
                                value="{{ strtolower($role) }}" id="role_{{ Str::slug($role) }}"
                                {{ in_array(strtolower($role), $selectedRoles) ? 'checked' : '' }}>

                            <label class="form-check-label" for="role_{{ Str::slug($role) }}">
                                {{ $role }}
                            </label>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>

        @error('roles')
            <div class="text-danger small mt-1">
                {{ $message }}
            </div>
        @enderror
    </div>

    {{-- Manager --}}
    <div class="col-md-2">
        <label class="form-label fw-semibold">
            Manager
        </label>

        <div class="input-group">
            <span class="input-group-text bg-light">
                <i class="fa fa-users"></i>
            </span>

            <select name="manager_id" class="form-select @error('manager_id') is-invalid @enderror">
                <option value="">-- None --</option>

                @foreach ($managers as $manager)
                    <option value="{{ $manager->id }}" @selected(old('manager_id', $user->manager_id ?? '') == $manager->id)>
                        {{ $manager->name }}
                    </option>
                @endforeach
            </select>
            @error('manager_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    {{-- Status --}}
    <div class="col-md-2">
        <label class="form-label fw-semibold">
            Status <span class="text-danger">*</span>
        </label>

        <div class="input-group">
            <span class="input-group-text bg-light">
                <i class="fa fa-toggle-on"></i>
            </span>

            <select name="status" class="form-select">
                <option value="active" @selected(old('status', $user->status ?? 'active') == 'active')>
                    Active
                </option>

                <option value="inactive" @selected(old('status', $user->status ?? '') == 'inactive')>
                    Inactive
                </option>
            </select>
        </div>
    </div>

    {{-- Password --}}
    <div class="col-md-3">
        <label class="form-label fw-semibold">
            Password
        </label>

        <div class="input-group">
            <span class="input-group-text bg-light">
                <i class="fa fa-lock"></i>
            </span>

            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror">

            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    {{-- Confirm Password --}}
    <div class="col-md-3">
        <label class="form-label fw-semibold">
            Confirm Password
        </label>

        <div class="input-group">
            <span class="input-group-text bg-light">
                <i class="fa fa-lock"></i>
            </span>

            <input type="password" name="password_confirmation"
                class="form-control @error('password') is-invalid @enderror">

            @error('password_confirmation')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

</div>

@push('scripts')
    <script></script>
@endpush

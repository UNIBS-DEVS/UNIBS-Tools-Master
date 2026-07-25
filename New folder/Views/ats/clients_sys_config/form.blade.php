@extends('layouts.app')

@section('title', $isEdit ? 'Edit Client Configuration | Unibs Tools' : 'Add Client Configuration | Unibs Tools')

@push('styles')
    <style>
        fieldset {
            margin-bottom: 0.5rem;
        }

        legend {
            font-size: 1.25rem;
        }
    </style>
@endpush

@section('content')
    <div class="container mt-4">

        <div class="card shadow-sm border-0">

            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0 fw-semibold">
                    <i class="fa fa-cogs me-2 text-primary"></i>
                    {{ $isEdit ? 'Edit' : 'Add' }} Configuration
                    <small class="text-muted">({{ $client->client_name }})</small>
                </h5>
            </div>

            <form method="POST"
                action="{{ $isEdit ? route('ats.clientsSysConfigs.update', $client->id) : route('ats.clientsSysConfigs.store', $client->id) }}">
                @csrf
                @if ($isEdit)
                    @method('PUT')
                @endif

                <div class="card-body">

                    <div class="row g-4">

                        {{-- Modules Config --}}
                        <fieldset class="border rounded p-3">
                            <legend class="w-auto px-2 fw-semibold text-primary">
                                🔹 Modules Config
                            </legend>

                            <div class="row">

                                @foreach ($modules as $module)
                                    <div class="col-md-3 col-sm-6 mb-2">

                                        <div class="form-check">

                                            <input type="checkbox" name="modules[]" value="{{ $module->id }}"
                                                class="form-check-input" id="module{{ $module->id }}"
                                                {{ in_array($module->id, old('modules', $selectedModules ?? [])) ? 'checked' : '' }}>

                                            <label class="form-check-label" for="module{{ $module->id }}">
                                                {{ $module->name }}
                                            </label>
                                        </div>

                                    </div>
                                @endforeach

                            </div>
                        </fieldset>

                        {{-- 🔹Support Config --}}
                        <fieldset class="border rounded p-3">
                            <legend class="w-auto px-2 fw-semibold text-primary">🔹Support Config</legend>

                            <div class="row g-3">

                                <div class="col-md-3">
                                    <label>Support User</label>
                                    <input type="text" name="support_user"
                                        value="{{ old('support_user', optional($config)->support_user) }}"
                                        class="form-control">
                                </div>

                                <div class="col-md-3">
                                    <label>Support Password</label>
                                    <input type="password" name="support_password"
                                        value="{{ old('support_password', optional($config)->support_password) }}"
                                        class="form-control">

                                </div>
                            </div>
                        </fieldset>

                        {{-- 🔹 DB CONFIG --}}
                        <fieldset class="border rounded p-3">
                            <legend class="w-auto px-2 fw-semibold text-primary">Database Config</legend>

                            <div class="row g-3">
                                <div class="col-md-2">
                                    <label>DB Host<span class="text-danger">*</span></label>
                                    <input type="text" name="db_host"
                                        value="{{ old('db_host', optional($config)->db_host) }}"
                                        class="form-control @error('db_host') is-invalid @enderror">
                                    @error('db_host')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-1">
                                    <label>MySql Port<span class="text-danger">*</span></label>

                                    <input type="text" inputmode="numeric" pattern="[0-9]*" name="db_mysql_port"
                                        value="{{ old('db_mysql_port', optional($config)->db_mysql_port) }}"
                                        class="form-control @error('db_mysql_port') is-invalid @enderror">


                                    @error('db_mysql_port')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-2">
                                    <label>DB Name<span class="text-danger">*</span></label>
                                    <input type="text" name="db_name"
                                        value="{{ old('db_name', optional($config)->db_name) }}"
                                        class="form-control @error('db_name') is-invalid @enderror">
                                    @error('db_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-2">
                                    <label>DB Username <span class="text-danger">*</span></label>
                                    <input type="text" name="db_username"
                                        value="{{ old('db_username', optional($config)->db_username) }}"
                                        class="form-control @error('db_username') is-invalid @enderror">
                                    @error('db_username')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-2">
                                    <label>DB Password</label>
                                    {{-- <input type="password" name="db_password"
                                        class="form-control @error('db_password') is-invalid @enderror"> --}}

                                    <input type="password" name="db_password"
                                        value="{{ old('db_password', optional($config)->db_password) }}"
                                        class="form-control @error('db_password') is-invalid @enderror">


                                    @error('db_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </fieldset>

                        {{-- 🔹 SMTP CONFIG --}}
                        <fieldset class="border rounded p-3">
                            <legend class="w-auto px-2 fw-semibold text-primary">SMTP Config</legend>

                            <div class="row g-3">
                                <div class="col-md-2">
                                    <label>SMTP Host</label>
                                    <input type="text" name="smtp_host"
                                        value="{{ old('smtp_host', optional($config)->smtp_host) }}" class="form-control">
                                </div>

                                <div class="col-md-2">
                                    <label>SMTP Port</label>
                                    <input type="number" name="smtp_port"
                                        value="{{ old('smtp_port', optional($config)->smtp_port) }}" class="form-control">
                                </div>

                                <div class="col-md-2">
                                    <label>SMTP Auth<span class="text-danger">*</span></label>
                                    <select name="smtp_auth" class="form-select @error('smtp_auth') is-invalid @enderror">
                                        <option value="">Select</option>
                                        <option value="tls" @selected(old('smtp_auth', optional($config)->smtp_auth) === 'tls')>TLS</option>
                                        <option value="ssl" @selected(old('smtp_auth', optional($config)->smtp_auth) === 'ssl')>SSL</option>
                                    </select>
                                    @error('smtp_auth')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </fieldset>

                        {{-- 🔹 MICROSOFT GRAPH --}}
                        <fieldset class="border rounded p-3">
                            <legend class="w-auto px-2 fw-semibold text-primary">Microsoft Graph</legend>

                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label>Client ID</label>
                                    <input type="text" name="graph_client_id"
                                        value="{{ old('graph_client_id', optional($config)->graph_client_id) }}"
                                        class="form-control">
                                </div>

                                <div class="col-md-4">
                                    <label>Tenant ID</label>
                                    <input type="text" name="graph_tenant_id"
                                        value="{{ old('graph_tenant_id', optional($config)->graph_tenant_id) }}"
                                        class="form-control">
                                </div>

                                <div class="col-md-4">
                                    <label>Client Secret ID</label>
                                    <input type="text" name="graph_client_secret_id"
                                        value="{{ old('graph_client_secret_id', optional($config)->graph_client_secret_id) }}"
                                        class="form-control">
                                </div>

                                <div class="col-md-4">
                                    <label>Client Secret Value</label>
                                    <input type="text" name="graph_client_secret_value"
                                        value="{{ old('graph_client_secret_value', optional($config)->graph_client_secret_value) }}"
                                        class="form-control">
                                </div>

                                <div class="col-md-4">
                                    <label>Redirect URL</label>
                                    <input type="text" name="graph_redirect_url"
                                        value="{{ old('graph_redirect_url', optional($config)->graph_redirect_url) }}"
                                        class="form-control">
                                </div>

                                <div class="col-md-4">
                                    <label>Expiry Date</label>
                                    <input type="date" name="graph_client_expiry_date"
                                        value="{{ old('graph_client_expiry_date', optional($config)->graph_client_expiry_date) }}"
                                        class="form-control">
                                </div>

                            </div>
                        </fieldset>

                        <div class="row g-4">

                            {{-- 🔹 RESUME PARSING --}}
                            <div class="col-md-6">
                                <fieldset class="border rounded p-3 h-100">
                                    <legend class="w-auto px-2 fw-semibold text-primary">
                                        Resume Parsing
                                    </legend>

                                    <div class="row g-2">
                                        <div class="col-md-6">
                                            <label>Email</label>
                                            <input type="email" name="resume_parse_email"
                                                value="{{ old('resume_parse_email', optional($config)->resume_parse_email) }}"
                                                class="form-control">
                                        </div>

                                        <div class="col-md-6">
                                            <label>Parsing Time</label>
                                            <input type="text" name="resume_parsing_time"
                                                value="{{ old('resume_parsing_time', isset($config->resume_parsing_time) ? implode(',', $config->resume_parsing_time) : '') }}"
                                                placeholder="e.g. 10:00,14:05,18:30"
                                                class="form-control @error('resume_parsing_time') is-invalid @enderror">

                                            @error('resume_parsing_time')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </fieldset>
                            </div>

                            {{-- 🔹 AUTHENTICATION --}}
                            <div class="col-md-6">
                                <fieldset class="border rounded p-3 h-100">
                                    <legend class="w-auto px-2 fw-semibold text-primary">
                                        Authentication
                                    </legend>

                                    <div class="row g-2">
                                        <div class="col-md-6">
                                            <label>Login Auth Type</label>
                                            <select name="login_auth_type"
                                                class="form-select @error('login_auth_type') is-invalid @enderror">
                                                <option value="">Select</option>
                                                <option value="basic" @selected(old('login_auth_type', optional($config)->login_auth_type) === 'basic')>
                                                    Basic
                                                </option>
                                                <option value="oauth" @selected(old('login_auth_type', optional($config)->auth_type) === 'oauth')>
                                                    OAuth
                                                </option>
                                            </select>

                                            @error('login_auth_type')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <label>Email Auth Type</label>
                                            <select name="email_auth_type"
                                                class="form-select @error('email_auth_type') is-invalid @enderror">
                                                <option value="">Select</option>
                                                <option value="smtp" @selected(old('email_auth_type', optional($config)->email_auth_type) === 'smtp')>
                                                    SMTP
                                                </option>
                                                <option value="graph_id" @selected(old('email_auth_type', optional($config)->email_auth_type) === 'graph_id')>
                                                    Graph API
                                                </option>
                                            </select>

                                            @error('email_auth_type')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </fieldset>
                            </div>

                        </div>

                        {{-- 🔹 AUTH TYPE --}}
                        {{-- <fieldset class="border rounded p-3">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label>Auth Type <span class="text-danger">*</span></label>
                                    <select name="auth_type" class="form-select @error('auth_type') is-invalid @enderror">
                                        <option value="">Select</option>
                                        <option value="google" @selected(old('auth_type', optional($config)->auth_type) === 'google')>Google</option>
                                        <option value="microsoft" @selected(old('auth_type', optional($config)->auth_type) === 'microsoft')>Microsoft</option>
                                    </select>
                                    @error('auth_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </fieldset> --}}

                    </div>

                    <div class="mt-4 text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save me-1"></i>
                            {{ $isEdit ? 'Update' : 'Save' }}
                        </button>
                    </div>

                </div>
            </form>
        </div>
    </div>
@endsection

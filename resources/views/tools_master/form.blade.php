@extends('layouts.app')

{{-- @section('title', $isEdit ? 'Edit Client Configuration | Unibs Tools' : 'Add Client Configuration | Unibs Tools') --}}
@section('title', 'System Settings | Unibs Tools')

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
                    Tool Settings
                </h5>
            </div>


            <form action="{{ route('tools-master.update') }}" method="POST">


                @csrf
                @method('PUT')

                <div class="card-body">

                    <div class="row g-4">

                        {{-- 🔹Support Config --}}
                        <fieldset class="border rounded p-3">
                            <legend class="w-auto px-2 fw-semibold text-primary">🔹Support Config</legend>

                            <div class="row g-3">

                                <div class="col-md-3">
                                    <label>Support User</label>
                                    <input type="text" name="support_user"
                                        value="{{ old('support_user', $tool->support_user) }}" class="form-control">
                                </div>

                                <div class="col-md-3">
                                    <label>Support Password</label>
                                    <input type="password" name="support_password"
                                        value="{{ old('support_password', $tool->support_password) }}" class="form-control">

                                </div>

                                <div class="col-md-3">
                                    <label>HR Email</label>
                                    <input type="text" name="hr_email" value="{{ old('hr_email', $tool->hr_email) }}"
                                        class="form-control">
                                </div>

                                <div class="col-md-3">
                                    <label>Accounts Email</label>
                                    <input type="text" name="accounts_email"
                                        value="{{ old('accounts_email', $tool->accounts_email) }}" class="form-control">
                                </div>

                                <div class="col-md-3">
                                    <label>Attendance Notification Email</label>
                                    <input type="text" name="attendance_notification_email"
                                        value="{{ old('attendance_notification_email', $tool->attendance_notification_email) }}"
                                        class="form-control">
                                </div>

                                <div class="col-md-3">
                                    <label>Timesheet Notification Email</label>
                                    <input type="text" name="timesheet_notification_email"
                                        value="{{ old('timesheet_notification_email', $tool->timesheet_notification_email) }}"
                                        class="form-control">
                                </div>

                                <div class="col-md-3">
                                    <label>Call Review Notification Email</label>
                                    <input type="text" name="call_review_notification_email"
                                        value="{{ old('call_review_notification_email', $tool->call_review_notification_email) }}"
                                        class="form-control">
                                </div>

                            </div>
                        </fieldset>


                        {{-- 🔹 SMTP CONFIG --}}
                        <fieldset class="border rounded p-3">
                            <legend class="w-auto px-2 fw-semibold text-primary">SMTP Config</legend>

                            <div class="row g-3">
                                <div class="col-md-2">
                                    <label>SMTP Host</label>
                                    <input type="text" name="smtp_host" value="{{ old('smtp_host', $tool->smtp_host) }}"
                                        class="form-control">
                                </div>

                                <div class="col-md-2">
                                    <label>SMTP Port</label>
                                    <input type="number" name="smtp_port" value="{{ old('smtp_port', $tool->smtp_port) }}"
                                        class="form-control">
                                </div>

                                <div class="col-md-2">
                                    <label>SMTP Auth<span class="text-danger">*</span></label>
                                    <select name="smtp_auth" class="form-select @error('smtp_auth') is-invalid @enderror">
                                        <option value="">Select</option>
                                        <option value="tls" @selected(old('smtp_auth', $tool->smtp_auth) === 'tls')>TLS</option>
                                        <option value="ssl" @selected(old('smtp_auth', $tool->smtp_auth) === 'ssl')>SSL</option>
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
                                        value="{{ old('graph_client_id', $tool->graph_client_id) }}" class="form-control">
                                </div>

                                <div class="col-md-4">
                                    <label>Tenant ID</label>
                                    <input type="text" name="graph_tenant_id"
                                        value="{{ old('graph_tenant_id', $tool->graph_tenant_id) }}" class="form-control">
                                </div>

                                <div class="col-md-4">
                                    <label>Client Secret ID</label>
                                    <input type="text" name="graph_client_secret_id"
                                        value="{{ old('graph_client_secret_id', $tool->graph_client_secret_id) }}"
                                        class="form-control">
                                </div>

                                <div class="col-md-4">
                                    <label>Client Secret Value</label>
                                    <input type="text" name="graph_client_secret_value"
                                        value="{{ old('graph_client_secret_value', $tool->graph_client_secret_value) }}"
                                        class="form-control">
                                </div>

                                <div class="col-md-4">
                                    <label>Redirect URL</label>
                                    <input type="text" name="graph_redirect_url"
                                        value="{{ old('graph_redirect_url', $tool->graph_redirect_url) }}"
                                        class="form-control">
                                </div>

                                <div class="col-md-4">
                                    <label>Expiry Date</label>
                                    <input type="date" name="graph_client_expiry_date"
                                        value="{{ old('graph_client_expiry_date', $tool->graph_client_expiry_date) }}"
                                        class="form-control">
                                </div>

                            </div>
                        </fieldset>

                        <div class="row g-4">
                            {{-- 🔹 AUTHENTICATION --}}
                            <div class="col-md-12">
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
                                                <option value="basic" @selected(old('login_auth_type', $tool->login_auth_type) === 'basic')>
                                                    Basic
                                                </option>
                                                <option value="oauth" @selected(old('login_auth_type', $tool->auth_type) === 'oauth')>
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
                                                <option value="smtp" @selected(old('email_auth_type', $tool->email_auth_type) === 'smtp')>
                                                    SMTP
                                                </option>
                                                <option value="graph_id" @selected(old('email_auth_type', $tool->email_auth_type) === 'graph_id')>
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
                    </div>

                    <div class="mt-4 text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save me-1"></i>
                            Update
                        </button>
                    </div>

                </div>
            </form>
        </div>
    </div>
@endsection

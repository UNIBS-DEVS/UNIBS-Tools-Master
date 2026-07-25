@extends('layouts.app')

@section('title', 'Database Setup')

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-dark text-white text-center py-4">
                        <div class="rounded-circle bg-light text-dark d-inline-flex align-items-center justify-content-center mb-3"
                            style="width: 60px; height: 60px;">
                            <i class="fa fa-database fa-2x"></i>
                        </div>
                        <h2 class="h4 fw-bold mb-1">Inspect Database</h2>
                        <p class="text-muted small mb-0">Select a pre-configured database connection profile to start.</p>
                    </div>
                    <div class="card-body p-4 bg-white">
                        @if ($errors->any())
                            <div class="alert alert-danger mb-4">
                                <h6 class="fw-bold mb-1"><i class="fa fa-triangle-exclamation me-2"></i>Connection Failed
                                </h6>
                                <p class="small mb-0 font-monospace" style="word-break: break-all;">{{ $errors->first() }}
                                </p>
                            </div>
                        @endif

                        <form action="{{ route('db-settings') }}" method="POST" id="connect-form" class="needs-validation">
                            @csrf

                            <div class="mb-4">
                                <label for="app_name"
                                    class="form-label small fw-semibold text-muted text-uppercase tracking-wider">Application</label>
                                <select id="app_name" name="app_name" class="form-select" required>
                                    <option value="">-- Select Application --</option>
                                    <option value="ats" {{ old('app_name') == 'ats' ? 'selected' : '' }}>ATS</option>
                                    <option value="lms" {{ old('app_name') == 'lms' ? 'selected' : '' }}>LMS</option>
                                    <option value="unione" {{ old('app_name') == 'unione' ? 'selected' : '' }}>Unione
                                    </option>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label for="profile_id"
                                    class="form-label small fw-semibold text-muted text-uppercase tracking-wider">Quick
                                    Connect Profile</label>
                                <select id="profile_id" name="profile_id" class="form-select" required disabled>
                                    <option value="">-- Select a Database Profile --</option>
                                </select>
                            </div>

                            <div class="d-grid pt-2">
                                <button type="submit" id="submit-btn" class="btn btn-primary fw-semibold">
                                    Establish Connection
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const appSelect = document.getElementById('app_name');
            const profileSelect = document.getElementById('profile_id');
            const submitBtn = document.getElementById('submit-btn');
            const form = document.getElementById('connect-form');

            function loadClients(appName, selectedProfile = null) {
                if (!appName) {
                    profileSelect.innerHTML = '<option value="">-- Select a Database Profile --</option>';
                    profileSelect.disabled = true;
                    return;
                }

                profileSelect.disabled = true;
                profileSelect.innerHTML = '<option value="">Loading profiles...</option>';

                fetch(`/inspect-database/clients/${appName}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Failed to load profiles');
                        }
                        return response.json();
                    })
                    .then(res => {
                        profileSelect.innerHTML = '<option value="">-- Select a Database Profile --</option>';
                        if (res.status && Array.isArray(res.data)) {
                            if (res.data.length === 0) {
                                profileSelect.innerHTML =
                                    '<option value="">No connection profiles found</option>';
                                return;
                            }
                            res.data.forEach(client => {
                                const option = document.createElement('option');
                                option.value = client.client_code;
                                option.textContent = (client.client_name || client.client_code) + ' (' +
                                    appName.toUpperCase() + ')';
                                if (selectedProfile && selectedProfile === client.client_code) {
                                    option.selected = true;
                                }
                                profileSelect.appendChild(option);
                            });
                            profileSelect.disabled = false;
                        } else {
                            profileSelect.innerHTML = '<option value="">No connection profiles found</option>';
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        profileSelect.innerHTML = '<option value="">Error loading profiles</option>';
                    });
            }

            appSelect.addEventListener('change', function() {
                loadClients(this.value);
            });

            profileSelect.addEventListener('change', function() {
                if (profileSelect.value) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML =
                        '<span class="spinner-border spinner-border-sm me-2"></span>Connecting...';
                    form.submit();
                }
            });

            form.addEventListener('submit', function(e) {
                if (!profileSelect.value || !appSelect.value) {
                    e.preventDefault();
                    return;
                }
                submitBtn.disabled = true;
                submitBtn.innerHTML =
                    '<span class="spinner-border spinner-border-sm me-2"></span>Connecting...';
            });

            const oldApp = "{{ old('app_name') }}";
            const oldProfile = "{{ old('profile_id') }}";
            if (oldApp) {
                loadClients(oldApp, oldProfile);
            }
        });
    </script>
@endsection

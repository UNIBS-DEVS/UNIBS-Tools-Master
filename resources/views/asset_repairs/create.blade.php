@extends('layouts.app')

@section('title', 'Log Repair Request | Unibs Tools')

@section('content')
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 mb-4">
                    {{-- Header --}}
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0 fw-semibold text-dark">
                            <i class="fa fa-wrench me-2 text-primary"></i>
                            Log Repair Request
                        </h5>
                    </div>

                    {{-- Body --}}
                    <div class="card-body p-4">
                        <form method="POST" action="{{ route('asset-repairs.store') }}">
                            @csrf

                            {{-- Select Asset --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    Select Asset <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="fa fa-box-open"></i>
                                    </span>
                                    @if (request('asset_id'))
                                        @php
                                            $preselectedAsset = $assets->firstWhere('id', request('asset_id'));
                                        @endphp
                                        <select class="form-select" disabled>
                                            @if ($preselectedAsset)
                                                <option value="{{ $preselectedAsset->id }}" selected>
                                                    {{ $preselectedAsset->asset_name }} (Code: {{ $preselectedAsset->asset_code }}, Status: {{ ucfirst($preselectedAsset->status) }})
                                                </option>
                                            @else
                                                @php
                                                    $fallbackAsset = \App\Models\AssetMaster::find(request('asset_id'));
                                                @endphp
                                                @if ($fallbackAsset)
                                                    <option value="{{ $fallbackAsset->id }}" selected>
                                                        {{ $fallbackAsset->asset_name }} (Code: {{ $fallbackAsset->asset_code }}, Status: {{ ucfirst($fallbackAsset->status) }})
                                                    </option>
                                                @endif
                                            @endif
                                        </select>
                                        <input type="hidden" name="asset_id" value="{{ request('asset_id') }}">
                                    @else
                                        <select name="asset_id" class="form-select @error('asset_id') is-invalid @enderror" required>
                                            <option value="">-- Choose Asset --</option>
                                            @foreach ($assets as $asset)
                                                <option value="{{ $asset->id }}" {{ old('asset_id') == $asset->id ? 'selected' : '' }}>
                                                    {{ $asset->asset_name }} (Code: {{ $asset->asset_code }}, Status: {{ ucfirst($asset->status) }})
                                                </option>
                                            @endforeach
                                        </select>
                                    @endif
                                    @error('asset_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                            </div>

                            {{-- Select Vendor --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    Select Vendor / Service Center
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="fa fa-handshake"></i>
                                    </span>
                                    <select name="vendor_id" class="form-select @error('vendor_id') is-invalid @enderror">
                                        <option value="">-- Choose Vendor (Optional) --</option>
                                        @foreach ($vendors as $vendor)
                                            <option value="{{ $vendor->id }}" {{ old('vendor_id') == $vendor->id ? 'selected' : '' }}>
                                                {{ $vendor->vendor_name }} ({{ $vendor->email ?? 'No email' }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('vendor_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Issue Description --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Issue Description</label>
                                <textarea name="issue_description" class="form-control @error('issue_description') is-invalid @enderror" rows="3" placeholder="Describe the fault or issue in detail...">{{ old('issue_description') }}</textarea>
                                @error('issue_description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Dates & Cost Row --}}
                            <div class="row g-3 mb-3">
                                {{-- Reported Date --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Reported Date</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="fa fa-calendar"></i>
                                        </span>
                                        <input type="datetime-local" name="reported_date" class="form-control @error('reported_date') is-invalid @enderror" value="{{ old('reported_date', now()->format('Y-m-d\TH:i')) }}">
                                        @error('reported_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Sent Date --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Sent Date</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="fa fa-calendar-alt"></i>
                                        </span>
                                        <input type="datetime-local" name="sent_date" class="form-control @error('sent_date') is-invalid @enderror" value="{{ old('sent_date') }}">
                                        @error('sent_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- Cost & Status Row --}}
                            <div class="row g-3 mb-4">
                                {{-- Repair Cost --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Estimated / Repair Cost (₹)</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">₹</span>
                                        <input type="number" step="0.01" name="repair_cost" class="form-control @error('repair_cost') is-invalid @enderror" placeholder="0.00" value="{{ old('repair_cost') }}">
                                        @error('repair_cost')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Repair Status --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        Repair Status <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="fa fa-toggle-on"></i>
                                        </span>
                                        <select name="repair_status" class="form-select @error('repair_status') is-invalid @enderror" required>
                                            <option value="Reported" {{ old('repair_status') == 'Reported' ? 'selected' : '' }}>Reported</option>
                                            <option value="Sent for Repair" {{ old('repair_status', 'Sent for Repair') == 'Sent for Repair' ? 'selected' : '' }}>Sent for Repair</option>
                                            <option value="Under Repair" {{ old('repair_status') == 'Under Repair' ? 'selected' : '' }}>Under Repair</option>
                                        </select>
                                        @error('repair_status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- Remarks --}}
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Remarks</label>
                                <textarea name="remarks" class="form-control @error('remarks') is-invalid @enderror" rows="2" placeholder="Enter any additional notes...">{{ old('remarks') }}</textarea>
                                @error('remarks')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Repair History Card --}}
                            <div class="card shadow-sm border-0 mb-4 bg-light" id="repairHistoryCard">
                                <div class="card-header bg-white border-bottom py-3">
                                    <h6 class="mb-0 fw-bold text-dark">
                                        <i class="fa fa-history me-2 text-primary"></i>
                                        Asset Repair History
                                    </h6>
                                </div>
                                <div class="card-body p-0">
                                    <div id="repairHistoryContent">
                                        <div class="text-muted p-4 text-center">
                                            <i class="fa fa-info-circle mb-2"></i>
                                            <p class="mb-0 small">Select an asset to view its repair history</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Submit Actions --}}
                            <div class="d-flex justify-content-end gap-2 border-top pt-3">
                                <a href="{{ route('asset.index') }}" class="btn btn-outline-secondary px-4">
                                    Cancel
                                </a>
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="fa fa-check-circle me-1"></i> Log Request
                                </button>
                            </div>

                        </form>
                    </div>
                </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Vanilla JS AJAX Fetch Script --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const assetSelect = document.querySelector('select[name="asset_id"]') || document.querySelector('input[name="asset_id"]');
            const historyContent = document.getElementById('repairHistoryContent');

            function fetchRepairHistory(assetId) {
                if (!assetId) {
                    historyContent.innerHTML = `
                        <div class="text-muted p-4 text-center">
                            <i class="fa fa-info-circle mb-2"></i>
                            <p class="mb-0 small">Select an asset to view its repair history</p>
                        </div>
                    `;
                    return;
                }

                historyContent.innerHTML = `
                    <div class="text-center p-4">
                        <div class="spinner-border spinner-border-sm text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <span class="ms-2 text-muted small">Loading repair history...</span>
                    </div>
                `;

                fetch(`/asset-repairs/history/${assetId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            historyContent.innerHTML = `<div class="text-danger p-4 text-center">${data.error}</div>`;
                            return;
                        }

                        if (data.length === 0) {
                            historyContent.innerHTML = `
                                <div class="text-muted p-4 text-center">
                                    <i class="fa fa-check-circle mb-2 text-success"></i>
                                    <p class="mb-0 small fw-semibold">No repairs logged for this asset</p>
                                </div>
                            `;
                            return;
                        }

                        let html = `
                            <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                                <table class="table table-hover align-middle mb-0 small">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="px-3">Reported</th>
                                            <th>Vendor</th>
                                            <th>Cost</th>
                                            <th class="px-3">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                        `;

                        data.forEach(repair => {
                            const dateObj = new Date(repair.reported_date);
                            const reportedDate = repair.reported_date 
                                ? dateObj.toLocaleDateString('en-IN', {day: '2-digit', month: 'short', year: 'numeric'}) 
                                : '-';
                            const vendorName = repair.vendor ? repair.vendor.vendor_name : 'No vendor';
                            const cost = repair.repair_cost ? '₹' + parseFloat(repair.repair_cost).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2}) : '-';

                            let badgeClass = 'bg-secondary-subtle text-secondary';
                            const status = repair.repair_status.toLowerCase();
                            if (['sent for repair', 'under repair', 'reported'].includes(status)) {
                                badgeClass = 'bg-warning-subtle text-warning';
                            } else if (['repaired', 'received'].includes(status)) {
                                badgeClass = 'bg-success-subtle text-success';
                            } else if (status === 'unrepairable') {
                                badgeClass = 'bg-danger-subtle text-danger';
                            }

                            html += `
                                <tr style="cursor: pointer;" onclick="window.location.href='/asset-repairs/${repair.id}'" title="Click to view details">
                                    <td class="px-3">${reportedDate}</td>
                                    <td>
                                        <div class="fw-semibold">${vendorName}</div>
                                        <div class="text-muted small text-truncate" style="max-width: 150px;">${repair.issue_description || '-'}</div>
                                    </td>
                                    <td class="fw-bold text-success">${cost}</td>
                                    <td class="px-3">
                                        <span class="badge ${badgeClass} rounded-pill px-2 py-1">${repair.repair_status}</span>
                                    </td>
                                </tr>
                            `;
                        });

                        html += `
                                    </tbody>
                                </table>
                            </div>
                        `;
                        historyContent.innerHTML = html;
                    })
                    .catch(err => {
                        historyContent.innerHTML = `<div class="text-danger p-4 text-center">Failed to load history</div>`;
                    });
            }

            if (assetSelect) {
                // Trigger on change
                assetSelect.addEventListener('change', function () {
                    fetchRepairHistory(this.value);
                });

                // Trigger on load
                if (assetSelect.value) {
                    fetchRepairHistory(assetSelect.value);
                }
            }
        });
    </script>
@endsection

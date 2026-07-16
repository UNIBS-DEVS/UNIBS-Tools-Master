@extends('layouts.app')

@section('title', 'Repair Details | Unibs Tools')

@section('content')
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm border-0">
                    {{-- Header --}}
                    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-semibold text-dark">
                            <i class="fa fa-info-circle me-2 text-info"></i>
                            Asset Repair Log Details
                        </h5>
                        <a href="{{ route('asset-repairs.index') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fa fa-arrow-left me-1"></i> Back to List
                        </a>
                    </div>

                    {{-- Body --}}
                    <div class="card-body p-4">
                        <div class="row mb-4">
                            {{-- Asset info --}}
                            <div class="col-md-6 mb-3">
                                <h6 class="text-primary fw-bold mb-3 border-bottom pb-2">
                                    <i class="fa fa-box-open me-1"></i> Asset Information
                                </h6>
                                <table class="table table-borderless table-sm mb-0">
                                    <tr>
                                        <td class="fw-semibold text-muted" width="120">Category:</td>
                                        <td class="text-dark">{{ $repair->asset->AssetMaster_category ?? 'None' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold text-muted">Name:</td>
                                        <td class="text-dark fw-bold">{{ $repair->asset->asset_name ?? 'None' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold text-muted">Asset Code:</td>
                                        <td class="text-dark">{{ $repair->asset->asset_code ?? 'None' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold text-muted">Serial No:</td>
                                        <td class="text-dark">{{ $repair->asset->serial_number ?? 'None' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold text-muted">Brand / Model:</td>
                                        <td class="text-dark">{{ $repair->asset->brand_name ?? 'None' }} / {{ $repair->asset->model_number ?? 'None' }}</td>
                                    </tr>
                                </table>
                            </div>

                            {{-- Vendor info --}}
                            <div class="col-md-6 mb-3">
                                <h6 class="text-primary fw-bold mb-3 border-bottom pb-2">
                                    <i class="fa fa-handshake me-1"></i> Vendor Details
                                </h6>
                                @if ($repair->vendor)
                                    <table class="table table-borderless table-sm mb-0">
                                        <tr>
                                            <td class="fw-semibold text-muted" width="120">Name:</td>
                                            <td class="text-dark fw-bold">{{ $repair->vendor->vendor_name }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold text-muted">Contact Person:</td>
                                            <td class="text-dark">{{ $repair->vendor->contact_person ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold text-muted">Email:</td>
                                            <td class="text-dark">{{ $repair->vendor->email ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold text-muted">Mobile No:</td>
                                            <td class="text-dark">{{ $repair->vendor->mobile_no ?? '-' }}</td>
                                        </tr>
                                    </table>
                                @else
                                    <div class="text-muted italic py-2">No vendor assigned for this repair request yet.</div>
                                @endif
                            </div>
                        </div>

                        {{-- Repair info --}}
                        <div class="row">
                            <div class="col-12">
                                <h6 class="text-primary fw-bold mb-3 border-bottom pb-2">
                                    <i class="fa fa-tools me-1"></i> Maintenance Details
                                </h6>
                                <table class="table table-bordered align-middle bg-light">
                                    <tr>
                                        <th class="text-muted fw-semibold py-2" width="200">Issue Description</th>
                                        <td class="text-dark py-2">{{ $repair->issue_description ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted fw-semibold py-2">Reported Date</th>
                                        <td class="text-dark py-2">
                                            {{ $repair->reported_date ? $repair->reported_date->format('d M Y h:i A') : '-' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted fw-semibold py-2">Sent Date</th>
                                        <td class="text-dark py-2">
                                            {{ $repair->sent_date ? $repair->sent_date->format('d M Y h:i A') : '-' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted fw-semibold py-2">Received Date</th>
                                        <td class="text-dark py-2">
                                            {{ $repair->received_date ? $repair->received_date->format('d M Y h:i A') : 'In Repair / Processing' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted fw-semibold py-2">Repair Cost</th>
                                        <td class="text-success fw-bold py-2">
                                            {{ $repair->repair_cost ? '₹' . number_format($repair->repair_cost, 2) : '-' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted fw-semibold py-2">Repair Status</th>
                                        <td class="py-2">
                                            @php
                                                $status = strtolower($repair->repair_status);
                                            @endphp
                                            @if (in_array($status, ['sent for repair', 'under repair', 'reported']))
                                                <span class="badge bg-warning text-dark px-3 py-2 rounded-pill">{{ ucfirst($repair->repair_status) }}</span>
                                            @elseif (in_array($status, ['repaired', 'received']))
                                                <span class="badge bg-success text-white px-3 py-2 rounded-pill">{{ ucfirst($repair->repair_status) }}</span>
                                            @elseif ($status === 'unrepairable')
                                                <span class="badge bg-danger text-white px-3 py-2 rounded-pill">Unrepairable</span>
                                            @else
                                                <span class="badge bg-secondary text-white px-3 py-2 rounded-pill">{{ ucfirst($repair->repair_status) }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted fw-semibold py-2">Remarks</th>
                                        <td class="text-dark py-2">{{ $repair->remarks ?? '-' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        {{-- Footer Action Buttons --}}
                        <div class="d-flex justify-content-end gap-2 border-top pt-3 mt-3">
                            <a href="{{ route('asset-repairs.edit', $repair->id) }}" class="btn btn-warning px-4 text-dark fw-semibold">
                                <i class="fa fa-edit me-1"></i> Edit / Update
                            </a>
                            <form action="{{ route('asset-repairs.destroy', $repair->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this repair record?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger px-4">
                                    <i class="fa fa-trash me-1"></i> Delete Record
                                </button>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

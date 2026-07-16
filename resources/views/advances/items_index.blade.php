@extends('layouts.app')
@section('title', 'advance Items | Unibs Tools')

@section('content')

    <div class="container mt-4">

        <div class="card shadow-sm border-0 mb-4">

            <div class="card-header bg-white d-flex justify-content-between align-items-center">

                <div>
                    <h5 class="mb-1 fw-semibold">
                        advance #{{ $advance->id }} Items
                    </h5>

                    <div class="text-muted small">
                         advance Title: {{ $advance->advance_reason ?? '-' }}
                    </div>
                </div>

                <div class="d-flex gap-2 align-items-center">
                    <a href="{{ route('advances.index') }}" class="btn btn-outline-secondary btn-sm">
                        Back
                    </a>
                    @if(($advance->status === 'Pending' || $advance->status === 'Rejected') && $items->isNotEmpty())
                        <form action="{{ route('advances.submit', $advance->id) }}" method="POST" class="d-inline"
                            onsubmit="return confirm('Are you sure you want to submit this advance request to your manager?')">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm">
                                <i class="fa fa-paper-plane me-1"></i> Submit Request
                            </button>
                        </form>
                    @endif

                    {{-- @if(($advance->status === 'Pending' || $advance->status === 'Rejected') && $items->isNotEmpty())
                        <form action="{{ route('advances.submit', $advance->id) }}" method="POST" class="d-inline"
                            onsubmit="return confirm('Are you sure you want to submit this advance request to your manager?')">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm">
                                <i class="fa fa-paper-plane me-1"></i> Submit Request
                            </button>
                        </form>
                    @endif --}}

                    <a href="{{ route('advances.items.index', $advance->id) }}#item-form" class="btn btn-primary btn-sm">
                        Add New Item
                    </a>
                </div>

            </div>

            <div class="card-body">

                @include('partials.message')

                @if ($errors->any())
                    <div class="alert alert-danger">
                        {{ $errors->first() }}
                    </div>
                @endif

                @if($advance->status === 'Rejected')
                    <div class="alert alert-danger border-0 shadow-sm mb-4">
                        <h6 class="fw-semibold mb-2"><i class="fa fa-exclamation-triangle me-2"></i>Rejection Details</h6>
                        @if($advance->manager_remarks)
                            <div class="mb-2">
                                <strong>Manager Remarks:</strong>
                                <span class="ms-1">{{ $advance->manager_remarks }}</span>
                            </div>
                        @endif
                        @if($advance->accounts_remarks)
                            <div>
                                <strong>Accounts Remarks:</strong>
                                <span class="ms-1">{{ $advance->accounts_remarks }}</span>
                            </div>
                        @endif
                    </div>
                @endif

                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="border rounded p-3 h-100 bg-light"> 
                            <div class="text-muted small mb-1">Employee</div>
                            <div class="fw-semibold">{{ $advance->employee?->name ?? '-' }}</div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="border rounded p-3 h-100 bg-light">
                            <div class="text-muted small mb-1">Total Items</div>
                            <div class="fw-semibold">{{ $items->count() }}</div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="border rounded p-3 h-100 bg-light">
                            <div class="text-muted small mb-1">Total Amount</div>
                            <div class="fw-semibold">&#8377; {{ number_format($items->sum('requested_amount'), 2) }}</div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>

                                <th>Category</th>
                                <th>Item Reason</th>
                                {{-- <th>Date</th> --}}
                                <th>Amount</th>

                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($items as $item)
                                <tr>

                                    <td>{{ $item->category?->category_name ?? '-' }}</td>
                                    <td>{{ $item->expense_reason ?? '-' }}</td>
                                    {{-- <td>{{ $item->created_at ? \Carbon\Carbon::parse($item->created_at)->format('d-M-Y') : '-' }}</td> --}}
                                    <td>&#8377; {{ number_format($item->requested_amount ?? 0, 2) }}</td>
                                    {{-- <td> --}}
                                        {{-- @if($item->attachments->isNotEmpty())
                                            @foreach($item->attachments as $attachment)
                                                <div class="mb-1">
                                                    <a href="{{ asset('storage/' . $attachment->attachment_path) }}"
                                                        target="_blank" class="btn btn-sm btn-outline-primary py-0 px-2 small">
                                                        <i class="fa fa-file me-1"></i> {{ $attachment->attachment_name }}
                                                    </a>
                                                </div>
                                            @endforeach
                                        @else
                                            <span class="text-muted small">No attachments</span>
                                        @endif --}}
                                    {{-- </td> --}}
                                    <td class="text-center">
                                        <div class="d-flex gap-2 justify-content-center">
                                            <a href="{{ route('advances.items.edit', [$advance->id, $item->id]) }}#item-form"
                                                class="btn btn-outline-warning btn-sm" title="Edit Item">
                                                <i class="fa-solid fa-pen"></i>
                                            </a>
                                            <form action="{{ route('advances.items.delete', [$advance->id, $item->id]) }}" method="POST"
                                                onsubmit="return confirm('Are you sure you want to delete this item?')" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm" title="Delete Item">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">
                                        No items found for this advance.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>

        </div>

        <div class="card shadow-sm border-0" id="item-form">

            <div class="card-header bg-white">
                <h5 class="mb-0 fw-semibold">
                    {{ $editingItem ? 'Edit advance Item' : 'Add advance Item' }}
                </h5>
            </div>

            <div class="card-body">
                <form
                    action="{{ $editingItem ? route('advances.items.update', [$advance->id, $editingItem->id]) : route('advances.items.store', $advance->id) }}"
                    method="POST">

                    @csrf

                    @if($editingItem)
                        @method('PUT')
                    @endif

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">
                                Category
                                <span class="text-danger">*</span>
                            </label>

                            <select name="category_id" class="form-select" required>
                                <option value="">Select Category</option>

                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ (string) old('category_id', $editingItem?->category_id) === (string) $category->id ? 'selected' : '' }}>
                                        {{ $category->category_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">
                                Amount
                                <span class="text-danger">*</span>
                            </label>

                            <input type="number" step="0.01" min="0" name="requested_amount" class="form-control"
                                value="{{ old('requested_amount', $editingItem?->requested_amount) }}" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">
                                Item Reason
                                <span class="text-danger">*</span>
                            </label>

                            <textarea name="expense_reason" rows="3" maxlength="500" class="form-control"
                                required>{{ old('expense_reason', $editingItem?->expense_reason) }}</textarea>
                        </div>

                        {{-- <div class="col-12">
                            <label class="form-label fw-semibold">
                                Attachments
                            </label>

                            <input type="file" name="attachments[]" class="form-control" multiple
                                accept=".jpg,.jpeg,.png,.pdf">

                            <div class="form-text">
                                Allowed file types: JPG, JPEG, PNG, PDF. Max file size: 5MB per file.
                            </div>
                        </div> --}}

                        {{-- @if($editingItem && $editingItem->attachments->isNotEmpty())
                            <div class="col-12">
                                <label class="form-label mb-2 fw-semibold">
                                    Current Attachments
                                </label>

                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($editingItem->attachments as $attachment)
                                        <a href="{{ asset('storage/' . $attachment->attachment_path) }}"
                                            target="_blank" class="btn btn-sm btn-outline-primary py-1 px-2">
                                            <i class="fa fa-file me-1"></i> {{ $attachment->attachment_name }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif --}}
                    </div>

                    <div class="mt-4 d-flex gap-2">
                        <a href="{{ route('advances.index') }}" class="btn btn-secondary">
                            Back
                        </a>

                        @if($editingItem)
                            <a href="{{ route('advances.items.index', $advance->id) }}" class="btn btn-outline-secondary">
                                Cancel Edit
                            </a>
                        @endif

                        <button type="submit" class="btn btn-primary">
                            {{ $editingItem ? 'Update Item' : 'Add Item' }}
                        </button>

                        <form action="{{ route('advances.submit', $advance->id) }}" method="POST" class="d-inline"
                            onsubmit="return confirm('Are you sure you want to submit this advance request to your manager?')">
                            @csrf

                        </form>


                    </div>

                    {{-- <div class="col-md-4">
                        <div class="border rounded p-3 h-100 bg-light">
                            {{-- <div class="text-muted small mb-1">
                                Approved Amount
                            </div> --}}

                            {{-- <div class="fw-semibold">
                                @if($advance->approved_amount !== null)
                                    ₹ {{ number_format($advance->approved_amount, 2) }}
                                @else
                                    -
                                @endif
                            </div>
                        </div>
                    </div> --}}

                </form>

            </div>
        </div>
    </div>
@endsection
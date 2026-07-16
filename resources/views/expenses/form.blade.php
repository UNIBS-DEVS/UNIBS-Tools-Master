@php
    $existingItems = [];

    if (
        isset($expense) &&
        isset($expense->items) &&
        count($expense->items) > 0
    ) {
        $existingItems = collect($expense->items)
            ->map(function ($item) {
                return [
                    'category_id' => $item->category_id,
                    'amount' => $item->amount,
                    'expense_reason' => $item->expense_reason,
                    'expense_date' => $item->expense_date,
                ];
            })
            ->values()
            ->all();
    }

    if (empty($existingItems)) {
        $existingItems = [
            [
                'category_id' => '',
                'amount' => '',
                'expense_reason' => '',
                'expense_date' => '',
            ]
        ];
    }

    $formItems = old('items', $existingItems);
    $formItems = is_array($formItems) && count($formItems) > 0
        ? array_values($formItems)
        : $existingItems;
@endphp

<div class="row">

    <div class="col-12">
        @include('partials.message')
    </div>

    @if ($errors->any())
        <div class="col-12 mb-3">
            <div class="alert alert-danger mb-0">
                {{ $errors->first() }}
            </div>
        </div>
    @endif

    <div class="col-md-12 mb-4">

        <label class="form-label">
            Expense Title
            <span class="text-danger">*</span>
        </label>

        <textarea name="expense_reason" rows="3" class="form-control"
            required>{{ old('expense_reason', $expense->user_remarks ?? '') }}</textarea>

    </div>

</div>

<hr>

<div class="d-flex justify-content-between align-items-center mb-3">

    <h5 class="mb-0">
        Expense Items
    </h5>



</div>

<div id="expense-items">

    @foreach($formItems as $index => $item)
        <div class="expense-item border rounded p-3 mb-3">

            <div class="row g-3">

                <div class="col-md-4">

                    <label class="form-label">
                        Category
                        <span class="text-danger">*</span>
                    </label>

                    <select name="items[{{ $index }}][category_id]" class="form-select" required>

                        <option value="">
                            Select Category
                        </option>

                        @foreach($categories as $category)

                            <option value="{{ $category->id }}" {{ (string) ($item['category_id'] ?? '') === (string) $category->id ? 'selected' : '' }}>
                                {{ $category->category_name }}
                            </option>

                        @endforeach

                    </select>

                </div>

                <div class="col-md-2">

                    <label class="form-label">
                        Amount
                        <span class="text-danger">*</span>
                    </label>

                    <input type="number" step="0.01" min="0" name="items[{{ $index }}][amount]"
                        value="{{ $item['amount'] ?? '' }}" class="form-control" required>

                </div>

                <div class="col-md-3">

                    <label class="form-label">
                        Expense Date
                        <span class="text-danger">*</span>
                    </label>

                    <input type="date" name="items[{{ $index }}][expense_date]" value="{{ $item['expense_date'] ?? '' }}"
                        class="form-control" required>

                </div>

                <div class="col-md-3">

                    <label class="form-label">
                        Attachments
                    </label>

                    <input type="file" name="items[{{ $index }}][attachments][]" class="form-control" multiple
                        accept=".jpg,.jpeg,.png,.pdf">

                </div>

                <div class="col-md-11">

                    <label class="form-label">
                        Item Reason
                        <span class="text-danger">*</span>
                    </label>

                    <textarea name="items[{{ $index }}][expense_reason]" rows="2" maxlength="500" class="form-control"
                        required>{{ $item['expense_reason'] ?? '' }}</textarea>

                </div>



                <div class="col-md-1 d-flex align-items-end">

                    <button type="button" class="btn btn-outline-danger remove-item">

                        <i class="fa fa-trash"></i>

                    </button>

                </div>

            </div>

        </div>
    @endforeach
</div>

<div class="mt-4 d-flex gap-2 justify-content-between align-items-center">

    <div>
        <a href="{{ route('expenses.index') }}" class="btn btn-sm btn-secondary">
            <i class="fa fa-arrow-left me-1"></i> Back
        </a>

        <button type="submit" name="action" value="draft" class="btn btn-sm btn-outline-primary">
            <i class="fa fa-save me-1"></i> Save Draft
        </button>

        <button type="submit" name="action" value="submit" class="btn btn-sm btn-primary">
            <i class="fa fa-paper-plane me-1"></i> Submit Request
        </button>
    </div>

    <button type="button" id="add-item" class="btn btn-sm btn-outline-success btn-sm">

        <i class="fa fa-plus"></i>
        Add Item

    </button>
</div>

@push('scripts')
    <script>

        let itemIndex = $('#expense-items .expense-item').length;

        function toggleRemoveButtons() {

            let items = $('#expense-items .expense-item');
            let disableRemove = items.length === 1;

            items.each(function () {

                $(this)
                    .find('.remove-item')
                    .prop('disabled', disableRemove);

            });

        }

        $('#add-item').on('click', function () {

            let html = `
                                <div class="expense-item border rounded p-3 mb-3">

                                    <div class="row g-3">

                                        <div class="col-md-4">

                                            <label class="form-label">
                                                Category
                                                <span class="text-danger">*</span>
                                            </label>

                                            <select
                                                name="items[${itemIndex}][category_id]"
                                                class="form-select"
                                                required>

                                                <option value="">
                                                    Select Category
                                                </option>

                                                @foreach($categories as $category)

                                                    <option value="{{ $category->id }}">
                                                        {{ $category->category_name }}
                                                    </option>

                                                @endforeach

                                            </select>

                                        </div>

                                        <div class="col-md-2">

                                            <label class="form-label">
                                                Amount
                                                <span class="text-danger">*</span>
                                            </label>

                                            <input
                                                type="number"
                                                step="0.01"
                                                min="0"
                                                name="items[${itemIndex}][amount]"
                                                class="form-control"
                                                required>

                                        </div>

                                        <div class="col-md-3">

                                            <label class="form-label">
                                                Expense Date
                                                <span class="text-danger">*</span>
                                            </label>

                                            <input
                                                type="date"
                                                name="items[${itemIndex}][expense_date]"
                                                class="form-control"
                                                required>

                                        </div>

                                        <div class="col-md-3">

                                            <label class="form-label">
                                                Item Reason
                                                <span class="text-danger">*</span>
                                            </label>

                                            <textarea
                                                name="items[${itemIndex}][expense_reason]"
                                                rows="2"
                                                maxlength="500"
                                                class="form-control"
                                                required></textarea>

                                        </div>

                                        <div class="col-md-11">

                                            <label class="form-label">
                                                Attachments
                                            </label>

                                            <input
                                                type="file"
                                                name="items[${itemIndex}][attachments][]"
                                                class="form-control"
                                                multiple
                                                accept=".jpg,.jpeg,.png,.pdf">

                                        </div>

                                        <div class="col-md-1 d-flex align-items-end">

                                            <button
                                                type="button"
                                                class="btn btn-outline-danger remove-item">

                                                <i class="fa fa-trash"></i>

                                            </button>

                                        </div>

                                    </div>

                                </div>
                            `;

            $('#expense-items').append(html);

            itemIndex++;

            toggleRemoveButtons();

        });

        $(document).on('click', '.remove-item', function () {

            if (!$(this).prop('disabled')) {

                $(this)
                    .closest('.expense-item')
                    .remove();

                toggleRemoveButtons();

            }

        });

        toggleRemoveButtons();

    </script>
@endpush
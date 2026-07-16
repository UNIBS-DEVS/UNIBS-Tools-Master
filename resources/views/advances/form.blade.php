@php
    $existingItems = [];



    if (
        isset($advance) &&
        isset($advance->items) &&
        count($advance->items) > 0
    ) {
        $existingItems = collect($advance->items)
            ->map(function ($item) {
                return [
                    'category_id' => $item->category_id,
                    'requested_amount' => $item->requested_amount,
                    'expense_reason' => $item->expense_reason,
                ];
            })
            ->values()
            ->all();
    }

    if (empty($existingItems)) {
        $existingItems = [
            [
                'category_id' => '',
                'requested_amount' => '',
                'expense_reason' => '',

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
            Advance Reason
            <span class="text-danger">*</span>
        </label>

        <textarea name="advance_reason" rows="3" class="form-control"
            required>{{ old('advance_reason', $advance->advance_reason ?? '') }}</textarea>

    </div>


</div>

<hr>

<div class="d-flex justify-content-between align-items-center mb-3">


    <h5 class="mb-0">
        Advance Items
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

                <div class="col-md-3">

                    <label class="form-label">
                        Requested Amount
                        <span class="text-danger">*</span>
                    </label>

                    <input type="number" step="0.01" min="0" name="items[{{ $index }}][requested_amount]"
                        value="{{ $item['requested_amount'] ?? '' }}" class="form-control" required>

                </div>

                {{-- <div class="col-md-3">

                    <label class="form-label">
                        Request Date
                        <span class="text-danger">*</span>
                    </label>

                    <input type="date" name="items[{{ $index }}][request_date]" value="{{ $item['request_date'] ?? '' }}"
                        class="form-control" required>

                </div> --}}

                <div class="col-md-4">

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

        <a href="{{ route('advances.index') }}" class="btn btn-sm btn-secondary">

            <i class="fa fa-arrow-left me-1"></i>
            Back

        </a>

        <button type="submit" name="action" value="draft" class="btn btn-sm btn-outline-primary">

            <i class="fa fa-save me-1"></i>
            Save Draft

        </button>

        <button type="submit" name="action" value="submit" class="btn btn-sm btn-primary">

            <i class="fa fa-paper-plane me-1"></i>
            Submit Request

        </button>

    </div>

    <button type="button" id="add-item" class="btn btn-sm btn-outline-success">

        <i class="fa fa-plus"></i>
        Add Item

    </button>

</div>

@push('scripts')

    <script>



        let itemIndex = $('#expense-items .expense-item').length;

        function toggleRemoveButtons() {
            const items = $('#expense-items .expense-item');

            const disableRemove = items.length === 1;

            items.each(function () {

                $(this)
                    .find('.remove-item')
                    .prop('disabled', disableRemove);

            });
        }

        $('#add-item').on('click', function () {

            const html = `
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

                                                <div class="col-md-3">

                                                    <label class="form-label">
                                                        Requested Amount
                                                        <span class="text-danger">*</span>
                                                    </label>

                                                    <input
                                                        type="number"
                                                        step="0.01"
                                                        min="0"
                                                        name="items[${itemIndex}][requested_amount]"
                                                        class="form-control"
                                                        required>

                                                    </div>



                                                <div class="col-md-4">

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


                                                </div >

                                        <div class="col-md-1 d-flex align-items-end">

                                            <button
                                                type="button"
                                                class="btn btn-outline-danger remove-item">

                                                <i class="fa fa-trash"></i>

                                            </button>

                                        </div>

                                            </div >

                                        </div >
                                        `;

            $('#expense-items').append(html);

            itemIndex++;

            toggleRemoveButtons();

        });

        $(document).on('click', '.remove-item', function () {

            if ($(this).prop('disabled')) {
                return;
            }

            $(this)
                .closest('.expense-item')
                .remove();

            toggleRemoveButtons();

        });

        toggleRemoveButtons();


    </script>
@endpush
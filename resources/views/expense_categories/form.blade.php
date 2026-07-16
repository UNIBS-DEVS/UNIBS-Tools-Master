<div class="mb-3">
    <label class="form-label">Category Name</label>

    <input type="text" name="category_name" class="form-control"
        value="{{ old('category_name', $category->category_name ?? '') }}" required>

    @error('category_name')
        <span class="text-danger">
            {{ $message }}
        </span>
    @enderror
</div>
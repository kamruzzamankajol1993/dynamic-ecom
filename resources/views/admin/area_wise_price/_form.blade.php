<div class="row">
    <div class="col-md-6 mb-3">
        <label for="label" class="form-label">Label <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('label') is-invalid @enderror" id="label" name="label" value="{{ old('label', $price->label ?? '') }}" required>
        @error('label')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="area" class="form-label">Area <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('area') is-invalid @enderror" id="area" name="area" value="{{ old('area', $price->area ?? '') }}" required>
        @error('area')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="days" class="form-label">Delivery Days <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('days') is-invalid @enderror" id="days" name="days" value="{{ old('days', $price->days ?? '') }}" required>
        @error('days')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="price" class="form-label">Price <span class="text-danger">*</span></label>
        <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price', $price->price ?? '') }}" required>
        @error('price')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
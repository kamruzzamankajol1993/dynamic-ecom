@extends('admin.master.master')
@section('title', 'Edit Offer Product')
@section('body')
<main class="main-content">
    <div class="container-fluid">
        <div class="mb-4">
            <h2>Edit Offer for: {{ $offerProduct->product->name }}</h2>
        </div>
        <div class="card">
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger"><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
                @endif
                <form action="{{ route('offer-product.update', $offerProduct->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Product</label>
                            <select name="product_id" class="form-control" required>
                                <option value="">Select a Product</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" {{ old('product_id', $offerProduct->product_id) == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Discount Price</label>
                            <input type="number" name="discount_price" step="0.01" class="form-control" value="{{ old('discount_price', $offerProduct->discount_price) }}" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Offer Start Date</label>
                            <input type="date" name="offer_start_date" class="form-control" value="{{ old('offer_start_date', $offerProduct->offer_start_date) }}" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Offer End Date</label>
                            <input type="date" name="offer_end_date" class="form-control" value="{{ old('offer_end_date', $offerProduct->offer_end_date) }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                             <label class="form-label">Status</label>
                             <select name="status" class="form-control">
                                 <option value="1" {{ old('status', $offerProduct->status) == 1 ? 'selected' : '' }}>Active</option>
                                 <option value="0" {{ old('status', $offerProduct->status) == 0 ? 'selected' : '' }}>Inactive</option>
                             </select>
                        </div>
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">Update Offer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
@endsection

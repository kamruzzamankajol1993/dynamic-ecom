@extends('admin.master.master')
@section('title', 'Create Offer Product')
@section('body')
<main class="main-content">
    <div class="container-fluid">
        <div class="mb-4">
            <h2>Create New Offer Product</h2>
        </div>
        <div class="card">
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger"><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
                @endif
                <form action="{{ route('offer-product.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Product</label>
                            <select name="product_id" class="form-control" required>
                                <option value="">Select a Product</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                                @endforeach
                            </select>
                        </div>
                         <div class="col-md-4 mb-3">
                            <label class="form-label">Discount Price</label>
                            <input type="number" name="discount_price" step="0.01" class="form-control" value="{{ old('discount_price') }}" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Offer Start Date</label>
                            <input type="date" name="offer_start_date" class="form-control" value="{{ old('offer_start_date') }}" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Offer End Date</label>
                            <input type="date" name="offer_end_date" class="form-control" value="{{ old('offer_end_date') }}" required>
                        </div>
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">Save Offer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
@endsection

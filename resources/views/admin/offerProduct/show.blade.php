@extends('admin.master.master')
@section('title', 'View Product Deal')

@section('body')
<main class="main-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Deal Details</h2>
            <a href="{{ route('offer-product.index') }}" class="btn btn-secondary">Back to List</a>
        </div>

        <div class="card">
            <div class="card-body">
                <h5 class="card-title">{{ $offerProduct->title }}</h5>
                <h6 class="card-subtitle mb-3 text-muted">Part of: {{ $offerProduct->bundleOffer->name }}</h6>
                <hr>
                <div class="row">
                    <div class="col-md-4"><strong>Discount Price:</strong> {{ $offerProduct->discount_price ? '$' . number_format($offerProduct->discount_price, 2) : 'N/A' }}</div>
                    <div class="col-md-4"><strong>Buy Quantity:</strong> {{ $offerProduct->buy_quantity }}</div>
                    <div class="col-md-4"><strong>Get Quantity:</strong> {{ $offerProduct->get_quantity }}</div>
                </div>
                <hr>
                <h6>Products in this Deal:</h6>
                <ul class="list-group">
                    @forelse ($products as $product)
                        <li class="list-group-item">{{ $product->name }} ({{ $product->product_code }})</li>
                    @empty
                        <li class="list-group-item">No products found for this deal.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</main>
@endsection

@extends('admin.master.master')
@section('title', 'Bundle Offer Details')
@section('body')
<main class="main-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Offer: {{ $bundleOffer->title }}</h2>
            <a href="{{ route('bundle-offer.index') }}" class="btn btn-secondary">Back to List</a>
        </div>
        <div class="row">
            <div class="col-md-7">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5>Tiers</h5>
                        <ul class="list-group">
                            @foreach($bundleOffer->tiers as $tier)
                            <li class="list-group-item">
                                Buy <b>{{$tier->buy_quantity}}</b> items for <b>${{$tier->offer_price}}</b>
                                @if($tier->get_quantity > 0)
                                    , and get <b>{{$tier->get_quantity}}</b> free.
                                @endif
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <div class="card">
                    <div class="card-body">
                        <h5>Eligible Products</h5>
                        <ul class="list-group">
                            @foreach($bundleOffer->products as $bundleProduct)
                                @php
                                    $name = 'N/A';
                                    if ($bundleProduct->productVariant) {
                                        $name = $bundleProduct->productVariant->product->name . ' - ' . $bundleProduct->productVariant->color->name;
                                    } elseif ($bundleProduct->product) {
                                        $name = $bundleProduct->product->name;
                                    }
                                @endphp
                                <li class="list-group-item">{{ $name }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection

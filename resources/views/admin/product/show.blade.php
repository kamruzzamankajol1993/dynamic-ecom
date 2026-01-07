@extends('admin.master.master')
@section('title', 'Product Details')
@section('css')
<style>
    /* --- Font & Layout Adjustments --- */
    .main-content {
        font-size: 0.9rem; /* Reduced base font size */
    }
    .main-content h2 { font-size: 1.6rem; }
    .main-content h5 { font-size: 1.1rem; }
    .main-content h6 { font-size: 0.95rem; }
    .btn { font-size: 0.875rem; }

    /* Cards */
    .card-body, .card-header {
        padding: 1rem;
    }

    /* Tables */
    .table {
        font-size: 0.875rem;
    }
    .table th, .table td {
        padding: 0.6rem 0.5rem; /* Reduce padding for a tighter look */
    }

    /* Specific to Show Page */
    .list-group-item {
        padding: 0.6rem 0.2rem; /* Adjust padding for list items */
        font-size: 0.875rem;
    }
    .list-group-flush>.list-group-item {
        border-width: 0 0 1px;
    }
    .variant-item p {
        margin-bottom: 0.5rem;
    }
    .list-group-item .badge {
        font-size: 0.8em;
        font-weight: 500;
    }
</style>
@endsection
@section('body')
<main class="main-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
            <h2 class="mb-0">Product: {{ $product->name }}</h2>
            <div>
                <a href="{{ route('product.index') }}" class="btn btn-secondary">Back to List</a>
                <a href="{{ route('product.edit', $product->id) }}" class="btn btn-info">Edit Product</a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                {{-- Main Product Details --}}
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Product Details</h5>
                        <div class="row">
                            <div class="col-md-4"><strong>Product Code:</strong></div>
                            <div class="col-md-8">{{ $product->product_code ?? 'N/A' }}</div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-4"><strong>Description:</strong></div>
                            <div class="col-md-8">{!! $product->description ?? 'No description provided.' !!}</div>
                        </div>
                    </div>
                </div>

                {{-- Assigned Size Chart --}}
                @if($product->assignChart)
                <div class="card mb-4">
                    <div class="card-header"><h5 class="mb-0">Assigned Size Chart</h5></div>
                    <div class="card-body">
                        <p class="text-muted">Based on default chart: {{ $product->assignChart->originalSizeChart->name ?? 'N/A' }}</p>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead>
                                    <tr>
                                        <th>Size</th>
                                        <th>Length</th>
                                        <th>Width</th>
                                        <th>Shoulder</th>
                                        <th>Sleeve</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($product->assignChart->entries as $entry)
                                    <tr>
                                        <td>{{ $entry->size }}</td>
                                        <td>{{ $entry->length }}</td>
                                        <td>{{ $entry->width }}</td>
                                        <td>{{ $entry->shoulder }}</td>
                                        <td>{{ $entry->sleeve }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endif


                {{-- Variations Details --}}
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Color & Size Variations</h5>
                    </div>
                    <div class="card-body">
                        @forelse($product->variants as $variant)
                            <div class="variant-item border rounded p-3 {{ !$loop->last ? 'mb-3' : '' }}">
                                <div class="row align-items-center">
                                    <div class="col-md-2">
                                        <img src="{{ $variant->variant_image ? asset('public/uploads/'.$variant->variant_image) : 'https://placehold.co/100x100' }}"
                                             class="img-fluid rounded" alt="Variant Image">
                                    </div>
                                    <div class="col-md-10">
                                        <h6><strong>Color:</strong> {{ $variant->color->name ?? 'N/A' }}</h6>
                                         <p class="mb-1"><strong>SKU:</strong> {{ $variant->variant_sku ?? 'N/A' }}</p>
                                        <p class="mb-2"><strong>Additional Price:</strong> {{ $variant->additional_price }}</p>
                                        <table class="table table-sm table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Size</th>
                                                    <th>Quantity</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $sizes = collect($variant->sizes)->keyBy('size_id');
                                                    $sizeModels = \App\Models\Size::whereIn('id', $sizes->keys())->get()->keyBy('id');
                                                @endphp
                                                @foreach($sizes as $sizeId => $sizeInfo)
                                                <tr>
                                                    <td>{{ $sizeModels[$sizeId]->name ?? 'Unknown Size' }}</td>
                                                    <td>{{ $sizeInfo['quantity'] }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted">This product has no color or size variations.</p>
                        @endforelse
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                {{-- Pricing & Organization --}}
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Media</h5>
                        <div>
                            {{-- Thumbnail Images Section --}}
                            <div class="mb-3">
                                <h6 class="mb-2">Thumbnail Images</h6>
                                <div class="d-flex flex-wrap gap-2">
                                    @if(!empty($product->thumbnail_image) && is_array($product->thumbnail_image))
                                        @foreach($product->thumbnail_image as $image)
                                            <img src="{{ asset('public/uploads/'.$image) }}" class="img-thumbnail" style="height: 80px; width: 80px; object-fit: cover;" alt="Thumbnail">
                                        @endforeach
                                    @else
                                        <p class="text-muted">No thumbnail images found.</p>
                                    @endif
                                </div>
                            </div>

                            {{-- Main Images Section --}}
                            <div class="mb-3">
                                <h6 class="mb-2">Main Images</h6>
                                <div class="d-flex flex-wrap gap-2">
                                    @if(!empty($product->main_image) && is_array($product->main_image))
                                        @foreach($product->main_image as $image)
                                            <img src="{{ asset('public/uploads/'.$image) }}" class="img-thumbnail" style="height: 80px; width: 80px; object-fit: cover;" alt="Main Image">
                                        @endforeach
                                    @else
                                        <p class="text-muted">No main images found.</p>
                                    @endif
                                </div>
                            </div>

                             {{-- Real Images Section --}}
                            <div>
                                <h6 class="mb-2">Real Images</h6>
                                <div class="d-flex flex-wrap gap-2">
                                    {{-- ▼▼▼ THIS SECTION HAS BEEN UPDATED ▼▼▼ --}}
                                    @if(!empty($product->real_image) && is_array($product->real_image))
                                        @foreach($product->real_image as $image)
                                            <img src="{{ asset('public/uploads/'.$image) }}" class="img-thumbnail" style="height: 80px; width: 80px; object-fit: cover;" alt="Real Image">
                                        @endforeach
                                    @else
                                        <p class="text-muted">No real images found.</p>
                                    @endif
                                    {{-- ▲▲▲ THIS SECTION HAS BEEN UPDATED ▲▲▲ --}}
                                </div>
                            </div>
                        </div>
                        <hr>
                        <h5 class="card-title mb-3">Pricing & Organization</h5>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between"><strong>Purchase Price:</strong> <span>{{ $product->purchase_price }}</span></li>
                            <li class="list-group-item d-flex justify-content-between"><strong>Base Price:</strong> <span>{{ $product->base_price }}</span></li>
                            <li class="list-group-item d-flex justify-content-between"><strong>Discount Price:</strong> <span>{{ $product->discount_price ?? 'N/A' }}</span></li>
                              {{-- --- MODIFIED: Display Multiple Categories --- --}}
                            <li class="list-group-item">
                                <strong class="d-block mb-2">Categories:</strong>
                                <div class="d-flex flex-wrap gap-1">
                                    @forelse($product->assigns->where('type', 'product_category') as $assign)
                                        @if($assign->category)
                                            <span class="badge bg-secondary">{{ $assign->category->name }}</span>
                                        @endif
                                    @empty
                                        <span>N/A</span>
                                    @endforelse
                                </div>
                            </li>
                            <li class="list-group-item d-flex justify-content-between"><strong>Brand:</strong> <span>{{ $product->brand->name ?? 'N/A' }}</span></li>
                            <li class="list-group-item d-flex justify-content-between"><strong>Unit:</strong> <span>{{ $product->unit->name ?? 'N/A' }}</span></li>
                            <li class="list-group-item d-flex justify-content-between"><strong>Fabric:</strong> <span>{{ $product->fabric->name ?? 'N/A' }}</span></li>
                            {{-- নতুন আইটেম --}}
    <li class="list-group-item d-flex justify-content-between">
        <strong>Pre Order:</strong>
        @if($product->is_pre_order)
            <span class="badge bg-warning text-dark">Yes</span>
        @else
            <span class="badge bg-secondary">No</span>
        @endif
    </li>

    {{-- মেসেজ দেখান যদি থাকে --}}
    @if($product->is_pre_order && $product->pre_order_msg)
    <li class="list-group-item">
        <strong>Pre Order Message:</strong>
        <p class="mt-1 text-muted">{{ $product->pre_order_msg }}</p>
    </li>
    @endif
                            <li class="list-group-item d-flex justify-content-between"><strong>Status:</strong>
                                @if($product->status)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection
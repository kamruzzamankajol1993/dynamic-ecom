@extends('admin.master.master')
@section('title', 'Product Details')
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
                        <div class="d-flex flex-wrap gap-2">
                            {{-- CORRECTED LOGIC FOR DISPLAYING MULTIPLE IMAGES --}}
                            @if(!empty($product->thumbnail_image) && is_array($product->thumbnail_image))
                                @foreach($product->thumbnail_image as $image)
                                    <img src="{{ asset('public/uploads/'.$image) }}" class="img-thumbnail" style="height: 80px; width: 80px; object-fit: cover;" alt="Thumbnail">
                                @endforeach
                            @else
                                <p class="text-muted">No thumbnail images.</p>
                            @endif
                             @if(!empty($product->main_image) && is_array($product->main_image))
                                @foreach($product->main_image as $image)
                                    <img src="{{ asset('public/uploads/'.$image) }}" class="img-thumbnail" style="height: 80px; width: 80px; object-fit: cover;" alt="Main Image">
                                @endforeach
                            @else
                                <p class="text-muted">No thumbnail images.</p>
                            @endif
                        </div>
                        <hr>
                        <h5 class="card-title mb-3">Pricing & Organization</h5>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between"><strong>Purchase Price:</strong> <span>{{ $product->purchase_price }}</span></li>
                            <li class="list-group-item d-flex justify-content-between"><strong>Base Price:</strong> <span>{{ $product->base_price }}</span></li>
                            <li class="list-group-item d-flex justify-content-between"><strong>Discount Price:</strong> <span>{{ $product->discount_price ?? 'N/A' }}</span></li>
                            <li class="list-group-item d-flex justify-content-between"><strong>Category:</strong> <span>{{ $product->category->name ?? 'N/A' }}</span></li>
                            <li class="list-group-item d-flex justify-content-between"><strong>Subcategory:</strong> <span>{{ $product->subcategory->name ?? 'N/A' }}</span></li>
                            <li class="list-group-item d-flex justify-content-between"><strong>Sub-Subcategory:</strong> <span>{{ $product->subSubcategory->name ?? 'N/A' }}</span></li>
                            <li class="list-group-item d-flex justify-content-between"><strong>Brand:</strong> <span>{{ $product->brand->name ?? 'N/A' }}</span></li>
                            <li class="list-group-item d-flex justify-content-between"><strong>Unit:</strong> <span>{{ $product->unit->name ?? 'N/A' }}</span></li>
                            <li class="list-group-item d-flex justify-content-between"><strong>Fabric:</strong> <span>{{ $product->fabric->name ?? 'N/A' }}</span></li>
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

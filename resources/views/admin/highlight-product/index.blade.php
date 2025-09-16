@extends('admin.master.master')
@section('title', 'Highlight Products')
@section('css')
    {{-- Add Select2 CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container .select2-selection--single {
            height: 38px;
        }
    </style>
@endsection
@section('body')
<main class="main-content">
    <div class="container-fluid">
        <div class="mb-4">
            <h2>Highlight Products</h2>
            <p>Select a product to highlight in each section on the website.</p>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                <form action="{{ route('highlight-product.store') }}" method="POST">
                    @csrf
                    {{-- First Section --}}
                    <div class="mb-5 border p-3 rounded">
                        <h4>First Section</h4>
                        <div class="mb-3">
                            <label for="first_section_title" class="form-label">Section Title</label>
                            <input type="text" name="first_section_title" id="first_section_title" class="form-control" value="{{ old('first_section_title', $firstSection->title ?? '') }}">
                        </div>
                        <div class="mb-3">
                            <label for="first_section_product_id" class="form-label">Select Product</label>
                            <select name="first_section_product_id" id="first_section_product_id" class="form-select product-select" required>
                                <option value="">Select a product</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" @selected(old('first_section_product_id', $firstSection->product_id ?? '') == $product->id)>
                                        {{ $product->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Second Section --}}
                    <div class="mb-4 border p-3 rounded">
                        <h4>Second Section</h4>
                        <div class="mb-3">
                            <label for="second_section_title" class="form-label">Section Title</label>
                            <input type="text" name="second_section_title" id="second_section_title" class="form-control" value="{{ old('second_section_title', $secondSection->title ?? '') }}">
                        </div>
                        <div class="mb-3">
                            <label for="second_section_product_id" class="form-label">Select Product</label>
                            <select name="second_section_product_id" id="second_section_product_id" class="form-select product-select" required>
                                <option value="">Select a product</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" @selected(old('second_section_product_id', $secondSection->product_id ?? '') == $product->id)>
                                        {{ $product->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Save Settings</button>
                </form>
            </div>
        </div>
    </div>
</main>
@endsection

@section('script')
    {{-- Add Select2 JS --}}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.product-select').select2({
                placeholder: "Search and select a product",
                allowClear: true
            });
        });
    </script>
@endsection

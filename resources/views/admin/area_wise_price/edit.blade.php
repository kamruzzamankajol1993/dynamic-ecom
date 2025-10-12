@extends('admin.master.master')
@section('title', 'Edit Area Wise Price')
@section('body')
<main class="main-content">
    <div class="container-fluid">
        <div class="mb-4">
            <h2>Edit Area Wise Price</h2>
        </div>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('area-wise-price.update', $price->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    @include('admin.area_wise_price._form')
                    <button type="submit" class="btn btn-primary">Update</button>
                </form>
            </div>
        </div>
    </div>
</main>
@endsection
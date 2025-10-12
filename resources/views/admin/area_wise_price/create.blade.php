@extends('admin.master.master')
@section('title', 'Add Area Wise Price')
@section('body')
<main class="main-content">
    <div class="container-fluid">
        <div class="mb-4">
            <h2>Add New Area Wise Price</h2>
        </div>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('area-wise-price.store') }}" method="POST">
                    @csrf
                    @include('admin.area_wise_price._form')
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>
</main>
@endsection
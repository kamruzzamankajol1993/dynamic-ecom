@extends('admin.master.master')
@section('title', 'Edit Supplier')
@section('body')
<main class="main-content">
    <div class="container-fluid">
        <div class="mb-4">
            <h2>Edit Supplier: {{ $supplier->company_name }}</h2>
        </div>
        <div class="card">
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger"><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
                @endif
                <form action="{{ route('supplier.update', $supplier->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Company Name</label>
                            <input type="text" name="company_name" class="form-control" value="{{ old('company_name', $supplier->company_name) }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Contact Person</label>
                            <input type="text" name="contact_person" class="form-control" value="{{ old('contact_person', $supplier->contact_person) }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone', $supplier->phone) }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email (Optional)</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $supplier->email) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">VAT/Tax Number (Optional)</label>
                            <input type="text" name="vat_number" class="form-control" value="{{ old('vat_number', $supplier->vat_number) }}">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Address</label>
                            <textarea name="address" class="form-control">{{ old('address', $supplier->address) }}</textarea>
                        </div>
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">Update Supplier</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
@endsection
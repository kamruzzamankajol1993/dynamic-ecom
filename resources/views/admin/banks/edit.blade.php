@extends('admin.master.master')
@section('title', 'Edit Bank')
@section('body')
<main class="main-content">
    <div class="container-fluid">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5>Edit Bank</h5>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger"><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
                @endif
                <form action="{{ route('banks.update', $bank->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">Bank Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $bank->name) }}" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Branch</label>
                        <input type="text" name="branch" value="{{ old('branch', $bank->branch) }}" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Account Number</label>
                        <input type="text" name="account_number" value="{{ old('account_number', $bank->account_number) }}" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea name="address" class="form-control">{{ old('address', $bank->address) }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select" required>
                            <option value="1" @selected(old('status', $bank->status) == 1)>Active</option>
                            <option value="0" @selected(old('status', $bank->status) == 0)>Inactive</option>
                        </select>
                    </div>
                    <div class="text-end">
                        <a href="{{ route('banks.index') }}" class="btn btn-secondary me-2">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update Bank</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
@endsection
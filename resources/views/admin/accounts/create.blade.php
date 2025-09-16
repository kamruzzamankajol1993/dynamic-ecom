@extends('admin.master.master')
@section('title', 'Add New Account')
@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection
@section('body')
<main class="main-content">
    <div class="container-fluid">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5>Add New Account</h5>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger"><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
                @endif
                <form action="{{ route('accounts.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Account Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Account Code</label>
                        <input type="text" name="code" value="{{ old('code') }}" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Account Type <span class="text-danger">*</span></label>
                        <select name="type" class="form-select" required>
                            <option value="Asset" @selected(old('type') == 'Asset')>Asset</option>
                            <option value="Liability" @selected(old('type') == 'Liability')>Liability</option>
                            <option value="Equity" @selected(old('type') == 'Equity')>Equity</option>
                            <option value="Revenue" @selected(old('type') == 'Revenue')>Revenue</option>
                            <option value="Expense" @selected(old('type') == 'Expense')>Expense</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Parent Account</label>
                        <select name="parent_id" class="form-control select2">
                            <option value="">None</option>
                            @foreach($parentAccounts as $account)
                                <option value="{{ $account->id }}" @selected(old('parent_id') == $account->id)>{{ $account->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-check mb-3">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" class="form-check-input" id="isActiveCheck" @checked(old('is_active', true))>
                        <label class="form-check-label" for="isActiveCheck">Is Active</label>
                    </div>
                    <div class="text-end">
                        <a href="{{ route('accounts.index') }}" class="btn btn-secondary me-2">Cancel</a>
                        <button type="submit" class="btn btn-primary">Save Account</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
@endsection
@section('script')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    $('.select2').select2({
        placeholder: "Select a parent account",
        allowClear: true
    });
});
</script>
@endsection
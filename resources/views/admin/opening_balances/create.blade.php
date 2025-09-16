@extends('admin.master.master')
@section('title', 'Add Opening Balance')
@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection
@section('body')
<main class="main-content">
    <div class="container-fluid">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5>Add New Opening Balance</h5>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger"><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
                @endif
                <form action="{{ route('opening-balances.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Account <span class="text-danger">*</span></label>
                        <select name="account_id" class="form-control select2" required>
                            <option value="">Select an account</option>
                            @foreach($accounts as $account)
                                <option value="{{ $account->id }}" @selected(old('account_id') == $account->id)>{{ $account->name }} ({{ $account->code ?? 'N/A' }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Amount <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="amount" value="{{ old('amount') }}" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Type <span class="text-danger">*</span></label>
                        <select name="type" class="form-select" required>
                            <option value="debit" @selected(old('type') == 'debit')>Debit</option>
                            <option value="credit" @selected(old('type') == 'credit')>Credit</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">As of Date <span class="text-danger">*</span></label>
                        <input type="date" name="as_of_date" value="{{ old('as_of_date', date('Y-m-d')) }}" class="form-control" required>
                    </div>
                    <div class="text-end">
                        <a href="{{ route('opening-balances.index') }}" class="btn btn-secondary me-2">Cancel</a>
                        <button type="submit" class="btn btn-primary">Save Balance</button>
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
    $('.select2').select2({ placeholder: "Select an account" });
});
</script>
@endsection
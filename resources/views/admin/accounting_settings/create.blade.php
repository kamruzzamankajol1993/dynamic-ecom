@extends('admin.master.master')
@section('title', 'Add Accounting Setting')
@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection
@section('body')
<main class="main-content">
    <div class="container-fluid">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5>Add New Accounting Setting</h5>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger"><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
                @endif
                <form action="{{ route('accounting-settings.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Transaction Type <span class="text-danger">*</span></label>
                        <select name="transaction_type" class="form-select" required>
                            <option value="" disabled selected>Select a type</option>
                            @foreach($availableTypes as $type)
                                <option value="{{ $type }}" @selected(old('transaction_type') == $type)>{{ $type }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Debit Account</label>
                        <select name="debit_account_id" class="form-control select2">
                            <option value="">None</option>
                            @foreach($accounts as $account)
                                <option value="{{ $account->id }}" @selected(old('debit_account_id') == $account->id)>{{ $account->name }} ({{ $account->code ?? 'N/A' }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Credit Account</label>
                        <select name="credit_account_id" class="form-control select2">
                            <option value="">None</option>
                            @foreach($accounts as $account)
                                <option value="{{ $account->id }}" @selected(old('credit_account_id') == $account->id)>{{ $account->name }} ({{ $account->code ?? 'N/A' }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="text-end">
                        <a href="{{ route('accounting-settings.index') }}" class="btn btn-secondary me-2">Cancel</a>
                        <button type="submit" class="btn btn-primary">Save Setting</button>
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
    $('.select2').select2({ placeholder: "Select an account", allowClear: true });
});
</script>
@endsection
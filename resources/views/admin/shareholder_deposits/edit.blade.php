@extends('admin.master.master')
@section('title', 'Edit Shareholder Deposit')
@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection
@section('body')
<main class="main-content">
    <div class="container-fluid">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5>Edit Shareholder Deposit</h5>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger"><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
                @endif
                <form action="{{ route('shareholder-deposits.update', $deposit->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">Shareholder <span class="text-danger">*</span></label>
                        <select name="shareholder_id" class="form-control select2" required>
                            @foreach($shareholders as $shareholder)
                                <option value="{{ $shareholder->id }}" @selected(old('shareholder_id', $deposit->shareholder_id) == $shareholder->id)>{{ $shareholder->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deposit To (Debit) <span class="text-danger">*</span></label>
                        <select name="cash_account_id" class="form-control select2" required>
                            @foreach($cashAccounts as $account)
                                <option value="{{ $account->id }}" @selected(old('cash_account_id', $deposit->cash_account_id) == $account->id)>{{ $account->name }} ({{ $account->code ?? 'N/A' }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deposit From (Credit) <span class="text-danger">*</span></label>
                        <select name="equity_account_id" class="form-control select2" required>
                             @foreach($equityAccounts as $account)
                                <option value="{{ $account->id }}" @selected(old('equity_account_id', $deposit->equity_account_id) == $account->id)>{{ $account->name }} ({{ $account->code ?? 'N/A' }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Amount <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="amount" value="{{ old('amount', $deposit->amount) }}" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Date <span class="text-danger">*</span></label>
                        <input type="date" name="date" value="{{ old('date', $deposit->date->format('Y-m-d')) }}" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Note</label>
                        <textarea name="note" class="form-control">{{ old('note', $deposit->note) }}</textarea>
                    </div>
                    <div class="text-end">
                        <a href="{{ route('shareholder-deposits.index') }}" class="btn btn-secondary me-2">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update Deposit</button>
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
    $('.select2').select2({ placeholder: "Select an option" });
});
</script>
@endsection
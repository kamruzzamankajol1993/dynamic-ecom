@extends('admin.master.master')
@section('title', 'Purchase Details')
@section('body')
<main class="main-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Purchase Details: {{ $purchase->purchase_no }}</h2>
            <a href="{{ route('purchase.index') }}" class="btn btn-secondary">Back to List</a>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <strong>Supplier:</strong> {{ $purchase->supplier->company_name ?? 'N/A' }}<br>
                        <strong>Date:</strong> {{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d M, Y') }}
                    </div>
                    <div class="col-md-6 text-md-end">
                        <strong>Status:</strong> 
                        @if($purchase->payment_status == 'paid') <span class="badge bg-success">Paid</span>
                        @else <span class="badge bg-danger">Due</span> @endif
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Color</th>
                                <th>Size</th>
                                <th>Quantity</th>
                                <th>Unit Cost</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($purchase->purchaseDetails as $item)
                            <tr>
                                <td>{{ $item->product->name }}</td>
                                <td>{{ $item->productVariant->color->name }}</td>
                                <td>{{ $item->size->name }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>৳{{ number_format($item->unit_cost, 2) }}</td>
                                <td>৳{{ number_format($item->total_cost, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="row justify-content-end mt-3">
                    <div class="col-md-4">
                        <table class="table">
                            <tr><th>Subtotal:</th><td>৳{{ number_format($purchase->subtotal, 2) }}</td></tr>
                            <tr><th>Discount:</th><td>- ৳{{ number_format($purchase->discount, 2) }}</td></tr>
                            <tr><th>Shipping:</th><td>৳{{ number_format($purchase->shipping_cost, 2) }}</td></tr>
                            <tr><th>Grand Total:</th><th>৳{{ number_format($purchase->total_amount, 2) }}</th></tr>
                            <tr><th>Paid:</th><td>৳{{ number_format($purchase->paid_amount, 2) }}</td></tr>
                            <tr><th>Due:</th><th>৳{{ number_format($purchase->due_amount, 2) }}</th></tr>
                        </table>
                    </div>
                </div>
                @if($purchase->notes)
                <div class="mt-3">
                    <strong>Notes:</strong>
                    <p>{{ $purchase->notes }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</main>
@endsection
@extends('admin.master.master')
@section('title', 'Order Details')

@section('css')
    <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    {{-- Add SweetAlert2 CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        body { background-color: #f4f7f6; }
        .card {
            border: none;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            margin-bottom: 1.5rem;
        }
        .card-header {
            background-color: #fff;
            border-bottom: 1px solid #e9ecef;
            padding: 1rem 1.5rem;
            font-weight: 600;
        }
        .card-body { padding: 1.5rem; }
        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 2rem;
        }
        .invoice-header .logo { max-height: 50px; }
        .invoice-header .invoice-info { text-align: right; }
        .invoice-header .invoice-info h4 { margin: 0; font-size: 1.25rem; }
        .invoice-header .invoice-info p { margin: 0; color: #6c757d; }
        .summary-card .list-group-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: none;
            padding: 0.75rem 0;
        }
        .summary-card .list-group-item strong { color: #343a40; }
        .summary-card .grand-total {
            font-size: 1.2rem;
            font-weight: bold;
            color: #0d6efd;
            border-top: 1px solid #e9ecef;
            margin-top: 0.5rem;
            padding-top: 0.75rem;
        }
        .actions-bar .btn { margin-right: 0.5rem; }
        .section-title { font-weight: 600; margin-bottom: 1rem; font-size: 1rem; }
        .address-block { line-height: 1.6; }

        @media print {
            body { background-color: #fff; }
            .actions-bar, .breadcrumb, .main-sidebar, .navbar, #paymentModal, .col-lg-4 { display: none !important; }
            .col-lg-8 { width: 100% !important; flex: 0 0 100%; max-width: 100%; }
            .main-content { padding: 0 !important; }
            .card { box-shadow: none; border: 1px solid #eee; }
        }
    </style>
@endsection

@section('body')
<main class="main-content">
    <div class="container-fluid">
        <div class="row">
            <!-- Main Content Column -->
            <div class="col-lg-8">
                <!-- Invoice Header -->
                <div class="card">
                    <div class="card-body">
                        <div class="invoice-header">
                            <div>
                                @if($companyInfo && $companyInfo->logo)
                                    <img src="{{ asset('/') }}public/black.png" alt="Company Logo" class="logo">
                                @else
                                    <h4 class="mb-0">{{ $companyInfo->ins_name ?? 'Company Name' }}</h4>
                                @endif
                                <address class="mt-2 mb-0 text-muted address-block">
                                    {{ $companyInfo->address ?? 'Company Address' }}<br>
                                    Phone: {{ $companyInfo->phone ?? 'N/A' }}
                                </address>
                            </div>
                            <div class="invoice-info">
                                <h4 class="text-primary">INVOICE</h4>
                                <p>#{{ $order->invoice_no }}</p>
                                <p>Date: {{ \Carbon\Carbon::parse($order->order_date)->format('d F, Y') }}</p>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="section-title">Billed To</h6>
                                <address class="address-block">
                                    <strong>{{ $order->customer->name }}</strong><br>
                                    {{ $order->customer->address ?? 'N/A' }}<br>
                                    <i class="fa fa-phone me-1"></i> {{ $order->customer->phone }}<br>
                                    <i class="fa fa-envelope me-1"></i> {{ $order->customer->email ?? 'N/A' }}
                                </address>
                            </div>
                            <div class="col-md-6 text-md-end">
                                <h6 class="section-title">Shipped To</h6>
                                <address class="address-block">
                                    <strong>{{ $order->customer->name }}</strong><br>
                                    {{ $order->shipping_address }}
                                </address>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Items -->
                <div class="card">
                    <div class="card-header">Order Items</div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4">Product</th>
                                        <th>Color / Size</th>
                                        <th class="text-center">Quantity</th>
                                        <th class="text-end">Unit Price</th>
                                        <th class="text-end pe-4">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->orderDetails as $detail)
                                    <tr>
                                        <td class="ps-4">{{ $detail->product->name ?? 'N/A' }}</td>
                                        <td>{{ $detail->color }} / {{ $detail->size }}</td>
                                        <td class="text-center">{{ $detail->quantity }}</td>
                                        <td class="text-end">{{ number_format($detail->unit_price, 2) }}</td>
                                        <td class="text-end pe-4">{{ number_format($detail->subtotal, 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Payment History -->
                <div class="card">
                    <div class="card-header">Payment History</div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-striped">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Method</th>
                                        <th>Note</th>
                                        <th class="text-end">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($order->payments as $payment)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('d M, Y') }}</td>
                                        <td>{{ $payment->payment_method }}</td>
                                        <td>{{ $payment->note }}</td>
                                        <td class="text-end">{{ number_format($payment->amount, 2) }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">No payments recorded yet.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar Column -->
            <div class="col-lg-4">
                <div class="card summary-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        Order Summary
                        <span class="badge bg-success text-uppercase">{{ $order->status }}</span>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">Subtotal <span>{{ number_format($order->subtotal, 2) }}</span></li>
                            <li class="list-group-item">Discount <span>- {{ number_format($order->discount, 2) }}</span></li>
                            <li class="list-group-item">Shipping <span>{{ number_format($order->shipping_cost, 2) }}</span></li>
                            <li class="list-group-item grand-total">Total <span>{{ number_format($order->total_amount, 2) }}</span></li>

                            @if($order->status == 'delivered')
                                <li class="list-group-item">Paid <span>{{ number_format($order->total_amount, 2) }}</span></li>
                                <li class="list-group-item grand-total text-success">Amount Due <span>0.00</span></li>
                            @else
                                <li class="list-group-item">Paid <span>{{ number_format($order->total_pay, 2) }}</span></li>
                                <li class="list-group-item grand-total text-danger">Amount Due <span>{{ number_format($order->due, 2) }}</span></li>
                            @endif
                        </ul>
                    </div>
                    <div class="card-footer bg-white p-3">
                        <div class="d-grid gap-2">
                             @if($order->status != 'delivered' && $order->due > 0)
                            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#paymentModal">
                                <i class="fa fa-money-bill me-1"></i> Make Payment
                            </button>
                            @endif
                            <a href="{{ route('order.edit', $order->id) }}" class="btn btn-outline-secondary"><i class="fa fa-edit me-1"></i> Edit Invoice</a>
                            <div class="btn-group">
                                <a href="{{ route('order.print.a4', $order->id) }}" target="_blank" class="btn btn-outline-secondary"><i class="fa fa-print me-1"></i> A4</a>
                                <a href="{{ route('order.print.pos', $order->id) }}" target="_blank" class="btn btn-outline-secondary"><i class="fa fa-receipt me-1"></i> POS</a>
                            </div>
                            {{-- Remove onsubmit and add an ID to the form --}}
                            <form id="delete-form" action="{{ route('order.destroy', $order->id) }}" method="POST" class="d-grid">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger"><i class="fa fa-trash me-1"></i> Delete Invoice</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Make Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Record a Payment for Order #{{ $order->invoice_no }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('order.payment.store', $order->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <strong>Amount Due:</strong> {{ number_format($order->due, 2) }} Taka
                    </div>
                    <div class="mb-3">
                        <label for="amount" class="form-label">Payment Amount*</label>
                        <input type="number" name="amount" class="form-control" value="{{ $order->due }}" max="{{ $order->due }}" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label for="payment_date" class="form-label">Payment Date*</label>
                        <input type="text" id="paymentDate" name="payment_date" class="form-control" value="{{ date('d-m-Y') }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="payment_method" class="form-label">Payment Method*</label>
                        <select name="payment_method" class="form-select" required>
                            <option value="Cash">Cash</option>
                            <option value="Bank Transfer">Bank Transfer</option>
                            <option value="Mobile Banking">Mobile Banking</option>
                            <option value="Card">Card</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="note" class="form-label">Note (Optional)</label>
                        <textarea name="note" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
{{-- Add SweetAlert2 JS --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    $("#paymentDate").datepicker({ dateFormat: 'dd-mm-yy' });

    // SweetAlert for delete confirmation
    $('#delete-form').on('submit', function(e){
        e.preventDefault();
        var form = this;

        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        })
    });
});
</script>
@endsection

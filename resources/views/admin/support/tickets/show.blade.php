@extends('admin.master.master')
@section('title', 'Ticket Details')
@section('body')
<main class="main-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Ticket Details</h2>
            <a href="{{ route('support-tickets.index') }}" class="btn btn-secondary">Back to List</a>
        </div>
        <div class="card">
            <div class="card-body">
                <h4>{{ $ticket->question }}</h4>
                <hr>
                <div class="mb-3">
                    <strong>Category:</strong> {{ $ticket->category->name ?? 'N/A' }}
                </div>
                <div class="mb-3">
                    <strong>Status:</strong>
                    @if($ticket->status) <span class="badge bg-success">Active</span>
                    @else <span class="badge bg-danger">Inactive</span> @endif
                </div>
                 <div class="mb-3">
                    <strong>Type:</strong>
                    @if($ticket->is_faq) <span class="badge bg-info">FAQ / Quick Answer</span>
                    @else <span class="badge bg-secondary">Regular Ticket</span> @endif
                </div>
                <hr>
                <h5>Answer:</h5>
                <div>
                    {!! $ticket->answer !!}
                </div>
            </div>
        </div>
    </div>
</main>
@endsection
@extends('admin.master.master')

@section('title')
Manage Contact Page | {{ $ins_name ?? 'Your App Name' }}
@endsection

@section('css')
<style>
    .card {
        border: none;
        border-radius: 0.75rem;
        box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.08);
    }
    .card-header {
        background-color: #ffffff;
        border-bottom: 1px solid #e9ecef;
        padding: 1.5rem 2.5rem;
        font-size: 1.35rem;
        font-weight: 700;
        color: #343a40;
    }
    .form-label {
        font-weight: 600;
    }
</style>
<style>
  
    .breadcrumb-item + .breadcrumb-item::before {
        content: var(--bs-breadcrumb-divider, "/");
        color: #6c757d;
        font-weight: normal;
    }
    .breadcrumb {
        background-color: #e9ecef;
        padding: 0.75rem 1.25rem;
        border-radius: 0.5rem;
        box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
    }
    .breadcrumb-item a {
        color: #007bff;
        text-decoration: none;
    }
    .breadcrumb-item a:hover {
        text-decoration: underline;
    }
    .breadcrumb-item.active {
        color: #6c757d;
    }
    .btn-create-blog {
        background-color: #007bff;
        border-color: #007bff;
        color: white;
        padding: 0.6rem 1.5rem;
        font-size: 0.9rem;
        border-radius: 0.4rem;
        transition: all 0.2s ease-in-out;
    }
    .btn-create-blog:hover {
        background-color: #0056b3;
        border-color: #0056b3;
        transform: translateY(-1px);
    }
   
    .action-buttons .btn {
        padding: 0.3rem 0.6rem;
        font-size: 0.8rem;
        margin-right: 0.25rem;
    }
    .status-badge {
        font-size: 0.8em;
        padding: 0.4em 0.8em;
        border-radius: 0.5rem;
    }
  
     .btn-back-to-list {
        background-color: #6c757d;
        border-color: #6c757d;
        color: white;
        padding: 0.6rem 1.5rem;
        font-size: 0.9rem;
        border-radius: 0.4rem;
        transition: all 0.2s ease-in-out;
    }
    .btn-back-to-list:hover {
        background-color: #5a6268;
        border-color: #5a6268;
        transform: translateY(-1px);
    }
</style>
@endsection

@section('body')
<div class="dashboard-body container py-4">
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Manage Contact Details</li>
            </ol>
        </nav>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm rounded-3">
        <div class="card-header text-center">
            <h3 class="mb-0">{{ isset($contact) ? 'Edit Contact Details' : 'Create Contact Details' }}</h3>
        </div>
        <div class="card-body">
            <form action="{{ isset($contact) ? route('contact.update', $contact->id) : route('contact.store') }}" method="POST">
                @csrf
                @if(isset($contact))
                    @method('PUT')
                @endif

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="phone_one" class="form-label">Phone Number 1</label>
                            <input type="text" class="form-control" id="phone_one" name="phone_one" value="{{ old('phone_one', $contact->phone_one ?? '') }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="phone_two" class="form-label">Phone Number 2 (Optional)</label>
                            <input type="text" class="form-control" id="phone_two" name="phone_two" value="{{ old('phone_two', $contact->phone_two ?? '') }}">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="email_one" class="form-label">Email Address 1</label>
                            <input type="email" class="form-control" id="email_one" name="email_one" value="{{ old('email_one', $contact->email_one ?? '') }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="email_two" class="form-label">Email Address 2 (Optional)</label>
                            <input type="email" class="form-control" id="email_two" name="email_two" value="{{ old('email_two', $contact->email_two ?? '') }}">
                        </div>
                    </div>
                </div>

                 <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="address_one" class="form-label">Address 1</label>
                            <textarea class="form-control" id="address_one" name="address_one" rows="3">{{ old('address_one', $contact->address_one ?? '') }}</textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="address_two" class="form-label">Address 2 (Optional)</label>
                           <textarea class="form-control" id="address_two" name="address_two" rows="3">{{ old('address_two', $contact->address_two ?? '') }}</textarea>
                        </div>
                    </div>
                </div>


                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-primary btn-lg">{{ isset($contact) ? 'Update Details' : 'Save Details' }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
{{-- Add any specific scripts for this page if needed --}}
@endsection

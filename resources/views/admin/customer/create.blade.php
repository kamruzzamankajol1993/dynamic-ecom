@extends('admin.master.master')
@section('title', 'Create Customer')
@section('body')
<main class="main-content">
    <div class="container-fluid">
        <div class="mb-4">
            <h2>Create New Customer</h2>
        </div>
        <div class="card">
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form action="{{ route('customer.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Type</label>
                            <select name="type" class="form-select" required>
                                <option value="normal" @selected(old('type') == 'normal')>Normal</option>
                                <option value="silver" @selected(old('type') == 'silver')>Silver</option>
                                <option value="platinum" @selected(old('type') == 'platinum')>Platinum</option>
                            </select>
                        </div>
                    </div>
                    <hr>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="create_login_account" value="1" id="createLoginAccount" @if(old('create_login_account')) checked @endif>
                        <label class="form-check-label" for="createLoginAccount">Create Login Account</label>
                    </div>
                    <div id="loginFields" style="display: none;">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email') }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Confirm Password</label>
                                <input type="password" name="password_confirmation" class="form-control">
                            </div>
                        </div>
                    </div>
                    <hr>
                    <h5>Addresses</h5>
                    <div id="address-container"></div>
                    <button type="button" id="add-address-btn" class="btn btn-sm btn-success mt-2"><i class="fa fa-plus me-1"></i>Add Address</button>
                    
                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">Save Customer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
@endsection

@section('script')
<script>
$(document).ready(function() {
    // Login fields toggle
    $('#createLoginAccount').on('change', function() {
        const loginFields = $('#loginFields');
        const isChecked = $(this).is(':checked');
        loginFields.toggle(isChecked);
        loginFields.find('input').prop('required', isChecked);
    }).trigger('change'); // Trigger on page load to set initial state

    // Dynamic address fields
    let addressIndex = 0;
    $('#add-address-btn').on('click', function() {
        const addressHtml = `
            <div class="row align-items-center mb-2 address-row">
                <div class="col-md-8">
                    <input type="text" name="addresses[${addressIndex}][address]" class="form-control" placeholder="Enter full address" required>
                </div>
                <div class="col-md-3">
                    <select name="addresses[${addressIndex}][address_type]" class="form-select">
                        <option value="Home">Home</option>
                        <option value="Office">Office</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-danger btn-sm remove-address-btn">&times;</button>
                </div>
            </div>`;
        $('#address-container').append(addressHtml);
        addressIndex++;
    });

    $('#address-container').on('click', '.remove-address-btn', function() {
        $(this).closest('.address-row').remove();
    });
});
</script>
@endsection

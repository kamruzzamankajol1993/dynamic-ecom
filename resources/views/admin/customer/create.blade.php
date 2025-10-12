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
                            <label class="form-label">Mobile</label>
                            <input type="number" name="phone" class="form-control" value="{{ old('phone') }}" 
                                   oninput="this.value = this.value.replace(/[^0-9]/g, ''); if (this.value.length > 11) this.value = this.value.slice(0, 11);" 
                                   pattern="[0-9]{11}" title="Please enter an 11-digit mobile number" required>
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
                                <div class="input-group">
                                    <input type="password" name="password" id="password" class="form-control">
                                    <button class="btn btn-outline-secondary toggle-password" type="button" data-target="#password">
                                        <i class="fa fa-eye-slash"></i>
                                    </button>
                                </div>
                                    <div id="passwordHelp" class="form-text">Password must be at least 8 characters long.</div>
                    
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Confirm Password</label>
                                <div class="input-group">
                                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
                                    <button class="btn btn-outline-secondary toggle-password" type="button" data-target="#password_confirmation">
                                        <i class="fa fa-eye-slash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <h5>Addresses <span class="text-danger">*</span></h5>
                    <div id="address-container">
                        <div class="row align-items-center mb-2 address-row">
                            <div class="col-md-7">
                                <input type="text" name="addresses[0][address]" class="form-control" placeholder="Enter full address" required>
                            </div>
                            <div class="col-md-3">
                                <select name="addresses[0][address_type]" class="form-select">
                                    <option value="Home">Home</option>
                                    <option value="Office">Office</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div class="col-md-1 text-center">
                                <label class="form-check-label" for="default_address_0">Default</label>
                                <input type="radio" name="default_address_index" value="0" id="default_address_0" class="form-check-input" checked required title="Select a default address">
                            </div>
                            <div class="col-md-1"></div>
                        </div>
                    </div>
                    <button type="button" id="add-address-btn" class="btn btn-sm btn-success mt-2"><i class="fa fa-plus me-1"></i>Add Another Address</button>
                    
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
    $('#createLoginAccount').on('change', function() {
        const loginFields = $('#loginFields');
        const isChecked = $(this).is(':checked');
        loginFields.toggle(isChecked);
        loginFields.find('input[name="email"], input[name="password"]').prop('required', isChecked);
    }).trigger('change');

    let addressIndex = 1; 
    $('#add-address-btn').on('click', function() {
        const addressHtml = `
            <div class="row align-items-center mb-2 address-row">
                <div class="col-md-7">
                    <input type="text" name="addresses[${addressIndex}][address]" class="form-control" placeholder="Enter full address" required>
                </div>
                <div class="col-md-3">
                    <select name="addresses[${addressIndex}][address_type]" class="form-select">
                        <option value="Home">Home</option>
                        <option value="Office">Office</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="col-md-1 text-center">
                    <label class="form-check-label" for="default_address_${addressIndex}">Default</label>
                    <input type="radio" name="default_address_index" value="${addressIndex}" id="default_address_${addressIndex}" class="form-check-input" required title="Select a default address">
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

    function validatePasswords() {
        const password = $('#password');
        const confirmPassword = $('#password_confirmation');
        if (confirmPassword.val().length > 0) {
            if (password.val() === confirmPassword.val()) {
                password.removeClass('is-invalid').addClass('is-valid');
                confirmPassword.removeClass('is-invalid').addClass('is-valid');
            } else {
                password.removeClass('is-valid').addClass('is-invalid');
                confirmPassword.removeClass('is-valid').addClass('is-invalid');
            }
        } else {
            password.removeClass('is-valid is-invalid');
            confirmPassword.removeClass('is-valid is-invalid');
        }
    }

    $('#password, #password_confirmation').on('keyup', validatePasswords);

    // Show/Hide Password functionality
    $('form').on('click', '.toggle-password', function() {
        const target = $($(this).data('target'));
        const icon = $(this).find('i');
        if (target.attr('type') === 'password') {
            target.attr('type', 'text');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        } else {
            target.attr('type', 'password');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        }
    });
});
</script>
@endsection
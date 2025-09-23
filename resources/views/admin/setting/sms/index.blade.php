@extends('admin.master.master')
@section('title', 'SMS Gateway Settings')

@section('body')
<main class="main-content">
    <div class="container-fluid">
        <h2 class="mb-4">SMS Gateway Settings</h2>

        <div class="card">
            <div class="card-body">
                @include('flash_message')
                <form action="{{ route('setting.sms.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="sms_api_key" class="form-label">API Key</label>
                            <input type="text" class="form-control" id="sms_api_key" name="sms_api_key" value="{{ $apiKey }}">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="sms_secret_key" class="form-label">Secret Key</label>
                            <input type="password" class="form-control" id="sms_secret_key" name="sms_secret_key" value="{{ $secretKey }}" placeholder="Enter new secret key to change">
                        </div>

                       

                        <div class="col-md-12 mb-3">
                            <label for="sms_body" class="form-label">SMS Body (for OTP)</label>
                            <textarea class="form-control" id="sms_body" name="sms_body" rows="3">{{ $smsBody }}</textarea>
                            <small class="form-text text-muted">
                              
                                Example: <code>Your verification code for Spotlight Attires is:- </code>
                            </small>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">Save Settings</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
@endsection
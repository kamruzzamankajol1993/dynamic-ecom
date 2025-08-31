@extends('admin.master.master')

@section('title', 'Analytics Settings')

@section('body')
<main class="main-content">
    <div class="container-fluid">
        <h2 class="mb-4">Analytics & Pixel Settings</h2>
        
        @include('flash_message')

        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Facebook Pixel Setting</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('settings.analytics.update') }}" method="POST">
                            @csrf
                            <div class="form-group row align-items-center mb-3">
                                <label for="facebook_pixel_status" class="col-md-3 col-form-label">Facebook Pixel</label>
                                <div class="col-md-9">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="facebook_pixel_status" id="facebook_pixel_status" value="1" @if($settings['facebook_pixel_status'] ?? 0 == 1) checked @endif>
                                        <label class="form-check-label" for="facebook_pixel_status">Enable</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row align-items-center mb-3">
                                <label for="facebook_pixel_id" class="col-md-3 col-form-label">Facebook Pixel ID</label>
                                <div class="col-md-9">
                                    <input type="text" id="facebook_pixel_id" name="facebook_pixel_id" class="form-control" value="{{ $settings['facebook_pixel_id'] ?? '' }}" placeholder="Facebook Pixel ID">
                                </div>
                            </div>
                             <div class="form-group row align-items-center mb-3">
                                <label for="facebook_access_token" class="col-md-3 col-form-label">Access Token</label>
                                <div class="col-md-9">
                                    <input type="text" id="facebook_access_token" name="facebook_access_token" class="form-control" value="{{ $settings['facebook_access_token'] ?? '' }}" placeholder="Access Token for Conversation API">
                                </div>
                            </div>
                            <div class="form-group row align-items-center mb-3">
                                <label for="facebook_test_event_code" class="col-md-3 col-form-label">Test Event Code</label>
                                <div class="col-md-9">
                                    <input type="text" id="facebook_test_event_code" name="facebook_test_event_code" class="form-control" value="{{ $settings['facebook_test_event_code'] ?? '' }}" placeholder="Test Event Code">
                                </div>
                            </div>
                            <div class="form-group row align-items-center mb-3">
                                <label for="facebook_domain_verification_code" class="col-md-3 col-form-label">Domain Verification</label>
                                <div class="col-md-9">
                                    <input type="text" id="facebook_domain_verification_code" name="facebook_domain_verification_code" class="form-control" value="{{ $settings['facebook_domain_verification_code'] ?? '' }}" placeholder="Domain Verification Code">
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-9 offset-md-3">
                                    <button type="submit" class="btn btn-primary">Save</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="mb-0">Google Analytics Setting</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('settings.analytics.update') }}" method="POST">
                            @csrf
                             <div class="form-group row align-items-center mb-3">
                                <label for="google_analytics_status" class="col-md-3 col-form-label">Google Analytics</label>
                                <div class="col-md-9">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="google_analytics_status" id="google_analytics_status" value="1" @if($settings['google_analytics_status'] ?? 0 == 1) checked @endif>
                                        <label class="form-check-label" for="google_analytics_status">Enable</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row align-items-center mb-3">
                                <label for="google_analytics_tracking_id" class="col-md-3 col-form-label">Tracking ID</label>
                                <div class="col-md-9">
                                    <input type="text" id="google_analytics_tracking_id" name="google_analytics_tracking_id" class="form-control" value="{{ $settings['google_analytics_tracking_id'] ?? '' }}" placeholder="Tracking ID">
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-9 offset-md-3">
                                    <button type="submit" class="btn btn-primary">Save</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card bg-light">
                    <div class="card-body">
                        <h5 class="card-title">Please be careful when you are configuring Facebook pixel.</h5>
                        <ol class="small text-muted ps-3">
                            <li>Log in to Facebook and go to your Ads Manager account.</li>
                            <li>Open the Navigation Bar and select Events Manager.</li>
                            <li>Copy your Pixel ID from underneath your Site Name and paste the number into the Facebook Pixel ID field.</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection
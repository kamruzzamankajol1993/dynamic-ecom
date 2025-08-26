@extends('admin.master.master')
@section('title', 'Reward Point Settings')

@section('body')
<main class="main-content">
    <div class="container-fluid">
        <div class="mb-4">
            <h2>Reward Point Settings</h2>
            <p class="text-muted">Configure how customers earn and redeem reward points.</p>
        </div>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('reward.settings.update') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-12 mb-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_enabled" value="1" id="isEnabled" {{ $settings->is_enabled ? 'checked' : '' }}>
                                <label class="form-check-label" for="isEnabled">Enable Reward Point System</label>
                            </div>
                        </div>

                        <h5 class="mb-3">Earning Points</h5>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Points per Unit</label>
                            <input type="number" name="earn_points_per_unit" class="form-control" value="{{ $settings->earn_points_per_unit }}" required>
                            <div class="form-text">e.g., Enter '1' if 1 point is earned.</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">For Every Amount Spent (Taka)</label>
                            <input type="number" name="earn_per_unit_amount" class="form-control" value="{{ $settings->earn_per_unit_amount }}" step="0.01" required>
                             <div class="form-text">e.g., Enter '100' for every 100 Taka spent.</div>
                        </div>

                        <hr class="my-4">

                        <h5 class="mb-3">Redeeming Points</h5>
                         <div class="col-md-6 mb-3">
                            <label class="form-label">Points to Redeem</label>
                            <input type="number" name="redeem_points_per_unit" class="form-control" value="{{ $settings->redeem_points_per_unit }}" required>
                            <div class="form-text">e.g., Enter '100' if 100 points are redeemed.</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Equals Discount Amount (Taka)</label>
                            <input type="number" name="redeem_per_unit_amount" class="form-control" value="{{ $settings->redeem_per_unit_amount }}" step="0.01" required>
                            <div class="form-text">e.g., Enter '1' for a 1 Taka discount.</div>
                        </div>

                    </div>
                    <button type="submit" class="btn btn-primary mt-3">Save Settings</button>
                </form>
            </div>
        </div>
    </div>
</main>
@endsection

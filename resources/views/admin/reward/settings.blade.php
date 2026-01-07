@extends('admin.master.master')
@section('title', 'Reward Point Settings')

@section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endsection

@section('body')
<main class="main-content">
    <div class="container-fluid">
        <div class="mb-4">
            <h2>Reward Point Settings</h2>
            <p class="text-muted">Configure how customers earn and redeem reward points.</p>
        </div>

        {{-- Flash Message Display --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if(session('info'))
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                {{ session('info') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Main Settings Card --}}
        <div class="card mb-4">
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

        {{-- Historical Points Generator Card --}}
        <div class="row">
            <div class="col-12">
                <div class="card border-warning">
                    <div class="card-header bg-warning text-white">
                        <h5 class="mb-0 text-white"><i class="fa fa-history me-1"></i> Generate Historical Points</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">
                            Use this tool to award points for orders delivered <strong>before</strong> the reward system was enabled. 
                            <br>
                            <span class="text-danger">* System will automatically skip orders that already have points. No duplicates will be created.</span>
                        </p>
                        
                        <form action="{{ route('reward.generate.historical') }}" method="POST" onsubmit="return confirm('Are you sure? This will add points to all eligible past orders up to this date.');">
                            @csrf
                            <div class="row align-items-end">
                                <div class="col-md-4">
                                    <label class="form-label">Select Cut-off Date</label>
                                    <input type="text" name="date" id="historicalDate" class="form-control bg-white" placeholder="Select Date" required>
                                    <div class="form-text">Orders delivered on or before this date will be processed.</div>
                                </div>
                                <div class="col-md-3">
                                    <button type="submit" class="btn btn-warning text-white w-100">
                                        <i class="fa fa-magic me-1"></i> Generate Points
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</main>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    // Initialize Flatpickr
    flatpickr("#historicalDate", {
        dateFormat: "Y-m-d",
        maxDate: "today", // Prevent future dates
        allowInput: true
    });
</script>
@endsection
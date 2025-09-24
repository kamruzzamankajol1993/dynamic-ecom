@extends('admin.master.master')
@section('title', 'Hero Right Section')
@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container--default .select2-selection--single { height: calc(2.25rem + 2px); padding: .375rem .75rem; border: 1px solid #ced4da; }
</style>
@endsection
@section('body')
<main class="main-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
            <h2 class="mb-0">Hero Right Section Management</h2>
        </div>
        @include('flash_message')
        <form action="{{ route('hero-right-slider.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                {{-- Top Row Section --}}
                <div class="col-12">
                    <div class="card mb-4">
                        <div class="card-header"><h4>Top Row Section</h4></div>
                        <div class="card-body">
                            @php $top = $sliders['top']; @endphp
                            <div class="row">
                                <div class="col-md-6 mb-3"><label class="form-label">Title*</label><input type="text" name="top[title]" class="form-control" value="{{ $top->title }}" required></div>
                                <div class="col-md-6 mb-3"><label class="form-label">Subtitle</label><input type="text" name="top[subtitle]" class="form-control" value="{{ $top->subtitle }}"></div>
                                <div class="col-md-6 mb-3"><label class="form-label">Image</label><input accept="image/webp" type="file" name="top[image]" class="form-control"><small class="text-muted">Upload to change. Recommended: 800x400px</small> @if($top->image)<img src="{{ asset('public/'.$top->image) }}" height="50" class="mt-2 d-block">@endif</div>
                                <div class="col-md-6 mb-3"><label class="form-label">Link to Bundle Offer*</label>
                                    <select name="top[bundle_offer_id]" class="form-select select2" required>
                                        @foreach($bundleOffers as $offer)<option value="{{ $offer->id }}" @selected($top->bundle_offer_id == $offer->id)>{{ $offer->name }}</option>@endforeach
                                    </select>
                                </div>
                                <div class="col-md-12"><div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="top[status]" value="1" @checked($top->status)><label class="form-check-label">Active</label></div></div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Bottom Row - Left --}}
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header"><h4>Bottom Row - Left Part</h4></div>
                        <div class="card-body">
                             @php $bottom_left = $sliders['bottom_left']; @endphp
                             @include('admin.hero_right_slider._form_bottom', ['section' => $bottom_left, 'prefix' => 'bottom_left'])
                        </div>
                    </div>
                </div>

                {{-- Bottom Row - Right --}}
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header"><h4>Bottom Row - Right Part</h4></div>
                        <div class="card-body">
                            @php $bottom_right = $sliders['bottom_right']; @endphp
                            @include('admin.hero_right_slider._form_bottom', ['section' => $bottom_right, 'prefix' => 'bottom_right'])
                        </div>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Save All Changes</button>
        </form>
    </div>
</main>
@endsection
@section('script')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    $('.select2').select2();
    // This script will be populated by the partials
});
</script>
@endsection
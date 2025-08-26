@extends('admin.master.master')
@section('title', 'Offer Section Control')

@section('body')
<main class="main-content">
    <div class="container-fluid">
        <h2 class="mb-4">Offer Section Control</h2>

        <div class="card">
            <div class="card-body">
                @include('flash_message')
                <form action="{{ route('offer-section.control.update') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="mb-3">Display Settings</h5>
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" name="is_visible" value="1" id="isVisible" @if($settings->is_visible) checked @endif>
                                <label class="form-check-label" for="isVisible">Show Offer Section on Frontend</label>
                            </div>
                            <div class="mb-3">
                                <label for="backgroundColor" class="form-label">Section Background Color</label>
                                {{-- Changed input type to "text" to allow gradients --}}
                                <input type="text" class="form-control" id="backgroundColor" name="background_color" value="{{ $settings->background_color }}" placeholder="e.g., #FFFFFF or linear-gradient(...)">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h5 class="mb-3">Content Settings</h5>
                            <div class="mb-3">
                                <label for="bundleOfferSelect" class="form-label">Select Offer to Display</label>
                                <select class="form-select" id="bundleOfferSelect" name="bundle_offer_id">
                                    <option value="">-- None --</option>
                                    @foreach($bundleOffers as $offer)
                                        <option value="{{ $offer->id }}" @if($settings->bundle_offer_id == $offer->id) selected @endif>
                                            {{ $offer->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="offerSectionRoute" class="form-label">Offer Route (e.g., /offers/summer-deal)</label>
                                <input type="text" class="form-control" id="offerSectionRoute" name="route" value="{{ $settings->route }}" placeholder="/offers/your-route">
                            </div>
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

@section('script')
<script>
$(document).ready(function() {
    $('#bundleOfferSelect').on('change', function() {
        // Get the text of the selected option, trimmed of any extra whitespace
        const selectedText = $(this).find('option:selected').text().trim();
        const routeInput = $('#offerSectionRoute');

        if ($(this).val()) {
            // Convert to lowercase
            let slug = selectedText.toLowerCase()
                // Replace spaces with hyphens
                .replace(/\s+/g, '-')
                // Remove any characters that are not letters, numbers, or hyphens
                .replace(/[^a-z0-9-]/g, '');
            
            // Set the value of the route input
            routeInput.val('/offers/' + slug);
        } else {
            // Clear the route if "None" is selected
            routeInput.val('');
        }
    });
});
</script>
@endsection

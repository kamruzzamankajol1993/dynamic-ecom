@extends('admin.master.master')

@section('title')
Default Location | {{ $ins_name ?? 'Your App' }}
@endsection

@section('css')
{{-- Add any page-specific CSS here --}}
@endsection

@section('body')
<div class="dashboard-body">
    <div class="breadcrumb-with-buttons mb-24 flex-between flex-wrap gap-8">
        <!-- Breadcrumb Start -->
        <div class="breadcrumb">
            <ul class="flex-align gap-4">
                <li><a href="{{ route('home') }}" class="text-gray-200 fw-normal text-15 hover-text-main-600">Home</a></li>
                <li><span class="text-gray-500 fw-normal d-flex"><i class="ph ph-caret-right"></i></span></li>
                <li><span class="text-main-600 fw-normal text-15">Default Location</span></li>
            </ul>
        </div>
        <!-- Breadcrumb End -->
    </div>

    <div class="card overflow-hidden">
        <div class="card-header">
            <h4 class="card-title mb-0">{{ isset($location) ? 'Edit Default Location' : 'Create Default Location' }}</h4>
        </div>
        <div class="card-body">
            
            {{-- Include for session-based success/info messages --}}
            @include('flash_message')

            {{-- Display validation errors if they exist --}}
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Whoops!</strong>
                    <span class="block sm:inline">There were some problems with your input.</span>
                    <ul class="mt-3 list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- The form action points to the store route for both creating and updating --}}
            <form action="{{ route('defaultLocation.store') }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label for="name" class="form-label">Location Name</label>
                    <input type="text" name="name" id="name" 
                           class="form-control"
                           placeholder="Enter location name"
                           value="{{ old('name', $location->name ?? '') }}" 
                           required>
                </div>

                <div class="mb-4">
                    <label for="latitude" class="form-label">Latitude</label>
                    <input type="text" name="latitude" id="latitude" 
                           class="form-control"
                           placeholder="Enter latitude"
                           value="{{ old('latitude', $location->latitude ?? '') }}" 
                           required>
                </div>

                <div class="mb-6">
                    <label for="longitude" class="form-label">Longitude</label>
                    <input type="text" name="longitude" id="longitude" 
                           class="form-control"
                           placeholder="Enter longitude"
                           value="{{ old('longitude', $location->longitude ?? '') }}" 
                           required>
                </div>

                <div class="flex items-center justify-end">
                    <button type="submit" class="btn btn-primary">
                        {{-- Change button text based on the action --}}
                        {{ isset($location) ? 'Update Location' : 'Save Location' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
{{-- Add any page-specific JavaScript here --}}
@endsection

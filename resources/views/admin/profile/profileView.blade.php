@extends('admin.master.master')

@section('title')
Profile | {{ $ins_name }}
@endsection

@section('css')
<style>
    .profile-details-list .list-group-item {
        border: none;
        padding: 0.75rem 0;
    }
    .profile-details-list .list-group-item strong {
        display: inline-block;
        width: 120px;
        color: #555;
    }
</style>
@endsection

@section('body')
<main class="main-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
            <h2 class="mb-0">User Profile</h2>
            <a href="{{ route('profileSetting') }}" class="btn btn-primary">
                <i data-feather="edit" class="me-1" style="width:18px; height:18px;"></i>
                Edit Profile
            </a>
        </div>

        @php
            $designationName = DB::table('designations')->where('id', Auth::user()->designation_id)->value('name');
            $branchName = DB::table('branches')->where('id', Auth::user()->branch_id)->value('name');
        @endphp

        <div class="row">
            <div class="col-lg-4">
                <div class="card text-center">
                    <div class="card-body">
                        @if(empty(Auth::user()->image))
                            <img src="{{asset('/')}}public/No_Image_Available.jpg" alt="user" class="img-fluid rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                        @else
                            <img src="{{asset('/')}}{{Auth::user()->image}}" alt="user" class="img-fluid rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                        @endif
                        <h4 class="card-title mb-1">{{ Auth::user()->name }}</h4>
                        <p class="text-muted">{{ $designationName }}</p>
                        <p class="text-muted small">Joined {{ date("F d, Y", strtotime(Auth::user()->created_at)) }}</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-4">About</h5>
                        
                        <ul class="list-group profile-details-list">
                            <li class="list-group-item d-flex">
                                <strong>Full Name:</strong>
                                <span>{{ Auth::user()->name }}</span>
                            </li>
                            <li class="list-group-item d-flex">
                                <strong>Email:</strong>
                                <span>{{ Auth::user()->email }}</span>
                            </li>
                            <li class="list-group-item d-flex">
                                <strong>Phone:</strong>
                                <span>{{ Auth::user()->phone }}</span>
                            </li>
                            <li class="list-group-item d-flex">
                                <strong>Branch:</strong>
                                <span>{{ $branchName }}</span>
                            </li>
                             <li class="list-group-item d-flex">
                                <strong>Address:</strong>
                                <span>{{ Auth::user()->address ?? 'N/A' }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection

@section('script')
@endsection
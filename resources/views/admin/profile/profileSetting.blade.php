@extends('admin.master.master')

@section('title')
Profile Setting | {{ $ins_name }}
@endsection

@section('css')

@endsection

@section('body')
<main class="main-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
            <h2 class="mb-0">Profile Setting</h2>
        </div>

        <div class="card">
            <div class="card-body">
                @include('flash_message')

                <form method="post" action="{{ route('profileSettingUpdate') }}" enctype="multipart/form-data" id="form" data-parsley-validate="">
                    @csrf
                    <input type="hidden" name="id" value="{{ Auth::user()->id }}">

                    <div class="row mb-4 pb-4 border-bottom">
                        <div class="col-md-3">
                            <h5 class="mb-1">Profile Photo</h5>
                            <p class="text-muted small">Update your profile photo.</p>
                        </div>
                        <div class="col-md-9">
                            <div class="d-flex align-items-center">
                                @if(empty(Auth::user()->image))
                                    <img src="{{asset('/')}}public/No_Image_Available.jpg" alt="user" class="img-fluid rounded-circle" style="width: 80px; height: 80px; object-fit: cover;">
                                @else
                                    <img src="{{asset('/')}}{{Auth::user()->image}}" alt="user" class="img-fluid rounded-circle" style="width: 80px; height: 80px; object-fit: cover;">
                                @endif
                                <input type="file" class="form-control ms-3" name="image" />
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4 pb-4 border-bottom">
                        <div class="col-md-3">
                            <h5 class="mb-1">Basic Information</h5>
                            <p class="text-muted small">Edit your personal details.</p>
                        </div>
                        <div class="col-md-9">
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" value="{{ Auth::user()->name }}" class="form-control" placeholder="Enter Name" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                                    <input oninput="this.value = this.value.slice(0, 11)" type="text" value="{{ Auth::user()->phone }}" class="form-control" data-parsley-length="[11, 11]" name="phone" placeholder="Enter Phone Number" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" value="{{ Auth::user()->email }}" class="form-control" name="email" placeholder="Enter Email" required>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Address <span class="text-danger">*</span></label>
                                    <textarea class="form-control" name="address" rows="3" placeholder="Enter Address" required>{{ Auth::user()->address }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-3">
                            <h5 class="mb-1">Change Password</h5>
                            <p class="text-muted small">Leave blank to keep current password.</p>
                        </div>
                        <div class="col-md-9">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">New Password</label>
                                    <input type="password" name="password" placeholder="Enter new password" class="form-control">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Confirm New Password</label>
                                    <input type="password" name="confirm-password" placeholder="Confirm new password" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-9 offset-md-3">
                             <button class="btn btn-primary" type="submit">
                                <i data-feather="save" class="me-1" style="width:18px; height:18px;"></i>
                                Save Changes
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
@endsection

@section('script')
@endsection
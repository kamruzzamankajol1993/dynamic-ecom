@extends('admin.master.master')

@section('title')

User Management | {{ $ins_name }}

@endsection


@section('css')

@endsection


@section('body')

<main class="main-content">
                <div class="container-fluid">
                    <h2 class="mb-4">Create User</h2>

                    <div class="card">
                        <div class="card-body">
                   @include('flash_message')

                         
        <form method="post" action="{{ route('users.store') }}" enctype="multipart/form-data" id="form" data-parsley-validate="">

            @csrf
        <div class="row">

            <div class="col-xl-4 col-xxl-4 col-sm-4 mb-3">
                <label class="form-label">Branch Name<span class="text-red font-w900">*</span>  </label>
                <select name="branch_id" class="form-control" required>
                        <option value="">--select brnach--</option>
                        @foreach($branchList as $branchInfos)
                        <option value="{{ $branchInfos->id }}">{{ $branchInfos->name }}</option>
                        @endforeach
                </select>
            </div>


            <div class="col-xl-4 col-xxl-4 col-sm-4 mb-3">
                <label class="form-label">Name<span class="text-red font-w900">*</span>  </label>
                <input type="text" name="name" class="form-control" id="" placeholder="Name" required>
            </div>


            <div class="col-xl-4 col-xxl-4 col-sm-4 mb-3">
                <label class="form-label">Designation Name<span class="text-red font-w900">*</span>  </label>
                <select name="designation_id" class="form-control" required>
                        <option value="">--select Designation--</option>
                        @foreach($designationList as $branchInfos)
                        <option value="{{ $branchInfos->id }}">{{ $branchInfos->name }}</option>
                        @endforeach
                </select>
            </div>

          


            <div class="col-xl-6 col-xxl-6 col-sm-6 mb-3">
                <label class="form-label">Phone Number<span class="text-red font-w900">*</span>  </label>
                <input oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                type = "number" maxlength = "11" class="form-control roles" id="text" data-parsley-length="[11, 11]" id="" name="phone" placeholder="Phone Number" required>
            </div>
            <div class="col-xl-6 col-xxl-6 col-sm-6 mb-3">
                <label class="form-label">Email<span class="text-red font-w900">*</span>  </label>
                <input type="email" class="form-control" name="email" id="" placeholder="Email" required>
            </div>
            <div class="col-xl-6 col-xxl-6 col-sm-6 mb-3">
                <label class="form-label">Role<span class="text-red font-w900">*</span>  </label>
                <select name="roles[]" class="form-control" required>
                    <option value="">--select brnach--</option>
                    @foreach ($roles as $value => $label)
                        <option value="{{ $value }}">
                            {{ $label }}
                        </option>
                     @endforeach
                </select>
            </div>
            <div class="col-xl-6 col-xxl-6 col-sm-6 mb-3">
                <label class="form-label">Profile Image<span class="text-red font-w900">*</span>  </label>
                <input type="file" class="form-control" name="image" id="" placeholder="Profile Image" required>
            </div>
            <div class="col-xl-12 col-xxl-12 col-sm-12 mb-3">
                <label class="form-label">Address<span class="text-red font-w900">*</span>  </label>
                <textarea class="form-control" name="address" id="" placeholder="Address" required></textarea>
            </div>
            
            <h4>Password</h4>
            <hr>
            <div class="col-xl-12 col-xxl-12 col-sm-12 mb-3">
                <label class="form-label">Password<span class="text-red font-w900">*</span></label>
                <input type="password" name="password" placeholder="Password" class="form-control" required>
            </div>
            <div class="col-xl-12 col-xxl-12 col-sm-12 mb-3">
                <label class="form-label">Confirm Password<span class="text-red font-w900">*</span></label>
                <input type="password" name="confirm-password" placeholder="Confirm Password" class="form-control" required>
            </div>
            
            
            <div class="col-xl-4 col-xxl-6 col-sm-6 mb-3 mt-4">
                <button class="btn btn-primary" title="Add Partner Info" type="submit"><i
                        class="fa-sharp fa-solid fa-add me-1"></i>Add User Info
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

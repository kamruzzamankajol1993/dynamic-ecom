@extends('admin.master.master')

@section('title')
Role Management | {{ $ins_name }}
@endsection

@section('css')
<style>
    .permission-group {
        border: 1px solid #e0e0e0;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 20px;
    }
    .permission-group-title {
        border-bottom: 2px solid #0d6efd;
        padding-bottom: 10px;
        margin-bottom: 15px;
        font-weight: 600;
        color: #333;
    }
</style>
@endsection

@section('body')
<main class="main-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h4 mb-0">Create New Role</h2>
            <a href="{{ route('roles.index') }}" class="btn btn-outline-primary btn-sm">
                <i class="fa-solid fa-arrow-left"></i> Back to List
            </a>
        </div>

        <div class="card">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0">Role Details</h5>
            </div>
            <div class="card-body">
                @include('flash_message')

                <form method="POST" action="{{ route('roles.store') }}">
                    @csrf
                    <div class="row">
                        {{-- Role Name --}}
                        <div class="col-md-12 mb-3">
                            <div class="form-group">
                                <label for="name" class="form-label"><strong>Role Name:</strong></label>
                                <input type="text" name="name" id="name" placeholder="e.g., Editor" class="form-control" required>
                            </div>
                        </div>

                        {{-- Permissions Section --}}
                        <div class="col-md-12">
                            <div class="form-group permission-group">
                                <strong class="permission-group-title d-block">Assign Permissions:</strong>
                                
                                {{-- "Select All" Checkbox --}}
                                <div class="form-check bg-light p-3 rounded mb-3">
                                    <input type="checkbox" id="checkAll" class="form-check-input">
                                    <label for="checkAll" class="form-check-label fw-bold">Select All Permissions</label>
                                </div>
                                <hr>

                                {{-- Permissions List --}}
                                <div class="row">
                                    @foreach($permission as $value)
                                    <div class="col-md-4 col-sm-6 mb-2">
                                        <div class="form-check">
                                            <input type="checkbox" name="permission[{{$value->id}}]" value="{{$value->id}}" class="form-check-input permission-checkbox" id="perm_{{$value->id}}">
                                            <label class="form-check-label" for="perm_{{$value->id}}">{{ $value->name }}</label>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        {{-- Submit Button --}}
                        <div class="col-12 text-center mt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa-solid fa-floppy-disk"></i> Submit
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
<script type="text/javascript">
    $(document).ready(function() {
        // "Select All" checkbox functionality
        $("#checkAll").click(function() {
            $('.permission-checkbox').prop('checked', $(this).prop('checked'));
        });

        // Individual permission checkbox functionality
        $(".permission-checkbox").change(function() {
            if (!$(this).prop("checked")) {
                $("#checkAll").prop("checked", false);
            }
            // Optional: check "All" if all permissions are selected
            if ($('.permission-checkbox:checked').length === $('.permission-checkbox').length) {
                $('#checkAll').prop('checked', true);
            }
        });
    });
</script>
@endsection
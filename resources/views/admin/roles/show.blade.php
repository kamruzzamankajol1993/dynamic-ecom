@extends('admin.master.master')

@section('title')
Role Management | {{ $ins_name }}
@endsection

@section('css')
<style>
    .permission-list .badge {
        font-size: 0.9rem;
        padding: 0.5em 0.9em;
    }
</style>
@endsection

@section('body')
<main class="main-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h4 mb-0">Role Details</h2>
            <a href="{{ route('roles.index') }}" class="btn btn-outline-primary btn-sm">
                <i class="fa-solid fa-arrow-left"></i> Back to List
            </a>
        </div>

        <div class="card">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <strong>Role:</strong> {{ $role->name }}
                </h5>
                <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-info btn-sm">
                    <i class="fa-solid fa-pencil"></i> Edit
                </a>
            </div>
            <div class="card-body">
                @include('flash_message')
                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <strong class="d-block mb-2">Permissions Assigned:</strong>
                            <div class="permission-list">
                                @if(!empty($rolePermissions) && count($rolePermissions) > 0)
                                    @foreach($rolePermissions as $v)
                                        <span class="badge bg-success me-2 mb-2">{{ $v->name }}</span>
                                    @endforeach
                                @else
                                    <span class="badge bg-secondary">No permissions assigned.</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection

@section('script')
@endsection
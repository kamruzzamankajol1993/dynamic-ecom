@extends('admin.master.master')

@section('title')

User Management | {{ $ins_name }}

@endsection


@section('css')
<style>

    .table-bordered {
    border: 1px solid #ccc;
    border-collapse: collapse;
}

.table-bordered th,
.table-bordered td {
    border: 1px solid #ccc;
    padding: 8px 12px;
    text-align: left;
}
    </style>
 <style>
        th.sortable {
            cursor: pointer;
        }
        th.sortable{
            background-color: #f8f9fa;
        }
    </style>
@endsection


@section('body')

<main class="main-content">
                <div class="container-fluid">
                    <h2 class="mb-4">User Profile Overview</h2>

                    <div class="card">
                        <div class="card-body">
                       <!-- Tabs -->
                    <ul class="nav nav-tabs mb-4" id="userTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab">
                                <i class="bi bi-person-lines-fill me-1"></i> Profile Info
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="security-tab" data-bs-toggle="tab" data-bs-target="#security" type="button" role="tab">
                                <i class="bi bi-shield-lock-fill me-1"></i> Security
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="userTabContent">
                        <!-- Profile Info Tab -->
                        <div class="tab-pane fade show active" id="profile" role="tabpanel">
                            <div class="row mt-2">
                                <div class="col-md-4 text-center mb-3">
                                    

                                         @if(empty($user->image))

                            <img src="{{asset('/')}}public/No_Image_Available.jpg" class="rounded-circle shadow-sm" width="160" height="160" alt="User Image"/>

                            @else
                            
                            <img src="{{ asset('/') }}{{ $user->image }}" class="rounded-circle shadow-sm" alt="No Image"/>

                            @endif


                                    <div class="mt-3">
                                        <span class="badge {{ $user->status == 1 ? 'bg-success' : 'bg-secondary' }}">
    <i class="bi bi-person-badge"></i> {{ $user->status == 1 ? 'Active' : 'Inactive' }}
</span>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="row mb-2">
                                        <div class="col-sm-4 fw-bold"><i class="bi bi-envelope-fill me-1"></i>Email:</div>
                                        <div class="col-sm-8">{{ $user->email }}</div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-sm-4 fw-bold"><i class="bi bi-phone-fill me-1"></i>Phone:</div>
                                        <div class="col-sm-8">{{ $user->phone ?? 'N/A' }}</div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-sm-4 fw-bold"><i class="bi bi-geo-alt-fill me-1"></i>Address:</div>
                                        <div class="col-sm-8">{{ $user->address ?? 'N/A' }}</div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-sm-4 fw-bold"><i class="bi bi-building me-1"></i>Branch ID:</div>
                                        <div class="col-sm-8">{{ \App\Models\Branch::where('id',$user->branch_id)->value('name') }}</div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-sm-4 fw-bold"><i class="bi bi-award-fill me-1"></i>Designation ID:</div>
                                        <div class="col-sm-8">{{ \App\Models\Designation::where('id',$user->designation_id)->value('name') }}</div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-sm-4 fw-bold"><i class="bi bi-patch-check-fill me-1"></i>Email Verified:</div>
                                        <div class="col-sm-8">
                                            @if($user->email_verified_at)
                                                <span class="badge bg-success">Verified</span>
                                            @else
                                                <span class="badge bg-secondary">Not Verified</span>
                                            @endif
                                        </div>
                                    </div>
                                   
                                </div>
                            </div>
                        </div>

                        <!-- Security Tab -->
                        <div class="tab-pane fade" id="security" role="tabpanel">
                            <div class="row mt-2">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold"><i class="bi bi-key-fill me-1"></i>Visible Password</label>
                                        <input type="text" class="form-control" value="{{ $user->viewpassword }}" readonly>
                                        <small class="text-muted"><i class="bi bi-exclamation-triangle-fill text-warning"></i> Plain text password (not secure)</small>
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                    </div> <!-- tab content -->
                        </div>
                    </div>
                </div>
               
            </main>

@endsection

@section('script')
@include('admin.users._partial.script')
@endsection


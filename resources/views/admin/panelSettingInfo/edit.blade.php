@extends('admin.master.master')

@section('title')
Update Panel Setting | {{ $ins_name }}
@endsection

@section('css')
<style>
    .image-preview-container {
        width: 150px;
        height: 150px;
        border: 2px dashed #ddd;
        border-radius: 10px;
        display: flex;
        justify-content: center;
        align-items: center;
        overflow: hidden;
        background-color: #f8f9fa;
        position: relative;
    }

    .image-preview {
        max-width: 100%;
        max-height: 100%;
    }
    
    .preview-text {
        color: #888;
        font-size: 0.9rem;
    }
</style>
@endsection

@section('body')
<main class="main-content">
    <div class="container-fluid">
        <h2 class="mb-4">Update Panel Setting</h2>

        <div class="card">
            <div class="card-body">
                @include('flash_message')

                <form method="post" action="{{ route('systemInformation.update', $panelSettingInfo->id) }}" enctype="multipart/form-data" id="form" data-parsley-validate="">
                    @csrf
                    @method('PUT')

                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab" aria-controls="general" aria-selected="true">General</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact" type="button" role="tab" aria-controls="contact" aria-selected="false">Contact</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="branding-tab" data-bs-toggle="tab" data-bs-target="#branding" type="button" role="tab" aria-controls="branding" aria-selected="false">Branding & Financials</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="seo-tab" data-bs-toggle="tab" data-bs-target="#seo" type="button" role="tab" aria-controls="seo" aria-selected="false">SEO</button>
                        </li>
                    </ul>

                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
                            <div class="row mt-4">
                                @if(Auth::user()->id == 1)
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Branch Name<span class="text-danger font-w900">*</span></label>
                                    <select name="branch_id" class="form-control" required>
                                        <option value="">-- Select Branch --</option>
                                        @foreach($branchInfo as $branchInfos)
                                        <option value="{{ $branchInfos->id }}" {{ $panelSettingInfo->branch_id == $branchInfos->id ? 'selected' : '' }}>
                                            {{ $branchInfos->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                @else
                                <input type="hidden" name="branch_id" value="{{ Auth::user()->branch_id }}">
                                @endif

                                <div class="col-md-12 mb-3">
                                    <label class="form-label">System Name<span class="text-danger font-w900">*</span></label>
                                    <input type="text" name="ins_name" class="form-control" value="{{ $panelSettingInfo->ins_name }}" placeholder="Enter System Name" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Admin Panel URL<span class="text-danger font-w900">*</span></label>
                                    <input type="text" class="form-control" name="main_url" value="{{ $panelSettingInfo->main_url }}" placeholder="e.g., https://admin.example.com" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Frontend URL<span class="text-danger font-w900">*</span></label>
                                    <input type="text" class="form-control" name="front_url" value="{{ $panelSettingInfo->front_url }}" placeholder="e.g., https://www.example.com" required>
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label class="form-label">System Address<span class="text-danger font-w900">*</span></label>
                                    <textarea class="form-control" name="address" rows="3" placeholder="Enter System Address" required>{{ $panelSettingInfo->address }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
                            <div class="row mt-4">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Primary Phone<span class="text-danger font-w900">*</span></label>
                                    <input oninput="this.value = this.value.slice(0, this.maxLength);" type="number" maxlength="11" class="form-control" data-parsley-length="[11, 11]" name="phone" value="{{ $panelSettingInfo->phone }}" placeholder="e.g., 01700000000" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Primary Email<span class="text-danger font-w900">*</span></label>
                                    <input type="email" class="form-control" name="email" value="{{ $panelSettingInfo->email }}" placeholder="e.g., primary@example.com" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Secondary Phone</label>
                                    <input oninput="this.value = this.value.slice(0, this.maxLength);" type="number" maxlength="11" class="form-control" data-parsley-length="[11, 11]" name="phone_one" value="{{ $panelSettingInfo->phone_one }}" placeholder="e.g., 01800000000">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Secondary Email</label>
                                    <input type="email" class="form-control" name="email_one" value="{{ $panelSettingInfo->email_one }}" placeholder="e.g., secondary@example.com">
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="branding" role="tabpanel" aria-labelledby="branding-tab">
                             <div class="row mt-4">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tax (%)<span class="text-danger font-w900">*</span></label>
                                    <input type="number" class="form-control" name="tax" value="{{ $panelSettingInfo->tax }}" placeholder="e.g., 15" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Service Charge (%)<span class="text-danger font-w900">*</span></label>
                                    <input type="number" class="form-control" name="charge" value="{{ $panelSettingInfo->charge }}" placeholder="e.g., 5" required>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">System Logo</label>
                                    <input type="file" class="form-control" name="logo" onchange="previewImage(this, 'logo-preview')">
                                    <small class="form-text text-muted">Leave blank to keep the current logo.</small>
                                    <div class="image-preview-container mt-2">
                                        <img id="logo-preview" src="{{ asset($panelSettingInfo->logo) }}" class="image-preview" alt="Logo Preview">
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">System Icon (Favicon)</label>
                                    <input type="file" class="form-control" name="icon" onchange="previewImage(this, 'icon-preview')">
                                    <small class="form-text text-muted">Leave blank to keep the current icon.</small>
                                    <div class="image-preview-container mt-2">
                                         <img id="icon-preview" src="{{ asset($panelSettingInfo->icon) }}" class="image-preview" alt="Icon Preview">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="seo" role="tabpanel" aria-labelledby="seo-tab">
                            <div class="row mt-4">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">SEO Keywords</label>
                                    <input type="text" class="form-control" name="keyword" value="{{ $panelSettingInfo->keyword }}" placeholder="e.g., keyword1, keyword2, keyword3">
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">SEO Description</label>
                                    <textarea class="form-control" name="description" rows="4" placeholder="Enter a brief description for search engines">{{ $panelSettingInfo->description }}</textarea>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Developed By</label>
                                    <input type="text" class="form-control" readonly value="{{ $panelSettingInfo->develop_by }}" name="develop_by">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button class="btn btn-primary" title="Update Panel Info" type="submit">
                            <i class="fas fa-sync-alt me-1"></i> Update Panel Info
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
@endsection

@section('script')
<script>
    function previewImage(input, previewId) {
        const preview = document.getElementById(previewId);
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection
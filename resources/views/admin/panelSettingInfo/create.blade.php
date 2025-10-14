@extends('admin.master.master')

@section('title')
Create Panel Info | {{ $ins_name }}
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
        display: none;
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
        <h2 class="mb-4">Create Panel Info</h2>

        <div class="card">
            <div class="card-body">
                @include('flash_message')

                <form method="post" action="{{ route('systemInformation.store') }}" enctype="multipart/form-data" id="form" data-parsley-validate="">
                    @csrf

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
                                        <option value="{{ $branchInfos->id }}">{{ $branchInfos->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @else
                                <input type="hidden" name="branch_id" value="{{ Auth::user()->branch_id }}">
                                @endif

                                <div class="col-md-12 mb-3">
                                    <label class="form-label">System Name<span class="text-danger font-w900">*</span></label>
                                    <input type="text" name="ins_name" class="form-control" placeholder="Enter System Name" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Admin Panel URL<span class="text-danger font-w900">*</span></label>
                                    <input type="text" class="form-control" name="main_url" placeholder="e.g., https://admin.example.com" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Frontend URL<span class="text-danger font-w900">*</span></label>
                                    <input type="text" class="form-control" name="front_url" placeholder="e.g., https://www.example.com" required>
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label class="form-label">System Address<span class="text-danger font-w900">*</span></label>
                                    <textarea class="form-control" name="address" rows="3" placeholder="Enter System Address" required></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
                            <div class="row mt-4">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Primary Phone<span class="text-danger font-w900">*</span></label>
                                    <input oninput="this.value = this.value.slice(0, this.maxLength);" type="number" maxlength="11" class="form-control" data-parsley-length="[11, 11]" name="phone" placeholder="e.g., 01700000000" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Primary Email<span class="text-danger font-w900">*</span></label>
                                    <input type="email" class="form-control" name="email" placeholder="e.g., primary@example.com" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Secondary Phone</label>
                                    <input oninput="this.value = this.value.slice(0, this.maxLength);" type="number" maxlength="11" class="form-control" data-parsley-length="[11, 11]" name="phone_one" placeholder="e.g., 01800000000">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Secondary Email</label>
                                    <input type="email" class="form-control" name="email_one" placeholder="e.g., secondary@example.com">
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="branding" role="tabpanel" aria-labelledby="branding-tab">
                            <div class="row mt-4">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tax (%)<span class="text-danger font-w900">*</span></label>
                                    <input type="number" class="form-control" name="tax" placeholder="e.g., 15" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Service Charge (%)<span class="text-danger font-w900">*</span></label>
                                    <input type="number" class="form-control" name="charge" placeholder="e.g., 5" required>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">System Logo<span class="text-danger font-w900">*</span></label>
                                    <input type="file" class="form-control" name="logo" required onchange="previewImage(this, 'logo-preview')">
                                    <div class="image-preview-container mt-2">
                                        <img id="logo-preview" class="image-preview" alt="Logo Preview">
                                        <span class="preview-text">150x150</span>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">System Icon (Favicon)<span class="text-danger font-w900">*</span></label>
                                    <input type="file" class="form-control" name="icon" required onchange="previewImage(this, 'icon-preview')">
                                    <div class="image-preview-container mt-2">
                                        <img id="icon-preview" class="image-preview" alt="Icon Preview">
                                        <span class="preview-text">32x32</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="seo" role="tabpanel" aria-labelledby="seo-tab">
                            <div class="row mt-4">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">SEO Keywords</label>
                                    <input type="text" class="form-control" name="keyword" placeholder="e.g., keyword1, keyword2, keyword3">
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">SEO Description</label>
                                    <textarea class="form-control" name="description" rows="4" placeholder="Enter a brief description for search engines"></textarea>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Developed By</label>
                                    <input type="text" class="form-control" readonly value="Resnova Tech Limited" name="develop_by">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button class="btn btn-primary" title="Add Panel Info" type="submit">
                            <i class="fas fa-plus me-1"></i> Add Panel Info
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
        const text = preview.nextElementSibling;

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
                if (text) text.style.display = 'none';
            };
            reader.readAsDataURL(input.files[0]);
        } else {
            preview.src = '#';
            preview.style.display = 'none';
            if (text) text.style.display = 'block';
        }
    }
</script>
@endsection
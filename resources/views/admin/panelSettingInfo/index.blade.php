@extends('admin.master.master')

@section('title')

Panel Setting | {{ $ins_name }}

@endsection


@section('css')

@endsection


@section('body')





<div class="content-body default-height">
    <div class="container-fluid">
        <div class="page-titles">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">{{ $ins_name }}</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Panel Setting</a></li>
            </ol>
        </div>

        <div class="row">
            <div class="col-xl-12">
                <div class="filter cm-content-box box-primary">
                    <div class="content-title SlideToolHeader">
                        <h4 class="cpa card-title">
                            @if(!$panelSettingInfo)
                            <i class="fa-sharp fa-solid fa-add me-2"></i>Add Panel Information
                            @else
                            <i class="fa-sharp fa-solid fa-add me-2"></i>Update Panel Information
                            @endif
                        </h4>
                        <div class="tools">
                            <a href="javascript:void(0);" class="expand handle"><i class="fa fa-angle-down"></i></a>
                        </div>
                    </div>
                    <div class="cm-content-body form excerpt">
                        <div class="card-body pb-2">

                            @if(!$panelSettingInfo)
                            <form method="post" action="{{ route('systemInformation.store') }}" enctype="multipart/form-data" id="form" data-parsley-validate="">

                                @csrf
                            <div class="row">
                                <div class="col-xl-4 col-xxl-4 col-sm-4 mb-3">
                                    <label class="form-label">System Name<span class="text-red font-w900">*</span>  </label>
                                    <input type="text" name="ins_name" class="form-control" id="" placeholder="System Name" required>
                                </div>
                                <div class="col-xl-4 col-xxl-4 col-sm-4 mb-3">
                                    <label class="form-label">System Phone Number<span class="text-red font-w900">*</span>  </label>
                                    <input oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                    type = "number" maxlength = "11" class="form-control roles" id="text" data-parsley-length="[11, 11]" id="" name="phone" placeholder="System Phone Number" required>
                                </div>
                                <div class="col-xl-4 col-xxl-4 col-sm-4 mb-3">
                                    <label class="form-label">System Email<span class="text-red font-w900">*</span>  </label>
                                    <input type="email" class="form-control" name="email" id="" placeholder="System Email" required>
                                </div>
                                <div class="col-xl-6 col-xxl-6 col-sm-6 mb-3">
                                    <label class="form-label">System Icon<span class="text-red font-w900">*</span>  </label>
                                    <input type="file" class="form-control" name="icon" id="" placeholder="System Icon" required>
                                </div>
                                <div class="col-xl-6 col-xxl-6 col-sm-6 mb-3">
                                    <label class="form-label">System Logo<span class="text-red font-w900">*</span>  </label>
                                    <input type="file" class="form-control" name="logo" id="" placeholder="System Logo" required>
                                </div>
                                <div class="col-xl-12 col-xxl-12 col-sm-12 mb-3">
                                    <label class="form-label">System Address<span class="text-red font-w900">*</span>  </label>
                                    <textarea class="form-control" name="address" id="" placeholder="System Address" required></textarea>
                                </div>
                                
                                <h4>Seo Information</h4>
                                <hr>
                                <div class="col-xl-12 col-xxl-12 col-sm-12 mb-3">
                                    <label class="form-label">Keyword</label>
                                    <input type="text" class="form-control" name="keyword" id="" placeholder="Keyword">
                                </div>
                                <div class="col-xl-12 col-xxl-12 col-sm-12 mb-3">
                                    <label class="form-label">Description</label>
                                    <textarea class="form-control" name="description" id="" placeholder="Description"></textarea>
                                </div>
                                <h4>Other</h4>
                                <hr>
                                <div class="col-xl-12 col-xxl-12 col-sm-12 mb-3">
                                    <label class="form-label">Developed By</label>
                                    <input type="text" class="form-control" name="develop_by" id="" placeholder="Developed By">
                                </div>
                                <div class="col-xl-4 col-xxl-6 col-sm-6 mb-3">
                                    <button class="btn btn-primary" title="Add Panel Info" type="submit"><i
                                            class="fa-sharp fa-solid fa-add me-1"></i>Add Panel Info
                                    </button>
                                  
                                </div>
                                

                            </div>
                        </form>
                        @else

                        <form method="post" action="{{ route('systemInformation.update',$panelSettingInfo->id)}}" enctype="multipart/form-data" id="form" data-parsley-validate="">
                            @csrf
                            @method('PUT')


                            <div class="row">
                                <div class="col-xl-4 col-xxl-4 col-sm-4 mb-3">
                                    <label class="form-label">System Name<span class="text-red font-w900">*</span>  </label>
                                    <input type="text" name="ins_name" value="{{ $panelSettingInfo->ins_name }}" class="form-control" id="" placeholder="System Name" required>
                                </div>
                                <div class="col-xl-4 col-xxl-4 col-sm-4 mb-3">
                                    <label class="form-label">System Phone Number<span class="text-red font-w900">*</span>  </label>
                                    <input oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                    type = "number" maxlength = "11" class="form-control col-xl-4" id="text" data-parsley-length="[11, 11]" value="{{ $panelSettingInfo->phone }}"  id="" name="phone" placeholder="System Phone Number" required>
                                </div>
                                <div class="col-xl-4 col-xxl-4 col-sm-4 mb-3">
                                    <label class="form-label">System Email<span class="text-red font-w900">*</span>  </label>
                                    <input type="email" class="form-control" value="{{ $panelSettingInfo->email }}"  name="email" id="" placeholder="System Email" required>
                                </div>
                                <div class="col-xl-6 col-xxl-6 col-sm-6 mb-3">
                                    <label class="form-label">System Icon<span class="text-red font-w900">*</span>  </label>
                                    <input type="file" class="form-control" name="icon" id="" placeholder="System Icon" >
                                    <img src="{{ asset('/') }}{{ $panelSettingInfo->icon }}" style="height:20px;"/>
                                </div>
                                <div class="col-xl-6 col-xxl-6 col-sm-6 mb-3">
                                    <label class="form-label">System Logo<span class="text-red font-w900">*</span>  </label>
                                    <input type="file" class="form-control" name="logo" id="" placeholder="System Logo" >
                                    <img src="{{ asset('/') }}{{ $panelSettingInfo->logo }}" style="height:20px;"/>
                                </div>
                                <div class="col-xl-12 col-xxl-12 col-sm-12 mb-3">
                                    <label class="form-label">System Address<span class="text-red font-w900">*</span>  </label>
                                    <textarea class="form-control" name="address" id="" placeholder="System Address" required>{{ $panelSettingInfo->address }}</textarea>
                                </div>
                                
                                <h4>Seo Information</h4>
                                <hr>
                                <div class="col-xl-12 col-xxl-12 col-sm-12 mb-3">
                                    <label class="form-label">Keyword</label>
                                    <input type="text" class="form-control" value="{{ $panelSettingInfo->keyword }}"  name="keyword" id="" placeholder="Keyword">
                                </div>
                                <div class="col-xl-12 col-xxl-12 col-sm-12 mb-3">
                                    <label class="form-label">Description</label>
                                    <textarea class="form-control" name="description" id="" placeholder="Description">{{ $panelSettingInfo->description }}</textarea>
                                </div>
                                <h4>Other</h4>
                                <hr>
                                <div class="col-xl-12 col-xxl-12 col-sm-12 mb-3">
                                    <label class="form-label">Developed By</label>
                                    <input type="text" class="form-control" value="{{ $panelSettingInfo->develop_by }}"  name="develop_by" id="" placeholder="Developed By">
                                </div>
                                <div class="col-xl-4 col-xxl-6 col-sm-6 mb-3">
                                    <button class="btn btn-primary" title="Update Panel Info" type="submit"><i
                                            class="fa-sharp fa-solid fa-add me-1"></i>Update Panel Info
                                    </button>
                                  
                                </div>
                                

                            </div>


                        </form>


                        @endif
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</div>


@endsection


@section('script')

@endsection
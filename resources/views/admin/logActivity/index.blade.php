@extends('admin.master.master')
@section('title')
Notification List
@endsection

@section('css')
@endsection

@section('body')

<div class="content-body default-height">
    <div class="container-fluid">
        <div class="page-titles">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">{{ $ins_name }}</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Notification List</a></li>
            </ol>
        </div>
@include('flash_message')
        <div class="row">
            <div class="col-xl-12">
               
                <div class="filter cm-content-box box-primary">
                    <div class="content-title SlideToolHeader">
                        <div class="cpa">
                            <i class="fa-solid fa-file-lines me-1"></i> Notification List
                        </div>
                        <div class="tools">
                            <a href="javascript:void(0);" class="expand handle"><i class="fa fa-angle-down"></i></a>
                        </div>
                    </div>
                    <div class="cm-content-body form excerpt">
                        <div class="card-body py-3">
                            <table id="responsiveTable" class="display responsive nowrap w-100">
                                <thead>
                                <tr>
                                    <th>Serial No.</th>
                                    <th>Person Name</th>
                                    <th>Activity</th>
                                    <th>Time</th>
                           
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @foreach($getAllTheData as $key=>$getAllTheDatas)
                                <tr>
                                    <td>{{ $key+1 }}</td>
                                    <td>{{ \App\Models\User::where('id',$getAllTheDatas->user_id)->value('name')}}</td>
                                    <td>{{ $getAllTheDatas->subject }}</td>
                                    <td>{{ $getAllTheDatas->activity_time }}</td>
                                    <td>{{ date("d-m-Y", strtotime($getAllTheDatas->created_at ))}}</td>
                                   
                                    <td>
                                        <div class="d-flex">
                                          
                                            <a href="#" onclick="deleteTag({{ $getAllTheDatas->id}})" class="btn btn-danger shadow btn-xs sharp"><i class="fa fa-trash"></i></a>
                                        
                                            
      
                                            <form id="delete-form-{{ $getAllTheDatas->id }}" action="{{ route('notificationList.destroy',$getAllTheDatas->id) }}" method="POST" style="display: none;">
                                              @method('DELETE')
                                                                            @csrf
      
                                                                        </form>
                                                                      
                                        </div>
                                    </td>
                                </tr>
                                @endforeach

                                </tbody>
                            </table>
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
<div class="modal fade" id="exampleModal{{ $key+1 }}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="exampleModalLabel">Update Information</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">

            <div class="progress" style="display: none;">
                <div class="bar"></div >
                <div class="percent">0%</div >
            </div>


            <form id="form" method="post" action="{{ route('permissions.update',$allPermissionGroup->group_name)}}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-4">
                            <label for="formrow-email-input" class="form-label">Group Name</label>
                            <input type="text" name="group_name" value="{{ $allPermissionGroup->group_name }}"  class="form-control" placeholder="Group Name" required>
                            <small></small>
                        </div>
                    </div>
                    <div class="col-md-12">

                        <table class="table table-bordered" id="dynamicAddRemovee{{ $key+1 }}">
                            <tr>
                                <th>Permission Name</th>
                                <th>Action</th>
                            </tr>
                            @foreach($permissionList as $j=>$allPermissionList)
                            @if($j == 0 )
                            <tr id="mDelete{{ $j+50000 }}">
                                <td><input type="text" name="name[]" value="{{ $allPermissionList->name }}" placeholder="Enter Ename" id="name{{ $j+50000 }}" class="form-control" />
                                </td>
                                <td><button type="button" name="add" id="dynamic-arr{{ $key+1 }}" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i></button></td>
                            </tr>
                            @else
                            <tr id="mDelete{{ $j+50000 }}">
                                <td><input type="text" name="name[]" value="{{ $allPermissionList->name }}" placeholder="Enter Ename" id="name{{ $j+50000 }}" class="form-control" />
                                </td>
                                <td><button type="button" id="remove-input-fieldd{{ $j+50000 }}" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></button></td>
                            </tr>

                            @endif
                            @endforeach
                        </table>

                    </div>






                </div>






                <div>
                    <button type="submit" class="btn btn-primary btn-sm w-md mt-3">Update</button>
                </div>


            </form>
        </div>

      </div>
    </div>
  </div>
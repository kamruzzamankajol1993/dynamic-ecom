@extends('admin.master.master')

@section('title')

Permission Management | {{ $ins_name }}

@endsection


@section('css')

@endsection


@section('body')

 <main class="main-content">
                <div class="container-fluid">
                    <h2 class="mb-4">Update Permission</h2>

                    <div class="card">
                        <div class="card-body">
                           <form id="form" method="post" action="{{ route('permissions.update',$pers)}}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-12">
                    <div class="mb-4">
                        <label for="formrow-email-input" class="form-label">Group Name</label>
                        <input type="text" name="group_name" value="{{ $pers }}"  class="form-control" placeholder="Group Name" required>
                        <small></small>
                    </div>
                </div>
                <div class="col-md-12">

                    <table class="table table-bordered" id="dynamicAddRemove">
                        <tr>
                            <th>Permission Name</th>
                            <th>Action</th>
                        </tr>
                        @foreach($persEdit as $j=>$allPermissionList)
                        @if($j == 0 )
                        <tr id="mDelete{{ $j+50000 }}">
                            <td><input type="text" name="name[]" value="{{ $allPermissionList->name }}" placeholder="Enter Ename" id="name{{ $j+50000 }}" class="form-control" />
                            </td>
                            <td><button type="button" name="add" id="dynamic-ar" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i></button></td>
                        </tr>
                        @else
                        <tr id="mDelete{{ $j+50000 }}">
                            <td><input type="text" name="name[]" value="{{ $allPermissionList->name }}" placeholder="Enter Ename" id="name{{ $j+50000 }}" class="form-control" />
                            </td>
                            <td><button type="button"  class="btn btn-danger btn-sm remove-input-field"><i class="fa fa-trash"></i></button></td>
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
               
            </main>


@endsection


@section('script')
<script type="text/javascript">
    var i = 0;
    $("#dynamic-ar").click(function () {
        ++i;
        $("#dynamicAddRemove").append('<tr><td><input type="text" name="name[]" id="name'+i+'" placeholder="Permission Name" class="form-control" /></td><td><button type="button" class="btn btn-danger btn-sm remove-input-field"><i class="fa fa-trash"></i></button></td></tr>'
            );
    });
    $(document).on('click', '.remove-input-field', function () {
        $(this).parents('tr').remove();
    });
</script>


<script type="text/javascript">
    var i = 0;
    $("[id^=dynamic-arr]").click(function () {
        ++i;
        var main_id = $(this).attr('id');
        var id_for_pass = main_id.slice(11);

        $("#dynamicAddRemovee"+id_for_pass).append('<tr id="mDelete'+i+'"><td><input type="text" name="name[]" id="name'+i+'" placeholder="Permission Name" class="form-control" /></td><td><button type="button" id="remove-input-field'+i+'" class="btn btn-danger btn-sm">Delete</button></td></tr>'
            );
    });


    $(document).on('click', '[id^=remove-input-fieldd]', function () {

        var main_id = $(this).attr('id');
        var id_for_pass = main_id.slice(19);

       // alert(id_for_pass);

        $("#mDelete"+id_for_pass).remove();

        //$(this).parents('tr').remove();
    });
</script>
@endsection
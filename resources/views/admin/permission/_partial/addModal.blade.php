<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="exampleModalLabel">Add Permission</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">

            <div class="progress" style="display: none;">
                <div class="bar"></div >
                <div class="percent">0%</div >
            </div>
            <form id="form" method="post" action="{{ route('permissions.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-4">
                            <label for="formrow-email-input" class="form-label">Group Name</label>
                            <input type="text" name="group_name"  class="form-control" placeholder="Group Name" required>
                            <small></small>
                        </div>
                    </div>
                    <div class="col-md-12">

                        <table class="table table-bordered" id="dynamicAddRemove">
                            <tr>
                                <th>Permission Name</th>
                                <th>Action</th>
                            </tr>
                            <tr>
                                <td><input type="text" name="name[]" placeholder="Permission Name" id="name0" class="form-control" />
                                </td>
                                <td><button type="button" name="add" id="dynamic-ar" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i></button></td>
                            </tr>
                        </table>

                    </div>



</div>






                <div>
                    <button type="submit" class="btn btn-primary btn-sm w-md mt-4">Submit</button>
                </div>


            </form>
        </div>

      </div>
    </div>
  </div>
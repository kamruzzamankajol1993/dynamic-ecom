<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="exampleModalLabel">Add Name</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">

            <div class="progress" style="display: none;">
                <div class="bar"></div >
                <div class="percent">0%</div >
            </div>
            <form id="form" method="post" action="{{ route('designation.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-4">
                            <label for="formrow-email-input" class="form-label text-dark">Name</label>
                            <input type="text" name="name"  class="form-control" placeholder="Name" required>
                            <small></small>
                        </div>
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
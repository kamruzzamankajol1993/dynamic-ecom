@extends('admin.master.master')
@section('title', 'Edit Contact Info')
@section('body')
<main class="main-content">
    <div class="container-fluid">
        <div class="mb-4">
            <h2>Edit Contact Info</h2>
        </div>
        <div class="card">
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger"><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
                @endif
                <form action="{{ route('ticket-contact-infos.update', $contact->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email Address</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $contact->email) }}" placeholder="example@email.com">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Phone Number</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone', $contact->phone) }}" placeholder="+8801...">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select" required>
                                <option value="1" @selected(old('status', $contact->status) == 1)>Active</option>
                                <option value="0" @selected(old('status', $contact->status) == 0)>Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">Update Contact</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
@endsection
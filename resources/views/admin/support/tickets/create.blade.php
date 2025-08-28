@extends('admin.master.master')
@section('title', 'Create Support Ticket')
@section('body')
<main class="main-content">
    <div class="container-fluid">
        <div class="mb-4">
            <h2>Create New Support Ticket / FAQ</h2>
        </div>
        <div class="card">
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger"><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
                @endif
                <form action="{{ route('support-tickets.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Question</label>
                            <input type="text" name="question" class="form-control" value="{{ old('question') }}" required>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Answer</label>
                            <textarea name="answer" class="form-control" rows="5" required>{{ old('answer') }}</textarea>
                            <small class="text-muted">You can use a rich text editor here (e.g., TinyMCE) for HTML content.</small>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Category</label>
                            <select name="category_id" class="form-select">
                                <option value="">None</option>
                                @foreach($categories as $category)
                                <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select" required>
                                <option value="1" @selected(old('status', 1) == 1)>Active</option>
                                <option value="0" @selected(old('status') == 0)>Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3 align-self-center">
                            <div class="form-check form-switch">
                              <input class="form-check-input" type="checkbox" name="is_faq" value="1" id="is_faq" @if(old('is_faq')) checked @endif>
                              <label class="form-check-label" for="is_faq">Mark as Quick Answer / FAQ</label>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">Save Ticket</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
@endsection
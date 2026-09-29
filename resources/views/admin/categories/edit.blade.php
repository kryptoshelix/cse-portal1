@extends('layouts.admin')
@section('title','Edit Category')
@section('content')
<h1 class="h4 fw-bold mb-3">Edit category “{{ $category->name }}”</h1>
<form method="POST" action="{{ route('admin.categories.update',$category) }}" class="card border-0 shadow-sm col-lg-6">
  @csrf @method('PUT')
  <div class="card-body">
    <div class="mb-3"><label for="name" class="form-label">Name</label>
      <input id="name" name="name" required maxlength="100" value="{{ old('name',$category->name) }}" class="form-control @error('name') is-invalid @enderror">
      @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
    <div class="mb-3"><label for="description" class="form-label">Description</label>
      <textarea id="description" name="description" rows="2" class="form-control">{{ old('description',$category->description) }}</textarea></div>
    <div class="form-check mb-3"><input type="checkbox" value="1" name="is_active" id="is_active" class="form-check-input" @checked(old('is_active',$category->is_active))><label class="form-check-label" for="is_active">Active (selectable in submission forms)</label></div>
    <div class="d-flex gap-2"><button class="btn btn-cse">Save changes</button><a class="btn btn-outline-secondary" href="{{ route('admin.categories.index') }}">Cancel</a></div>
  </div>
</form>
@endsection

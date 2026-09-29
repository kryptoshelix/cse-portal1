@extends('layouts.admin')
@section('title','Categories')
@section('content')
<div class="row g-4">
  <div class="col-lg-7">
    <h1 class="h4 fw-bold mb-3">Achievement categories</h1>
    <div class="card border-0 shadow-sm"><div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead><tr><th>Name</th><th>Description</th><th>Records</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
        <tbody>
        @forelse($categories as $c)
          <tr>
            <td class="fw-medium">{{ $c->name }}</td>
            <td class="small text-muted">{{ Str::limit($c->description, 60) }}</td>
            <td class="small">{{ $c->achievements_count }}</td>
            <td><span class="badge bg-{{ $c->is_active ? 'success' : 'secondary' }}">{{ $c->is_active ? 'Active' : 'Inactive' }}</span></td>
            <td class="text-end">
              <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.categories.edit',$c) }}"><i class="bi bi-pencil"></i></a>
              @include('partials.confirm-form', ['action'=>route('admin.categories.destroy',$c),'method'=>'DELETE','btnClass'=>'btn-outline-danger','confirm'=>'Delete this category? It can only be deleted if no achievements use it.','slot'=>'<i class="bi bi-trash"></i>'])
            </td>
          </tr>
        @empty
          <tr><td colspan="5" class="text-center text-muted py-4">No categories yet.</td></tr>
        @endforelse
        </tbody>
      </table>
    </div></div>
  </div>
  <div class="col-lg-5">
    <h2 class="h5 fw-bold mb-3">Add category</h2>
    <form method="POST" action="{{ route('admin.categories.store') }}" class="card border-0 shadow-sm">
      @csrf
      <div class="card-body">
        <div class="mb-3"><label for="name" class="form-label">Name</label>
          <input id="name" name="name" required maxlength="100" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror">
          @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="mb-3"><label for="description" class="form-label">Description</label>
          <textarea id="description" name="description" rows="2" maxlength="500" class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
          @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="form-check mb-3"><input type="checkbox" value="1" name="is_active" id="is_active" class="form-check-input" @checked(old('is_active',true))><label class="form-check-label" for="is_active">Active</label></div>
        <button class="btn btn-cse w-100">Create category</button>
      </div>
    </form>
  </div>
</div>
@endsection
